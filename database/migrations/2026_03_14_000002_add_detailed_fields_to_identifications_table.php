<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('identifications', function (Blueprint $table) {
            $table->foreignId('culture_id')->nullable()->constrained()->nullOnDelete();
            $table->string('village')->nullable();
            $table->string('organisation_paysanne')->nullable();
            $table->string('statut_producteur')->nullable();
            $table->string('nom_parcelle')->nullable();
            
            // Toggles
            $table->boolean('participation_formations')->default(false);
            $table->boolean('production_parallele')->default(false);
            $table->boolean('diversite_biologique')->default(false);
            $table->boolean('gestion_dechets')->default(false);
            $table->boolean('emballage_non_conforme')->default(false);
            $table->boolean('rotation_cultures')->default(false);
            $table->boolean('isolement_parcelles')->default(false);
            $table->boolean('preparation_sol')->default(false);
            $table->boolean('fertilisation')->default(false);
            $table->boolean('semences')->default(false);
            $table->boolean('gestion_adventices')->default(false);
            $table->boolean('gestion_ravageurs')->default(false);
            $table->boolean('recolte')->default(false);
            $table->boolean('stockage')->default(false);
            
            $table->text('commentaire')->nullable();

            // Calendrier
            $table->date('date_preparation_sol')->nullable();
            $table->date('date_semis')->nullable();
            $table->date('date_sarclage_1')->nullable();
            $table->date('date_sarclage_2')->nullable();
            $table->date('date_fertilisation')->nullable();
            $table->date('date_recolte')->nullable();

            // Arbres (JSON arrays)
            $table->json('arbres')->nullable();

            // Environnement
            $table->string('niveau_pente')->nullable();
            $table->string('type_culture')->nullable();
            $table->boolean('a_cours_eau')->default(false);
            $table->boolean('maisons_environnantes')->default(false);
            $table->string('cultures_proximite')->nullable();
            $table->string('rencontre_avec')->nullable();

            // Media & GPS
            $table->text('photo_parcelle')->nullable();
            $table->text('signature_producteur')->nullable();
            $table->json('coordonnees_polygon')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('identifications', function (Blueprint $table) {
            $table->dropForeign(['culture_id']);
            $table->dropColumn([
                'culture_id', 'village', 'organisation_paysanne', 'statut_producteur', 'nom_parcelle',
                'participation_formations', 'production_parallele', 'diversite_biologique',
                'gestion_dechets', 'emballage_non_conforme', 'rotation_cultures', 'isolement_parcelles',
                'preparation_sol', 'fertilisation', 'semences', 'gestion_adventices', 'gestion_ravageurs',
                'recolte', 'stockage', 'commentaire', 'date_preparation_sol', 'date_semis', 'date_sarclage_1',
                'date_sarclage_2', 'date_fertilisation', 'date_recolte', 'arbres', 'niveau_pente',
                'type_culture', 'a_cours_eau', 'maisons_environnantes', 'cultures_proximite', 'rencontre_avec',
                'photo_parcelle', 'signature_producteur', 'coordonnees_polygon'
            ]);
        });
    }
};
