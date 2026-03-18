import React, { useState, useCallback } from 'react';
import {
    View, Text, TouchableOpacity, StyleSheet,
    FlatList, Alert, ActivityIndicator,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useFocusEffect } from '@react-navigation/native';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import { getQueue, syncAll, removeFromQueue } from '../../api/client';
import { COLORS, RADIUS, SHADOWS, SPACING } from '../../theme';

export default function SyncScreen() {
    const [queue, setQueue] = useState([]);
    const [syncing, setSyncing] = useState(false);
    const [progress, setProgress] = useState(null);

    useFocusEffect(useCallback(() => { loadQueue(); }, []));

    const loadQueue = async () => {
        const q = await getQueue();
        setQueue(q);
    };

    const handleSync = async () => {
        if (queue.length === 0) {
            Alert.alert('Rien à envoyer', 'Tous vos données sont déjà synchronisées.');
            return;
        }

        setSyncing(true);
        setProgress(null);

        try {
            const result = await syncAll((p) => setProgress(p));

            await loadQueue(); // Rafraîchir la liste

            if (result.failed === 0) {
                Alert.alert(
                    '✅ Synchronisation réussie',
                    `${result.synced} enregistrement(s) envoyé(s) au serveur.`
                );
            } else {
                Alert.alert(
                    '⚠️ Synchronisation partielle',
                    `${result.synced} envoyé(s), ${result.failed} échec(s).\nLes éléments en erreur restent dans la liste.`
                );
            }
        } catch (error) {
            Alert.alert('Erreur', error.message || 'Impossible de synchroniser.');
        } finally {
            setSyncing(false);
            setProgress(null);
        }
    };

    const handleDeleteItem = (id, label) => {
        Alert.alert(
            'Supprimer',
            `Supprimer "${label}" de la file d'attente ?\nCette action est irréversible.`,
            [
                { text: 'Annuler' },
                {
                    text: 'Supprimer',
                    style: 'destructive',
                    onPress: async () => {
                        await removeFromQueue(id);
                        loadQueue();
                    },
                },
            ]
        );
    };

    const renderItem = ({ item }) => (
        <View style={[styles.card, item.status === 'error' && styles.cardError]}>
            <View style={styles.cardIcon}>
                <MaterialCommunityIcons
                    name={item.status === 'error' ? 'alert-circle' : 'clock-outline'}
                    size={24}
                    color={item.status === 'error' ? COLORS.error : COLORS.textDisabled}
                />
            </View>
            <View style={styles.cardBody}>
                <Text style={styles.cardLabel} numberOfLines={1}>{item.label || item.url}</Text>
                <Text style={styles.cardDate}>{item.createdAt}</Text>
                {item.status === 'error' && (
                    <Text style={styles.cardError_text}>{item.errorMsg}</Text>
                )}
            </View>
            <View style={[
                styles.badge,
                { backgroundColor: item.status === 'error' ? '#FEE2E2' : '#FEF3C7' }
            ]}>
                <Text style={[
                    styles.badgeText,
                    { color: item.status === 'error' ? '#DC2626' : '#D97706' }
                ]}>
                    {item.status === 'error' ? 'Erreur' : 'En attente'}
                </Text>
            </View>
            <TouchableOpacity
                style={styles.deleteBtn}
                onPress={() => handleDeleteItem(item.id, item.label)}
            >
                <MaterialCommunityIcons name="trash-can-outline" size={20} color={COLORS.textDisabled} />
            </TouchableOpacity>
        </View>
    );

    const pendingCount = queue.filter(i => i.status === 'pending').length;
    const errorCount = queue.filter(i => i.status === 'error').length;

    return (
        <SafeAreaView style={styles.container}>

            {/* Stats */}
            <View style={styles.statsRow}>
                <View style={styles.statBox}>
                    <Text style={styles.statNumber}>{queue.length}</Text>
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

            {/* Progress bar pendant sync */}
            {syncing && progress && (
                <View style={styles.progressBox}>
                    <Text style={styles.progressText}>
                        Envoi {progress.current}/{progress.total} — {progress.label}
                    </Text>
                    <View style={styles.progressBar}>
                        <View style={[
                            styles.progressFill,
                            { width: `${(progress.current / progress.total) * 100}%` }
                        ]} />
                    </View>
                </View>
            )}

            {/* Liste */}
            <FlatList
                data={queue}
                keyExtractor={(item) => item.id}
                renderItem={renderItem}
                contentContainerStyle={styles.list}
                ListEmptyComponent={
                    <View style={styles.empty}>
                        <MaterialCommunityIcons name="check-circle-outline" size={60} color="#4CAF50" />
                        <Text style={styles.emptyTitle}>Tout est synchronisé</Text>
                        <Text style={styles.emptySubtitle}>Aucune donnée en attente d'envoi.</Text>
                    </View>
                }
            />

            {/* Bouton Synchroniser */}
            {queue.length > 0 && (
                <View style={styles.footer}>
                    <TouchableOpacity
                        style={[styles.syncBtn, syncing && styles.syncBtnDisabled]}
                        onPress={handleSync}
                        disabled={syncing}
                    >
                        {syncing ? (
                            <>
                                <ActivityIndicator color="#fff" style={{ marginRight: 10 }} />
                                <Text style={styles.syncBtnText}>Synchronisation...</Text>
                            </>
                        ) : (
                            <>
                                <MaterialCommunityIcons name="cloud-upload-outline" size={22} color="#fff" style={{ marginRight: 8 }} />
                                <Text style={styles.syncBtnText}>Envoyer au serveur ({queue.length})</Text>
                            </>
                        )}
                    </TouchableOpacity>
                </View>
            )}
        </SafeAreaView>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: '#F5F5F5' },

    statsRow: {
        flexDirection: 'row',
        backgroundColor: '#fff',
        paddingVertical: 16,
        paddingHorizontal: 24,
        borderBottomWidth: 1,
        borderBottomColor: '#E0E0E0',
    },
    statBox: { flex: 1, alignItems: 'center' },
    statNumber: { fontSize: 28, fontWeight: '900', color: '#111' },
    statLabel: { fontSize: 12, color: '#888', marginTop: 2 },

    progressBox: {
        backgroundColor: '#fff',
        marginHorizontal: 16,
        marginTop: 12,
        padding: 14,
        borderRadius: 12,
        borderWidth: 1,
        borderColor: '#E0E0E0',
    },
    progressText: { fontSize: 12, color: '#555', marginBottom: 8 },
    progressBar: { height: 6, backgroundColor: '#E0E0E0', borderRadius: 3, overflow: 'hidden' },
    progressFill: { height: '100%', backgroundColor: '#1565C0', borderRadius: 3 },

    list: { padding: 16, paddingBottom: 120 },

    card: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: '#fff',
        borderRadius: 12,
        padding: 14,
        marginBottom: 10,
        borderWidth: 1,
        borderColor: '#E0E0E0',
    },
    cardError: { borderColor: '#FCA5A5', backgroundColor: '#FFF5F5' },
    cardIcon: { marginRight: 12 },
    cardBody: { flex: 1 },
    cardLabel: { fontSize: 14, fontWeight: '700', color: '#111' },
    cardDate: { fontSize: 11, color: '#888', marginTop: 2 },
    cardError_text: { fontSize: 11, color: '#DC2626', marginTop: 2 },

    badge: { paddingHorizontal: 8, paddingVertical: 4, borderRadius: 20, marginLeft: 8 },
    badgeText: { fontSize: 11, fontWeight: '700' },
    deleteBtn: { padding: 6, marginLeft: 4 },

    empty: { alignItems: 'center', paddingTop: 80 },
    emptyTitle: { fontSize: 18, fontWeight: '800', color: '#111', marginTop: 16 },
    emptySubtitle: { fontSize: 14, color: '#888', marginTop: 6 },

    footer: {
        position: 'absolute',
        bottom: 0, left: 0, right: 0,
        padding: 16,
        backgroundColor: '#fff',
        borderTopWidth: 1,
        borderTopColor: '#E0E0E0',
    },
    syncBtn: {
        backgroundColor: '#1565C0',
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        padding: 16,
        borderRadius: 40,
    },
    syncBtnDisabled: { opacity: 0.6 },
    syncBtnText: { color: '#fff', fontWeight: '900', fontSize: 16 },
});