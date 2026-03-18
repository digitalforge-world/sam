import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { getQueue } from '../api/client';
import NetInfo from '@react-native-community/netinfo';

/**
 * SyncBadge — badge rouge qui montre le nombre d'éléments
 * en attente de synchronisation. À placer dans ta navigation.
 *
 * Usage dans ton TabNavigator :
 *
 * import SyncBadge from '../components/SyncBadge';
 *
 * <Tab.Screen
 *   name="Sync"
 *   component={SyncScreen}
 *   options={{
 *     tabBarLabel: 'Sync',
 *     tabBarIcon: ({ color, size }) => (
 *       <View>
 *         <MaterialCommunityIcons name="cloud-sync" size={size} color={color} />
 *         <SyncBadge />
 *       </View>
 *     ),
 *   }}
 * />
 */
export default function SyncBadge() {
    const [count, setCount] = useState(0);
    const [isOnline, setIsOnline] = useState(true);

    useEffect(() => {
        checkQueue();
        const interval = setInterval(checkQueue, 5000); // check toutes les 5s

        const unsubNet = NetInfo.addEventListener(state => {
            setIsOnline(state.isConnected);
        });

        return () => {
            clearInterval(interval);
            unsubNet();
        };
    }, []);

    const checkQueue = async () => {
        const queue = await getQueue();
        setCount(queue.length);
    };

    if (count === 0) return null;

    return (
        <View style={[styles.badge, { backgroundColor: isOnline ? '#1565C0' : '#E53935' }]}>
            <Text style={styles.text}>{count > 99 ? '99+' : count}</Text>
        </View>
    );
}

const styles = StyleSheet.create({
    badge: {
        position: 'absolute',
        top: -4, right: -8,
        minWidth: 18, height: 18,
        borderRadius: 9,
        alignItems: 'center',
        justifyContent: 'center',
        paddingHorizontal: 4,
    },
    text: { color: '#fff', fontSize: 10, fontWeight: '900' },
});