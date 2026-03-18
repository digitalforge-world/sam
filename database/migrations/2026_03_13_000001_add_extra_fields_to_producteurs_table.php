<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('producteurs', function (Blueprint $table) {
            $table->string('sexe', 10)->nullable()->after('prenom');              // 'M' | 'F'
            $table->string('telephone', 20)->nullable()->after('sexe');
            $table->string('type_carte', 50)->nullable()->after('telephone');    // CNI | PASSEPORT | ELECTEUR | PERMIS
            $table->string('statut', 10)->default('nouveau')->after('type_carte'); // 'nouveau' | 'ancien'
            $table->smallInteger('annee_adhesion')->nullable()->after('statut'); // null si statut = nouveau
        });
    }

    public function down(): void
    {
        Schema::table('producteurs', function (Blueprint $table) {
            $table->dropColumn(['sexe', 'telephone', 'type_carte', 'statut', 'annee_adhesion']);
        });
    }
};
