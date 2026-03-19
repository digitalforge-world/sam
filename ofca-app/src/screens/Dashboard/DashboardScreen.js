import React, { useState, useEffect, useRef } from 'react';
import {
    View,
    Text,
    StyleSheet,
    TouchableOpacity,
    ScrollView,
    Animated,
    Dimensions,
} from 'react-native';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import * as SecureStore from 'expo-secure-store';
import { useFocusEffect } from '@react-navigation/native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { COLORS, SHADOWS, SPACING, RADIUS, MENU_ITEMS } from '../../theme';

const { width } = Dimensions.get('window');

export default function DashboardScreen({ navigation, route }) {
    const insets = useSafeAreaInsets();
    const { setUserToken } = route.params;
    const [userInfo, setUserInfo] = useState(null);
    const fadeAnims = useRef(MENU_ITEMS.map(() => new Animated.Value(0))).current;
    const slideAnims = useRef(MENU_ITEMS.map(() => new Animated.Value(20))).current;

    useFocusEffect(
        React.useCallback(() => {
            loadUser();
            animateCards();
        }, [])
    );

    const loadUser = async () => {
        try {
            const stored = await SecureStore.getItemAsync('userInfo');
            if (stored) setUserInfo(JSON.parse(stored));
        } catch (_) {}
    };

    const animateCards = () => {
        fadeAnims.forEach(a => a.setValue(0));
        slideAnims.forEach(a => a.setValue(20));
        const animations = MENU_ITEMS.map((_, i) =>
            Animated.parallel([
                Animated.timing(fadeAnims[i], {
                    toValue: 1,
                    duration: 350,
                    delay: i * 60,
                    useNativeDriver: true,
                }),
                Animated.timing(slideAnims[i], {
                    toValue: 0,
                    duration: 350,
                    delay: i * 60,
                    useNativeDriver: true,
                }),
            ])
        );
        Animated.stagger(60, animations).start();
    };

    const getGreeting = () => {
        const h = new Date().getHours();
        if (h < 12) return 'Bonjour';
        if (h < 18) return 'Bon après-midi';
        return 'Bonsoir';
    };

    const firstName = userInfo?.name?.split(' ')[0] || 'Contrôleur';

    return (
        <View style={styles.root}>
            {/* Header */}
            <View style={[styles.header, { paddingTop: Math.max(insets.top, SPACING.lg) }]}>
                <View style={styles.headerLeft}>
                    <Text style={styles.greeting}>{getGreeting()},</Text>
                    <Text style={styles.userName}>{firstName} 👋</Text>
                </View>
                <TouchableOpacity
                    style={styles.profileBtn}
                    onPress={() => navigation.navigate('Profile', { setUserToken })}
                >
                    <MaterialCommunityIcons name="account-circle-outline" size={38} color={COLORS.primary} />
                </TouchableOpacity>
            </View>

            {/* Bannière OFCA */}
            <View style={styles.banner}>
                <View style={styles.bannerContent}>
                    <MaterialCommunityIcons name="leaf" size={22} color="#fff" style={{ marginBottom: 4 }} />
                    <Text style={styles.bannerTitle}>OFCA Contrôleur</Text>
                    <Text style={styles.bannerSub}>Système de gestion terrain agricole</Text>
                </View>
                <View style={styles.bannerDecor1} />
                <View style={styles.bannerDecor2} />
            </View>

            <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={styles.scrollContent}>
                <Text style={styles.sectionTitle}>Modules</Text>

                {/* Grille 2 colonnes */}
                <View style={styles.grid}>
                    {MENU_ITEMS.map((item, index) => (
                        <Animated.View
                            key={item.route}
                            style={[
                                styles.cardWrapper,
                                {
                                    opacity: fadeAnims[index],
                                    transform: [{ translateY: slideAnims[index] }],
                                },
                            ]}
                        >
                            <TouchableOpacity
                                style={[styles.card, { backgroundColor: item.surface }]}
                                onPress={() => navigation.navigate(item.route, { setUserToken })}
                                activeOpacity={0.75}
                            >
                                <View style={[styles.iconCircle, { backgroundColor: item.color }]}>
                                    <MaterialCommunityIcons
                                        name={item.icon}
                                        size={26}
                                        color="#fff"
                                    />
                                </View>
                                <Text style={[styles.cardTitle, { color: item.color }]}>{item.title}</Text>
                                <Text style={styles.cardDesc}>{item.description}</Text>
                                <View style={styles.cardArrow}>
                                    <MaterialCommunityIcons name="chevron-right" size={16} color={item.color} />
                                </View>
                            </TouchableOpacity>
                        </Animated.View>
                    ))}
                </View>
            </ScrollView>
        </View>
    );
}

const CARD_WIDTH = (width - SPACING.lg * 2 - SPACING.md) / 2;

const styles = StyleSheet.create({
    root: {
        flex: 1,
        backgroundColor: COLORS.background,
    },
    header: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        paddingHorizontal: SPACING.lg,
        paddingBottom: SPACING.md,
        backgroundColor: COLORS.white,
        borderBottomWidth: 1,
        borderBottomColor: '#f3f4f6',
    },
    headerLeft: {},
    greeting: {
        fontSize: 13,
        color: COLORS.textTertiary,
        fontWeight: '500',
    },
    userName: {
        fontSize: 20,
        fontWeight: '800',
        color: COLORS.textPrimary,
        letterSpacing: -0.3,
    },
    profileBtn: {
        padding: 4,
    },
    banner: {
        backgroundColor: COLORS.primary,
        marginHorizontal: SPACING.lg,
        marginTop: SPACING.lg,
        borderRadius: RADIUS.xl,
        padding: SPACING.xl,
        overflow: 'hidden',
        ...SHADOWS.colored(COLORS.primary),
    },
    bannerContent: {
        zIndex: 2,
    },
    bannerTitle: {
        fontSize: 18,
        fontWeight: '800',
        color: '#fff',
        letterSpacing: -0.3,
    },
    bannerSub: {
        fontSize: 13,
        color: 'rgba(255,255,255,0.8)',
        marginTop: 2,
    },
    bannerDecor1: {
        position: 'absolute',
        right: -20,
        top: -20,
        width: 100,
        height: 100,
        borderRadius: 50,
        backgroundColor: 'rgba(255,255,255,0.12)',
    },
    bannerDecor2: {
        position: 'absolute',
        right: 40,
        bottom: -30,
        width: 80,
        height: 80,
        borderRadius: 40,
        backgroundColor: 'rgba(255,255,255,0.08)',
    },
    scrollContent: {
        padding: SPACING.lg,
        paddingBottom: 40,
    },
    sectionTitle: {
        fontSize: 15,
        fontWeight: '700',
        color: COLORS.textTertiary,
        marginBottom: SPACING.md,
        textTransform: 'uppercase',
        letterSpacing: 0.8,
    },
    grid: {
        flexDirection: 'row',
        flexWrap: 'wrap',
        gap: SPACING.md,
    },
    cardWrapper: {
        width: CARD_WIDTH,
    },
    card: {
        borderRadius: RADIUS.xl,
        padding: SPACING.lg,
        minHeight: 130,
        ...SHADOWS.small,
        position: 'relative',
    },
    iconCircle: {
        width: 48,
        height: 48,
        borderRadius: 24,
        justifyContent: 'center',
        alignItems: 'center',
        marginBottom: SPACING.sm,
    },
    cardTitle: {
        fontSize: 14,
        fontWeight: '800',
        marginBottom: 2,
    },
    cardDesc: {
        fontSize: 11,
        color: COLORS.textTertiary,
        lineHeight: 14,
    },
    cardArrow: {
        position: 'absolute',
        bottom: SPACING.md,
        right: SPACING.md,
    },
});
