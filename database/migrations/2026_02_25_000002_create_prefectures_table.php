<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prefectures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->string('nom', 100);
            $table->string('code', 2);
            $table->timestamps();
            $table->unique(['region_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prefectures');
    }
};
