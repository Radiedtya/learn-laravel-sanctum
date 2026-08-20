<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->string('judul_film')->unique();
            $table->string('slug')->unique();
            $table->integer('durasi');
            $table->decimal('rating', 2, 1);
            $table->text('deskripsi')->nullable();
            $table->year('tahun_rilis');
            $table->string('poster');
            $table->string('sutradara');
            $table->foreignId('genre_id')->constrained('genres')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('aktor_film', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aktor_id')->constrained('aktors')->onDelete('cascade');
            $table->foreignId('film_id')->constrained('films')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aktor_film');
        Schema::dropIfExists('films');
    }
};
