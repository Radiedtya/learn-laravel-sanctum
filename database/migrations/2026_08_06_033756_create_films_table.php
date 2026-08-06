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
            $table->string('judul_film');
            $table->integer('durasi');
            $table->decimal('rating', 2, 1);
            $table->text('deskripsi')->nullable();
            $table->year('tahun_rilis');
            $table->string('poster');
            $table->foreignId('genre_id')->constrained('genres')->onDelete('cascade');
            $table->string('sutradara');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
