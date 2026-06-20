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
        Schema::create('chat_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('conversation_id')->nullable()->constrained('conversations')->nullOnDelete();
            $table->foreignId('message_id')->nullable()->constrained('messages')->nullOnDelete();

            $table->string('model', 100)->index();
            $table->string('provider', 50)->nullable();

            $table->unsignedInteger('prompt_tokens')->default(0);
            $table->unsignedInteger('completion_tokens')->default(0);
            $table->unsignedInteger('reasoning_tokens')->default(0);
            $table->unsignedInteger('cached_tokens')->default(0);
            $table->unsignedInteger('total_tokens')->default(0);

            $table->decimal('cost_usd', 12, 8)->default(0);
            $table->boolean('web_plugin_used')->default(false);
            $table->unsignedInteger('latency_ms')->nullable();

            $table->string('status', 20)->default('success');
            $table->string('error_code', 100)->nullable();

            $table->timestamp('created_at')->useCurrent()->index();

            // INDEX
            $table->index(['user_id', 'created_at']);
            $table->index(['model', 'created_at']);
            $table->index(['status', 'created_at']);

            // Pour les recherches de conversations/messages liés
            $table->index(['conversation_id']);
            $table->index(['message_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_usage');
    }
};
