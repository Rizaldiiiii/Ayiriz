<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama_pemesan');
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->enum('status', ['diproses', 'selesai'])->default('diproses');
            $table->unsignedInteger('total');
            $table->decimal('royalty_percent_saat_selesai', 5, 2)->nullable();
            $table->unsignedInteger('royalty_nominal')->default(0);
            $table->string('metode_bayar')->default('bayar_di_kasir');
            $table->timestamp('selesai_at')->nullable();
            $table->boolean('is_sample')->default(false);
            $table->timestamps();

            $table->index(['outlet_id', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
