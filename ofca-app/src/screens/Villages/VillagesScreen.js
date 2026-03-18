import React, { useState, useCallback } from 'react';
import { View, Text, FlatList, StyleSheet, ActivityIndicator, TouchableOpacity, Animated } from 'react-native';
import { useFocusEffect } from '@react-navigation/native';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import apiClient from '../../api/client';
import { COLORS, SHADOWS, SPACING, RADIUS } from '../../theme';
import LoadingScreen from '../../components/LoadingScreen';
import EmptyState from '../../components/EmptyState';

export default function VillagesScreen({ navigation }) {
    const [villages, setVillages] = useState([]);
    const [loading, setLoading] = useState(true);

    useFocusEffect(
        useCallback(() => {
            loadVillages();
        }, [])
    );

    const loadVillages = async () => {
        try {
            const response = await apiClient.get('/villages');
            // Gérer le format de réponse (data: [] ou [])
            setVillages(response.data.data ?? response.data);
        } catch (error) {
            console.log('Error loading villages:', error);
        } finally {
            setLoading(false);
        }
    };

    const renderVillage = ({ item, index }) => (
        <TouchableOpacity 
            style={styles.card}
            activeOpacity={0.7}
        >
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
    subHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        paddingHorizontal: SPACING.lg,
        paddingVertical: SPACING.md,
        backgroundColor: COLORS.white,
        ...SHADOWS.small,
    },
    subHeaderCount: { fontSize: 13, color: COLORS.textTertiary, fontWeight: '600' },
    addButton: { 
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: COLORS.village, 
        paddingHorizontal: SPACING.lg, 
        paddingVertical: 8,
        borderRadius: RADIUS.full,
        gap: 4,
        ...SHADOWS.colored(COLORS.village),
    },
    addButtonText: { color: '#fff', fontWeight: '700', fontSize: 13 },
    list: { padding: SPACING.md, paddingBottom: 40 },
    card: { 
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: COLORS.white, 
        padding: SPACING.md, 
        borderRadius: RADIUS.lg, 
        marginBottom: SPACING.sm, 
        ...SHADOWS.small,
    },
    iconCircle: {
        width: 46,
        height: 46,
        borderRadius: 23,
        justifyContent: 'center',
        alignItems: 'center',
        marginRight: SPACING.md,
    },
    cardContent: { flex: 1 },
    cardTitle: { fontWeight: '800', fontSize: 16, color: COLORS.textPrimary, marginBottom: 2 },
    metaRow: { flexDirection: 'row', alignItems: 'center', gap: 4 },
    cardSubTitle: { fontSize: 12, color: COLORS.textTertiary },
    zoneBadge: {
        alignSelf: 'flex-start',
        borderWidth: 1,
        borderColor: COLORS.border,
        borderRadius: 4,
        paddingHorizontal: 6,
        paddingVertical: 1,
        marginTop: 6,
    },
    zoneBadgeText: { fontSize: 10, color: COLORS.textTertiary, fontWeight: '600' }
});
