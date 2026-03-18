import React, { useState, useRef } from 'react';
import {
    View,
    Text,
    TextInput,
    TouchableOpacity,
    StyleSheet,
    Alert,
    ActivityIndicator,
    Image,
    KeyboardAvoidingView,
    Platform,
    Animated,
    Dimensions,
} from 'react-native';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import apiClient from '../../api/client';
import * as SecureStore from 'expo-secure-store';
import { COLORS, RADIUS, SHADOWS, SPACING } from '../../theme';

const { width, height } = Dimensions.get('window');

export default function LoginScreen({ route }) {
    const { setUserToken } = route.params;
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [loading, setLoading] = useState(false);
    const [showPassword, setShowPassword] = useState(false);
    const [emailFocused, setEmailFocused] = useState(false);
    const [passwordFocused, setPasswordFocused] = useState(false);

    const shakeAnim = useRef(new Animated.Value(0)).current;
    const fadeAnim = useRef(new Animated.Value(0)).current;
    const slideAnim = useRef(new Animated.Value(30)).current;

    React.useEffect(() => {
        Animated.parallel([
            Animated.timing(fadeAnim, { toValue: 1, duration: 700, useNativeDriver: true }),
            Animated.timing(slideAnim, { toValue: 0, duration: 700, useNativeDriver: true }),
        ]).start();
    }, []);

    const shake = () => {
        shakeAnim.setValue(0);
        Animated.sequence([
            Animated.timing(shakeAnim, { toValue: 10, duration: 60, useNativeDriver: true }),
            Animated.timing(shakeAnim, { toValue: -10, duration: 60, useNativeDriver: true }),
            Animated.timing(shakeAnim, { toValue: 10, duration: 60, useNativeDriver: true }),
            Animated.timing(shakeAnim, { toValue: -10, duration: 60, useNativeDriver: true }),
            Animated.timing(shakeAnim, { toValue: 0, duration: 60, useNativeDriver: true }),
        ]).start();
    };

    const handleLogin = async () => {
        if (!email || !password) {
            shake();
            Alert.alert('Champs requis', 'Veuillez remplir tous les champs.');
            return;
        }

        setLoading(true);
        try {
            const response = await apiClient.post('/login', {
                email,
                password,
                device_name: 'mobile-app',
            });

            if (response.data.access_token) {
                await SecureStore.setItemAsync('userToken', response.data.access_token);
                if (response.data.user) {
                    await SecureStore.setItemAsync('userInfo', JSON.stringify(response.data.user));
                }
                setUserToken(response.data.access_token);
            } else {
                shake();
                Alert.alert('Erreur', 'Identifiants incorrects');
            }
        } catch (error) {
            shake();
            if (error.response?.status === 422) {
                Alert.alert('Erreur', error.response.data.message || 'Identifiants incorrects');
            } else if (error.response?.data?.message) {
                Alert.alert('Erreur', error.response.data.message);
            } else {
                Alert.alert('Connexion impossible', 'Vérifiez votre connexion internet et réessayez.');
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <KeyboardAvoidingView
            style={styles.root}
            behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        >
            {/* Arrière-plan décoratif */}
            <View style={styles.bgDecor1} />
            <View style={styles.bgDecor2} />
            <View style={styles.bgDecor3} />

            <Animated.View
                style={[
                    styles.content,
                    { opacity: fadeAnim, transform: [{ translateY: slideAnim }] },
                ]}
            >
                {/* Logo & Titre */}
                <View style={styles.logoSection}>
                    <View style={styles.logoContainer}>
                        <Image
                            source={require('../../../assets/logo-ofca.png')}
                            style={styles.logo}
                            resizeMode="contain"
                        />
                    </View>
                    <Text style={styles.appName}>OFCA Contrôleur</Text>
                    <Text style={styles.appTagline}>Système de gestion terrain</Text>
                </View>

                {/* Carte de connexion */}
                <Animated.View
                    style={[styles.card, { transform: [{ translateX: shakeAnim }] }]}
                >
                    <Text style={styles.cardTitle}>Connexion</Text>
                    <Text style={styles.cardSubtitle}>Entrez vos identifiants pour continuer</Text>

                    {/* Champ Email */}
                    <View style={[styles.inputWrapper, emailFocused && styles.inputWrapperFocused]}>
                        <MaterialCommunityIcons
                            name="email-outline"
                            size={20}
                            color={emailFocused ? COLORS.primary : COLORS.textTertiary}
                            style={styles.inputIcon}
                        />
                        <TextInput
                            style={styles.textInput}
                            placeholder="Adresse email"
                            placeholderTextColor={COLORS.textTertiary}
                            autoCapitalize="none"
                            keyboardType="email-address"
                            value={email}
                            onChangeText={setEmail}
                            onFocus={() => setEmailFocused(true)}
                            onBlur={() => setEmailFocused(false)}
                        />
                    </View>

                    {/* Champ Mot de passe */}
                    <View style={[styles.inputWrapper, passwordFocused && styles.inputWrapperFocused]}>
                        <MaterialCommunityIcons
                            name="lock-outline"
                            size={20}
                            color={passwordFocused ? COLORS.primary : COLORS.textTertiary}
                            style={styles.inputIcon}
                        />
                        <TextInput
                            style={[styles.textInput, { flex: 1 }]}
                            placeholder="Mot de passe"
                            placeholderTextColor={COLORS.textTertiary}
                            secureTextEntry={!showPassword}
                            value={password}
                            onChangeText={setPassword}
                            onFocus={() => setPasswordFocused(true)}
                            onBlur={() => setPasswordFocused(false)}
                        />
                        <TouchableOpacity
                            onPress={() => setShowPassword(!showPassword)}
                            style={styles.eyeButton}
                        >
                            <MaterialCommunityIcons
                                name={showPassword ? 'eye-off-outline' : 'eye-outline'}
                                size={20}
                                color={COLORS.textTertiary}
                            />
                        </TouchableOpacity>
                    </View>

                    {/* Bouton de connexion */}
                    <TouchableOpacity
                        style={[styles.loginButton, loading && styles.loginButtonLoading]}
                        onPress={handleLogin}
                        disabled={loading}
                        activeOpacity={0.85}
                    >
                        {loading ? (
                            <ActivityIndicator color="#fff" size="small" />
                        ) : (
                            <>
                                <MaterialCommunityIcons name="login" size={18} color="#fff" style={{ marginRight: 8 }} />
                                <Text style={styles.loginButtonText}>SE CONNECTER</Text>
                            </>
                        )}
                    </TouchableOpacity>
                </Animated.View>

                {/* Footer */}
                <Text style={styles.footer}>
                    © {new Date().getFullYear()} OFCA — Tous droits réservés
                </Text>
            </Animated.View>
        </KeyboardAvoidingView>
    );
}

const styles = StyleSheet.create({
    root: {
        flex: 1,
        backgroundColor: '#f0fdf4',
    },
    bgDecor1: {
        position: 'absolute',
        top: -80,
        right: -60,
        width: 250,
        height: 250,
        borderRadius: 125,
        backgroundColor: COLORS.primaryLight,
        opacity: 0.15,
    },
    bgDecor2: {
        position: 'absolute',
        bottom: 100,
        left: -80,
        width: 200,
        height: 200,
        borderRadius: 100,
        backgroundColor: COLORS.primary,
        opacity: 0.1,
    },
    bgDecor3: {
        position: 'absolute',
        top: height * 0.35,
        right: -40,
        width: 120,
        height: 120,
        borderRadius: 60,
        backgroundColor: COLORS.primaryDark,
        opacity: 0.08,
    },
    content: {
        flex: 1,
        justifyContent: 'center',
        paddingHorizontal: SPACING.xl,
    },
    logoSection: {
        alignItems: 'center',
        marginBottom: SPACING.xxxl,
    },
    logoContainer: {
        width: 100,
        height: 100,
        borderRadius: RADIUS.xxl,
        backgroundColor: COLORS.white,
        justifyContent: 'center',
        alignItems: 'center',
        ...SHADOWS.large,
        marginBottom: SPACING.lg,
    },
    logo: {
        width: 80,
        height: 80,
    },
    appName: {
        fontSize: 26,
        fontWeight: '800',
        color: COLORS.primaryDark,
        letterSpacing: -0.5,
        marginBottom: SPACING.xs,
    },
    appTagline: {
        fontSize: 14,
        color: COLORS.textTertiary,
        fontWeight: '500',
    },
    card: {
        backgroundColor: COLORS.white,
        borderRadius: RADIUS.xxl,
        padding: SPACING.xxl,
        ...SHADOWS.large,
    },
    cardTitle: {
        fontSize: 22,
        fontWeight: '800',
        color: COLORS.textPrimary,
        marginBottom: SPACING.xs,
    },
    cardSubtitle: {
        fontSize: 14,
        color: COLORS.textTertiary,
        marginBottom: SPACING.xxl,
    },
    inputWrapper: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: COLORS.background,
        borderWidth: 1.5,
        borderColor: COLORS.border,
        borderRadius: RADIUS.lg,
        paddingHorizontal: SPACING.md,
        marginBottom: SPACING.md,
        height: 52,
    },
    inputWrapperFocused: {
        borderColor: COLORS.primary,
        backgroundColor: COLORS.primarySurface,
    },
    inputIcon: {
        marginRight: SPACING.sm,
    },
    textInput: {
        flex: 1,
        fontSize: 15,
        color: COLORS.textPrimary,
        paddingVertical: 0,
    },
    eyeButton: {
        padding: SPACING.xs,
    },
    loginButton: {
        flexDirection: 'row',
        backgroundColor: COLORS.primary,
        borderRadius: RADIUS.lg,
        height: 52,
        justifyContent: 'center',
        alignItems: 'center',
        marginTop: SPACING.sm,
        ...SHADOWS.colored(COLORS.primary),
    },
    loginButtonLoading: {
        opacity: 0.8,
    },
    loginButtonText: {
        color: COLORS.white,
        fontWeight: '800',
        fontSize: 15,
        letterSpacing: 0.5,
    },
    footer: {
        textAlign: 'center',
        marginTop: SPACING.xxl,
        fontSize: 12,
        color: COLORS.textDisabled,
    },
});
