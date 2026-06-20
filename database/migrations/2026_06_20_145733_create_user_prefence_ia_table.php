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
        Schema::create('user_preference_ia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->integer('humour_level')->default(5);
            $table->integer('sarcasm_level')->default(5);
            $table->integer('pedagogy_level')->default(5);
            $table->integer('patience_level')->default(5);
            $table->integer('anger_level')->default(5);
            $table->boolean('web_plugin_enabled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_prefence_ia');
    }
};
