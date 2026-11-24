<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('antrean', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->string('nama_pemesan');
            $table->unsignedInteger('total');
            $table->boolean('is_sample')->default(false);
            $table->timestamps();

            $table->index(['outlet_id', 'created_at']);
        });

        Schema::create('antrean_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('antrean_id')->constrained('antrean')->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->string('nama_menu_saat_submit');
            $table->unsignedInteger('harga_saat_submit');
            $table->unsignedInteger('jumlah');
            $table->unsignedInteger('subtotal');

            $table->index('antrean_id');
            $table->index('menu_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('antrean_items');
        Schema::dropIfExists('antrean');
    }
};
