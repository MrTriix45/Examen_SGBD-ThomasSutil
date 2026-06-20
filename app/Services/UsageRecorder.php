<?php
namespace App\Services;

use App\Models\Chat_Usage;
use Illuminate\Support\Facades\Auth;

class UsageRecorder
{
    public function record(array $usage, string $model, array $context = []): void
    {
        Chat_Usage::create([
            'user_id'           => Auth::id(),
            'conversation_id'   => $context['conversation_id'] ?? null,
            'message_id'        => $context['message_id'] ?? null,
            'model'             => $model,
            'provider'          => explode('/', $model)[0] ?? null,
            'prompt_tokens'     => $usage['prompt_tokens'] ?? 0,
            'completion_tokens' => $usage['completion_tokens'] ?? 0,
            'reasoning_tokens'  => $usage['completion_tokens_details']['reasoning_tokens'] ?? 0,
            'cached_tokens'     => $usage['prompt_tokens_details']['cached_tokens'] ?? 0,
            'total_tokens'      => $usage['total_tokens'] ?? 0,
            'cost_usd'          => $usage['cost'] ?? 0,
            'web_plugin_used'   => $context['web_plugin_used'] ?? false,
            'latency_ms'        => $context['latency_ms'] ?? null,
            'status'            => $context['status'] ?? 'success',
            'error_code'        => $context['error_code'] ?? null,
        ]);
    }
}