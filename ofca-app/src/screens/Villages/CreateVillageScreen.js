import React, { useState, useEffect } from 'react';
import {
    View,
    Text,
    TextInput,
    TouchableOpacity,
    StyleSheet,
    ScrollView,
    Alert,
    ActivityIndicator,
    KeyboardAvoidingView,
    Platform,
} from 'react-native';
import { Picker } from '@react-native-picker/picker';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import apiClient from '../../api/client';
import { COLORS, RADIUS, SHADOWS, SPACING } from '../../theme';

export default function CreateVillageScreen({ navigation }) {
    const [regions, setRegions] = useState([]);
    const [prefectures, setPrefectures] = useState([]);
    const [communes, setCommunes] = useState([]);
    const [cantons, setCantons] = useState([]);

    const [nom, setNom] = useState('');
    const [zone, setZone] = useState('');
    const [selectedRegion, setSelectedRegion] = useState('');
    const [selectedPrefecture, setSelectedPrefecture] = useState('');
    const [selectedCommune, setSelectedCommune] = useState('');
    const [selectedCanton, setSelectedCanton] = useState('');
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        loadLocations();
    }, []);

    const loadLocations = async () => {
        try {
            const [regRes, prefRes, commRes, cantRes] = await Promise.all([
                apiClient.get('/regions'),
                apiClient.get('/prefectures'),
                apiClient.get('/communes'),
                apiClient.get('/cantons'),
            ]);
            setRegions(regRes.data);
            setPrefectures(prefRes.data);
            setCommunes(commRes.data);
            setCantons(cantRes.data);
        } catch (e) {
            Alert.alert('Erreur', 'Impossible de charger les localisations');
        }
    };

    // Filtrer les communes selon la préfecture sélectionnée
    const filteredCommunes = selectedPrefecture
        ? communes.filter(c => c.prefecture_id?.toString() === selectedPrefecture)
        : communes;

    const handleSubmit = async () => {
        if (!nom.trim() || !selectedRegion || !selectedPrefecture || !selectedCommune || !selectedCanton) {
            Alert.alert('Attention', 'Veuillez remplir tous les champs obligatoires.');
            return;
        }

        setSubmitting(true);
        try {
            await apiClient.post('/villages', {
                nom: nom.trim(),
                zone: zone.trim(),
                region_id: selectedRegion,
                prefecture_id: selectedPrefecture,
                commune_id: selectedCommune,
                canton_id: selectedCanton,
            });
            Alert.alert('Succès ✅', 'Village créé avec succès !');
            navigation.goBack();
        } catch (error) {
            // Récupère le vrai message d'erreur du serveur (validation 422, etc.)
            const serverMsg = error?.response?.data?.message
                || (error?.response?.data?.errors
                    ? Object.values(error.response.data.errors).flat().join('\n')
                    : null)
                || error?.message
                || 'Impossible de créer le village.';
            Alert.alert('Erreur', serverMsg);
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <KeyboardAvoidingView
            behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
            style={styles.container}
        >
            <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
                
                <View style={styles.card}>
                    <View style={styles.cardHeader}>
                        <View style={styles.iconCircle}>
                            <MaterialCommunityIcons name="home-plus" size={24} color={COLORS.village} />
                        </View>
                        <View>
                            <Text style={styles.cardHeaderTitle}>Nouveau Village</Text>
                            <Text style={styles.cardHeaderSub}>Renseignez les informations de localisation</Text>
                        </View>
                    </View>

                    <View style={styles.formGroup}>
                        <Text style={styles.label}>Nom du village *</Text>
                        <TextInput style={styles.input} value={nom} onChangeText={setNom} placeholder="Ex: Village de Kpomé" />
                    </View>

                    <View style={styles.formGroup}>
                        <Text style={styles.label}>Zone (optionnel)</Text>
                        <TextInput style={styles.input} value={zone} onChangeText={setZone} placeholder="Ex: Zone Nord" />
                    </View>

                    <View style={styles.formGroup}>
                        <Text style={styles.label}>Région *</Text>
                        <View style={styles.pickerWrapper}>
                            <Picker selectedValue={selectedRegion} onValueChange={setSelectedRegion}>
                                {regions.map(r => <Picker.Item key={r.id} label={r.nom} value={r.id.toString()} />)}
                            </Picker>
                        </View>
                    </View>

                    <View style={styles.formGroup}>
                        <Text style={styles.label}>Préfecture *</Text>
                        <View style={styles.pickerWrapper}>
                            <Picker
                                selectedValue={selectedPrefecture}
                                onValueChange={(val) => {
                                    setSelectedPrefecture(val);
                                    setSelectedCommune(''); // reset commune si préfecture change
                                }}
                            >
                                <Picker.Item label="-- Choisir une préfecture --" value="" />
                                {prefectures.map(p => <Picker.Item key={p.id} label={p.nom} value={p.id.toString()} />)}
                            </Picker>
                        </View>
                    </View>

                    <View style={styles.formGroup}>
                        <Text style={styles.label}>Commune *</Text>
                        <View style={styles.pickerWrapper}>
                            <Picker selectedValue={selectedCommune} onValueChange={setSelectedCommune}>
                                <Picker.Item label="-- Choisir une commune --" value="" />
                                {filteredCommunes.map(c => <Picker.Item key={c.id} label={c.nom} value={c.id.toString()} />)}
                            </Picker>
                        </View>
                    </View>

                    <View style={styles.formGroup}>
                        <Text style={styles.label}>Canton *</Text>
                        <View style={styles.pickerWrapper}>
                            <Picker selectedValue={selectedCanton} onValueChange={setSelectedCanton}>
                                {cantons.map(c => <Picker.Item key={c.id} label={c.nom} value={c.id.toString()} />)}
                            </Picker>
                        </View>
                    </View>

                    <TouchableOpacity 
                        style={[styles.submitBtn, submitting && styles.submitBtnDisabled]} 
                        onPress={handleSubmit}
                        disabled={submitting}
                    >
                        {submitting ? <ActivityIndicator color="#fff" /> : (
                            <>
                                <MaterialCommunityIcons name="check" size={20} color="#fff" style={{marginRight: 8}} />
                                <Text style={styles.submitBtnText}>Enregistrer le Village</Text>
                            </>
                        )}
                    </TouchableOpacity>
                </View>

            </ScrollView>
        </KeyboardAvoidingView>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: COLORS.background },
    scrollContent: { padding: SPACING.lg },
    card: {
        backgroundColor: COLORS.white,
        borderRadius: RADIUS.xxl,
        padding: SPACING.xl,
        ...SHADOWS.medium,
        marginTop: SPACING.md,
    },
    cardHeader: { flexDirection: 'row', alignItems: 'center', gap: SPACING.md, marginBottom: SPACING.xl },
    iconCircle: { width: 50, height: 50, borderRadius: 25, backgroundColor: COLORS.villageSurface, justifyContent: 'center', alignItems: 'center' },
    cardHeaderTitle: { fontSize: 20, fontWeight: '800', color: COLORS.textPrimary },
    cardHeaderSub: { fontSize: 13, color: COLORS.textTertiary, marginTop: 2 },
    formGroup: { marginBottom: SPACING.lg },
    label: { fontSize: 13, fontWeight: '700', color: COLORS.textSecondary, marginBottom: 8 },
    input: {
        borderWidth: 1.5,
        borderColor: COLORS.border,
        borderRadius: RADIUS.md,
        padding: SPACING.md,
        backgroundColor: COLORS.background,
        fontSize: 15,
        color: COLORS.textPrimary,
    },
    pickerWrapper: {
        borderWidth: 1.5,
        borderColor: COLORS.border,
        borderRadius: RADIUS.md,
        backgroundColor: COLORS.background,
        overflow: 'hidden',
    },
    submitBtn: {
        flexDirection: 'row',
        backgroundColor: COLORS.village,
        padding: SPACING.lg,
        borderRadius: RADIUS.lg,
        alignItems: 'center',
        justifyContent: 'center',
        marginTop: SPACING.xl,
        ...SHADOWS.colored(COLORS.village),
    },
    submitBtnDisabled: { opacity: 0.6 },
    submitBtnText: { color: '#fff', fontWeight: '800', fontSize: 16 },
});
