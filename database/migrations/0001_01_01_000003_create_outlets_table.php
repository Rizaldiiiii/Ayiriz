<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->string('pemilik');
            $table->string('no_hp', 30);
            $table->string('alamat');
            $table->string('jam_buka', 100);
            $table->enum('status', ['terverifikasi', 'belum_terverifikasi', 'nonaktif'])->default('belum_terverifikasi')->index();
            $table->decimal('royalty_percent', 5, 2)->default(10.00);
            $table->boolean('is_sample')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outlets');
    }
};
