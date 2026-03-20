import React, { useState, useCallback, useRef } from 'react';
import {
    View, Text, TextInput, TouchableOpacity, StyleSheet,
    ScrollView, Alert, ActivityIndicator, Switch, Modal, Image
} from 'react-native';
import { Picker } from '@react-native-picker/picker';
import { useFocusEffect } from '@react-navigation/native';
import * as ImagePicker from 'expo-image-picker';
import * as Location from 'expo-location';
import * as FileSystem from 'expo-file-system/legacy';
import { WebView } from 'react-native-webview';
import SignatureScreen from 'react-native-signature-canvas';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import apiClient from '../../api/client';
import { COLORS, RADIUS, SHADOWS, SPACING } from '../../theme';
import LoadingScreen from '../../components/LoadingScreen';

// ─────────────────────────────────────────────────────────────
// LEAFLET MAP HTML — CartoDB tiles (pas de blocage Access)
// ─────────────────────────────────────────────────────────────
function buildLeafletHTML(center, points, reviewMode = false) {
    const ptsJSON = JSON.stringify(points);
    const centerLat = center?.latitude ?? 6.1319;
    const centerLng = center?.longitude ?? 1.2228;

    return `<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
  <meta name="referrer" content="origin">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    html,body{width:100%;height:100%;overflow:hidden}
    #map{width:100%;height:100%}
    .delete-btn{background:#E53935;color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:14px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-weight:bold;line-height:1;}
  </style>
</head>
<body>
<div id="map"></div>
<script>
  var reviewMode=${reviewMode ? 'true' : 'false'};
  var map=L.map('map',{zoomControl:true,attributionControl:false}).setView([${centerLat},${centerLng}],17);

  // ✅ CartoDB — pas de blocage Access Blocked
  L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',{
    maxZoom:19,
    subdomains:'abcd'
  }).addTo(map);

  var pts=[],markers=[],polygon=null;

  function redraw(p){
    pts=p;
    markers.forEach(function(m){map.removeLayer(m);}); markers=[];
    if(polygon){map.removeLayer(polygon);polygon=null;}
    pts.forEach(function(pt,i){
      var color=i===0?'#4CAF50':'#2196F3';
      if(reviewMode){
        var icon=L.divIcon({
          className:'',
          html:'<div style="width:24px;height:24px;border-radius:50%;background:'+color+';border:3px solid #fff;display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:bold;box-shadow:0 2px 4px rgba(0,0,0,0.3);">'+(i+1)+'</div>',
          iconSize:[28,28],iconAnchor:[14,14]
        });
        var m=L.marker([pt.latitude,pt.longitude],{icon:icon,draggable:true});
        var idx=i;
        m.bindPopup(
          '<div style="text-align:center;min-width:120px;">'
          +'<b>Point '+(i+1)+'</b><br>'
          +'<small>'+pt.latitude.toFixed(6)+', '+pt.longitude.toFixed(6)+'</small><br><br>'
          +'<button class="delete-btn" onclick="deletePoint('+idx+')">✕ Supprimer</button>'
          +'</div>'
        );
        m.on('dragend',function(e){
          var ll=e.target.getLatLng();
          pts[idx]={latitude:ll.lat,longitude:ll.lng};
          window.ReactNativeWebView.postMessage(JSON.stringify({type:'pointMoved',index:idx,lat:ll.lat,lng:ll.lng}));
          redraw(pts);
        });
        m.addTo(map); markers.push(m);
      } else {
        var m=L.circleMarker([pt.latitude,pt.longitude],{
          radius:9,color:'#fff',weight:2,fillColor:color,fillOpacity:1
        }).bindTooltip(String(i+1),{permanent:true,direction:'top',offset:[0,-10]});
        m.addTo(map); markers.push(m);
      }
    });
    if(pts.length>=2){
      var ll=pts.map(function(pt){return[pt.latitude,pt.longitude];});
      polygon=L.polygon(ll,{
        color:reviewMode?'#FF6F00':'#1565C0',
        weight:2,
        fillColor:reviewMode?'#FF6F00':'#2196F3',
        fillOpacity:0.15
      }).addTo(map);
      if(reviewMode&&pts.length>=3){map.fitBounds(polygon.getBounds(),{padding:[40,40]});}
    }
  }

  function deletePoint(idx){
    map.closePopup();
    pts.splice(idx,1);
    window.ReactNativeWebView.postMessage(JSON.stringify({type:'deletePoint',index:idx}));
    redraw(pts);
  }

  redraw(${ptsJSON});

  map.on('click',function(e){
    if(!reviewMode){
      window.ReactNativeWebView.postMessage(JSON.stringify({type:'tap',lat:e.latlng.lat,lng:e.latlng.lng}));
    }
  });

  window.addEventListener('message',function(ev){
    try{
      var msg=JSON.parse(ev.data);
      if(msg.type==='update'){pts=msg.points;redraw(pts);}
      if(msg.type==='center'){map.setView([msg.lat,msg.lng],17);}
      if(msg.type==='fitBounds'&&pts.length>=2){
        var bounds=L.latLngBounds(pts.map(function(p){return[p.latitude,p.longitude];}));
        map.fitBounds(bounds,{padding:[40,40]});
      }
    }catch(e){}
  });
</script>
</body>
</html>`;
}

// ─────────────────────────────────────────────────────────────
// OFFLINE TILES
// ─────────────────────────────────────────────────────────────
const TILES_DIR = FileSystem.documentDirectory + 'osm_tiles/';
const latLonToTile = (lat, lon, z) => ({
    x: Math.floor(((lon + 180) / 360) * Math.pow(2, z)),
    y: Math.floor(((1 - Math.log(Math.tan(lat * Math.PI / 180) + 1 / Math.cos(lat * Math.PI / 180)) / Math.PI) / 2) * Math.pow(2, z)),
});
const downloadTilesForArea = async (coords, onProgress) => {
    if (!coords || coords.length < 3) return;
    const lats = coords.map(c => c.latitude), lons = coords.map(c => c.longitude);
    const bounds = { north: Math.max(...lats) + 0.01, south: Math.min(...lats) - 0.01, east: Math.max(...lons) + 0.01, west: Math.min(...lons) - 0.01 };
    let allTiles = [];
    for (let z = 14; z <= 17; z++) {
        const tl = latLonToTile(bounds.north, bounds.west, z), br = latLonToTile(bounds.south, bounds.east, z);
        for (let x = tl.x; x <= br.x; x++) for (let y = tl.y; y <= br.y; y++) allTiles.push({ x, y, z });
    }
    let done = 0;
    for (const t of allTiles) {
        const dir = `${TILES_DIR}${t.z}/${t.x}/`, file = `${dir}${t.y}.png`;
        if (!(await FileSystem.getInfoAsync(file)).exists) {
            await FileSystem.makeDirectoryAsync(dir, { intermediates: true }).catch(() => { });
            await FileSystem.downloadAsync(`https://tile.openstreetmap.org/${t.z}/${t.x}/${t.y}.png`, file, { headers: { 'User-Agent': 'ParcelleMobileApp/1.0' } }).catch(() => { });
        }
        onProgress && onProgress(Math.round((++done / allTiles.length) * 100));
    }
};
const tilesExist = async () => (await FileSystem.getInfoAsync(TILES_DIR)).exists;

// ─────────────────────────────────────────────────────────────
// STORAGE
// ─────────────────────────────────────────────────────────────
const PARCELLES_KEY = 'parcelles_saved';
const saveParcelle = async (data) => {
    try {
        const raw = await AsyncStorage.getItem(PARCELLES_KEY);
        const existing = raw ? JSON.parse(raw) : [];
        const entry = {
            id: Date.now().toString(),
            date: new Date().toLocaleDateString('fr-FR'),
            nom: data.nom_parcelle,
            superficie: data.superficie,
            coordonnees: data.coordonnees_polygon,
            payload: data,
        };
        await AsyncStorage.setItem(PARCELLES_KEY, JSON.stringify([...existing, entry]));
        return entry;
    } catch { return null; }
};

// ─────────────────────────────────────────────────────────────
// DateInput
// ─────────────────────────────────────────────────────────────
const DateInput = ({ label, value, onChange }) => (
    <View style={styles.dateRow}>
        <TextInput
            style={styles.dateInput}
            value={value}
            onChangeText={onChange}
            placeholder={label}
            placeholderTextColor={COLORS.textDisabled}
        />
        <MaterialCommunityIcons name="calendar-month-outline" size={22} color={COLORS.textDisabled} style={styles.dateIcon} />
    </View>
);

// ─────────────────────────────────────────────────────────────
// MAIN SCREEN
// ─────────────────────────────────────────────────────────────
export default function IdentificationsScreen({ navigation }) {

    const [villages, setVillages] = useState([]);
    const [organisations, setOrganisations] = useState([]);
    const [producteurs, setProducteurs] = useState([]);
    const [cultures, setCultures] = useState([]);
    const [loadingData, setLoadingData] = useState(true);

    const [selectedVillage, setSelectedVillage] = useState('');
    const [selectedOrganisation, setSelectedOrganisation] = useState('');
    const [selectedProducteur, setSelectedProducteur] = useState('');
    const [selectedCulture, setSelectedCulture] = useState('');
    const [statutProducteur, setStatutProducteur] = useState('');
    const [nomParcelle, setNomParcelle] = useState('');
    const [superficie, setSuperficie] = useState('');

    const [participationFormations, setParticipationFormations] = useState(false);
    const [productionParallele, setProductionParallele] = useState(false);
    const [diversiteBiologique, setDiversiteBiologique] = useState(false);
    const [gestionDechets, setGestionDechets] = useState(false);
    const [emballageNonConforme, setEmballageNonConforme] = useState(false);
    const [rotationCultures, setRotationCultures] = useState(false);
    const [isolementParcelles, setIsolementParcelles] = useState(false);
    const [preparationSol, setPreparationSol] = useState(false);
    const [fertilisation, setFertilisation] = useState(false);
    const [semences, setSemences] = useState(false);
    const [gestionAdventices, setGestionAdventices] = useState(false);
    const [gestionRavageurs, setGestionRavageurs] = useState(false);
    const [recolte, setRecolte] = useState(false);
    const [stockage, setStockage] = useState(false);

    const [commentaire, setCommentaire] = useState('');
    const [datePrepSol, setDatePrepSol] = useState('');
    const [dateSemis, setDateSemis] = useState('');
    const [dateSarclage1, setDateSarclage1] = useState('');
    const [dateSarclage2, setDateSarclage2] = useState('');
    const [dateFertilisation, setDateFertilisation] = useState('');
    const [dateRecolte, setDateRecolte] = useState('');

    const [arbres, setArbres] = useState([]);
    const [currentArbreNom, setCurrentArbreNom] = useState('');
    const [currentArbreNombre, setCurrentArbreNombre] = useState('');
    const [showArbreModal, setShowArbreModal] = useState(false);

    const [niveauPente, setNiveauPente] = useState('');
    const [typeCulture, setTypeCulture] = useState('');
    const [coursEau, setCoursEau] = useState(false);
    const [maisons, setMaisons] = useState(false);
    const [culturesProximite, setCulturesProximite] = useState('');
    const [rencontreAvec, setRencontreAvec] = useState('');
    const [submitting, setSubmitting] = useState(false);

    const [photoURI, setPhotoURI] = useState(null);
    const [signatureURI, setSignatureURI] = useState(null);
    const [signatureVisible, setSignatureVisible] = useState(false);
    const refSignature = useRef();

    const [mapVisible, setMapVisible] = useState(false);
    const [mapCenter, setMapCenter] = useState({ latitude: 6.1319, longitude: 1.2228 });
    const [polygonCoords, setPolygonCoords] = useState([]);
    const [coordonnees, setCoordonnees] = useState(null);
    const [reviewMode, setReviewMode] = useState(false);
    const webViewRef = useRef(null);

    const [offlineReady, setOfflineReady] = useState(false);
    const [downloadingTiles, setDownloadingTiles] = useState(false);
    const [downloadProgress, setDownloadProgress] = useState(0);
    
    // Etats pour l'Historique
    const [historique, setHistorique] = useState([]);
    const [showHistModal, setShowHistModal] = useState(false);
    const [histAnnee, setHistAnnee] = useState('');
    const [histCrop, setHistCrop] = useState('');
    const [histCropsLabel, setHistCropsLabel] = useState('');
    const [histChemicals, setHistChemicals] = useState({ herbicides: false, engrais: false });
    const [showChemModal, setShowChemModal] = useState(false);

    useFocusEffect(useCallback(() => {
        loadDependencies();
        tilesExist().then(setOfflineReady);
    }, []));

    const loadDependencies = async () => {
        setLoadingData(true);
        try {
            const [villRes, orgRes, prodRes, cultRes] = await Promise.all([
                apiClient.get('/villages'),
                apiClient.get('/organisations'),
                apiClient.get('/producteurs'),
                apiClient.get('/cultures'),
            ]);
            setVillages(villRes.data.data ?? villRes.data);
            setOrganisations(orgRes.data.data ?? orgRes.data);
            setProducteurs(prodRes.data.data ?? prodRes.data);
            setCultures(cultRes.data.data ?? cultRes.data);
        } catch {
            Alert.alert('Erreur', 'Impossible de charger les dépendances.');
        } finally {
            setLoadingData(false);
        }
    };

    const takePhoto = async () => {
        const { status } = await ImagePicker.requestCameraPermissionsAsync();
        if (status !== 'granted') { Alert.alert('Permission', 'La caméra est nécessaire.'); return; }
        const result = await ImagePicker.launchCameraAsync({
            mediaTypes: ImagePicker.MediaTypeOptions.Images,
            allowsEditing: true, aspect: [4, 3], quality: 0.7,
        });
        if (!result.canceled) setPhotoURI(result.assets[0].uri);
    };

    const handleSignature = (sig) => { setSignatureURI(sig); setSignatureVisible(false); };
    const clearSignature = () => { refSignature.current?.clearSignature(); setSignatureURI(null); };

    // ── Carte ─────────────────────────────────────────────────
    const openMap = async () => {
        const { status } = await Location.requestForegroundPermissionsAsync();
        if (status !== 'granted') { Alert.alert('Permission', 'Localisation requise.'); return; }
        try {
            const loc = await Location.getCurrentPositionAsync({ accuracy: Location.Accuracy.Balanced });
            setMapCenter({ latitude: loc.coords.latitude, longitude: loc.coords.longitude });
        } catch (e) { }
        setPolygonCoords([]);
        setReviewMode(false);
        setMapVisible(true);
    };

    const addPointToPolygon = (coord) => {
        setPolygonCoords(prev => {
            const newCoords = [...prev, coord];
            if (newCoords.length >= 3) setSuperficie(calculateAreaInHectares(newCoords));
            webViewRef.current?.injectJavaScript(`
                window.dispatchEvent(new MessageEvent('message',{
                    data: JSON.stringify({type:'update',points:${JSON.stringify(newCoords)}})
                })); true;
            `);
            return newCoords;
        });
    };

    const captureUserLocationPoint = async () => {
        try {
            const loc = await Location.getCurrentPositionAsync({ accuracy: Location.Accuracy.Highest });
            const coord = { latitude: loc.coords.latitude, longitude: loc.coords.longitude };
            addPointToPolygon(coord);
            webViewRef.current?.injectJavaScript(`
                window.dispatchEvent(new MessageEvent('message',{
                    data:'{"type":"center","lat":'+${loc.coords.latitude}+',"lng":'+${loc.coords.longitude}+'}'
                })); true;
            `);
        } catch { Alert.alert('Erreur GPS', 'Signal trop faible.'); }
    };

    const goToReviewMode = () => {
        if (polygonCoords.length < 3) { Alert.alert('Insuffisant', 'Ajoutez au moins 3 points.'); return; }
        setReviewMode(true);
        webViewRef.current?.injectJavaScript(`
            reviewMode=true; redraw(pts);
            window.dispatchEvent(new MessageEvent('message',{data:'{"type":"fitBounds"}'}));
            true;
        `);
    };

    const backToDrawMode = () => {
        setReviewMode(false);
        webViewRef.current?.injectJavaScript(`reviewMode=false; redraw(pts); true;`);
    };

    const handleDeletePoint = (index) => {
        setPolygonCoords(prev => {
            const newCoords = prev.filter((_, i) => i !== index);
            if (newCoords.length >= 3) setSuperficie(calculateAreaInHectares(newCoords));
            else setSuperficie('');
            return newCoords;
        });
    };

    const handlePointMoved = (index, lat, lng) => {
        setPolygonCoords(prev => {
            const newCoords = [...prev];
            newCoords[index] = { latitude: lat, longitude: lng };
            if (newCoords.length >= 3) setSuperficie(calculateAreaInHectares(newCoords));
            return newCoords;
        });
    };

    const validateFromReview = () => {
        if (polygonCoords.length < 3) { Alert.alert('Insuffisant', 'Il faut au moins 3 points.'); return; }
        setCoordonnees(polygonCoords);
        setMapVisible(false);
        setReviewMode(false);
        Alert.alert('📴 Cartes hors ligne', 'Télécharger les cartes de cette zone ?', [
            { text: 'Non merci' },
            { text: 'Télécharger', onPress: () => handleDownloadTiles(polygonCoords) },
        ]);
    };

    const calculateAreaInHectares = (coords) => {
        if (!coords || coords.length < 3) return '0.0000';
        let area = 0; const R = 6378137;
        for (let i = 0; i < coords.length; i++) {
            const j = (i + 1) % coords.length;
            area += ((coords[j].longitude - coords[i].longitude) * Math.PI / 180) *
                (2 + Math.sin(coords[i].latitude * Math.PI / 180) + Math.sin(coords[j].latitude * Math.PI / 180));
        }
        return (Math.abs(area * R * R / 2) / 10000).toFixed(4);
    };

    const handleDownloadTiles = async (coords) => {
        setDownloadingTiles(true); setDownloadProgress(0);
        try {
            await downloadTilesForArea(coords, setDownloadProgress);
            setOfflineReady(true);
            Alert.alert('✅ Terminé', 'Cartes disponibles hors ligne.');
        } catch { Alert.alert('Erreur', 'Téléchargement échoué.'); }
        finally { setDownloadingTiles(false); }
    };

    const handleAddArbre = () => {
        if (!currentArbreNom.trim() || !currentArbreNombre.trim()) return;
        setArbres(prev => [...prev, { nom: currentArbreNom, nombre: parseInt(currentArbreNombre, 10) }]);
        setCurrentArbreNom(''); setCurrentArbreNombre('');
        setShowArbreModal(false);
    };

    const handleAddHistorique = () => {
        if (!histAnnee.trim() || !histCrop) {
            Alert.alert('Erreur', 'Veuillez saisir l\'année et la culture.');
            return;
        }
        
        const chems = [];
        if (histChemicals.herbicides) chems.push('Herbicides');
        if (histChemicals.engrais) chems.push('Engrais chimique');

        const newEntry = {
            id: Date.now().toString(),
            annee: histAnnee,
            crop: histCrop,
            cropLabel: histCropsLabel,
            chemicals: chems
        };

        setHistorique(prev => [...prev, newEntry]);
        
        // Reset
        setHistAnnee('');
        setHistCrop('');
        setHistCropsLabel('');
        setHistChemicals({ herbicides: false, engrais: false });
        setShowHistModal(false);
    };

    const removeHistorique = (id) => {
        setHistorique(prev => prev.filter(h => h.id !== id));
    };

    // ── SUBMIT ────────────────────────────────────────────────
    const handleSubmit = async () => {
        if (!selectedProducteur) {
            Alert.alert('Champ obligatoire', 'Veuillez sélectionner un producteur.');
            return;
        }
        const nomFinal = nomParcelle.trim() ||
            `Parcelle_${new Date().toLocaleDateString('fr-FR').replace(/\//g, '-')}`;
        const superficieFinal = superficie ? parseFloat(superficie) : 0;

        setSubmitting(true);
        const payload = {
            village_id: selectedVillage || null,
            organisation_id: selectedOrganisation || null,
            producteur_id: selectedProducteur,
            culture_id: selectedCulture || null,
            statut_producteur: statutProducteur,
            nom_parcelle: nomFinal,
            superficie: superficieFinal,
            participation_formations: participationFormations,
            production_parallele: productionParallele,
            diversite_biologique: diversiteBiologique,
            gestion_dechets: gestionDechets,
            emballage_non_conforme: emballageNonConforme,
            rotation_cultures: rotationCultures,
            isolement_parcelles: isolementParcelles,
            preparation_sol: preparationSol,
            fertilisation,
            semences,
            gestion_adventices: gestionAdventices,
            gestion_ravageurs: gestionRavageurs,
            recolte,
            stockage,
            commentaire,
            date_preparation_sol: datePrepSol || null,
            date_semis: dateSemis || null,
            date_sarclage_1: dateSarclage1 || null,
            date_sarclage_2: dateSarclage2 || null,
            date_fertilisation: dateFertilisation || null,
            date_recolte: dateRecolte || null,
            arbres,
            niveau_pente: niveauPente,
            type_culture: typeCulture,
            a_cours_eau: coursEau,
            maisons_environnantes: maisons,
            cultures_proximite: culturesProximite,
            rencontre_avec: rencontreAvec,
            photo_parcelle: photoURI,
            signature_producteur: signatureURI,
            coordonnees_polygon: coordonnees,
            historique: historique,
        };

        try {
            await apiClient.post('/identifications', payload);
            Alert.alert('Succès ✅', 'Identification enregistrée !');
            navigation.goBack();
        } catch (apiError) {
            const saved = await saveParcelle(payload);
            if (saved) {
                Alert.alert('📴 Sauvegardé localement',
                    `"${nomFinal}" sera synchronisé dès que vous aurez internet.`,
                    [{ text: 'OK', onPress: () => navigation.goBack() }]);
            } else {
                Alert.alert('Erreur', apiError.response?.data?.message || 'Problème d\'enregistrement.');
            }
        } finally { setSubmitting(false); }
    };

    if (loadingData) return <LoadingScreen message="Initialisation du formulaire GPS..." />;

    const renderToggle = (label, value, onValueChange) => (
        <View style={styles.toggleRow}>
            <Text style={styles.toggleLabel}>{label}</Text>
            <Switch
                trackColor={{ false: COLORS.border, true: COLORS.identification }}
                thumbColor="#fff"
                onValueChange={onValueChange}
                value={value}
            />
        </View>
    );

    const renderPicker = (label, value, onChange, items, labelKey = 'label', valueKey = 'value') => (
        <View style={styles.pickerWrapper}>
            <Picker selectedValue={value} onValueChange={onChange} style={styles.picker}>
                <Picker.Item label={label} value="" />
                {items.map((item, i) => (
                    <Picker.Item key={i} label={item[labelKey] ?? item} value={(item[valueKey] ?? item).toString()} />
                ))}
            </Picker>
        </View>
    );

    return (
        <View style={styles.container}>
            <ScrollView style={styles.scrollArea} showsVerticalScrollIndicator={false}>

                {/* ── Infos générales ─────────────────────── */}
                <View style={styles.card}>
                    {renderPicker('Village', selectedVillage, setSelectedVillage, villages, 'nom', 'id')}
                    {renderPicker('Organisation paysanne', selectedOrganisation, setSelectedOrganisation, organisations, 'nom', 'id')}
                    {renderPicker('Producteur *', selectedProducteur, setSelectedProducteur,
                        producteurs.map(p => ({ label: `${p.nom} ${p.prenom}`, value: p.id })), 'label', 'value')}

                    <View style={styles.inputWrapper}>
                        <TextInput
                            style={styles.textInput}
                            value={nomParcelle}
                            onChangeText={setNomParcelle}
                            placeholder="Nom de la parcelle (optionnel)"
                            placeholderTextColor={COLORS.textDisabled}
                        />
                    </View>

                    {renderPicker('Culture à certifier', selectedCulture, setSelectedCulture, cultures, 'nom', 'id')}
                    {renderPicker('Statut', statutProducteur, setStatutProducteur,
                        [{ label: 'Nouveau', value: 'Nouveau' }, { label: 'Ancien', value: 'Ancien' }], 'label', 'value')}

                    <View style={styles.superficieBox}>
                        <View style={{ flex: 1 }}>
                            <Text style={styles.superficieLabel}>Superficie (auto via carte)</Text>
                            <Text style={styles.superficieValue}>{superficie || '0.0000'} ha</Text>
                        </View>
                        <TouchableOpacity style={styles.mapIconBtn} onPress={openMap}>
                            <MaterialCommunityIcons name="map-marker-plus" size={26} color="#fff" />
                        </TouchableOpacity>
                    </View>

                    {coordonnees && (
                        <View style={styles.coordsBadge}>
                            <MaterialCommunityIcons name="map-check" size={14} color="#2E7D32" />
                            <Text style={styles.coordsBadgeText}>{coordonnees.length} points GPS — {superficie} ha</Text>
                            <TouchableOpacity onPress={openMap} style={styles.editCoordsBtn}>
                                <MaterialCommunityIcons name="pencil" size={14} color={COLORS.identification} />
                                <Text style={styles.editCoordsText}>Modifier</Text>
                            </TouchableOpacity>
                        </View>
                    )}
                </View>

                {/* ── Historique ──────────────────────────── */}
                <View style={styles.sectionBlock}>
                    <View style={styles.sectionRow}>
                        <Text style={styles.sectionBigTitle}>Historique</Text>
                        <TouchableOpacity style={styles.addIconBtn} onPress={() => setShowHistModal(true)}>
                            <MaterialCommunityIcons name="plus-box-outline" size={32} color={COLORS.identification} />
                        </TouchableOpacity>
                    </View>
                    
                    {historique.length === 0 ? (
                        <Text style={styles.emptyText}>Aucun historique ajouté</Text>
                    ) : (
                        historique.map((h) => (
                            <View key={h.id} style={styles.histItemCard}>
                                <View style={styles.histItemInfo}>
                                    <Text style={styles.histItemTitle}>{h.annee} — {h.cropLabel || 'Culture'}</Text>
                                    <Text style={styles.histItemSub}>{h.chemicals.length > 0 ? h.chemicals.join(', ') : 'Aucun produit chimique'}</Text>
                                </View>
                                <TouchableOpacity onPress={() => removeHistorique(h.id)}>
                                    <MaterialCommunityIcons name="close-circle" size={24} color={COLORS.error} />
                                </TouchableOpacity>
                            </View>
                        ))
                    )}
                </View>

                {/* ── Agriculture Bio ─────────────────────── */}
                <View style={styles.sectionBlock}>
                    <Text style={styles.sectionBigTitle}>COMPRÉHENSION DE L'AGRICULTURE BIOLOGIQUE ET LA GESTION DE L'ENVIRONNEMENT</Text>
                </View>
                <View style={styles.togglesCard}>
                    {renderToggle('Participation aux formations', participationFormations, setParticipationFormations)}
                    {renderToggle('Production parallèle', productionParallele, setProductionParallele)}
                    {renderToggle('Diversité Biologique encouragée', diversiteBiologique, setDiversiteBiologique)}
                    {renderToggle('Gestion de déchets', gestionDechets, setGestionDechets)}
                    {renderToggle("Présence d'emballage de produits non conformes", emballageNonConforme, setEmballageNonConforme)}
                    {renderToggle('Rotation de cultures', rotationCultures, setRotationCultures)}
                    {renderToggle('Isolement des parcelles', isolementParcelles, setIsolementParcelles)}
                    {renderToggle('Préparation du sol', preparationSol, setPreparationSol)}
                    {renderToggle('Fertilisation', fertilisation, setFertilisation)}
                    {renderToggle('Semences', semences, setSemences)}
                    {renderToggle('Gestion des adventices', gestionAdventices, setGestionAdventices)}
                    {renderToggle('Gestion des ravageurs et phytomaladie', gestionRavageurs, setGestionRavageurs)}
                    {renderToggle('Récolte', recolte, setRecolte)}
                    {renderToggle('Stockage', stockage, setStockage)}
                </View>

                {/* ── Commentaire ─────────────────────────── */}
                <View style={styles.commentCard}>
                    <TextInput
                        style={styles.commentInput}
                        value={commentaire}
                        onChangeText={setCommentaire}
                        placeholder="Commentaire"
                        placeholderTextColor={COLORS.textDisabled}
                        multiline
                        numberOfLines={3}
                    />
                </View>

                {/* ── Calendrier Cultural ─────────────────── */}
                <View style={styles.sectionBlock}>
                    <Text style={styles.sectionBigTitle}>CALENDRIER DES OPÉRATIONS CULTURALES, CULTURES À CERTIFIER</Text>
                </View>
                <View style={styles.calendarCard}>
                    <DateInput label="Préparation du sol/labour" value={datePrepSol} onChange={setDatePrepSol} />
                    <DateInput label="Semis" value={dateSemis} onChange={setDateSemis} />
                    <DateInput label="1er Sarclage" value={dateSarclage1} onChange={setDateSarclage1} />
                    <DateInput label="2eme Sarclage" value={dateSarclage2} onChange={setDateSarclage2} />
                    <DateInput label="Apport fumier/Engrais organique" value={dateFertilisation} onChange={setDateFertilisation} />
                    <DateInput label="Récolte" value={dateRecolte} onChange={setDateRecolte} />
                </View>

                {/* ── Arbres ──────────────────────────────── */}
                <View style={styles.sectionBlock}>
                    <View style={styles.sectionRow}>
                        <Text style={styles.sectionBigTitle}>Arbres</Text>
                        <TouchableOpacity style={styles.addIconBtn} onPress={() => setShowArbreModal(true)}>
                            <MaterialCommunityIcons name="plus-box-outline" size={28} color={COLORS.textSecondary} />
                        </TouchableOpacity>
                    </View>
                    {arbres.length === 0
                        ? <Text style={styles.emptyText}>Aucun arbre ajouté</Text>
                        : arbres.map((a, i) => (
                            <View key={i} style={styles.arbreRow}>
                                <Text style={styles.arbreText}>{a.nom}</Text>
                                <Text style={styles.arbreCount}>{a.nombre}</Text>
                            </View>
                        ))}
                </View>

                {/* ── Isolement Parcelle ──────────────────── */}
                <View style={styles.card}>
                    {renderPicker('Niveau de pente', niveauPente, setNiveauPente, [
                        { label: 'Sans pente', value: 'WITHOUT' },
                        { label: 'Faible', value: 'LOW' },
                        { label: 'Modérée', value: 'MODERATE' },
                        { label: 'Forte', value: 'HIGH' },
                    ], 'label', 'value')}
                    {renderPicker('Type de culture', typeCulture, setTypeCulture, [
                        { label: 'Culture unique', value: 'SINGLE' },
                        { label: 'Culture mixte', value: 'MIXED' },
                        { label: 'Agroforesterie', value: 'AGROFORESTRY' },
                    ], 'label', 'value')}
                    <View style={styles.inlineToggle}>
                        <Switch trackColor={{ false: COLORS.border, true: COLORS.identification }} thumbColor="#fff" onValueChange={setCoursEau} value={coursEau} />
                        <Text style={styles.inlineToggleLabel}>A des cours d'eau à proximité ?</Text>
                    </View>
                    <View style={styles.inlineToggle}>
                        <Switch trackColor={{ false: COLORS.border, true: COLORS.identification }} thumbColor="#fff" onValueChange={setMaisons} value={maisons} />
                        <Text style={styles.inlineToggleLabel}>Ya-t'il des maisons environnantes ?</Text>
                    </View>
                    {renderPicker('Cultures à proximité', culturesProximite, setCulturesProximite, cultures, 'nom', 'id')}
                    {renderPicker('Rencontre avec', rencontreAvec, setRencontreAvec, [
                        { label: 'Producteur', value: 'PRODUCTEUR' },
                        { label: 'Responsable', value: 'RESPONSABLE' },
                        { label: 'Les deux', value: 'BOTH' },
                    ], 'label', 'value')}
                </View>

                {/* ── Photo ───────────────────────────────── */}
                <TouchableOpacity style={styles.photoBox} onPress={takePhoto}>
                    {photoURI
                        ? <Image source={{ uri: photoURI }} style={styles.fullImage} />
                        : <MaterialCommunityIcons name="image-outline" size={48} color={COLORS.textDisabled} />}
                </TouchableOpacity>

                {/* ── Signature ───────────────────────────── */}
                <View style={styles.signatureSection}>
                    <View style={styles.signatureHeader}>
                        <Text style={styles.signatureLabel}>Signature du producteur</Text>
                        {signatureURI && (
                            <TouchableOpacity onPress={clearSignature}>
                                <MaterialCommunityIcons name="delete-sweep-outline" size={24} color={COLORS.error} />
                            </TouchableOpacity>
                        )}
                    </View>
                    <TouchableOpacity style={styles.signatureBox} onPress={() => setSignatureVisible(true)}>
                        {signatureURI
                            ? <Image source={{ uri: signatureURI }} style={styles.fullImage} resizeMode="contain" />
                            : <View style={styles.signaturePlaceholder}>
                                <MaterialCommunityIcons name="draw" size={32} color={COLORS.textDisabled} />
                                <Text style={styles.signaturePlaceholderText}>Appuyer pour signer</Text>
                            </View>
                        }
                    </TouchableOpacity>
                </View>

                {/* ── Bouton Sauvegarder ──────────────────── */}
                <TouchableOpacity
                    style={[styles.submitBtn, submitting && styles.btnDisabled]}
                    onPress={handleSubmit}
                    disabled={submitting}
                >
                    {submitting
                        ? <ActivityIndicator color="#fff" />
                        : <Text style={styles.submitBtnText}>Sauvegarder</Text>}
                </TouchableOpacity>

                <View style={{ height: 60 }} />
            </ScrollView>

            {/* Offline pill */}
            <View style={[styles.offlinePill, { backgroundColor: offlineReady ? '#2E7D32' : '#F57F17' }]}>
                <MaterialCommunityIcons name={offlineReady ? 'wifi-off' : 'wifi'} size={14} color="#fff" />
            </View>

            {/* ══════════════════════════════════════════════
                Modal CARTE
            ══════════════════════════════════════════════ */}
            <Modal visible={mapVisible} animationType="fade" statusBarTranslucent>
                <View style={styles.modalFull}>

                    <View style={styles.mapHeader}>
                        <TouchableOpacity style={styles.mapHeaderBack} onPress={() => {
                            if (reviewMode) backToDrawMode();
                            else { setPolygonCoords([]); setMapVisible(false); }
                        }}>
                            <MaterialCommunityIcons name="arrow-left" size={24} color="#111" />
                        </TouchableOpacity>
                        <Text style={styles.mapHeaderTitle}>
                            {reviewMode ? '🔍 Vérification du tracé' : '📍 Tracer la parcelle'}
                        </Text>
                        {reviewMode && (
                            <View style={styles.mapHeaderBadge}>
                                <Text style={styles.mapHeaderBadgeText}>{polygonCoords.length} pts</Text>
                            </View>
                        )}
                    </View>

                    <WebView
                        ref={webViewRef}
                        source={{ html: buildLeafletHTML(mapCenter, polygonCoords, reviewMode) }}
                        style={styles.map}
                        onMessage={(event) => {
                            try {
                                const data = JSON.parse(event.nativeEvent.data);
                                if (data.type === 'tap') addPointToPolygon({ latitude: data.lat, longitude: data.lng });
                                if (data.type === 'deletePoint') handleDeletePoint(data.index);
                                if (data.type === 'pointMoved') handlePointMoved(data.index, data.lat, data.lng);
                            } catch (e) { }
                        }}
                        javaScriptEnabled domStorageEnabled originWhitelist={['*']} scrollEnabled={false}
                    />

                    <View style={styles.mapOverlayTop}>
                        <Text style={styles.mapAreaText}>
                            {polygonCoords.length >= 3 ? calculateAreaInHectares(polygonCoords) : '0.00'} ha
                        </Text>
                        <Text style={styles.mapPointsText}>{polygonCoords.length} point(s)</Text>
                    </View>

                    {!reviewMode && (
                        <>
                            <TouchableOpacity style={styles.btnCenterMap} onPress={captureUserLocationPoint}>
                                <View style={styles.centerOuter}><View style={styles.centerInner} /></View>
                            </TouchableOpacity>
                            <View style={styles.mapControls}>
                                <TouchableOpacity style={styles.mapActionBtn} onPress={captureUserLocationPoint}>
                                    <MaterialCommunityIcons name="plus-circle" size={32} color="#fff" />
                                    <Text style={styles.mapActionText}>Point GPS</Text>
                                </TouchableOpacity>
                                {polygonCoords.length >= 3 && (
                                    <TouchableOpacity style={[styles.mapActionBtn, { backgroundColor: '#FF6F00' }]} onPress={goToReviewMode}>
                                        <MaterialCommunityIcons name="eye-outline" size={32} color="#fff" />
                                        <Text style={styles.mapActionText}>Réviser</Text>
                                    </TouchableOpacity>
                                )}
                                <TouchableOpacity
                                    style={[styles.mapActionBtn, { backgroundColor: COLORS.error }]}
                                    onPress={() => { setPolygonCoords([]); setMapVisible(false); }}>
                                    <MaterialCommunityIcons name="close-circle" size={32} color="#fff" />
                                    <Text style={styles.mapActionText}>Annuler</Text>
                                </TouchableOpacity>
                            </View>
                        </>
                    )}

                    {reviewMode && (
                        <View style={styles.reviewPanel}>
                            <View style={styles.reviewInfo}>
                                <MaterialCommunityIcons name="information-outline" size={16} color="#666" />
                                <Text style={styles.reviewInfoText}>Appuyez sur un point pour le supprimer ou le déplacer</Text>
                            </View>
                            <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.pointsList}>
                                {polygonCoords.map((pt, i) => (
                                    <TouchableOpacity
                                        key={i}
                                        style={[styles.pointChip, i === 0 && styles.pointChipFirst]}
                                        onPress={() => Alert.alert(
                                            `Point ${i + 1}`,
                                            `Lat: ${pt.latitude.toFixed(6)}\nLng: ${pt.longitude.toFixed(6)}`,
                                            [
                                                { text: 'Fermer' },
                                                {
                                                    text: '🗑 Supprimer', style: 'destructive',
                                                    onPress: () => {
                                                        handleDeletePoint(i);
                                                        webViewRef.current?.injectJavaScript(`pts.splice(${i},1);redraw(pts);true;`);
                                                    }
                                                },
                                            ]
                                        )}
                                    >
                                        <Text style={styles.pointChipText}>{i + 1}</Text>
                                    </TouchableOpacity>
                                ))}
                            </ScrollView>
                            <View style={styles.reviewBtns}>
                                <TouchableOpacity style={styles.reviewBtnModifier} onPress={backToDrawMode}>
                                    <MaterialCommunityIcons name="pencil" size={20} color="#111" />
                                    <Text style={styles.reviewBtnModifierText}>Modifier</Text>
                                </TouchableOpacity>
                                <TouchableOpacity style={styles.reviewBtnValider} onPress={validateFromReview}>
                                    <MaterialCommunityIcons name="check-circle" size={20} color="#fff" />
                                    <Text style={styles.reviewBtnValiderText}>Valider ce tracé</Text>
                                </TouchableOpacity>
                            </View>
                        </View>
                    )}
                </View>
            </Modal>

            {/* ══════════════════════════════════════════════
                Modal HISTORIQUE
            ══════════════════════════════════════════════ */}
            <Modal visible={showHistModal} animationType="slide" transparent>
                <View style={styles.modalOverlay}>
                    <View style={styles.modalContent}>
                        <View style={styles.modalHeader}>
                            <Text style={styles.modalTitle}>Ajouter un Historique</Text>
                            <TouchableOpacity onPress={() => setShowHistModal(false)}>
                                <MaterialCommunityIcons name="close" size={24} color="#666" />
                            </TouchableOpacity>
                        </View>

                        <View style={styles.inputWrapper}>
                            <TextInput
                                style={styles.textInput}
                                value={histAnnee}
                                onChangeText={setHistAnnee}
                                placeholder="Année"
                                placeholderTextColor={COLORS.textDisabled}
                                keyboardType="numeric"
                            />
                        </View>

                        <View style={styles.pickerWrapper}>
                            <Picker 
                                selectedValue={histCrop} 
                                onValueChange={(val, idx) => {
                                    setHistCrop(val);
                                    if(idx > 0) setHistCropsLabel(cultures[idx-1].nom);
                                }} 
                                style={styles.picker}
                            >
                                <Picker.Item label="Crops" value="" color={COLORS.textDisabled} />
                                {cultures.map((c) => (
                                    <Picker.Item key={c.id} label={c.nom} value={c.id} />
                                ))}
                            </Picker>
                        </View>

                        <TouchableOpacity 
                            style={styles.customSelect} 
                            onPress={() => setShowChemModal(true)}
                        >
                            <Text style={[styles.selectText, (histChemicals.herbicides || histChemicals.engrais) ? {} : {color: COLORS.textDisabled}]}>
                                {(histChemicals.herbicides || histChemicals.engrais) 
                                    ? `${histChemicals.herbicides ? 'Herbicides' : ''}${histChemicals.herbicides && histChemicals.engrais ? ', ' : ''}${histChemicals.engrais ? 'Engrais chimique' : ''}`
                                    : 'Chemicals'}
                            </Text>
                            <MaterialCommunityIcons name="chevron-down" size={24} color="#666" />
                        </TouchableOpacity>

                        <TouchableOpacity style={styles.modalSubmitBtn} onPress={handleAddHistorique}>
                            <Text style={styles.modalSubmitText}>Ajouter</Text>
                        </TouchableOpacity>
                    </View>
                </View>
            </Modal>

            {/* Sub-Modal CHEMICALS */}
            <Modal visible={showChemModal} animationType="fade" transparent>
                <View style={styles.modalOverlay}>
                    <View style={styles.modalSmallContent}>
                        <Text style={styles.modalSubTitle}>Sélectionnner Chemicals</Text>
                        
                        <TouchableOpacity 
                            style={styles.checkRow} 
                            onPress={() => setHistChemicals(prev => ({...prev, herbicides: !prev.herbicides}))}
                        >
                            <Text style={styles.checkLabel}>Herbicides</Text>
                            <MaterialCommunityIcons 
                                name={histChemicals.herbicides ? "checkbox-marked" : "checkbox-blank-outline"} 
                                size={24} 
                                color={histChemicals.herbicides ? COLORS.identification : "#666"} 
                            />
                        </TouchableOpacity>

                        <TouchableOpacity 
                            style={styles.checkRow} 
                            onPress={() => setHistChemicals(prev => ({...prev, engrais: !prev.engrais}))}
                        >
                            <Text style={styles.checkLabel}>Engrais chimique</Text>
                            <MaterialCommunityIcons 
                                name={histChemicals.engrais ? "checkbox-marked" : "checkbox-blank-outline"} 
                                size={24} 
                                color={histChemicals.engrais ? COLORS.identification : "#666"} 
                            />
                        </TouchableOpacity>

                        <TouchableOpacity style={styles.okBtn} onPress={() => setShowChemModal(false)}>
                            <Text style={styles.okBtnText}>OK</Text>
                        </TouchableOpacity>
                    </View>
                </View>
            </Modal>

            {/* ══════════════════════════════════════════════
                Modal SIGNATURE
            ══════════════════════════════════════════════ */}
            <Modal visible={signatureVisible} animationType="slide">
                <View style={styles.sigContainer}>
                    <View style={styles.sigHeader}>
                        <Text style={styles.sigTitle}>Signature du producteur</Text>
                        <Text style={styles.sigSubtitle}>Signez dans le cadre blanc ci-dessous</Text>
                    </View>
                    <View style={styles.sigCanvas}>
                        <SignatureScreen
                            ref={refSignature}
                            onOK={handleSignature}
                            onEmpty={() => Alert.alert('Attention', 'Veuillez signer avant de valider.')}
                            descriptionText=""
                            webStyle={`
                                .m-signature-pad { border:none; box-shadow:none; height:100%; }
                                .m-signature-pad--footer { display:none; }
                                body,html { height:100%; overflow:hidden; background:#fff; }
                            `}
                        />
                    </View>
                    <View style={styles.sigBtns}>
                        <TouchableOpacity style={styles.sigBtnEffacer} onPress={() => refSignature.current?.clearSignature()}>
                            <MaterialCommunityIcons name="eraser" size={22} color="#fff" />
                            <Text style={styles.sigBtnText}>Effacer</Text>
                        </TouchableOpacity>
                        <TouchableOpacity style={styles.sigBtnValider} onPress={() => refSignature.current?.readSignature()}>
                            <MaterialCommunityIcons name="check-circle" size={22} color="#fff" />
                            <Text style={styles.sigBtnText}>Valider</Text>
                        </TouchableOpacity>
                    </View>
                    <TouchableOpacity style={styles.sigBtnAnnuler} onPress={() => setSignatureVisible(false)}>
                        <Text style={styles.sigBtnAnnulerText}>Annuler et fermer</Text>
                    </TouchableOpacity>
                </View>
            </Modal>

            {/* ══════════════════════════════════════════════
                Modal ARBRE
            ══════════════════════════════════════════════ */}
            <Modal visible={showArbreModal} transparent animationType="slide">
                <View style={styles.arbreModalOverlay}>
                    <View style={styles.arbreModalBox}>
                        <Text style={styles.arbreModalTitle}>Ajouter un arbre</Text>
                        <TextInput style={styles.arbreInput} value={currentArbreNom} onChangeText={setCurrentArbreNom}
                            placeholder="Nom de l'arbre" placeholderTextColor={COLORS.textDisabled} />
                        <TextInput style={styles.arbreInput} value={currentArbreNombre} onChangeText={setCurrentArbreNombre}
                            placeholder="Nombre" keyboardType="numeric" placeholderTextColor={COLORS.textDisabled} />
                        <View style={styles.arbreModalBtns}>
                            <TouchableOpacity style={styles.arbreBtnCancel} onPress={() => setShowArbreModal(false)}>
                                <Text style={styles.arbreBtnText}>Annuler</Text>
                            </TouchableOpacity>
                            <TouchableOpacity style={styles.arbreBtnAdd} onPress={handleAddArbre}>
                                <Text style={[styles.arbreBtnText, { color: '#fff' }]}>Ajouter</Text>
                            </TouchableOpacity>
                        </View>
                    </View>
                </View>
            </Modal>

            {/* Overlay téléchargement */}
            {downloadingTiles && (
                <View style={styles.downloadOverlay}>
                    <ActivityIndicator size="large" color="#fff" />
                    <Text style={styles.downloadText}>Téléchargement cartes offline...</Text>
                    <View style={styles.progressBar}>
                        <View style={[styles.progressFill, { width: `${downloadProgress}%` }]} />
                    </View>
                    <Text style={styles.downloadPct}>{downloadProgress}%</Text>
                </View>
            )}
        </View>
    );
}

// ─────────────────────────────────────────────────────────────
// STYLES
// ─────────────────────────────────────────────────────────────
const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: '#F5F5F5' },
    scrollArea: { flex: 1 },

    card: { backgroundColor: '#fff', marginBottom: 8, paddingVertical: 4 },
    togglesCard: { backgroundColor: '#fff', marginBottom: 8 },
    calendarCard: { backgroundColor: '#fff', marginBottom: 8, paddingHorizontal: 16, paddingVertical: 8 },
    commentCard: { backgroundColor: '#fff', marginBottom: 8, paddingHorizontal: 16, paddingVertical: 8 },

    inputWrapper: { paddingHorizontal: 16, paddingVertical: 12, borderBottomWidth: 1, borderBottomColor: '#E0E0E0' },
    textInput: { height: 44, fontSize: 16, color: '#111' },

    pickerWrapper: { borderBottomWidth: 1, borderBottomColor: '#E0E0E0', backgroundColor: '#fff', marginBottom: 2 },
    picker: { height: 56, color: COLORS.textPrimary },

    superficieBox: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: 16, paddingVertical: 14, borderBottomWidth: 1, borderBottomColor: '#E0E0E0' },
    superficieLabel: { fontSize: 12, color: COLORS.textDisabled, marginBottom: 2 },
    superficieValue: { fontSize: 22, fontWeight: '900', color: '#111' },
    mapIconBtn: { width: 52, height: 52, borderRadius: 14, backgroundColor: '#1A1A2E', alignItems: 'center', justifyContent: 'center' },

    coordsBadge: { flexDirection: 'row', alignItems: 'center', gap: 6, marginHorizontal: 16, marginBottom: 10, padding: 8, backgroundColor: '#E8F5E9', borderRadius: 8 },
    coordsBadgeText: { fontSize: 12, fontWeight: '700', color: '#2E7D32', flex: 1 },
    editCoordsBtn: { flexDirection: 'row', alignItems: 'center', gap: 3 },
    editCoordsText: { fontSize: 11, color: COLORS.identification, fontWeight: '700' },

    sectionBlock: { paddingHorizontal: 16, paddingTop: 20, paddingBottom: 8, backgroundColor: '#F5F5F5' },
    sectionRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
    sectionBigTitle: { fontSize: 14, fontWeight: '800', color: '#111', flex: 1 },
    addIconBtn: { padding: 4 },
    emptyText: { textAlign: 'center', color: COLORS.textDisabled, fontSize: 13, marginTop: 8 },

    toggleRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingHorizontal: 16, paddingVertical: 14, borderBottomWidth: 1, borderBottomColor: '#F0F0F0' },
    toggleLabel: { fontSize: 15, color: '#111', flex: 1, paddingRight: 12 },
    inlineToggle: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: 16, paddingVertical: 12, gap: 12, borderBottomWidth: 1, borderBottomColor: '#E0E0E0' },
    inlineToggleLabel: { fontSize: 15, color: '#111', flex: 1 },

    commentInput: { fontSize: 14, color: '#111', minHeight: 80, textAlignVertical: 'top', paddingTop: 8 },

    dateRow: { flexDirection: 'row', alignItems: 'center', borderWidth: 1, borderColor: '#E0E0E0', borderRadius: 8, paddingHorizontal: 12, marginBottom: 10, backgroundColor: '#fff' },
    dateInput: { flex: 1, height: 50, fontSize: 14, color: '#111' },
    dateIcon: { marginLeft: 8 },

    arbreRow: { flexDirection: 'row', justifyContent: 'space-between', paddingHorizontal: 16, paddingVertical: 10, borderBottomWidth: 1, borderBottomColor: '#F0F0F0', backgroundColor: '#fff' },
    arbreText: { fontSize: 14, color: '#111' },
    arbreCount: { fontSize: 14, fontWeight: 'bold', color: COLORS.identification },

    photoBox: { height: 120, backgroundColor: '#fff', marginBottom: 2, alignItems: 'center', justifyContent: 'center', overflow: 'hidden' },
    fullImage: { width: '100%', height: '100%' },

    signatureSection: { backgroundColor: '#fff', paddingHorizontal: 16, paddingTop: 12, paddingBottom: 12, marginBottom: 8 },
    signatureHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 8 },
    signatureLabel: { fontSize: 15, color: '#111', fontWeight: '600' },
    signatureBox: { height: 160, borderWidth: 1, borderColor: '#E0E0E0', borderRadius: 8, backgroundColor: '#FAFAFA', overflow: 'hidden', justifyContent: 'center', alignItems: 'center' },
    signaturePlaceholder: { alignItems: 'center', gap: 8 },
    signaturePlaceholderText: { fontSize: 13, color: COLORS.textDisabled },

    submitBtn: { backgroundColor: '#1A1A2E', margin: 16, padding: 18, borderRadius: 40, alignItems: 'center' },
    submitBtnText: { color: '#fff', fontWeight: '900', fontSize: 16 },
    btnDisabled: { opacity: 0.6 },

    offlinePill: { position: 'absolute', bottom: 100, right: 16, width: 36, height: 36, borderRadius: 18, alignItems: 'center', justifyContent: 'center', elevation: 6 },

    // Map
    modalFull: { flex: 1 },
    map: { flex: 1 },
    mapHeader: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: 16, paddingTop: 50, paddingBottom: 12, backgroundColor: '#fff', elevation: 3 },
    mapHeaderBack: { padding: 4, marginRight: 10 },
    mapHeaderTitle: { flex: 1, fontSize: 16, fontWeight: '800', color: '#111' },
    mapHeaderBadge: { backgroundColor: '#FF6F00', paddingHorizontal: 10, paddingVertical: 4, borderRadius: 20 },
    mapHeaderBadgeText: { color: '#fff', fontSize: 12, fontWeight: '800' },
    mapOverlayTop: { position: 'absolute', top: 110, left: 20, right: 20, backgroundColor: 'rgba(255,255,255,0.93)', padding: 10, borderRadius: 12, alignItems: 'center' },
    mapAreaText: { fontSize: 20, fontWeight: '900', color: COLORS.identification },
    mapPointsText: { fontSize: 12, color: COLORS.textDisabled, marginTop: 2 },
    btnCenterMap: { position: 'absolute', right: 16, top: 180, width: 44, height: 44, borderRadius: 22, backgroundColor: 'rgba(255,255,255,0.95)', alignItems: 'center', justifyContent: 'center', elevation: 5 },
    centerOuter: { width: 32, height: 32, borderRadius: 16, borderWidth: 3, borderColor: '#333', alignItems: 'center', justifyContent: 'center' },
    centerInner: { width: 9, height: 9, borderRadius: 5, backgroundColor: '#333' },
    mapControls: { position: 'absolute', bottom: 40, left: 20, right: 20, flexDirection: 'row', justifyContent: 'center', gap: 12 },
    mapActionBtn: { backgroundColor: COLORS.identification, padding: 14, borderRadius: 20, alignItems: 'center', minWidth: 85 },
    mapActionText: { color: '#fff', fontWeight: '800', fontSize: 11, marginTop: 4 },

    reviewPanel: { position: 'absolute', bottom: 0, left: 0, right: 0, backgroundColor: '#fff', borderTopLeftRadius: 20, borderTopRightRadius: 20, padding: 16, elevation: 10 },
    reviewInfo: { flexDirection: 'row', alignItems: 'center', gap: 6, marginBottom: 12 },
    reviewInfoText: { fontSize: 12, color: '#666', flex: 1 },
    pointsList: { marginBottom: 14 },
    pointChip: { width: 36, height: 36, borderRadius: 18, backgroundColor: '#2196F3', alignItems: 'center', justifyContent: 'center', marginRight: 8, elevation: 2 },
    pointChipFirst: { backgroundColor: '#4CAF50' },
    pointChipText: { color: '#fff', fontWeight: '900', fontSize: 13 },
    reviewBtns: { flexDirection: 'row', gap: 12 },
    reviewBtnModifier: { flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', backgroundColor: '#F0F0F0', borderRadius: 14, padding: 14, gap: 6 },
    reviewBtnModifierText: { fontWeight: '800', color: '#111', fontSize: 15 },
    reviewBtnValider: { flex: 2, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', backgroundColor: '#1565C0', borderRadius: 14, padding: 14, gap: 6 },
    reviewBtnValiderText: { fontWeight: '800', color: '#fff', fontSize: 15 },

    // Signature Modal
    sigContainer: { flex: 1, backgroundColor: '#F5F5F5' },
    sigHeader: { paddingTop: 55, paddingBottom: 16, paddingHorizontal: 20, backgroundColor: '#fff', borderBottomWidth: 1, borderBottomColor: '#E0E0E0', alignItems: 'center' },
    sigTitle: { fontSize: 18, fontWeight: '800', color: '#111' },
    sigSubtitle: { fontSize: 13, color: '#666', marginTop: 4 },
    sigCanvas: { height: 320, backgroundColor: '#fff', margin: 16, borderRadius: 12, overflow: 'hidden', elevation: 3, borderWidth: 1, borderColor: '#E0E0E0' },
    sigBtns: { flexDirection: 'row', paddingHorizontal: 16, gap: 12, marginBottom: 12 },
    sigBtnEffacer: { flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', backgroundColor: '#757575', padding: 16, borderRadius: 12, gap: 8 },
    sigBtnValider: { flex: 2, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', backgroundColor: '#2E7D32', padding: 16, borderRadius: 12, gap: 8 },
    sigBtnText: { color: '#fff', fontWeight: '800', fontSize: 16 },
    sigBtnAnnuler: { padding: 14, alignItems: 'center' },
    sigBtnAnnulerText: { color: '#E53935', fontWeight: '700', fontSize: 15 },

    // Arbre Modal
    arbreModalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.5)', justifyContent: 'flex-end' },
    arbreModalBox: { backgroundColor: '#fff', borderTopLeftRadius: 20, borderTopRightRadius: 20, padding: 24 },
    arbreModalTitle: { fontSize: 18, fontWeight: '800', marginBottom: 16 },
    arbreInput: { borderWidth: 1, borderColor: '#E0E0E0', borderRadius: 10, padding: 12, fontSize: 14, marginBottom: 12, color: '#111' },
    arbreModalBtns: { flexDirection: 'row', gap: 12, marginTop: 8 },
    arbreBtnCancel: { flex: 1, backgroundColor: '#F0F0F0', borderRadius: 10, padding: 14, alignItems: 'center' },
    arbreBtnAdd: { flex: 1, backgroundColor: COLORS.identification, borderRadius: 10, padding: 14, alignItems: 'center' },
    arbreBtnText: { fontWeight: 'bold', fontSize: 15 },

    // Download
    downloadOverlay: { position: 'absolute', top: 0, left: 0, right: 0, bottom: 0, backgroundColor: 'rgba(0,0,0,0.75)', justifyContent: 'center', alignItems: 'center', gap: 16 },
    downloadText: { color: '#fff', fontWeight: '800', fontSize: 16 },
    progressBar: { width: '70%', height: 10, backgroundColor: 'rgba(255,255,255,0.3)', borderRadius: 5, overflow: 'hidden' },
    progressFill: { height: '100%', backgroundColor: '#fff', borderRadius: 5 },
    downloadPct: { color: '#fff', fontWeight: '900', fontSize: 20 },

    // Historique Items
    histItemCard: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: '#fff',
        marginHorizontal: 16,
        padding: 16,
        borderRadius: 12,
        marginBottom: 8,
        borderWidth: 1,
        borderColor: '#E0E0E0',
        ...SHADOWS.small
    },
    histItemInfo: { flex: 1 },
    histItemTitle: { fontSize: 16, fontWeight: '700', color: COLORS.textPrimary },
    histItemSub: { fontSize: 13, color: COLORS.textSecondary, marginTop: 4 },

    // Modal Styles
    modalOverlay: {
        flex: 1,
        backgroundColor: 'rgba(0,0,0,0.5)',
        justifyContent: 'center',
        padding: 20
    },
    modalContent: {
        backgroundColor: '#fff',
        borderRadius: 24,
        padding: 24,
        ...SHADOWS.medium
    },
    modalSmallContent: {
        backgroundColor: '#fff',
        borderRadius: 24,
        padding: 24,
        marginHorizontal: 10,
        ...SHADOWS.medium
    },
    modalHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: 24
    },
    modalTitle: { fontSize: 20, fontWeight: '800', color: COLORS.textPrimary },
    modalSubTitle: { fontSize: 18, fontWeight: '700', color: COLORS.textPrimary, marginBottom: 16 },
    
    customSelect: {
        flexDirection: 'row',
        alignItems: 'center',
        borderWidth: 1,
        borderColor: '#D0D0D0',
        height: 56,
        borderRadius: 10,
        paddingHorizontal: 14,
        marginTop: 12,
        backgroundColor: '#fff'
    },
    selectText: { flex: 1, fontSize: 15, color: '#111' },
    
    checkRow: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'space-between',
        paddingVertical: 14,
        borderBottomWidth: 0.5,
        borderBottomColor: '#F0F0F0'
    },
    checkLabel: { fontSize: 16, color: COLORS.textPrimary },
    
    okBtn: {
        backgroundColor: COLORS.identification,
        paddingVertical: 12,
        paddingHorizontal: 30,
        borderRadius: 25,
        alignSelf: 'flex-end',
        marginTop: 24
    },
    okBtnText: { color: '#fff', fontWeight: '800', fontSize: 15 },
    
    modalSubmitBtn: {
        backgroundColor: COLORS.identification,
        height: 56,
        borderRadius: 28,
        alignItems: 'center',
        justifyContent: 'center',
        marginTop: 32
    },
    modalSubmitText: { color: '#fff', fontSize: 16, fontWeight: '900' },
});