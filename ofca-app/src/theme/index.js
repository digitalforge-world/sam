// OFCA App — Design System / Theme
// Fichier central pour tous les tokens de design

export const COLORS = {
    // Primaire — Vert OFCA
    primary: '#059669',
    primaryDark: '#047857',
    primaryLight: '#10b981',
    primarySurface: '#ecfdf5',

    // Secondaire — Bleu ardoise
    secondary: '#3b82f6',
    secondaryDark: '#1d4ed8',
    secondaryLight: '#60a5fa',
    secondarySurface: '#eff6ff',

    // Accents — couleurs thématiques par module
    village: '#8b5cf6',      // Violet
    villageSurface: '#f5f3ff',
    organisation: '#f59e0b', // Ambre
    organisationSurface: '#fffbeb',
    producteur: '#10b981',   // Vert
    producteurSurface: '#ecfdf5',
    identification: '#3b82f6', // Bleu
    identificationSurface: '#eff6ff',
    controle: '#ef4444',     // Rouge
    controleSurface: '#fef2f2',
    exportation: '#06b6d4',  // Cyan
    exportationSurface: '#ecfeff',
    credit: '#f97316',       // Orange
    creditSurface: '#fff7ed',
    profile: '#6b7280',      // Gris
    profileSurface: '#f9fafb',

    // Neutres
    white: '#ffffff',
    background: '#f3f4f6',
    surface: '#ffffff',
    border: '#e5e7eb',
    borderLight: '#f3f4f6',

    // Textes
    textPrimary: '#111827',
    textSecondary: '#374151',
    textTertiary: '#6b7280',
    textDisabled: '#9ca3af',
    textOnPrimary: '#ffffff',

    // États
    success: '#10b981',
    successSurface: '#ecfdf5',
    warning: '#f59e0b',
    warningSurface: '#fffbeb',
    error: '#ef4444',
    errorSurface: '#fef2f2',
    info: '#3b82f6',
    infoSurface: '#eff6ff',
};

export const TYPOGRAPHY = {
    h1: { fontSize: 28, fontWeight: '800', color: COLORS.textPrimary, letterSpacing: -0.5 },
    h2: { fontSize: 22, fontWeight: '700', color: COLORS.textPrimary },
    h3: { fontSize: 18, fontWeight: '600', color: COLORS.textPrimary },
    h4: { fontSize: 16, fontWeight: '600', color: COLORS.textPrimary },
    body: { fontSize: 15, fontWeight: '400', color: COLORS.textSecondary },
    bodySmall: { fontSize: 13, fontWeight: '400', color: COLORS.textTertiary },
    label: { fontSize: 13, fontWeight: '600', color: COLORS.textSecondary },
    caption: { fontSize: 11, fontWeight: '500', color: COLORS.textTertiary },
    button: { fontSize: 15, fontWeight: '700', color: COLORS.textOnPrimary },
};

export const SPACING = {
    xs: 4,
    sm: 8,
    md: 12,
    lg: 16,
    xl: 20,
    xxl: 24,
    xxxl: 32,
};

export const RADIUS = {
    sm: 6,
    md: 10,
    lg: 14,
    xl: 20,
    xxl: 28,
    full: 999,
};

export const SHADOWS = {
    small: {
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 1 },
        shadowOpacity: 0.08,
        shadowRadius: 4,
        elevation: 2,
    },
    medium: {
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.10,
        shadowRadius: 8,
        elevation: 4,
    },
    large: {
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 8 },
        shadowOpacity: 0.12,
        shadowRadius: 16,
        elevation: 8,
    },
    colored: (color) => ({
        shadowColor: color,
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.3,
        shadowRadius: 8,
        elevation: 6,
    }),
};

export const MENU_ITEMS = [
    {
        title: 'Villages',
        route: 'Villages',
        icon: 'map-marker-multiple',
        iconFamily: 'MaterialCommunityIcons',
        color: COLORS.village,
        surface: COLORS.villageSurface,
        description: 'Gérer les villages',
    },
    {
        title: 'Organisations',
        route: 'Organisations',
        icon: 'office-building',
        iconFamily: 'MaterialCommunityIcons',
        color: COLORS.organisation,
        surface: COLORS.organisationSurface,
        description: 'Orgs. paysannes',
    },
    {
        title: 'Producteurs',
        route: 'Producteurs',
        icon: 'account-group',
        iconFamily: 'MaterialCommunityIcons',
        color: COLORS.producteur,
        surface: COLORS.producteurSurface,
        description: 'Membres enregistrés',
    },
    {
        title: 'Identifications',
        route: 'Identifications',
        icon: 'map-search',
        iconFamily: 'MaterialCommunityIcons',
        color: COLORS.identification,
        surface: COLORS.identificationSurface,
        description: 'Parcelles GPS',
    },
    {
        title: 'Contrôles',
        route: 'Controles',
        icon: 'clipboard-check',
        iconFamily: 'MaterialCommunityIcons',
        color: COLORS.controle,
        surface: COLORS.controleSurface,
        description: 'Contrôles internes',
    },
    {
        title: 'Exportations',
        route: 'Exportations',
        icon: 'ship-wheel',
        iconFamily: 'MaterialCommunityIcons',
        color: COLORS.exportation,
        surface: COLORS.exportationSurface,
        description: 'Suivi des exports',
    },
    {
        title: 'Crédits',
        route: 'Credits',
        icon: 'cash-multiple',
        iconFamily: 'MaterialCommunityIcons',
        color: COLORS.credit,
        surface: COLORS.creditSurface,
        description: 'Demandes de crédit',
    },
    {
        title: 'Mon Profil',
        route: 'Profile',
        icon: 'account-circle',
        iconFamily: 'MaterialCommunityIcons',
        color: COLORS.profile,
        surface: COLORS.profileSurface,
        description: 'Mon compte',
    },
];
