<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parcelles', function (Blueprint $table) {
            // Align with IdentificationsScreen.js options
            // Pente: Sans pente, Faible, Modérée, Forte
            $table->string('niveau_pente')->nullable()->change();
            
            // Type Culture: Culture unique, Culture mixte, Agroforesterie
            $table->string('type_culture')->nullable()->change();
            
            // Approbation: BIO, CONVERSION, DECLASSIFIED
            $table->string('approbation_production')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('parcelles', function (Blueprint $table) {
            $table->enum('niveau_pente', ['WITHOUT', 'SMALL', 'MEDIUM', 'HIGH'])->nullable()->change();
            $table->enum('type_culture', ['ASSOCIATIVE', 'SPACER', 'PURE', 'STOLEN'])->nullable()->change();
            $table->enum('approbation_production', ['BIO', 'CONVERSION', 'DECLASSIFIED'])->nullable()->change();
        });
    }
};
