import React from 'react';
import { View, Text, StyleSheet, ScrollView } from 'react-native';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import { COLORS, RADIUS, SHADOWS, SPACING } from '../../theme';

const FEATURES = [
    {
        icon: 'cash-check',
        title: 'Demandes de crédit',
        description: 'Soumission et suivi des demandes par les producteurs',
        color: COLORS.credit,
    },
    {
        icon: 'file-document-check-outline',
        title: 'Évaluation & scoring',
        description: 'Analyse automatique de l\'éligibilité au crédit',
        color: COLORS.primary,
    },
    {
        icon: 'bank-outline',
        title: 'Suivi des remboursements',
        description: 'Planification et historique des paiements',
        color: COLORS.identification,
    },
    {
        icon: 'chart-bar',
        title: 'Tableau de bord financier',
        description: 'Statistiques des prêts accordés par campagne',
        color: COLORS.village,
    },
];

export default function CreditsScreen() {
    return (
        <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>

            {/* En-tête illustrée */}
            <View style={styles.hero}>
                <View style={styles.heroIconCircle}>
                    <MaterialCommunityIcons name="cash-multiple" size={48} color="#fff" />
                </View>
                <Text style={styles.heroTitle}>Crédits</Text>
                <Text style={styles.heroSub}>Module de gestion des crédits agricoles</Text>

                <View style={styles.comingSoonBadge}>
                    <MaterialCommunityIcons name="clock-fast" size={14} color={COLORS.credit} />
                    <Text style={styles.comingSoonText}>Bientôt disponible</Text>
                </View>
            </View>

            {/* Description */}
            <View style={styles.descCard}>
                <MaterialCommunityIcons name="information-outline" size={20} color={COLORS.info} style={{ marginBottom: SPACING.md }} />
                <Text style={styles.descText}>
                    Ce module est en cours de développement. Il permettra de gérer les demandes
                    de crédit des producteurs membres de l'OFCA, du dépôt de dossier jusqu'au
                    remboursement.
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
        backgroundColor: COLORS.credit,
        justifyContent: 'center',
        alignItems: 'center',
        marginBottom: SPACING.lg,
        ...SHADOWS.colored(COLORS.credit),
    },
    heroTitle: { fontSize: 24, fontWeight: '800', color: COLORS.textPrimary, marginBottom: 4 },
    heroSub: { fontSize: 14, color: COLORS.textTertiary, marginBottom: SPACING.lg },
    comingSoonBadge: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: COLORS.creditSurface,
        paddingHorizontal: SPACING.md,
        paddingVertical: SPACING.xs,
        borderRadius: RADIUS.full,
        gap: 6,
    },
    comingSoonText: { fontSize: 13, fontWeight: '700', color: COLORS.credit },
    descCard: {
        backgroundColor: COLORS.infoSurface,
        borderRadius: RADIUS.lg,
        marginHorizontal: SPACING.lg,
        marginBottom: SPACING.xl,
        padding: SPACING.lg,
        borderLeftWidth: 4,
        borderLeftColor: COLORS.info,
    },
    descText: { fontSize: 14, color: COLORS.textSecondary, lineHeight: 20 },
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
