<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('identifications', function (Blueprint $table) {
            // Passer en mediumText pour eviter que les images base64 soient tronquees
            $table->mediumText('photo_parcelle')->nullable()->change();
            $table->mediumText('signature_producteur')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('identifications', function (Blueprint $table) {
            $table->text('photo_parcelle')->nullable()->change();
            $table->text('signature_producteur')->nullable()->change();
        });
    }
};
