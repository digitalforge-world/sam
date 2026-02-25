<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arbres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcelle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('culture_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('nombre');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arbres');
    }
};
