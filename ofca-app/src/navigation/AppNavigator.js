import React, { useState, useEffect } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import * as SecureStore from 'expo-secure-store';
import { ActivityIndicator, View, StatusBar, TouchableOpacity, Text, StyleSheet } from 'react-native';
import { MaterialCommunityIcons } from '@expo/vector-icons';

import LoginScreen from '../screens/Auth/LoginScreen';
import DashboardScreen from '../screens/Dashboard/DashboardScreen';
import ProfileScreen from '../screens/Profile/ProfileScreen';
import VillagesScreen from '../screens/Villages/VillagesScreen';
import CreateVillageScreen from '../screens/Villages/CreateVillageScreen';
import OrganisationsScreen from '../screens/Organisations/OrganisationsScreen';
import ProducteursScreen from '../screens/Producteurs/ProducteursScreen';
import ExportationsScreen from '../screens/Exportations/ExportationsScreen';
import CreditsScreen from '../screens/Credits/CreditsScreen';
import ControlesScreen from '../screens/Controles/ControlesScreen';
import IdentificationsScreen from '../screens/Identifications/IdentificationsScreen';
import SyncScreen from '../screens/Sync/SyncScreen'; // ← NOUVEAU

import { setUnauthenticatedHandler, getQueue } from '../api/client';
import { COLORS } from '../theme';

const Stack = createNativeStackNavigator();

// ── Bouton Sync dans le header (avec badge) ──────────────────
function SyncHeaderButton({ navigation }) {
    const [count, setCount] = useState(0);

    useEffect(() => {
        // Vérifier la file toutes les 5 secondes
        const check = async () => {
            const queue = await getQueue();
            setCount(queue.length);
        };
        check();
        const interval = setInterval(check, 5000);
        return () => clearInterval(interval);
    }, []);

    return (
        <TouchableOpacity
            onPress={() => navigation.navigate('Sync')}
            style={styles.syncBtn}
        >
            <MaterialCommunityIcons
                name="cloud-sync-outline"
                size={24}
                color={count > 0 ? '#E53935' : COLORS.primary}
            />
            {count > 0 && (
                <View style={styles.badge}>
                    <Text style={styles.badgeText}>{count > 99 ? '99+' : count}</Text>
                </View>
            )}
        </TouchableOpacity>
    );
}

export default function AppNavigator() {
    const [isLoading, setIsLoading] = useState(true);
    const [userToken, setUserToken] = useState(null);

    useEffect(() => {
        setUnauthenticatedHandler(() => setUserToken(null));

        const bootstrapAsync = async () => {
            let token;
            try {
                token = await SecureStore.getItemAsync('userToken');
            } catch (e) { }
            setUserToken(token);
            setIsLoading(false);
        };

        bootstrapAsync();
    }, []);

    if (isLoading) {
        return (
            <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: '#fff' }}>
                <ActivityIndicator size="large" color={COLORS.primary} />
            </View>
        );
    }

    return (
        <NavigationContainer>
            <StatusBar barStyle="dark-content" backgroundColor="#fff" />
            <Stack.Navigator
                screenOptions={({ navigation }) => ({
                    headerStyle: { backgroundColor: '#fff' },
                    headerShadowVisible: false,
                    headerTitleStyle: { fontWeight: '700', fontSize: 17, color: COLORS.textPrimary },
                    headerTintColor: COLORS.primary,
                    // ← Bouton sync dans tous les headers (sauf Login)
                    headerRight: () => <SyncHeaderButton navigation={navigation} />,
                })}
            >
                {userToken == null ? (
                    <Stack.Screen
                        name="Login"
                        component={LoginScreen}
                        options={{ headerShown: false }}
                        initialParams={{ setUserToken }}
                    />
                ) : (
                    <>
                        <Stack.Screen
                            name="Dashboard"
                            component={DashboardScreen}
                            initialParams={{ setUserToken }}
                            options={{ headerShown: false }}
                        />
                        <Stack.Screen name="Villages" component={VillagesScreen} options={{ title: 'Gestion des Villages' }} />
                        <Stack.Screen name="CreateVillage" component={CreateVillageScreen} options={{ title: 'Nouveau Village' }} />
                        <Stack.Screen name="Organisations" component={OrganisationsScreen} options={{ title: 'Organisations Paysannes' }} />
                        <Stack.Screen name="Producteurs" component={ProducteursScreen} options={{ title: 'Ajouter un producteur' }} />
                        <Stack.Screen name="Exportations" component={ExportationsScreen} options={{ title: 'Exportations' }} />
                        <Stack.Screen name="Credits" component={CreditsScreen} options={{ title: 'Demandes de Crédit' }} />
                        <Stack.Screen name="Controles" component={ControlesScreen} options={{ title: 'Contrôle interne' }} />
                        <Stack.Screen name="Identifications" component={IdentificationsScreen} options={{ title: 'Identifications GPS' }} />
                        <Stack.Screen name="Profile" component={ProfileScreen} initialParams={{ setUserToken }} options={{ title: 'Mon Profil' }} />

                        {/* ── NOUVEAU : écran de synchronisation ── */}
                        <Stack.Screen
                            name="Sync"
                            component={SyncScreen}
                            options={{
                                title: 'Synchronisation',
                                headerRight: () => null, // pas de bouton sync sur cet écran
                            }}
                        />
                    </>
                )}
            </Stack.Navigator>
        </NavigationContainer>
    );
}

const styles = StyleSheet.create({
    syncBtn: {
        marginRight: 8,
        padding: 4,
        position: 'relative',
    },
    badge: {
        position: 'absolute',
        top: 0, right: 0,
        minWidth: 16, height: 16,
        borderRadius: 8,
        backgroundColor: '#E53935',
        alignItems: 'center',
        justifyContent: 'center',
        paddingHorizontal: 3,
    },
    badgeText: {
        color: '#fff',
        fontSize: 9,
        fontWeight: '900',
    },
});