<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('harga');
            $table->unsignedInteger('stok_porsi')->default(0);
            $table->boolean('tersedia')->default(true);
            $table->string('foto')->nullable();
            $table->boolean('is_sample')->default(false);
            $table->timestamps();

            $table->index(['outlet_id', 'tersedia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
