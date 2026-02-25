<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('controles', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50);
            $table->foreignId('parcelle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('producteur_id')->constrained()->cascadeOnDelete();
            $table->foreignId('culture_id')->constrained()->cascadeOnDelete();
            $table->foreignId('controleur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('superficie_parcelle', 8, 4)->nullable();
            $table->decimal('superficie_bio', 8, 3)->nullable();
            $table->string('campagne', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('controles');
    }
};
