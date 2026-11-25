<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->enum('emote', ['enak', 'pedas', 'manis']);
            $table->boolean('is_sample')->default(false);
            $table->timestamps();

            $table->unique(['order_id', 'menu_id']);
            $table->index(['menu_id', 'emote']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
