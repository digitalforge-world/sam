import React, { useState, useCallback } from 'react';
import {
    View, Text, TextInput, TouchableOpacity, StyleSheet,
    ScrollView, Alert, ActivityIndicator,
} from 'react-native';
import { Picker } from '@react-native-picker/picker';
import { useFocusEffect } from '@react-navigation/native';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import apiClient from '../../api/client';
import { COLORS, RADIUS, SHADOWS, SPACING } from '../../theme';
import LoadingScreen from '../../components/LoadingScreen';

export default function ControlesScreen({ navigation }) {

    // Listes
    const [villages, setVillages] = useState([]);
    const [organisations, setOrganisations] = useState([]);
    const [producteurs, setProducteurs] = useState([]);
    const [parcelles, setParcelles] = useState([]);
    const [cultures, setCultures] = useState([]);
    const [loadingData, setLoadingData] = useState(true);

    // Champs formulaire
    const [selectedVillage, setSelectedVillage] = useState('');
    const [selectedOrganisation, setSelectedOrganisation] = useState('');
    const [selectedProducteur, setSelectedProducteur] = useState('');
    const [selectedParcelle, setSelectedParcelle] = useState('');
    const [selectedCulture, setSelectedCulture] = useState('');
    const [superficieBio, setSuperficieBio] = useState('');
    const [submitting, setSubmitting] = useState(false);

    useFocusEffect(useCallback(() => { loadAll(); }, []));

    const loadAll = async () => {
        setLoadingData(true);
        try {
            const [villRes, orgRes, prodRes, parRes, cultRes] = await Promise.all([
                apiClient.get('/villages'),
                apiClient.get('/organisations'),
                apiClient.get('/producteurs'),
                apiClient.get('/parcelles'),
                apiClient.get('/cultures'),
            ]);
            setVillages(villRes.data.data ?? villRes.data);
            setOrganisations(orgRes.data.data ?? orgRes.data);
            setProducteurs(prodRes.data.data ?? prodRes.data);
            setParcelles(parRes.data.data ?? parRes.data);
            setCultures(cultRes.data.data ?? cultRes.data);
        } catch {
            Alert.alert('Erreur', 'Impossible de charger les données.');
        } finally {
            setLoadingData(false);
        }
    };

    const handleSubmit = async () => {
        if (!selectedProducteur || !selectedParcelle || !selectedCulture) {
            Alert.alert('Champs obligatoires', 'Producteur, Parcelle et Culture sont requis.');
            return;
        }
        setSubmitting(true);
        try {
            await apiClient.post('/controles', {
                village_id: selectedVillage || null,
                organisation_id: selectedOrganisation || null,
                producteur_id: selectedProducteur,
                parcelle_id: selectedParcelle,
                culture_id: selectedCulture,
                superficie_bio: superficieBio ? parseFloat(superficieBio) : null,
            });
            Alert.alert('Succès ✅', 'Contrôle enregistré !');
            navigation.goBack();
        } catch (e) {
            Alert.alert('Erreur', e.response?.data?.message || 'Problème d\'enregistrement.');
        } finally {
            setSubmitting(false);
        }
    };

    if (loadingData) return <LoadingScreen message="Chargement..." />;

    const renderPicker = (label, value, onChange, items, labelKey = 'nom', valueKey = 'id') => (
        <View style={styles.pickerBox}>
            <Picker
                selectedValue={value}
                onValueChange={onChange}
                style={styles.picker}
            >
                <Picker.Item label={label} value="" color={COLORS.textDisabled} />
                {items.map((item, i) => (
                    <Picker.Item
                        key={i}
                        label={typeof item === 'object' ? (item[labelKey] ?? item.label ?? '') : item}
                        value={(typeof item === 'object' ? (item[valueKey] ?? item.value ?? item.id) : item).toString()}
                    />
                ))}
            </Picker>
        </View>
    );

    return (
        <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>

            {/* Village */}
            {renderPicker('Village', selectedVillage, setSelectedVillage, villages)}

            {/* Organisation paysanne */}
            {renderPicker('Organisation paysanne', selectedOrganisation, setSelectedOrganisation, organisations)}

            {/* Producteur */}
            {renderPicker(
                'Producteur', selectedProducteur, setSelectedProducteur,
                producteurs.map(p => ({ label: `${p.nom} ${p.prenom}`, value: p.id })),
                'label', 'value'
            )}

            {/* Parcelle */}
            {renderPicker(
                'Parcelle', selectedParcelle, setSelectedParcelle,
                parcelles.map(p => ({ label: p.code ?? p.nom ?? `Parcelle #${p.id}`, value: p.id })),
                'label', 'value'
            )}

            {/* Culture à certifier */}
            {renderPicker('Culture à certifier', selectedCulture, setSelectedCulture, cultures)}

            {/* Superficie dédiée au bio */}
            <View style={styles.superficieBox}>
                <Text style={styles.superficieFloatLabel}>Superficie dédié au bio</Text>
                <View style={styles.superficieRow}>
                    <TextInput
                        style={styles.superficieInput}
                        value={superficieBio}
                        onChangeText={setSuperficieBio}
                        keyboardType="numeric"
                        placeholder="0.0"
                        placeholderTextColor={COLORS.textDisabled}
                    />
                    <Text style={styles.superficieUnit}>ha</Text>
                </View>
            </View>

            {/* Bouton Sauvegarder */}
            <TouchableOpacity
                style={[styles.submitBtn, submitting && styles.btnDisabled]}
                onPress={handleSubmit}
                disabled={submitting}
            >
                {submitting
                    ? <ActivityIndicator color="#fff" />
                    : <Text style={styles.submitBtnText}>Sauvegarder</Text>
                }
            </TouchableOpacity>

            <View style={{ height: 60 }} />
        </ScrollView>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: '#fff' },

    // Picker style — bordures comme le screenshot
    pickerBox: {
        borderWidth: 1,
        borderColor: '#D0D0D0',
        borderRadius: 10,
        marginHorizontal: 16,
        marginTop: 12,
        backgroundColor: '#fff',
        overflow: 'hidden',
    },
    picker: {
        height: 56,
        color: '#333',
    },

    // Superficie avec label flottant
    superficieBox: {
        borderWidth: 1,
        borderColor: '#D0D0D0',
        borderRadius: 10,
        marginHorizontal: 16,
        marginTop: 12,
        paddingHorizontal: 14,
        paddingTop: 8,
        paddingBottom: 10,
        backgroundColor: '#fff',
        position: 'relative',
    },
    superficieFloatLabel: {
        fontSize: 12,
        color: '#E07020',
        fontWeight: '600',
        marginBottom: 4,
    },
    superficieRow: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
    },
    superficieInput: {
        fontSize: 22,
        fontWeight: '700',
        color: '#111',
        flex: 1,
        padding: 0,
    },
    superficieUnit: {
        fontSize: 18,
        fontWeight: '600',
        color: '#555',
    },

    // Bouton
    submitBtn: {
        backgroundColor: '#1A1A2E',
        marginHorizontal: 16,
        marginTop: 24,
        padding: 18,
        borderRadius: 40,
        alignItems: 'center',
    },
    submitBtnText: { color: '#fff', fontWeight: '900', fontSize: 16 },
    btnDisabled: { opacity: 0.6 },
});