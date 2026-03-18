import React, { useState, useEffect, useCallback } from 'react';
import {
    View, Text, StyleSheet, TouchableOpacity,
    Alert, ScrollView, ActivityIndicator,
} from 'react-native';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import { useFocusEffect } from '@react-navigation/native';
import * as SecureStore from 'expo-secure-store';
import NetInfo from '@react-native-community/netinfo';
import apiClient, { getQueue, syncAll } from '../../api/client';
import { COLORS, RADIUS, SHADOWS, SPACING } from '../../theme';

export default function ProfileScreen({ route }) {
    const { setUserToken } = route.params;
    const [userInfo, setUserInfo] = useState(null);
    const [loggingOut, setLoggingOut] = useState(false);

    // ── Offline state ─────────────────────────────────────────
    const [queueCount, setQueueCount] = useState(0);
    const [queueItems, setQueueItems] = useState([]);
    const [isOnline, setIsOnline] = useState(true);
    const [syncing, setSyncing] = useState(false);
    const [syncProgress, setSyncProgress] = useState(null);

    useEffect(() => { loadUser(); }, []);

    // Recharger la file à chaque fois qu'on revient sur le profil
    useFocusEffect(useCallback(() => {
        loadQueue();
        const unsub = NetInfo.addEventListener(state => setIsOnline(!!state.isConnected));
        return () => unsub();
    }, []));

    const loadUser = async () => {
        try {
            const stored = await SecureStore.getItemAsync('userInfo');
            if (stored) setUserInfo(JSON.parse(stored));
        } catch (e) { }
    };

    const loadQueue = async () => {
        const q = await getQueue();
        setQueueItems(q);
        setQueueCount(q.length);
    };

    // ── Logout ────────────────────────────────────────────────
    const handleLogout = () => {
        Alert.alert(
            'Déconnexion',
            queueCount > 0
                ? `Attention ! Vous avez ${queueCount} opération(s) non synchronisée(s).\nVous perdrez ces données si vous vous déconnectez sans synchroniser.`
                : 'Voulez-vous vraiment vous déconnecter ?',
            [
                { text: 'Annuler', style: 'cancel' },
                { text: 'Déconnecter', style: 'destructive', onPress: confirmLogout },
            ]
        );
    };

    const confirmLogout = async () => {
        setLoggingOut(true);
        try {
            await apiClient.post('/logout').catch(() => { });
        } finally {
            await SecureStore.deleteItemAsync('userToken').catch(() => { });
            await SecureStore.deleteItemAsync('userInfo').catch(() => { });
            setUserToken(null);
        }
    };

    // ── Sync manuel ───────────────────────────────────────────
    const handleSync = async () => {
        if (!isOnline) {
            Alert.alert('Hors ligne', 'Connectez-vous à internet pour synchroniser vos données.');
            return;
        }
        if (queueCount === 0) {
            Alert.alert('Aucune donnée', 'Toutes vos données sont déjà synchronisées.');
            return;
        }

        Alert.alert(
            'Synchroniser',
            `Envoyer ${queueCount} opération(s) au serveur ?`,
            [
                { text: 'Annuler' },
                {
                    text: 'Envoyer',
                    onPress: async () => {
                        setSyncing(true);
                        setSyncProgress(null);
                        try {
                            const result = await syncAll((p) => setSyncProgress(p));
                            await loadQueue();

                            if (result.failed === 0) {
                                Alert.alert('✅ Succès', `${result.synced} opération(s) envoyée(s) au serveur.`);
                            } else {
                                Alert.alert(
                                    '⚠️ Partiel',
                                    `${result.synced} envoyée(s), ${result.failed} échec(s).\nLes erreurs restent en attente.`
                                );
                            }
                        } catch (e) {
                            Alert.alert('Erreur', e.message || 'Synchronisation échouée.');
                        } finally {
                            setSyncing(false);
                            setSyncProgress(null);
                        }
                    },
                },
            ]
        );
    };

    const getInitials = (name) => {
        if (!name) return '?';
        return name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
    };

    const pendingCount = queueItems.filter(i => i.status === 'pending').length;
    const errorCount = queueItems.filter(i => i.status === 'error').length;

    const infoItems = [
        { icon: 'account-outline', label: 'Nom complet', value: userInfo?.name ?? '—', color: COLORS.primary },
        { icon: 'email-outline', label: 'Adresse email', value: userInfo?.email ?? '—', color: COLORS.secondary },
        { icon: 'shield-account-outline', label: 'Rôle', value: userInfo?.role ?? 'Contrôleur', color: COLORS.organisation },
    ];

    return (
        <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>

            {/* ── Avatar ──────────────────────────────────────── */}
            <View style={styles.avatarSection}>
                <View style={styles.avatarCircle}>
                    <Text style={styles.avatarText}>{getInitials(userInfo?.name)}</Text>
                </View>
                <Text style={styles.userName}>{userInfo?.name ?? 'Contrôleur OFCA'}</Text>
                <Text style={styles.userEmail}>{userInfo?.email ?? '—'}</Text>
                <View style={styles.roleBadge}>
                    <MaterialCommunityIcons name="shield-check" size={13} color={COLORS.primary} />
                    <Text style={styles.roleText}>{userInfo?.role ?? 'Agent de terrain'}</Text>
                </View>
            </View>

            {/* ── Carte Offline ────────────────────────────────── */}
            <View style={styles.section}>
                <Text style={styles.sectionTitle}>Données hors ligne</Text>

                {/* Statut connexion */}
                <View style={styles.onlineRow}>
                    <View style={[styles.dot, { backgroundColor: isOnline ? '#4CAF50' : '#E53935' }]} />
                    <Text style={styles.onlineText}>
                        {isOnline ? 'Connecté à internet' : 'Mode hors ligne'}
                    </Text>
                </View>

                {/* Stats file d'attente */}
                <View style={styles.statsRow}>
                    <View style={styles.statBox}>
                        <Text style={styles.statNumber}>{queueCount}</Text>
                        <Text style={styles.statLabel}>Total</Text>
                    </View>
                    <View style={styles.statBox}>
                        <Text style={[styles.statNumber, { color: '#D97706' }]}>{pendingCount}</Text>
                        <Text style={styles.statLabel}>En attente</Text>
                    </View>
                    <View style={styles.statBox}>
                        <Text style={[styles.statNumber, { color: '#DC2626' }]}>{errorCount}</Text>
                        <Text style={styles.statLabel}>Erreurs</Text>
                    </View>
                </View>

                {/* Barre de progression pendant sync */}
                {syncing && syncProgress && (
                    <View style={styles.progressBox}>
                        <Text style={styles.progressText}>
                            Envoi {syncProgress.current}/{syncProgress.total} — {syncProgress.label}
                        </Text>
                        <View style={styles.progressBar}>
                            <View style={[
                                styles.progressFill,
                                { width: `${(syncProgress.current / syncProgress.total) * 100}%` }
                            ]} />
                        </View>
                    </View>
                )}

                {/* Dernières opérations en attente */}
                {queueItems.slice(0, 3).map((item, i) => (
                    <View key={i} style={styles.queueItem}>
                        <MaterialCommunityIcons
                            name={item.status === 'error' ? 'alert-circle-outline' : 'clock-outline'}
                            size={16}
                            color={item.status === 'error' ? '#DC2626' : '#D97706'}
                        />
                        <Text style={styles.queueLabel} numberOfLines={1}>{item.label || item.url}</Text>
                        <Text style={styles.queueDate}>{item.createdAt}</Text>
                    </View>
                ))}
                {queueCount > 3 && (
                    <Text style={styles.moreItems}>+ {queueCount - 3} autre(s) opération(s)...</Text>
                )}

                {/* Bouton Envoyer au serveur */}
                <TouchableOpacity
                    style={[
                        styles.syncBtn,
                        !isOnline && styles.syncBtnDisabled,
                        queueCount === 0 && styles.syncBtnEmpty,
                        syncing && styles.syncBtnDisabled,
                    ]}
                    onPress={handleSync}
                    disabled={syncing}
                >
                    {syncing ? (
                        <>
                            <ActivityIndicator color="#fff" size="small" style={{ marginRight: 8 }} />
                            <Text style={styles.syncBtnText}>Synchronisation...</Text>
                        </>
                    ) : (
                        <>
                            <MaterialCommunityIcons
                                name="cloud-upload-outline"
                                size={20}
                                color="#fff"
                                style={{ marginRight: 8 }}
                            />
                            <Text style={styles.syncBtnText}>
                                {queueCount > 0
                                    ? `Envoyer au serveur (${queueCount})`
                                    : 'Tout est synchronisé ✓'}
                            </Text>
                        </>
                    )}
                </TouchableOpacity>
            </View>

            {/* ── Informations compte ──────────────────────────── */}
            <View style={styles.section}>
                <Text style={styles.sectionTitle}>Informations du compte</Text>
                {infoItems.map((item, i) => (
                    <View key={i} style={styles.infoRow}>
                        <View style={[styles.infoIcon, { backgroundColor: item.color + '18' }]}>
                            <MaterialCommunityIcons name={item.icon} size={20} color={item.color} />
                        </View>
                        <View style={styles.infoContent}>
                            <Text style={styles.infoLabel}>{item.label}</Text>
                            <Text style={styles.infoValue}>{item.value}</Text>
                        </View>
                    </View>
                ))}
            </View>

            {/* ── Application ──────────────────────────────────── */}
            <View style={styles.section}>
                <Text style={styles.sectionTitle}>Application</Text>
                <View style={styles.infoRow}>
                    <View style={[styles.infoIcon, { backgroundColor: COLORS.identificationSurface }]}>
                        <MaterialCommunityIcons name="server" size={20} color={COLORS.identification} />
                    </View>
                    <View style={styles.infoContent}>
                        <Text style={styles.infoLabel}>Serveur</Text>
                        <Text style={styles.infoValueSmall}>ofca.digitalforges.org</Text>
                    </View>
                </View>
                <View style={styles.infoRow}>
                    <View style={[styles.infoIcon, { backgroundColor: COLORS.villageSurface }]}>
                        <MaterialCommunityIcons name="cellphone" size={20} color={COLORS.village} />
                    </View>
                    <View style={styles.infoContent}>
                        <Text style={styles.infoLabel}>Version</Text>
                        <Text style={styles.infoValueSmall}>OFCA App v1.0.0</Text>
                    </View>
                </View>
            </View>

            {/* ── Déconnexion ───────────────────────────────────── */}
            <TouchableOpacity
                style={[styles.logoutBtn, loggingOut && styles.logoutBtnDisabled]}
                onPress={handleLogout}
                disabled={loggingOut}
                activeOpacity={0.8}
            >
                {loggingOut ? (
                    <ActivityIndicator color="#fff" />
                ) : (
                    <>
                        <MaterialCommunityIcons name="logout" size={20} color="#fff" />
                        <Text style={styles.logoutText}>Se Déconnecter</Text>
                    </>
                )}
            </TouchableOpacity>

            <View style={{ height: 40 }} />
        </ScrollView>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: COLORS.background },

    avatarSection: {
        alignItems: 'center',
        paddingVertical: SPACING.xxxl,
        paddingHorizontal: SPACING.xl,
        backgroundColor: COLORS.white,
        borderBottomLeftRadius: RADIUS.xxl,
        borderBottomRightRadius: RADIUS.xxl,
        ...SHADOWS.medium,
        marginBottom: SPACING.xl,
    },
    avatarCircle: {
        width: 90, height: 90, borderRadius: 45,
        backgroundColor: COLORS.primary,
        justifyContent: 'center', alignItems: 'center',
        marginBottom: SPACING.lg,
        ...SHADOWS.colored(COLORS.primary),
    },
    avatarText: { fontSize: 34, fontWeight: '800', color: '#fff' },
    userName: { fontSize: 22, fontWeight: '800', color: COLORS.textPrimary, marginBottom: 4 },
    userEmail: { fontSize: 14, color: COLORS.textTertiary, marginBottom: SPACING.md },
    roleBadge: { flexDirection: 'row', alignItems: 'center', backgroundColor: COLORS.primarySurface, paddingHorizontal: SPACING.md, paddingVertical: SPACING.xs, borderRadius: RADIUS.full, gap: 4 },
    roleText: { fontSize: 13, fontWeight: '700', color: COLORS.primary },

    section: {
        backgroundColor: COLORS.white,
        borderRadius: RADIUS.xl,
        marginHorizontal: SPACING.lg,
        marginBottom: SPACING.lg,
        padding: SPACING.lg,
        ...SHADOWS.small,
    },
    sectionTitle: {
        fontSize: 12, fontWeight: '700', color: COLORS.textDisabled,
        textTransform: 'uppercase', letterSpacing: 0.8,
        marginBottom: SPACING.lg,
    },

    // Connexion statut
    onlineRow: { flexDirection: 'row', alignItems: 'center', marginBottom: SPACING.md, gap: 8 },
    dot: { width: 10, height: 10, borderRadius: 5 },
    onlineText: { fontSize: 14, fontWeight: '600', color: COLORS.textSecondary },

    // Stats
    statsRow: { flexDirection: 'row', marginBottom: SPACING.lg },
    statBox: { flex: 1, alignItems: 'center', paddingVertical: SPACING.sm, backgroundColor: COLORS.background, borderRadius: RADIUS.md, marginHorizontal: 4 },
    statNumber: { fontSize: 22, fontWeight: '900', color: COLORS.textPrimary },
    statLabel: { fontSize: 11, color: COLORS.textDisabled, marginTop: 2 },

    // Progress
    progressBox: { backgroundColor: COLORS.background, borderRadius: RADIUS.md, padding: SPACING.md, marginBottom: SPACING.md },
    progressText: { fontSize: 12, color: COLORS.textSecondary, marginBottom: 6 },
    progressBar: { height: 6, backgroundColor: '#E0E0E0', borderRadius: 3, overflow: 'hidden' },
    progressFill: { height: '100%', backgroundColor: COLORS.primary, borderRadius: 3 },

    // Queue items preview
    queueItem: { flexDirection: 'row', alignItems: 'center', paddingVertical: 6, gap: 8, borderBottomWidth: 1, borderBottomColor: COLORS.borderLight },
    queueLabel: { flex: 1, fontSize: 12, color: COLORS.textSecondary },
    queueDate: { fontSize: 10, color: COLORS.textDisabled },
    moreItems: { fontSize: 12, color: COLORS.textDisabled, textAlign: 'center', marginTop: 6, marginBottom: 4 },

    // Bouton Sync
    syncBtn: {
        flexDirection: 'row', alignItems: 'center', justifyContent: 'center',
        backgroundColor: COLORS.primary,
        padding: SPACING.lg, borderRadius: RADIUS.lg,
        marginTop: SPACING.lg,
        ...SHADOWS.colored(COLORS.primary),
    },
    syncBtnDisabled: { opacity: 0.5 },
    syncBtnEmpty: { backgroundColor: '#4CAF50' },
    syncBtnText: { color: '#fff', fontWeight: '800', fontSize: 15 },

    // Info rows
    infoRow: { flexDirection: 'row', alignItems: 'center', paddingVertical: SPACING.sm, borderBottomWidth: 1, borderBottomColor: COLORS.borderLight },
    infoIcon: { width: 40, height: 40, borderRadius: RADIUS.md, justifyContent: 'center', alignItems: 'center', marginRight: SPACING.md },
    infoContent: { flex: 1 },
    infoLabel: { fontSize: 12, color: COLORS.textDisabled, fontWeight: '600', marginBottom: 2 },
    infoValue: { fontSize: 15, color: COLORS.textPrimary, fontWeight: '600' },
    infoValueSmall: { fontSize: 14, color: COLORS.textSecondary, fontWeight: '500' },

    // Logout
    logoutBtn: {
        flexDirection: 'row', alignItems: 'center', justifyContent: 'center',
        backgroundColor: COLORS.error,
        marginHorizontal: SPACING.lg, padding: SPACING.lg,
        borderRadius: RADIUS.lg, gap: SPACING.sm,
        ...SHADOWS.colored(COLORS.error),
    },
    logoutBtnDisabled: { opacity: 0.6 },
    logoutText: { color: '#fff', fontWeight: '800', fontSize: 16 },
});