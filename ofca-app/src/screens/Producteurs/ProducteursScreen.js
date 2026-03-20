import React, { useState, useCallback } from 'react';
import {
    View, Text, TextInput, TouchableOpacity, StyleSheet,
    ScrollView, Alert, ActivityIndicator,
} from 'react-native';
import { Picker } from '@react-native-picker/picker';
import { useFocusEffect } from '@react-navigation/native';
import apiClient from '../../api/client';
import { COLORS } from '../../theme';
import LoadingScreen from '../../components/LoadingScreen';

export default function ProducteursScreen({ navigation }) {

    // Listes
    const [villages, setVillages] = useState([]);
    const [organisations, setOrganisations] = useState([]);
    const [loadingData, setLoadingData] = useState(true);

    // Champs formulaire
    const [selectedVillage, setSelectedVillage] = useState('');
    const [selectedOrganisation, setSelectedOrganisation] = useState('');
    const [nomPrenoms, setNomPrenoms] = useState('');
    const [sexe, setSexe] = useState('');
    const [telephone, setTelephone] = useState('');
    const [typeCarte, setTypeCarte] = useState('');
    const [statut, setStatut] = useState('');
    const [annee, setAnnee] = useState('');
    const [submitting, setSubmitting] = useState(false);

    useFocusEffect(useCallback(() => { loadAll(); }, []));

    const loadAll = async () => {
        setLoadingData(true);
        try {
            const [villRes, orgRes] = await Promise.all([
                apiClient.get('/villages'),
                apiClient.get('/organisations'),
            ]);
            setVillages(villRes.data.data ?? villRes.data);
            setOrganisations(orgRes.data.data ?? orgRes.data);
        } catch {
            Alert.alert('Erreur', 'Impossible de charger les données.');
        } finally {
            setLoadingData(false);
        }
    };

    const handleSubmit = async () => {
        // Séparer nom et prénom depuis "Nom et Prénoms"
        const parts = nomPrenoms.trim().split(' ');
        const nom = parts[0] ?? '';
        const prenom = parts.slice(1).join(' ') ?? '';

        if (!nom) {
            Alert.alert('Champ obligatoire', 'Veuillez saisir le Nom et Prénoms.');
            return;
        }
        if (!selectedVillage) {
            Alert.alert('Champ obligatoire', 'Veuillez sélectionner un village.');
            return;
        }

        setSubmitting(true);
        try {
            await apiClient.post('/producteurs', {
                nom,
                prenom,
                sexe: sexe || null,
                telephone: telephone.trim() || null,
                type_carte: typeCarte || null,
                statut: statut || null,
                annee_adhesion: annee || null,
                village_id: selectedVillage,
                organisation_paysanne_id: selectedOrganisation || null,
            });
            Alert.alert('Succès ✅', 'Producteur enregistré !');
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
            <Picker selectedValue={value} onValueChange={onChange} style={styles.picker}>
                <Picker.Item label={label} value="" color="#AAA" />
                {items.map((item, i) => (
                    <Picker.Item
                        key={i}
                        label={typeof item === 'object' ? (item[labelKey] ?? item.label ?? '') : item}
                        value={(typeof item === 'object'
                            ? (item[valueKey] ?? item.value ?? item.id)
                            : item).toString()}
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

            {/* Nom et Prénoms */}
            <View style={styles.inputBox}>
                <TextInput
                    style={styles.input}
                    value={nomPrenoms}
                    onChangeText={setNomPrenoms}
                    placeholder="Nom et Prénoms"
                    placeholderTextColor="#AAA"
                />
            </View>

            {/* Sexe */}
            {renderPicker('Sexe', sexe, setSexe, [
                { label: 'Masculin', value: 'Masculin' },
                { label: 'Féminin', value: 'Féminin' },
            ], 'label', 'value')}

            {/* Numéro de téléphone */}
            <View style={styles.inputBox}>
                <TextInput
                    style={styles.input}
                    value={telephone}
                    onChangeText={setTelephone}
                    placeholder="Numero de téléphone"
                    placeholderTextColor="#AAA"
                    keyboardType="phone-pad"
                />
            </View>

            {/* Type de carte */}
            {renderPicker('Type de carte', typeCarte, setTypeCarte, [
                { label: "Carte d'identité", value: 'Carte d\'identité' },
                { label: "Carte d'électeur", value: "Carte d'électeur" },
                { label: 'Passeport', value: 'Passeport' },
                { label: 'Permis de conduire', value: 'Permis de conduire' },
            ], 'label', 'value')}

            {/* Statut */}
            {renderPicker('Statut', statut, setStatut, [
                { label: 'Nouveau', value: 'Nouveau' },
                { label: 'Ancien', value: 'Ancien' },
            ], 'label', 'value')}

            {/* Année (toujours visible comme dans le screenshot) */}
            <View style={styles.inputBox}>
                <TextInput
                    style={styles.input}
                    value={annee}
                    onChangeText={setAnnee}
                    placeholder="Anneé"
                    placeholderTextColor="#AAA"
                    keyboardType="numeric"
                    maxLength={4}
                />
            </View>

            {/* Bouton Soumettre */}
            <TouchableOpacity
                style={[styles.submitBtn, submitting && styles.btnDisabled]}
                onPress={handleSubmit}
                disabled={submitting}
            >
                {submitting
                    ? <ActivityIndicator color="#fff" />
                    : <Text style={styles.submitBtnText}>Soumettre</Text>
                }
            </TouchableOpacity>

            <View style={{ height: 60 }} />
        </ScrollView>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: '#fff' },

    // Champs texte
    inputBox: {
        borderWidth: 1,
        borderColor: '#D0D0D0',
        borderRadius: 10,
        marginHorizontal: 16,
        marginTop: 12,
        paddingHorizontal: 14,
        backgroundColor: '#fff',
        justifyContent: 'center',
    },
    input: {
        height: 56,
        fontSize: 15,
        color: '#111',
    },

    // Pickers
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

    // Bouton
    submitBtn: {
        backgroundColor: '#1A1A2E',
        marginHorizontal: 16,
        marginTop: 32,
        padding: 18,
        borderRadius: 40,
        alignItems: 'center',
    },
    submitBtnText: { color: '#fff', fontWeight: '900', fontSize: 16 },
    btnDisabled: { opacity: 0.6 },
});