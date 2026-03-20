<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parcelles', function (Blueprint $table) {
            $table->boolean('route_proximite')->default(false)->after('maisons_proximite');
            $table->boolean('usine_proximite')->default(false)->after('route_proximite');
            $table->boolean('depotoir_proximite')->default(false)->after('usine_proximite');
            $table->boolean('ferme_proximite')->default(false)->after('depotoir_proximite');
        });
    }

    public function down(): void
    {
        Schema::table('parcelles', function (Blueprint $table) {
            $table->dropColumn(['route_proximite', 'usine_proximite', 'depotoir_proximite', 'ferme_proximite']);
        });
    }
};
