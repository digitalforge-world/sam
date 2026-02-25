<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcelles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('indice');
            $table->foreignId('producteur_id')->constrained()->cascadeOnDelete();
            $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('culture_id')->nullable()->constrained()->nullOnDelete();

            // Superficie
            $table->decimal('superficie', 8, 4)->nullable();
            $table->decimal('superficie_bio', 8, 3)->nullable();
            $table->decimal('rendement_bio', 8, 3)->nullable();
            $table->unsignedBigInteger('volume_production')->nullable();

            // Caractéristiques
            $table->enum('niveau_pente', ['WITHOUT', 'SMALL', 'MEDIUM', 'HIGH'])->nullable();
            $table->enum('type_culture', ['SINGLE', 'ASSOCIATIVE', 'SPACER', 'PURE', 'STOLEN'])->nullable();
            $table->enum('type_employes', ['SEASONAL', 'PERMANENT'])->nullable();
            $table->enum('approbation_production', ['BIO', 'OK', 'DECLASSIFIED'])->nullable();

            // Booléens
            $table->boolean('bio')->default(false);
            $table->boolean('a_cours_eau')->default(false);
            $table->boolean('maisons_proximite')->default(false);
            $table->boolean('transformation_ferme')->default(false);

            // Géographie (stockée en JSON pour compatibilité)
            $table->json('centre')->nullable();
            $table->json('contour')->nullable();

            $table->timestamps();
            $table->unique(['producteur_id', 'indice']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcelles');
    }
};
