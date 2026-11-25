<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->string('nama');
            $table->string('satuan', 50);
            $table->decimal('stok', 10, 2)->default(0);
            $table->decimal('batas_minimum', 10, 2)->default(0);
            $table->boolean('is_sample')->default(false);
            $table->timestamps();

            $table->index('outlet_id');
        });

        Schema::create('menu_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained('ingredients')->cascadeOnDelete();
            $table->decimal('takaran', 10, 2);

            $table->unique(['menu_id', 'ingredient_id']);
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained('ingredients')->cascadeOnDelete();
            $table->enum('jenis', ['masuk', 'keluar', 'penyesuaian']);
            $table->decimal('jumlah', 10, 2);
            $table->string('keterangan')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['ingredient_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('menu_ingredients');
        Schema::dropIfExists('ingredients');
    }
};
