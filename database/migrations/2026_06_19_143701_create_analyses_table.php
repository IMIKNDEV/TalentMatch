<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->json('competences_extraites');
            $table->integer('annees_experience');
            $table->string('niveau_etudes');
            $table->json('langues');
            $table->unsignedTinyInteger('matching_score');
            $table->json('points_forts');
            $table->json('lacunes');
            $table->json('competences_manquantes');
            $table->string('recommandation');
            $table->text('justification');
            $table->foreignId('candidat_id')->unique()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
