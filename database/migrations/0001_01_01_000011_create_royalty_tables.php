<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('royalty_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->decimal('percent_lama', 5, 2);
            $table->decimal('percent_baru', 5, 2);
            $table->string('alasan');
            $table->foreignId('diubah_oleh')->constrained('users')->cascadeOnDelete();
            $table->dateTime('berlaku_mulai');
            $table->boolean('is_sample')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['outlet_id', 'created_at']);
        });

        Schema::create('royalty_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->string('semester', 50);
            $table->integer('total_penjualan')->default(0);
            $table->integer('total_royalti_terhitung')->default(0);
            $table->enum('keputusan', ['tetap', 'naik']);
            $table->decimal('percent_baru', 5, 2);
            $table->text('catatan')->nullable();
            $table->foreignId('dibuat_oleh')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_sample')->default(false);
            $table->timestamps();

            $table->unique(['outlet_id', 'semester']);
            $table->index(['outlet_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('royalty_audits');
        Schema::dropIfExists('royalty_histories');
    }
};
