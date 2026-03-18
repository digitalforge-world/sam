import axios from 'axios';
import * as SecureStore from 'expo-secure-store';
import AsyncStorage from '@react-native-async-storage/async-storage';
import NetInfo from '@react-native-community/netinfo';

const apiClient = axios.create({
    baseURL: 'https://ofca.digitalforges.org/api',
    headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
    timeout: 15000,
});

// ── Token Bearer ──────────────────────────────────────────────
apiClient.interceptors.request.use(
    async (config) => {
        try {
            const token = await SecureStore.getItemAsync('userToken');
            if (token) config.headers.Authorization = `Bearer ${token}`;
        } catch (_) { }
        return config;
    },
    (error) => Promise.reject(error)
);

// ── Gestion 401 ───────────────────────────────────────────────
let _onUnauthenticated = null;
export function setUnauthenticatedHandler(handler) { _onUnauthenticated = handler; }

apiClient.interceptors.response.use(
    (response) => response,
    async (error) => {
        if (error.response?.status === 401) {
            try {
                await SecureStore.deleteItemAsync('userToken');
                await SecureStore.deleteItemAsync('userInfo');
            } catch (_) { }
            if (_onUnauthenticated) _onUnauthenticated();
        }
        return Promise.reject(error);
    }
);

// ─────────────────────────────────────────────────────────────
// STORAGE KEYS
// ─────────────────────────────────────────────────────────────
const CACHE_KEY = 'ofca_cache';
const QUEUE_KEY = 'ofca_queue';
const CACHE_TTL = 24 * 60 * 60 * 1000; // 24h

// ── Cache (données de référence) ─────────────────────────────
const getCache = async (key) => {
    try {
        const raw = await AsyncStorage.getItem(CACHE_KEY);
        const all = raw ? JSON.parse(raw) : {};
        const item = all[key];
        if (!item || Date.now() - item.ts > CACHE_TTL) return null;
        return item.data;
    } catch { return null; }
};

const setCache = async (key, data) => {
    try {
        const raw = await AsyncStorage.getItem(CACHE_KEY);
        const all = raw ? JSON.parse(raw) : {};
        all[key] = { data, ts: Date.now() };
        await AsyncStorage.setItem(CACHE_KEY, JSON.stringify(all));
    } catch { }
};

// ── File d'attente (formulaires offline) ─────────────────────
export const addToQueue = async (method, url, data, label = '') => {
    try {
        const raw = await AsyncStorage.getItem(QUEUE_KEY);
        const queue = raw ? JSON.parse(raw) : [];
        queue.push({
            id: Date.now().toString(),
            method, url, data, label,
            createdAt: new Date().toLocaleString('fr-FR'),
            status: 'pending',
        });
        await AsyncStorage.setItem(QUEUE_KEY, JSON.stringify(queue));
    } catch { }
};

export const getQueue = async () => {
    try {
        const raw = await AsyncStorage.getItem(QUEUE_KEY);
        return raw ? JSON.parse(raw) : [];
    } catch { return []; }
};

export const removeFromQueue = async (id) => {
    try {
        const raw = await AsyncStorage.getItem(QUEUE_KEY);
        const queue = raw ? JSON.parse(raw) : [];
        const filtered = queue.filter(item => item.id !== id);
        await AsyncStorage.setItem(QUEUE_KEY, JSON.stringify(filtered));
    } catch { }
};

// ── Sync manuel ───────────────────────────────────────────────
export const syncAll = async (onProgress) => {
    const state = await NetInfo.fetch();
    if (!state.isConnected) throw new Error('Pas de connexion internet.');

    const queue = await getQueue();
    if (queue.length === 0) return { synced: 0, failed: 0, total: 0 };

    let synced = 0, failed = 0;

    for (let i = 0; i < queue.length; i++) {
        const item = queue[i];
        onProgress && onProgress({ current: i + 1, total: queue.length, label: item.label });
        try {
            await _originalPost(item.url, item.data);
            await removeFromQueue(item.id);
            synced++;
        } catch (error) {
            // Marquer en erreur sans supprimer
            const raw = await AsyncStorage.getItem(QUEUE_KEY);
            const q = raw ? JSON.parse(raw) : [];
            const idx = q.findIndex(x => x.id === item.id);
            if (idx !== -1) {
                q[idx].status = 'error';
                q[idx].errorMsg = error.response?.data?.message || 'Erreur serveur';
                await AsyncStorage.setItem(QUEUE_KEY, JSON.stringify(q));
            }
            failed++;
        }
    }

    return { synced, failed, total: queue.length };
};

// ─────────────────────────────────────────────────────────────
// WRAPPERS OFFLINE-FIRST
// ─────────────────────────────────────────────────────────────
const _originalGet = apiClient.get.bind(apiClient);
const _originalPost = apiClient.post.bind(apiClient);
const _originalPut = apiClient.put.bind(apiClient);
const _originalDelete = apiClient.delete.bind(apiClient);

apiClient.get = async (url, config = {}) => {
    const key = url + JSON.stringify(config.params || {});
    const state = await NetInfo.fetch();
    if (state.isConnected) {
        try {
            const res = await _originalGet(url, config);
            await setCache(key, res.data);
            return res;
        } catch (err) {
            const cached = await getCache(key);
            if (cached) return { data: cached, fromCache: true };
            throw err;
        }
    } else {
        const cached = await getCache(key);
        if (cached) return { data: cached, fromCache: true };
        throw new Error('Hors ligne — pas de cache pour ' + url);
    }
};

apiClient.post = async (url, data = {}, config = {}) => {
    const state = await NetInfo.fetch();
    if (state.isConnected) return _originalPost(url, data, config);
    const label = url.split('/').pop() + ' — ' + new Date().toLocaleTimeString('fr-FR');
    await addToQueue('POST', url, data, label);
    return { data: { offline: true, queued: true } };
};

apiClient.put = async (url, data = {}, config = {}) => {
    const state = await NetInfo.fetch();
    if (state.isConnected) return _originalPut(url, data, config);
    await addToQueue('PUT', url, data, url);
    return { data: { offline: true, queued: true } };
};

apiClient.delete = async (url, config = {}) => {
    const state = await NetInfo.fetch();
    if (state.isConnected) return _originalDelete(url, config);
    await addToQueue('DELETE', url, {}, url);
    return { data: { offline: true, queued: true } };
};

export default apiClient;