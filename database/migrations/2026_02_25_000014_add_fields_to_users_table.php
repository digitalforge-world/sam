<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('prenom', 100)->nullable()->after('name');
            $table->enum('type', ['ADMIN', 'SUPERVISEUR', 'CONTROLEUR'])->default('CONTROLEUR')->after('prenom');
            $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete()->after('type');
            $table->boolean('est_actif')->default(true)->after('zone_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['zone_id']);
            $table->dropColumn(['prenom', 'type', 'zone_id', 'est_actif']);
        });
    }
};
