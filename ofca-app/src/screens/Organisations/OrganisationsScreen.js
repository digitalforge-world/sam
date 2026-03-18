import React, { useState, useCallback } from 'react';
import {
    View,
    Text,
    TextInput,
    TouchableOpacity,
    StyleSheet,
    FlatList,
    Alert,
    ActivityIndicator,
    Modal,
    KeyboardAvoidingView,
    Platform,
    ScrollView,
} from 'react-native';
import { Picker } from '@react-native-picker/picker';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import { useFocusEffect } from '@react-navigation/native';
import apiClient from '../../api/client';
import { COLORS, RADIUS, SHADOWS, SPACING } from '../../theme';
import LoadingScreen from '../../components/LoadingScreen';
import EmptyState from '../../components/EmptyState';

export default function OrganisationsScreen() {
    const [organisations, setOrganisations] = useState([]);
    const [loadingList, setLoadingList] = useState(true);
    const [villages, setVillages] = useState([]);
    const [zones, setZones] = useState([]);

    const [modalVisible, setModalVisible] = useState(false);
    const [nom, setNom] = useState('');
    const [selectedVillage, setSelectedVillage] = useState('');
    const [selectedZone, setSelectedZone] = useState('');
    const [submitting, setSubmitting] = useState(false);

    useFocusEffect(
        useCallback(() => {
            loadAll();
        }, [])
    );

    const loadAll = async () => {
        setLoadingList(true);
        try {
            const [orgRes, villRes, zoneRes] = await Promise.all([
                apiClient.get('/organisations'),
                apiClient.get('/villages'),
                apiClient.get('/zones'),
            ]);
            setOrganisations(orgRes.data.data ?? orgRes.data);
            setVillages(villRes.data.data ?? villRes.data);
            setZones(zoneRes.data.data ?? zoneRes.data);
        } catch (error) {
            console.error('Erreur chargement:', error);
            Alert.alert('Erreur', 'Impossible de charger les données');
        } finally {
            setLoadingList(false);
        }
    };

    const handleSubmit = async () => {
        if (!nom.trim()) {
            Alert.alert('Champ manquant', 'Veuillez saisir un nom');
            return;
        }
        if (!selectedVillage) {
            Alert.alert('Champ manquant', 'Veuillez sélectionner un village');
            return;
        }

        setSubmitting(true);
        try {
            await apiClient.post('/organisations', {
                nom: nom.trim(),
                village_id: selectedVillage,
                zone_id: selectedZone,
            });
            Alert.alert('Succès ✅', 'Organisation enregistrée !');
            resetForm();
            setModalVisible(false);
            loadAll();
        } catch (error) {
            const msg = error.response?.data?.message || 'Erreur lors de la création';
            Alert.alert('Erreur', msg);
        } finally {
            setSubmitting(false);
        }
    };

    const resetForm = () => {
        setNom('');
        setSelectedVillage('');
        setSelectedZone('');
    };

    const renderOrganisation = ({ item }) => (
        <View style={styles.card}>
            <View style={[styles.cardIconBox, { backgroundColor: COLORS.organisationSurface }]}>
                <MaterialCommunityIcons name="office-building" size={26} color={COLORS.organisation} />
            </View>
            <View style={styles.cardBody}>
                <Text style={styles.cardTitle}>{item.nom}</Text>
                <View style={styles.metaRow}>
                    <MaterialCommunityIcons name="map-marker-outline" size={13} color={COLORS.textTertiary} />
                    <Text style={styles.metaText}>{item.village?.nom || 'N/A'}</Text>
                </View>
                {item.zone && (
                    <View style={styles.metaRow}>
                        <MaterialCommunityIcons name="map-outline" size={13} color={COLORS.textTertiary} />
                        <Text style={styles.metaText}>Zone: {item.zone.nom}</Text>
                    </View>
                )}
            </View>
        </View>
    );

    if (loadingList) return <LoadingScreen message="Chargement des organisations..." />;

    return (
        <View style={styles.container}>
            <View style={styles.subHeader}>
                <Text style={styles.subHeaderCount}>{organisations.length} organisation(s)</Text>
                <TouchableOpacity
                    style={styles.addBtn}
                    onPress={() => setModalVisible(true)}
                    activeOpacity={0.8}
                >
                    <MaterialCommunityIcons name="plus" size={18} color="#fff" />
                    <Text style={styles.addBtnText}>Nouvelle</Text>
                </TouchableOpacity>
            </View>

            <FlatList
                data={organisations}
                keyExtractor={(item) => item.id.toString()}
                renderItem={renderOrganisation}
                contentContainerStyle={styles.list}
                showsVerticalScrollIndicator={false}
                ListEmptyComponent={
                    <EmptyState
                        icon="🏢"
                        title="Aucune organisation"
                        subtitle="Enregistrez les coopératives et groupements ici."
                    />
                }
            />

            <Modal visible={modalVisible} animationType="slide" transparent>
                <View style={styles.modalOverlay}>
                    <KeyboardAvoidingView
                        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
                        style={{ width: '100%' }}
                    >
                        <View style={styles.modalContainer}>
                            <View style={styles.modalHandle} />
                            <View style={styles.modalHeader}>
                                <Text style={styles.modalTitle}>Nouvelle Organisation</Text>
                                <TouchableOpacity onPress={() => setModalVisible(false)} style={styles.closeBtn}>
                                    <MaterialCommunityIcons name="close" size={20} color={COLORS.textTertiary} />
                                </TouchableOpacity>
                            </View>

                            <ScrollView style={styles.formContent} showsVerticalScrollIndicator={false}>
                                <View style={styles.formGroup}>
                                    <Text style={styles.label}>Nom de l'organisation *</Text>
                                    <TextInput style={styles.input} value={nom} onChangeText={setNom} placeholder="Ex: Coopérative OFCA" />
                                </View>

                                <View style={styles.formGroup}>
                                    <Text style={styles.label}>Zone *</Text>
                                    <View style={styles.pickerWrapper}>
                                        <Picker selectedValue={selectedZone} onValueChange={setSelectedZone}>
                                            <Picker.Item label="— Sélectionner une zone —" value="" />
                                            {zones.map(z => <Picker.Item key={z.id} label={z.nom} value={z.id.toString()} />)}
                                        </Picker>
                                    </View>
                                </View>

                                <View style={styles.formGroup}>
                                    <Text style={styles.label}>Village *</Text>
                                    <View style={styles.pickerWrapper}>
                                        <Picker selectedValue={selectedVillage} onValueChange={setSelectedVillage}>
                                            <Picker.Item label="— Sélectionner un village —" value="" />
                                            {villages.map(v => <Picker.Item key={v.id} label={v.nom} value={v.id.toString()} />)}
                                        </Picker>
                                    </View>
                                </View>

                                <TouchableOpacity
                                    style={[styles.submitBtn, submitting && styles.submitBtnDisabled]}
                                    onPress={handleSubmit}
                                    disabled={submitting}
                                >
                                    {submitting ? <ActivityIndicator color="#fff" /> : <Text style={styles.submitBtnText}>Enregistrer</Text>}
                                </TouchableOpacity>
                            </ScrollView>
                        </View>
                    </KeyboardAvoidingView>
                </View>
            </Modal>
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
    addBtn: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: COLORS.organisation,
        paddingHorizontal: SPACING.lg,
        paddingVertical: 8,
        borderRadius: RADIUS.full,
        gap: 6,
        ...SHADOWS.colored(COLORS.organisation),
    },
    addBtnText: { color: '#fff', fontWeight: '800', fontSize: 13 },
    list: { padding: SPACING.md, paddingBottom: 40 },
    card: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: COLORS.white,
        padding: SPACING.lg,
        borderRadius: RADIUS.xl,
        marginBottom: SPACING.md,
        ...SHADOWS.small,
    },
    cardIconBox: {
        width: 52,
        height: 52,
        borderRadius: RADIUS.lg,
        justifyContent: 'center',
        alignItems: 'center',
        marginRight: SPACING.lg,
    },
    cardBody: { flex: 1 },
    cardTitle: { fontSize: 16, fontWeight: '800', color: COLORS.textPrimary, marginBottom: 4 },
    metaRow: { flexDirection: 'row', alignItems: 'center', gap: 6, marginBottom: 2 },
    metaText: { fontSize: 12, color: COLORS.textTertiary },
    // Modal (identique aux autres pour la cohérence)
    modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'flex-end' },
    modalContainer: { backgroundColor: COLORS.white, borderTopLeftRadius: RADIUS.xxl, borderTopRightRadius: RADIUS.xxl, maxHeight: '80%' },
    modalHandle: { width: 40, height: 4, backgroundColor: COLORS.border, borderRadius: 2, alignSelf: 'center', marginTop: 12 },
    modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', padding: SPACING.xl, borderBottomWidth: 1, borderBottomColor: COLORS.borderLight },
    modalTitle: { fontSize: 20, fontWeight: '800', color: COLORS.textPrimary },
    closeBtn: { width: 36, height: 36, borderRadius: 18, backgroundColor: COLORS.background, justifyContent: 'center', alignItems: 'center' },
    formContent: { padding: SPACING.xl },
    formGroup: { marginBottom: SPACING.lg },
    label: { fontSize: 13, fontWeight: '700', color: COLORS.textSecondary, marginBottom: 8 },
    input: { borderWidth: 1.5, borderColor: COLORS.border, borderRadius: RADIUS.md, padding: SPACING.md, backgroundColor: COLORS.background, fontSize: 15, color: COLORS.textPrimary },
    pickerWrapper: { borderWidth: 1.5, borderColor: COLORS.border, borderRadius: RADIUS.md, backgroundColor: COLORS.background, overflow: 'hidden' },
    submitBtn: { backgroundColor: COLORS.organisation, padding: SPACING.lg, borderRadius: RADIUS.lg, alignItems: 'center', height: 56, justifyContent: 'center', marginTop: SPACING.md, marginBottom: 40, ...SHADOWS.colored(COLORS.organisation) },
    submitBtnDisabled: { opacity: 0.6 },
    submitBtnText: { color: COLORS.white, fontWeight: '800', fontSize: 16 },
});
