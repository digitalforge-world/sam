<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Identification extends Model
{
    protected $fillable = [
        'numero', 'producteur_id', 'controleur_id',
        'superficie', 'statut', 'approbation', 'campagne',
        'culture_id', 'village', 'organisation_paysanne', 'statut_producteur', 'nom_parcelle',
        'participation_formations', 'production_parallele', 'diversite_biologique',
        'gestion_dechets', 'emballage_non_conforme', 'rotation_cultures', 'isolement_parcelles',
        'preparation_sol', 'fertilisation', 'semences', 'gestion_adventices', 'gestion_ravageurs',
        'recolte', 'stockage', 'commentaire', 'date_preparation_sol', 'date_semis', 'date_sarclage_1',
        'date_sarclage_2', 'date_fertilisation', 'date_recolte', 'arbres', 'niveau_pente',
        'type_culture', 'a_cours_eau', 'maisons_environnantes', 'cultures_proximite', 'rencontre_avec',
        'photo_parcelle', 'signature_producteur', 'coordonnees_polygon'
    ];

    protected $casts = [
        'participation_formations' => 'boolean',
        'production_parallele' => 'boolean',
        'diversite_biologique' => 'boolean',
        'gestion_dechets' => 'boolean',
        'emballage_non_conforme' => 'boolean',
        'rotation_cultures' => 'boolean',
        'isolement_parcelles' => 'boolean',
        'preparation_sol' => 'boolean',
        'fertilisation' => 'boolean',
        'semences' => 'boolean',
        'gestion_adventices' => 'boolean',
        'gestion_ravageurs' => 'boolean',
        'recolte' => 'boolean',
        'stockage' => 'boolean',
        'a_cours_eau' => 'boolean',
        'maisons_environnantes' => 'boolean',
        'arbres' => 'array',
        'coordonnees_polygon' => 'array',
    ];


    public function producteur(): BelongsTo { return $this->belongsTo(Producteur::class); }
    public function controleur(): BelongsTo { return $this->belongsTo(User::class, 'controleur_id'); }
}
