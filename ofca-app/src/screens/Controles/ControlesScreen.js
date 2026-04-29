import React, { useState, useCallback, useRef } from 'react';
import {
    View, Text, TextInput, TouchableOpacity, StyleSheet,
    ScrollView, Alert, ActivityIndicator, Switch
} from 'react-native';
import { Picker } from '@react-native-picker/picker';
import { useFocusEffect } from '@react-navigation/native';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import SignatureScreen from 'react-native-signature-canvas';
import apiClient from '../../api/client';
import { COLORS, RADIUS, SHADOWS, SPACING } from '../../theme';
import LoadingScreen from '../../components/LoadingScreen';

const QUESTIONS_PART_1 = [
    "Utilisation de produits non autorisés (herbicides, engrais chimiques ou fumures organiques provenant d'élevage intensif, produits phytosanitaires, etc.) sur la parcelle bio",
    "Existence d'une zone tampon d'au moins 2 mètres entre les parcelles bio et les parcelles conventionnelles voisines (pas applicable lorsque la parcelle conventionnelle est en jachère)",
    "Parcelles proches d'une zone de pollution (carrière, usine, habitations, etc.)",
    "Gestion durable de la fertilité des sols (préparation du sol, gestion des résidus de récolte, lutte contre l'érosion, apport de fertilisants organiques)",
    "Le producteur a un plan de rotation bien défini sur l'ensemble de ses parcelles bio",
    "Biodiversité (présence d'arbres, haies, zones à haute valeur écologique, existence de bandes naturelles)",
    "Existence de culture parallèle (le producteur a une autre parcelle de la culture certifiée non enregistrée dans le projet bio)",
    "Pratique d'hygiène de la parcelle (présence de sachets plastiques ou autres emballages plastiques, de matières fécales d'origine animale ou humaine, autres matières polluantes)"
];

const QUESTIONS_PART_2 = [
    "Origine du matériel de reproduction végétal (MRV)",
    "Concordance des données entre les déclarations du producteur et la fiche de suivi (Nombre/superficies des parcelles bio enregistrées, les autres cultures du producteurs et les pratiques culturales, les superficies de ou des cultures à certifier, etc.)",
    "Gestion récolte et post-récolte/mesures de séparation lors des opérations suivante: récoltes, battage, vannage, emballage, transport, stockage, vente, etc.)",
    "Connaissances et respect de la règlementation interne (participation aux réunions, règles de base en AB...)",
    "Conduite des cultures conventionnelles dans l'exploitation (Risque d'utilisation de pdt chimique de synthèse/AB, changement de parcelles conventionnelle, notification dans le changement de parcelle, ...)"
];

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
    
    // Questions
    const [answersPart1, setAnswersPart1] = useState(
        QUESTIONS_PART_1.map(() => ({ conforme: false, commentaire: '' }))
    );

    const [answersPart2, setAnswersPart2] = useState(
        QUESTIONS_PART_2.map(() => ({ action: '', responsable: '', delai: '0', besoinSuivi: false }))
    );

    // Signature
    const signatureRef = useRef();

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

    const handleAnswer1Change = (index, field, value) => {
        const newAnswers = [...answersPart1];
        newAnswers[index][field] = value;
        setAnswersPart1(newAnswers);
    };

    const handleAnswer2Change = (index, field, value) => {
        const newAnswers = [...answersPart2];
        newAnswers[index][field] = value;
        setAnswersPart2(newAnswers);
    };

    const onSavePress = () => {
        if (!selectedProducteur || !selectedParcelle || !selectedCulture) {
            Alert.alert('Champs obligatoires', 'Producteur, Parcelle et Culture sont requis.');
            return;
        }
        setSubmitting(true);
        if (signatureRef.current) {
            signatureRef.current.readSignature();
        } else {
            submitData(null);
        }
    };

    const handleSignature = (signature) => {
        submitData(signature);
    };

    const handleEmptySignature = () => {
        submitData(null);
    };

    const clearSignature = () => {
        if (signatureRef.current) {
            signatureRef.current.clearSignature();
        }
    };

    const submitData = async (signatureData) => {
        try {
            await apiClient.post('/controles', {
                village_id: selectedVillage || null,
                organisation_id: selectedOrganisation || null,
                producteur_id: selectedProducteur,
                parcelle_id: selectedParcelle,
                culture_id: selectedCulture,
                superficie_bio: superficieBio ? parseFloat(superficieBio) : null,
                reponses_part1: answersPart1,
                reponses_part2: answersPart2,
                signature: signatureData,
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
                <Picker.Item label={label} value="" color={COLORS?.textDisabled || '#999'} />
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
        <ScrollView style={styles.container} showsVerticalScrollIndicator={false} contentContainerStyle={styles.scrollContent}>

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
                        placeholderTextColor={COLORS?.textDisabled || '#999'}
                    />
                    <Text style={styles.superficieUnit}>ha</Text>
                </View>
            </View>

            {/* Section Questions Partie 1 */}
            <View style={styles.questionsSection}>
                {QUESTIONS_PART_1.map((q, index) => (
                    <View key={index} style={styles.questionContainer}>
                        <View style={styles.questionHeader}>
                            <Text style={styles.questionNumber}>{index + 1}.</Text>
                            <Text style={styles.questionText}>{q}</Text>
                        </View>
                        
                        <View style={styles.switchContainer}>
                            <Text style={styles.switchLabel}>Est en conformité</Text>
                            <Switch
                                value={answersPart1[index].conforme}
                                onValueChange={(val) => handleAnswer1Change(index, 'conforme', val)}
                                trackColor={{ false: '#d3d3d3', true: '#2b303a' }}
                                thumbColor={answersPart1[index].conforme ? '#fff' : '#fff'}
                                ios_backgroundColor="#d3d3d3"
                                style={styles.switchControl}
                            />
                        </View>

                        <View style={styles.commentBox}>
                            <TextInput
                                style={styles.commentInput}
                                placeholder="Commentaires"
                                placeholderTextColor="#333"
                                value={answersPart1[index].commentaire}
                                onChangeText={(val) => handleAnswer1Change(index, 'commentaire', val)}
                            />
                            <MaterialCommunityIcons name="menu-down" size={24} color="#333" style={styles.commentIcon} />
                        </View>
                    </View>
                ))}
            </View>

            {/* Section Questions Partie 2 */}
            <View style={styles.questionsSection}>
                {QUESTIONS_PART_2.map((q, index) => {
                    const globalIndex = index + QUESTIONS_PART_1.length + 1;
                    return (
                        <View key={index} style={styles.questionContainer}>
                            <View style={styles.questionHeader}>
                                <Text style={styles.questionNumber}>{globalIndex}.</Text>
                                <Text style={styles.questionText}>{q}</Text>
                            </View>
                            
                            <View style={styles.commentBox}>
                                <TextInput
                                    style={styles.commentInput}
                                    placeholder="Actions"
                                    placeholderTextColor="#333"
                                    value={answersPart2[index].action}
                                    onChangeText={(val) => handleAnswer2Change(index, 'action', val)}
                                />
                                <MaterialCommunityIcons name="menu-down" size={24} color="#333" style={styles.commentIcon} />
                            </View>

                            <View style={[styles.commentBox, { marginTop: 12 }]}>
                                <TextInput
                                    style={styles.commentInput}
                                    placeholder="Responsables"
                                    placeholderTextColor="#333"
                                    value={answersPart2[index].responsable}
                                    onChangeText={(val) => handleAnswer2Change(index, 'responsable', val)}
                                />
                                <MaterialCommunityIcons name="menu-down" size={24} color="#333" style={styles.commentIcon} />
                            </View>

                            <View style={[styles.superficieBox, { marginHorizontal: 0, marginTop: 20 }]}>
                                <Text style={styles.superficieFloatLabel}>Délai</Text>
                                <View style={styles.superficieRow}>
                                    <TextInput
                                        style={styles.superficieInput}
                                        value={answersPart2[index].delai}
                                        onChangeText={(val) => handleAnswer2Change(index, 'delai', val)}
                                        keyboardType="numeric"
                                        placeholder="0"
                                        placeholderTextColor="#999"
                                    />
                                    <Text style={styles.superficieUnit}>Jours</Text>
                                </View>
                            </View>

                            <View style={[styles.switchContainer, { marginTop: 12 }]}>
                                <Text style={styles.switchLabel}>Besoin de suivi</Text>
                                <Switch
                                    value={answersPart2[index].besoinSuivi}
                                    onValueChange={(val) => handleAnswer2Change(index, 'besoinSuivi', val)}
                                    trackColor={{ false: '#d3d3d3', true: '#2b303a' }}
                                    thumbColor={answersPart2[index].besoinSuivi ? '#fff' : '#fff'}
                                    ios_backgroundColor="#d3d3d3"
                                    style={styles.switchControl}
                                />
                            </View>
                        </View>
                    );
                })}
            </View>

            {/* Zone de signature */}
            <View style={styles.signatureSection}>
                <View style={styles.signatureHeader}>
                    <Text style={styles.signatureTitle}>Signature du producteur</Text>
                    <TouchableOpacity onPress={clearSignature}>
                        <MaterialCommunityIcons name="refresh" size={26} color="#333" />
                    </TouchableOpacity>
                </View>
                <View style={styles.signatureContainer}>
                    <SignatureScreen
                        ref={signatureRef}
                        onOK={handleSignature}
                        onEmpty={handleEmptySignature}
                        descriptionText=""
                        clearText="Effacer"
                        confirmText="Sauvegarder"
                        webStyle={`
                            .m-signature-pad {box-shadow: none; border: none; margin: 0; padding: 0;}
                            .m-signature-pad--body {border: none;}
                            .m-signature-pad--footer {display: none; margin: 0;}
                            body,html {width: 100%; height: 100%;}
                        `}
                    />
                </View>
            </View>

            {/* Bouton Sauvegarder */}
            <TouchableOpacity
                style={[styles.submitBtn, submitting && styles.btnDisabled]}
                onPress={onSavePress}
                disabled={submitting}
            >
                {submitting
                    ? <ActivityIndicator color="#fff" />
                    : <Text style={styles.submitBtnText}>Sauvegarder</Text>
                }
            </TouchableOpacity>

        </ScrollView>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: '#fff' },
    scrollContent: {
        paddingBottom: 40,
        paddingTop: 10,
    },
    pickerBox: {
        borderWidth: 1,
        borderColor: '#333',
        borderRadius: 6,
        marginHorizontal: 16,
        marginBottom: 12,
        backgroundColor: '#fff',
        overflow: 'hidden',
    },
    picker: {
        height: 56,
        color: '#333',
    },
    superficieBox: {
        borderWidth: 1,
        borderColor: '#333',
        borderRadius: 6,
        marginHorizontal: 16,
        marginTop: 12,
        marginBottom: 12,
        paddingHorizontal: 14,
        paddingVertical: 12,
        backgroundColor: '#fff',
        position: 'relative',
    },
    superficieFloatLabel: {
        position: 'absolute',
        top: -10,
        left: 10,
        backgroundColor: '#fff',
        paddingHorizontal: 6,
        fontSize: 12,
        color: '#666',
    },
    superficieRow: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
    },
    superficieInput: {
        fontSize: 20,
        color: '#333',
        flex: 1,
        padding: 0,
    },
    superficieUnit: {
        fontSize: 18,
        color: '#333',
    },
    questionsSection: {
        marginTop: 10,
    },
    questionContainer: {
        marginHorizontal: 16,
        marginBottom: 30,
    },
    questionHeader: {
        flexDirection: 'column',
        marginBottom: 16,
    },
    questionNumber: {
        fontSize: 16,
        color: '#333',
        marginBottom: 4,
    },
    questionText: {
        fontSize: 16,
        color: '#333',
        lineHeight: 24,
    },
    switchContainer: {
        flexDirection: 'column',
        alignItems: 'flex-start',
        marginBottom: 16,
    },
    switchLabel: {
        fontSize: 16,
        color: '#333',
        marginBottom: 8,
    },
    switchControl: {
        transform: [{ scaleX: 1.1 }, { scaleY: 1.1 }],
    },
    commentBox: {
        flexDirection: 'row',
        alignItems: 'center',
        borderWidth: 1,
        borderColor: '#333',
        borderRadius: 6,
        backgroundColor: '#fff',
        paddingHorizontal: 12,
        height: 50,
    },
    commentInput: {
        flex: 1,
        fontSize: 16,
        color: '#333',
    },
    commentIcon: {
        marginLeft: 8,
    },
    signatureSection: {
        marginHorizontal: 16,
        marginTop: 10,
        marginBottom: 20,
    },
    signatureHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: 10,
    },
    signatureTitle: {
        fontSize: 18,
        color: '#333',
        fontWeight: '500',
    },
    signatureContainer: {
        height: 200,
        borderWidth: 1,
        borderColor: '#333',
        borderRadius: 8,
        overflow: 'hidden',
    },
    submitBtn: {
        backgroundColor: '#2b303a',
        marginHorizontal: 16,
        padding: 16,
        borderRadius: 30,
        alignItems: 'center',
        marginTop: 10,
    },
    submitBtnText: { color: '#fff', fontWeight: '600', fontSize: 16 },
    btnDisabled: { opacity: 0.6 },
});