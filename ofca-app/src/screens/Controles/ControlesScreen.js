import React, { useState, useCallback, useRef } from 'react';
import {
    View, Text, TextInput, TouchableOpacity, StyleSheet,
    ScrollView, Alert, ActivityIndicator, Switch, Modal
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
    "Pratique d'hygiène de la parcelle (présence de sachets plastiques ou autres emballages plastiques, de matières fécales d'origine animale ou humaine, autres matières polluantes)",
    "Origine du matériel de reproduction végétal (MRV)",
    "Concordance des données entre les déclarations du producteur et la fiche de suivi (Nombre/superficies des parcelles bio enregistrées, les autres cultures du producteurs et les pratiques culturales, les superficies de ou des cultures à certifier, etc.)",
    "Gestion récolte et post-récolte/mesures de séparation lors des opérations suivante: récoltes, battage, vannage, emballage, transport, stockage, vente, etc.)",
    "Connaissances et respect de la règlementation interne (participation aux réunions, règles de base en AB...)",
    "Conduite des cultures conventionnelles dans l'exploitation (Risque d'utilisation de pdt chimique de synthèse/AB, changement de parcelles conventionnelle, notification dans le changement de parcelle, ...)"
];

const QUESTIONS_PART_2 = [...QUESTIONS_PART_1];

const PRESET_ACTIONS_PART_2 = {
    0: ["Mise en conversion de la parcelle sur 3 ans"],
    1: ["Installer la zone tampon d’au moins 3m d’ici une semaine", "Elargir la zone tampon d’au moins 3m d’ici une semaine"],
    2: ["Déclassement de la parcelle"],
    3: [
        "Faire la rotation de cultures",
        "Faire de petits tas des résidus de défrichage avant de les bruler",
        "Mettre la parcelle en conversion sur 3 ans"
    ],
    4: ["Définir un plan de rotation et le mettre en pratique dès la campagne suivante"],
    5: ["Planter quelques arbres sur la parcelle avant la saison prochaine"],
    6: [
        "Déclasser et ne pas acheter le produit dans le circuit bio",
        "Assister à la récolte pour éviter le mélange de produit bio et conventionnel",
        "Enregistrer et contrôler toutes les parcelles du producteur avant la récolte"
    ],
    7: [
        "Débarrasser la parcelle des emballages sachets plastiques",
        "Interdire de déféquer sur la parcelle",
        "Mettre en conversion la parcelle sur 3 ans"
    ],
    8: [
        "Déclasser le produit et ne pas le commercialiser dans le circuit bio",
        "Mettre en conversion la parcelle sur 3 ans"
    ],
    9: ["Actualiser les données"],
    10: [
        "Recycler les producteurs sur les bonnes pratiques des opérations de récolte et post-récolte",
        "Identifier un magasin de l’OP et le rendre conforme aux règles de stockage des produits biologiques"
    ],
    11: [
        "Sensibiliser le producteur sur la nécessité de la mise en pratique des règles de production biologique",
        "Multiplier les réunions et formations",
        "Sensibiliser le producteur sur l’importance de participer aux réunions et formations"
    ],
    12: [
        "Mise en conversion de la parcelle",
        "Sensibiliser le producteur sur l’intérêt de la pratique bio dans sa vie quotidienne autre que la culture certifiée",
        "Séparer et isoler les parcelles dédiées au bio des parcelles conventionnelles"
    ]
};

const PRESET_RESPONSABLES_PART_2 = {
    0: ["RSCC"],
    1: ["Producteur", "A2C"],
    2: ["RSCC"],
    3: ["Producteur", "RSCC"],
    4: ["Producteur"],
    5: ["Producteur"],
    6: ["A2C", "3C", "RSCC"],
    7: ["Producteur", "A2C", "RSCC"],
    8: ["RSCC"],
    9: ["A2C", "3C", "RSCC"],
    10: ["Producteur", "A2C", "3C", "RSCC"],
    11: ["A2C", "3C", "RSCC"],
    12: ["Producteur", "A2C", "3C", "RSCC"]
};

const PRESET_COMMENTS_PART_1 = {
    0: [
        "Pas de trace d'utilisation d'herbicide ni d'engrais chimique sur la parcelle",
        "Présence des boites d'herbicide/sac d'engrais chimique sur la parcelle",
        "Traces d'utilisation d'herbicide sur la parcelle (brulure des herbes sur la parcelle)"
    ],
    1: [
        "Pas de zone tampon",
        "Largeur de la zone tampon insuffisante",
        "Présence de zone tampon de largeur d'au moins 3m",
        "Parcelle complètement isolée sans risque"
    ],
    2: [
        "Parcelle proche d'une habitation",
        "Parcelle proche d'une carrière/usine/route",
        "Parcelle isolée des zones de pollution"
    ],
    3: [
        "Pratique de la rotation de cultures",
        "Pratique de la monoculture",
        "Utilisation des résidus de défrichage et de récolte comme fertilisants",
        "Feu de brousse comme moyen de défrichage",
        "Labour perpendiculaire à la pente"
    ],
    4: [
        "Pratique de la rotation de culture par le producteur",
        "Pratique de la monoculture"
    ],
    6: [
        "Le producteur fait la même culture en conventionnelle sans mesure de séparation de la récolte",
        "Toutes les parcelles du producteur sont contrôlées par le système de production biologique",
        "Certaines parcelles du producteur sont en conventionnel"
    ],
    7: [
        "Présence d'emballages/sachets plastiques sur la parcelle",
        "Présence de déchets humains sur la parcelle/boite de conserve/piles et batteries usées/moustiquaires",
        "Parcelle bien entretenue dans les normes de production bio"
    ],
    8: [
        "Utilisation du matériel de reproduction végétal (MRV) provenant de SAM-TOGO",
        "Utilisation des reconduites de la campagne passée",
        "Origine inconnue de MRV",
        "Utilisation de MRV OGM"
    ],
    9: [
        "Les déclarations du producteur sont conformes aux informations recueillies",
        "Ecart dans les déclarations et des informations recueillies"
    ],
    10: [
        "Les opérations de récolte et post récolte s'effectuent conformément aux bonnes pratiques",
        "Les opérations de récolte et post récolte ne s'effectuent pas conformément aux bonnes pratiques",
        "Les quantités vendues ne sont pas conformes aux estimations de récolte",
        "Le magasin de stockage de l'OP n'est pas conforme"
    ],
    11: [
        "Le producteur maitrise et applique les règles de production biologique",
        "Le producteur participe régulièrement aux réunions et formation",
        "Le producteur applique partiellement les règles de production"
    ],
    12: [
        "Le producteur fait d'autres cultures conventionnelles sur les parcelles dédiées au bio",
        "Le producteur fait d'autres cultures conventionnelles sur les parcelles non dédiées au bio",
        "Le producteur maitrise les pratiques de la production biologique",
        "Les parcelles dédiées au bio sont séparées et isolées des parcelles conventionnelles dans son exploitation",
        "Les parcelles dédiées au bio ne sont ni séparées ni isolées des parcelles conventionnelles dans son exploitation"
    ]
};

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

    // Point 14: Autres cultures conventionnelles
    const [autresCultures, setAutresCultures] = useState([]);
    const [showAutreModal, setShowAutreModal] = useState(false);
    const [tempAutreCultureId, setTempAutreCultureId] = useState('');
    const [tempAutreSuperficie, setTempAutreSuperficie] = useState('');
    const [tempAutreComments, setTempAutreComments] = useState({
        phyto: false,
        chimique: false,
        herbicide: false,
        ogm: false,
        stockage: false
    });
    const [showAutreCommentsModal, setShowAutreCommentsModal] = useState(false);

    // Modal Comments
    const [modalVisible, setModalVisible] = useState(false);
    const [activeModalData, setActiveModalData] = useState(null);
    const [modalTempText, setModalTempText] = useState('');

    const openCommentModal = (part, index, field) => {
        let currentText = '';
        if (part === 1) {
            currentText = answersPart1[index][field];
        } else {
            currentText = answersPart2[index][field];
        }
        setActiveModalData({ part, index, field });
        setModalTempText(currentText || '');
        setModalVisible(true);
    };

    const saveModalData = () => {
        if (activeModalData) {
            const { part, index, field } = activeModalData;
            if (part === 1) {
                handleAnswer1Change(index, field, modalTempText);
            } else {
                handleAnswer2Change(index, field, modalTempText);
            }
        }
        setModalVisible(false);
    };

    const togglePreset = (preset) => {
        let text = modalTempText || '';
        if (text.includes(preset)) {
            text = text.replace(preset, '').trim();
            text = text.replace(/^\n+|\n+$/g, '').replace(/\n{2,}/g, '\n');
            setModalTempText(text);
        } else {
            setModalTempText(text ? text + '\n' + preset : preset);
        }
    };

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

    const handleAddAutreCulture = () => {
        if (!tempAutreCultureId || !tempAutreSuperficie) {
            Alert.alert('Erreur', 'Veuillez sélectionner une culture et saisir la superficie.');
            return;
        }

        const selectedComments = [];
        if (tempAutreComments.phyto) selectedComments.push('Traitement phytosanitaire de la culture');
        if (tempAutreComments.chimique) selectedComments.push('Fertilisation chimique de la culture');
        if (tempAutreComments.herbicide) selectedComments.push('Utilisation d’herbicide sur la parcelle');
        if (tempAutreComments.ogm) selectedComments.push('Utilisation des MRV OGM');
        if (tempAutreComments.stockage) selectedComments.push('Utilisation du même magasin de stockage');

        const cultureObj = cultures.find(c => c.id.toString() === tempAutreCultureId.toString());

        const newEntry = {
            id: Date.now().toString(),
            culture_id: tempAutreCultureId,
            culture_label: cultureObj ? cultureObj.nom : 'Culture inconnue',
            superficie: parseFloat(tempAutreSuperficie),
            commentaires: selectedComments
        };

        setAutresCultures(prev => [...prev, newEntry]);
        
        // Reset
        setTempAutreCultureId('');
        setTempAutreSuperficie('');
        setTempAutreComments({
            phyto: false, chimique: false, herbicide: false, ogm: false, stockage: false
        });
        setShowAutreModal(false);
    };

    const removeAutreCulture = (id) => {
        setAutresCultures(prev => prev.filter(a => a.id !== id));
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
                autres_cultures: autresCultures,
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
        <>
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

                        <TouchableOpacity 
                            style={styles.commentBox}
                            onPress={() => openCommentModal(1, index, 'commentaire')}
                        >
                            <Text style={[styles.commentInput, !answersPart1[index].commentaire && { color: '#333' }]} numberOfLines={1}>
                                {answersPart1[index].commentaire || "Commentaires"}
                            </Text>
                            <MaterialCommunityIcons name="menu-down" size={24} color="#333" style={styles.commentIcon} />
                        </TouchableOpacity>
                    </View>
                ))}

                {/* Point 14. Section Repeatable */}
                <View style={[styles.questionContainer, { marginTop: 20 }]}>
                    <View style={styles.sectionHeaderRow}>
                        <Text style={styles.questionNumber}>14.</Text>
                        <Text style={[styles.questionText, { flex: 1 }]}>Identifier les autres cultures conventionnelles et leurs superficies</Text>
                    </View>

                    <View style={styles.culturesHeaderRow}>
                        <Text style={styles.culturesTitle}>Cultures</Text>
                        <TouchableOpacity style={styles.addBtnCircle} onPress={() => setShowAutreModal(true)}>
                            <MaterialCommunityIcons name="plus" size={24} color="#333" />
                        </TouchableOpacity>
                    </View>

                    {autresCultures.map((item) => (
                        <View key={item.id} style={styles.autreCultureCard}>
                            <View style={styles.autreCultureMain}>
                                <Text style={styles.autreCultureName}>{item.culture_label}</Text>
                                <Text style={styles.autreCultureArea}>{item.superficie} ha</Text>
                                {item.commentaires.length > 0 && (
                                    <Text style={styles.autreCultureComments}>{item.commentaires.join(', ')}</Text>
                                )}
                            </View>
                            <TouchableOpacity onPress={() => removeAutreCulture(item.id)}>
                                <MaterialCommunityIcons name="close-circle" size={24} color="#e74c3c" />
                            </TouchableOpacity>
                        </View>
                    ))}
                </View>
            </View>

            {/* Section Questions Partie 2 */}
            <View style={styles.questionsSection}>
                <View style={styles.resumeHeader}>
                    <Text style={styles.resumeTitle1}>RESUME DES</Text>
                    <Text style={styles.resumeTitle2}>CONTROLS</Text>
                </View>
                {QUESTIONS_PART_2.map((q, index) => {
                    const globalIndex = index + QUESTIONS_PART_1.length + 1;
                    return (
                        <View key={index} style={styles.questionContainer}>
                            <View style={styles.questionHeader}>
                                <Text style={styles.questionNumber}>{index + 1}.</Text>
                                <Text style={styles.questionText}>{q}</Text>
                            </View>

                            <TouchableOpacity 
                                style={styles.commentBox}
                                onPress={() => openCommentModal(2, index, 'action')}
                            >
                                <Text style={[styles.commentInput, !answersPart2[index].action && { color: '#333' }]} numberOfLines={1}>
                                    {answersPart2[index].action || "Actions"}
                                </Text>
                                <MaterialCommunityIcons name="menu-down" size={24} color="#333" style={styles.commentIcon} />
                            </TouchableOpacity>

                            <TouchableOpacity 
                                style={[styles.commentBox, { marginTop: 12 }]}
                                onPress={() => openCommentModal(2, index, 'responsable')}
                            >
                                <Text style={[styles.commentInput, !answersPart2[index].responsable && { color: '#333' }]} numberOfLines={1}>
                                    {answersPart2[index].responsable || "Responsables"}
                                </Text>
                                <MaterialCommunityIcons name="menu-down" size={24} color="#333" style={styles.commentIcon} />
                            </TouchableOpacity>

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

            {/* Modal for comments/actions */}
            <Modal
                visible={modalVisible}
                animationType="slide"
                transparent={true}
                onRequestClose={() => setModalVisible(false)}
            >
                <View style={styles.modalOverlay}>
                    <View style={styles.modalContent}>
                        <View style={styles.modalInputContainer}>
                            <TextInput
                                style={styles.modalTextInput}
                                multiline
                                placeholder="Saisir ou sélectionner..."
                                value={modalTempText}
                                onChangeText={setModalTempText}
                            />
                        </View>
                        
                        <ScrollView style={styles.presetList} showsVerticalScrollIndicator={false}>
                            {activeModalData?.part === 1 && activeModalData?.field === 'commentaire' && PRESET_COMMENTS_PART_1[activeModalData.index]?.map((preset, idx) => (
                                <TouchableOpacity key={idx} style={styles.presetRow} onPress={() => togglePreset(preset)}>
                                    <Text style={styles.presetText}>{preset}</Text>
                                    <MaterialCommunityIcons 
                                        name={modalTempText?.includes(preset) ? "checkbox-marked" : "checkbox-blank-outline"} 
                                        size={24} 
                                        color="#333" 
                                    />
                                </TouchableOpacity>
                            ))}

                            {activeModalData?.part === 2 && activeModalData?.field === 'action' && PRESET_ACTIONS_PART_2[activeModalData.index]?.map((preset, idx) => (
                                <TouchableOpacity key={idx} style={styles.presetRow} onPress={() => togglePreset(preset)}>
                                    <Text style={styles.presetText}>{preset}</Text>
                                    <MaterialCommunityIcons 
                                        name={modalTempText?.includes(preset) ? "checkbox-marked" : "checkbox-blank-outline"} 
                                        size={24} 
                                        color="#333" 
                                    />
                                </TouchableOpacity>
                            ))}

                            {activeModalData?.part === 2 && activeModalData?.field === 'responsable' && PRESET_RESPONSABLES_PART_2[activeModalData.index]?.map((preset, idx) => (
                                <TouchableOpacity key={idx} style={styles.presetRow} onPress={() => togglePreset(preset)}>
                                    <Text style={styles.presetText}>{preset}</Text>
                                    <MaterialCommunityIcons 
                                        name={modalTempText?.includes(preset) ? "checkbox-marked" : "checkbox-blank-outline"} 
                                        size={24} 
                                        color="#333" 
                                    />
                                </TouchableOpacity>
                            ))}
                        </ScrollView>
                        
                        <View style={styles.modalFooter}>
                            <TouchableOpacity style={styles.modalOkBtn} onPress={saveModalData}>
                                <Text style={styles.modalOkText}>OK</Text>
                            </TouchableOpacity>
                        </View>
                    </View>
                </View>
            </Modal>

            {/* Modal Point 14 - Ajouter Culture */}
            <Modal visible={showAutreModal} animationType="slide" transparent>
                <View style={styles.modalOverlay}>
                    <View style={styles.modalContent}>
                        <View style={styles.modalHeader}>
                            <Text style={styles.modalTitle}>Ajouter une culture</Text>
                            <TouchableOpacity onPress={() => setShowAutreModal(false)}>
                                <MaterialCommunityIcons name="close" size={24} color="#333" />
                            </TouchableOpacity>
                        </View>

                        {renderPicker('Culture', tempAutreCultureId, setTempAutreCultureId, cultures)}

                        <View style={[styles.superficieBox, { marginHorizontal: 0 }]}>
                            <Text style={styles.superficieFloatLabel}>Superficie</Text>
                            <View style={styles.superficieRow}>
                                <TextInput
                                    style={styles.superficieInput}
                                    value={tempAutreSuperficie}
                                    onChangeText={setTempAutreSuperficie}
                                    keyboardType="numeric"
                                    placeholder="0.0"
                                    placeholderTextColor="#999"
                                />
                                <Text style={styles.superficieUnit}>ha</Text>
                            </View>
                        </View>

                        <TouchableOpacity 
                            style={styles.commentBox}
                            onPress={() => setShowAutreCommentsModal(true)}
                        >
                            <Text style={[styles.commentInput, !Object.values(tempAutreComments).some(v => v) && { color: '#999' }]} numberOfLines={1}>
                                {Object.values(tempAutreComments).some(v => v) 
                                    ? "Commentaires sélectionnés" 
                                    : "Commentaires"}
                            </Text>
                            <MaterialCommunityIcons name="menu-down" size={24} color="#333" style={styles.commentIcon} />
                        </TouchableOpacity>

                        <View style={[styles.modalFooter, { marginTop: 20 }]}>
                            <TouchableOpacity style={styles.modalOkBtn} onPress={handleAddAutreCulture}>
                                <Text style={styles.modalOkText}>Ajouter</Text>
                            </TouchableOpacity>
                        </View>
                    </View>
                </View>
            </Modal>

            {/* Sub-Modal Point 14 - Commentaires Checkboxes */}
            <Modal visible={showAutreCommentsModal} animationType="fade" transparent>
                <View style={styles.modalOverlay}>
                    <View style={styles.modalSmallContent}>
                        <View style={styles.modalInputContainer}>
                            <TextInput 
                                style={styles.modalTextInput} 
                                editable={false} 
                                placeholder="Saisir ou sélectionner..." 
                            />
                        </View>

                        <ScrollView style={styles.presetList}>
                            <TouchableOpacity style={styles.presetRow} onPress={() => setTempAutreComments(p => ({...p, phyto: !p.phyto}))}>
                                <Text style={styles.presetText}>Traitement phytosanitaire de la culture</Text>
                                <MaterialCommunityIcons name={tempAutreComments.phyto ? "checkbox-marked" : "checkbox-blank-outline"} size={24} color="#333" />
                            </TouchableOpacity>
                            <TouchableOpacity style={styles.presetRow} onPress={() => setTempAutreComments(p => ({...p, chimique: !p.chimique}))}>
                                <Text style={styles.presetText}>Fertilisation chimique de la culture</Text>
                                <MaterialCommunityIcons name={tempAutreComments.chimique ? "checkbox-marked" : "checkbox-blank-outline"} size={24} color="#333" />
                            </TouchableOpacity>
                            <TouchableOpacity style={styles.presetRow} onPress={() => setTempAutreComments(p => ({...p, herbicide: !p.herbicide}))}>
                                <Text style={styles.presetText}>Utilisation d’herbicide sur la parcelle</Text>
                                <MaterialCommunityIcons name={tempAutreComments.herbicide ? "checkbox-marked" : "checkbox-blank-outline"} size={24} color="#333" />
                            </TouchableOpacity>
                            <TouchableOpacity style={styles.presetRow} onPress={() => setTempAutreComments(p => ({...p, ogm: !p.ogm}))}>
                                <Text style={styles.presetText}>Utilisation des MRV OGM</Text>
                                <MaterialCommunityIcons name={tempAutreComments.ogm ? "checkbox-marked" : "checkbox-blank-outline"} size={24} color="#333" />
                            </TouchableOpacity>
                            <TouchableOpacity style={styles.presetRow} onPress={() => setTempAutreComments(p => ({...p, stockage: !p.stockage}))}>
                                <Text style={styles.presetText}>Utilisation du même magasin de stockage</Text>
                                <MaterialCommunityIcons name={tempAutreComments.stockage ? "checkbox-marked" : "checkbox-blank-outline"} size={24} color="#333" />
                            </TouchableOpacity>
                        </ScrollView>

                        <View style={styles.modalFooter}>
                            <TouchableOpacity style={styles.modalOkBtn} onPress={() => setShowAutreCommentsModal(false)}>
                                <Text style={styles.modalOkText}>OK</Text>
                            </TouchableOpacity>
                        </View>
                    </View>
                </View>
            </Modal>
        </>
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
    resumeHeader: {
        marginHorizontal: 16,
        marginTop: 20,
        marginBottom: 20,
    },
    resumeTitle1: {
        fontSize: 32,
        fontWeight: '900',
        color: '#2E7D32', // Green like in screenshot
        lineHeight: 36,
    },
    resumeTitle2: {
        fontSize: 32,
        fontWeight: '900',
        color: '#2E7D32',
        lineHeight: 36,
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
    modalOverlay: {
        flex: 1,
        backgroundColor: 'rgba(0,0,0,0.5)',
        justifyContent: 'flex-end',
    },
    modalContent: {
        backgroundColor: '#fff',
        borderTopLeftRadius: 20,
        borderTopRightRadius: 20,
        padding: 20,
        maxHeight: '80%',
    },
    modalInputContainer: {
        borderWidth: 1,
        borderColor: '#ccc',
        borderRadius: 8,
        padding: 10,
        marginBottom: 20,
        minHeight: 80,
    },
    modalTextInput: {
        fontSize: 16,
        color: '#333',
        textAlignVertical: 'top',
        minHeight: 60,
    },
    presetList: {
        marginBottom: 20,
    },
    presetRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        paddingVertical: 15,
    },
    presetText: {
        flex: 1,
        fontSize: 16,
        color: '#333',
        marginRight: 10,
        lineHeight: 24,
    },
    modalFooter: {
        alignItems: 'flex-end',
    },
    modalOkBtn: {
        backgroundColor: '#fdece8',
        paddingHorizontal: 30,
        paddingVertical: 12,
        borderRadius: 25,
    },
    modalOkText: {
        color: '#e74c3c',
        fontSize: 16,
        fontWeight: 'bold',
    },
    sectionHeaderRow: {
        flexDirection: 'row',
        alignItems: 'flex-start',
        marginBottom: 10,
    },
    culturesHeaderRow: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: 15,
    },
    culturesTitle: {
        fontSize: 18,
        color: '#333',
        fontWeight: '500',
    },
    addBtnCircle: {
        width: 32,
        height: 32,
        borderRadius: 8,
        borderWidth: 1,
        borderColor: '#333',
        alignItems: 'center',
        justifyContent: 'center',
    },
    autreCultureCard: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: '#fff',
        padding: 12,
        borderRadius: 8,
        borderWidth: 1,
        borderColor: '#eee',
        marginBottom: 8,
    },
    autreCultureMain: {
        flex: 1,
    },
    autreCultureName: {
        fontSize: 16,
        fontWeight: 'bold',
        color: '#333',
    },
    autreCultureArea: {
        fontSize: 14,
        color: '#666',
        marginTop: 2,
    },
    autreCultureComments: {
        fontSize: 12,
        color: '#999',
        marginTop: 4,
        fontStyle: 'italic',
    },
    modalHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: 20,
    },
    modalTitle: {
        fontSize: 18,
        fontWeight: 'bold',
        color: '#333',
    },
    modalSmallContent: {
        backgroundColor: '#fff',
        borderRadius: 20,
        padding: 20,
        marginHorizontal: 10,
        marginBottom: 20,
        maxHeight: '70%',
    },
});