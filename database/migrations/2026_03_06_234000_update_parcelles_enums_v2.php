<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Data updates first
        DB::table('parcelles')->where('approbation_production', 'OK')->update(['approbation_production' => 'CONVERSION']);
        DB::table('parcelles')->where('type_culture', 'SINGLE')->update(['type_culture' => null]);

        // 2. Update schema
        Schema::table('parcelles', function (Blueprint $table) {
            $table->enum('approbation_production', ['BIO', 'CONVERSION', 'DECLASSIFIED'])->nullable()->change();
            $table->enum('type_employes', ['SEASONAL', 'PERMANENT', 'FAMILIAL', 'LABOR'])->nullable()->change();
            $table->enum('type_culture', ['ASSOCIATIVE', 'SPACER', 'PURE', 'STOLEN'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('parcelles', function (Blueprint $table) {
            $table->enum('approbation_production', ['BIO', 'OK', 'DECLASSIFIED'])->nullable()->change();
            $table->enum('type_employes', ['SEASONAL', 'PERMANENT'])->nullable()->change();
            $table->enum('type_culture', ['SINGLE', 'ASSOCIATIVE', 'SPACER', 'PURE', 'STOLEN'])->nullable()->change();
        });
        
        DB::table('parcelles')->where('approbation_production', 'CONVERSION')->update(['approbation_production' => 'OK']);
    }
};
