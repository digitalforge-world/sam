import React, { useState, useCallback } from 'react';
import { View, Text, FlatList, StyleSheet, TouchableOpacity } from 'react-native';
import { useFocusEffect } from '@react-navigation/native';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import NetInfo from '@react-native-community/netinfo';
import apiClient from '../../api/client';
import { COLORS, SPACING, RADIUS } from '../../theme';
import LoadingScreen from '../../components/LoadingScreen';
import EmptyState from '../../components/EmptyState';

export default function VillagesScreen({ navigation }) {
    const [villages, setVillages] = useState([]);
    const [loading, setLoading] = useState(true);
    const [isOffline, setIsOffline] = useState(false);
    const [fromCache, setFromCache] = useState(false);

    useFocusEffect(useCallback(() => { loadVillages(); }, []));

    const loadVillages = async () => {
        setLoading(true);
        try {
            const state = await NetInfo.fetch();
            setIsOffline(!state.isConnected);

            const response = await apiClient.get('/villages');
            setVillages(response.data.data ?? response.data);
            setFromCache(!!response.fromCache);
        } catch (error) {
            console.log('Error loading villages:', error);
        } finally {
            setLoading(false);
        }
    };

    const renderVillage = ({ item }) => (
        <TouchableOpacity style={styles.card} activeOpacity={0.7}>
            <View style={[styles.iconCircle, { backgroundColor: COLORS.villageSurface }]}>
                <MaterialCommunityIcons name="home-city-outline" size={24} color={COLORS.village} />
            </View>
            <View style={styles.cardContent}>
                <Text style={styles.cardTitle}>{item.nom}</Text>
                <View style={styles.metaRow}>
                    <MaterialCommunityIcons name="map-marker-outline" size={12} color={COLORS.textTertiary} />
                    <Text style={styles.cardSubTitle}>
                        {item.canton?.nom || 'Canton N/A'} · {item.prefecture?.nom || 'Pref. N/A'}
                    </Text>
                </View>
                {item.zone && (
                    <View style={styles.zoneBadge}>
                        <Text style={styles.zoneBadgeText}>Zone: {item.zone}</Text>
                    </View>
                )}
            </View>
            <MaterialCommunityIcons name="chevron-right" size={20} color={COLORS.border} />
        </TouchableOpacity>
    );

    if (loading) return <LoadingScreen message="Chargement des villages..." />;

    return (
        <View style={styles.container}>

            {/* ── Bandeaux offline ──────────────────────── */}
            {isOffline && (
                <View style={styles.offlineBanner}>
                    <MaterialCommunityIcons name="wifi-off" size={16} color="#fff" />
                    <Text style={styles.offlineBannerText}>Mode hors ligne — données en cache</Text>
                </View>
            )}
            {fromCache && !isOffline && (
                <View style={styles.cacheBanner}>
                    <MaterialCommunityIcons name="database-clock" size={14} color="#fff" />
                    <Text style={styles.cacheBannerText}>Données chargées depuis le cache local</Text>
                </View>
            )}

            {/* Sub-header */}
            <View style={styles.subHeader}>
                <Text style={styles.subHeaderCount}>{villages.length} village(s) enregistré(s)</Text>
                <TouchableOpacity
                    style={styles.addButton}
                    onPress={() => navigation.navigate('CreateVillage')}
                    activeOpacity={0.8}
                >
                    <MaterialCommunityIcons name="plus" size={18} color="#fff" />
                    <Text style={styles.addButtonText}>Créer</Text>
                </TouchableOpacity>
            </View>

            <FlatList
                data={villages}
                keyExtractor={(item) => item.id.toString()}
                renderItem={renderVillage}
                contentContainerStyle={styles.list}
                showsVerticalScrollIndicator={false}
                ListEmptyComponent={
                    <EmptyState
                        icon="🏘️"
                        title="Aucun village trouvé"
                        subtitle="Enregistrez le premier village de votre zone d'intervention."
                    />
                }
            />
        </View>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: COLORS.background },

    // Bandeaux
    offlineBanner: {
        flexDirection: 'row', alignItems: 'center', gap: 8,
        backgroundColor: '#F57F17', padding: 10, paddingHorizontal: 16,
    },
    offlineBannerText: { color: '#fff', fontWeight: '700', fontSize: 13, flex: 1 },
    cacheBanner: {
        flexDirection: 'row', alignItems: 'center', gap: 8,
        backgroundColor: '#1565C0', padding: 8, paddingHorizontal: 16,
    },
    cacheBannerText: { color: '#fff', fontSize: 12, flex: 1 },

    subHeader: {
        flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center',
        paddingHorizontal: SPACING.lg, paddingVertical: SPACING.md,
        backgroundColor: COLORS.white, elevation: 2,
    },
    subHeaderCount: { fontSize: 13, color: COLORS.textTertiary, fontWeight: '600' },
    addButton: {
        flexDirection: 'row', alignItems: 'center',
        backgroundColor: COLORS.village,
        paddingHorizontal: SPACING.lg, paddingVertical: 8,
        borderRadius: RADIUS.full, gap: 4, elevation: 3,
    },
    addButtonText: { color: '#fff', fontWeight: '700', fontSize: 13 },

    list: { padding: SPACING.md, paddingBottom: 40 },
    card: {
        flexDirection: 'row', alignItems: 'center',
        backgroundColor: COLORS.white, padding: SPACING.md,
        borderRadius: RADIUS.lg, marginBottom: SPACING.sm, elevation: 2,
    },
    iconCircle: {
        width: 46, height: 46, borderRadius: 23,
        justifyContent: 'center', alignItems: 'center', marginRight: SPACING.md,
    },
    cardContent: { flex: 1 },
    cardTitle: { fontWeight: '800', fontSize: 16, color: COLORS.textPrimary, marginBottom: 2 },
    metaRow: { flexDirection: 'row', alignItems: 'center', gap: 4 },
    cardSubTitle: { fontSize: 12, color: COLORS.textTertiary },
    zoneBadge: {
        alignSelf: 'flex-start', borderWidth: 1, borderColor: COLORS.border,
        borderRadius: 4, paddingHorizontal: 6, paddingVertical: 1, marginTop: 6,
    },
    zoneBadgeText: { fontSize: 10, color: COLORS.textTertiary, fontWeight: '600' },
});