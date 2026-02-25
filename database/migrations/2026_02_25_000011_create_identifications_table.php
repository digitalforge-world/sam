<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('identifications', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50);
            $table->foreignId('producteur_id')->constrained()->cascadeOnDelete();
            $table->foreignId('controleur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('superficie', 8, 4)->nullable();
            $table->enum('statut', ['EN_ATTENTE', 'APPROUVE', 'REJETE'])->default('EN_ATTENTE');
            $table->enum('approbation', ['BIO', 'OK', 'DECLASSIFIED'])->nullable();
            $table->string('campagne', 20);
            $table->timestamps();
            $table->unique(['numero', 'campagne']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('identifications');
    }
};
