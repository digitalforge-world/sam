import React from 'react';
import { View, Text, StyleSheet, ScrollView } from 'react-native';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import { COLORS, RADIUS, SHADOWS, SPACING } from '../../theme';

const FEATURES = [
    {
        icon: 'truck-delivery-outline',
        title: 'Suivi des expéditions',
        description: 'Traçabilité complète des conteneurs et palettes',
        color: COLORS.exportation,
    },
    {
        icon: 'file-certificate-outline',
        title: 'Documents d\'export',
        description: 'Gestion des certificats et manifestes',
        color: COLORS.identification,
    },
    {
        icon: 'chart-line',
        title: 'Statistiques de volume',
        description: 'Suivi des tonnages exportés par campagne',
        color: COLORS.village,
    },
    {
        icon: 'earth',
        title: 'Destination & lots',
        description: 'Traçabilité pays de destination par lot',
        color: COLORS.primary,
    },
];

export default function ExportationsScreen() {
    return (
        <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>

            {/* En-tête illustrée */}
            <View style={styles.hero}>
                <View style={styles.heroIconCircle}>
                    <MaterialCommunityIcons name="ship-wheel" size={48} color="#fff" />
                </View>
                <Text style={styles.heroTitle}>Exportations</Text>
                <Text style={styles.heroSub}>Module de suivi des exportations</Text>

                <View style={styles.comingSoonBadge}>
                    <MaterialCommunityIcons name="clock-fast" size={14} color={COLORS.exportation} />
                    <Text style={styles.comingSoonText}>Bientôt disponible</Text>
                </View>
            </View>

            {/* Description */}
            <View style={styles.descCard}>
                <MaterialCommunityIcons name="information-outline" size={20} color={COLORS.info} style={{ marginBottom: SPACING.md }} />
                <Text style={styles.descText}>
                    Ce module est en cours de développement. Il centralisera toutes les informations
                    relatives aux exportations de produits agricoles certifiés de l'OFCA.
                </Text>
            </View>

            {/* Aperçu des fonctionnalités */}
            <Text style={styles.sectionTitle}>Fonctionnalités prévues</Text>
            {FEATURES.map((f, i) => (
                <View key={i} style={styles.featureCard}>
                    <View style={[styles.featureIcon, { backgroundColor: f.color + '18' }]}>
                        <MaterialCommunityIcons name={f.icon} size={24} color={f.color} />
                    </View>
                    <View style={styles.featureText}>
                        <Text style={styles.featureTitle}>{f.title}</Text>
                        <Text style={styles.featureDesc}>{f.description}</Text>
                    </View>
                    <MaterialCommunityIcons name="lock-outline" size={16} color={COLORS.textDisabled} />
                </View>
            ))}

            <View style={{ height: 40 }} />
        </ScrollView>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: COLORS.background },
    hero: {
        alignItems: 'center',
        paddingVertical: SPACING.xxxl,
        paddingHorizontal: SPACING.xl,
        backgroundColor: COLORS.white,
        borderBottomLeftRadius: RADIUS.xxl,
        borderBottomRightRadius: RADIUS.xxl,
        ...SHADOWS.medium,
        marginBottom: SPACING.xl,
    },
    heroIconCircle: {
        width: 96,
        height: 96,
        borderRadius: 48,
        backgroundColor: COLORS.exportation,
        justifyContent: 'center',
        alignItems: 'center',
        marginBottom: SPACING.lg,
        ...SHADOWS.colored(COLORS.exportation),
    },
    heroTitle: { fontSize: 24, fontWeight: '800', color: COLORS.textPrimary, marginBottom: 4 },
    heroSub: { fontSize: 14, color: COLORS.textTertiary, marginBottom: SPACING.lg },
    comingSoonBadge: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: COLORS.exportationSurface,
        paddingHorizontal: SPACING.md,
        paddingVertical: SPACING.xs,
        borderRadius: RADIUS.full,
        gap: 6,
    },
    comingSoonText: { fontSize: 13, fontWeight: '700', color: COLORS.exportation },
    descCard: {
        backgroundColor: COLORS.infoSurface,
        borderRadius: RADIUS.lg,
        marginHorizontal: SPACING.lg,
        marginBottom: SPACING.xl,
        padding: SPACING.lg,
        borderLeftWidth: 4,
        borderLeftColor: COLORS.info,
    },
    descText: {
        fontSize: 14,
        color: COLORS.textSecondary,
        lineHeight: 20,
    },
    sectionTitle: {
        fontSize: 12,
        fontWeight: '700',
        color: COLORS.textDisabled,
        textTransform: 'uppercase',
        letterSpacing: 0.8,
        marginHorizontal: SPACING.lg,
        marginBottom: SPACING.md,
    },
    featureCard: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: COLORS.white,
        borderRadius: RADIUS.lg,
        marginHorizontal: SPACING.lg,
        marginBottom: SPACING.md,
        padding: SPACING.lg,
        ...SHADOWS.small,
    },
    featureIcon: {
        width: 48,
        height: 48,
        borderRadius: RADIUS.md,
        justifyContent: 'center',
        alignItems: 'center',
        marginRight: SPACING.md,
    },
    featureText: { flex: 1 },
    featureTitle: { fontSize: 15, fontWeight: '700', color: COLORS.textPrimary },
    featureDesc: { fontSize: 13, color: COLORS.textTertiary, marginTop: 2 },
});
