<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('identifications', function (Blueprint $table) {
            $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('organisation_id')->nullable()->constrained('organisation_paysannes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('identifications', function (Blueprint $table) {
            $table->dropForeign(['village_id']);
            $table->dropForeign(['organisation_id']);
            $table->dropColumn(['village_id', 'organisation_id']);
        });
    }
};
