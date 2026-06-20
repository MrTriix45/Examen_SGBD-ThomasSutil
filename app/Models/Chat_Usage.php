<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Chat_Usage extends Model
{
    protected $table = 'chat_usage';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'conversation_id',
        'message_id',
        'model',
        'provider',
        'prompt_tokens',
        'completion_tokens',
        'reasoning_tokens',
        'cached_tokens',
        'total_tokens',
        'cost_usd',
        'web_plugin_used',
        'latency_ms',
        'status',
        'error_code',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // FUNCTION //

    public static function getStatsByConversation(User $user)
    {
        return DB::table('chat_usage')
            ->leftJoin('conversations', 'conversations.id', '=', 'chat_usage.conversation_id')
            ->select(
                'chat_usage.conversation_id',
                'conversations.title as conversation_title',
                DB::raw('COUNT(*) as total_requests'),
                DB::raw('COALESCE(SUM(chat_usage.total_tokens), 0) as total_tokens'),
                DB::raw('COALESCE(SUM(chat_usage.cost_usd), 0)     as total_cost'),
                DB::raw('COALESCE(AVG(chat_usage.latency_ms), 0)   as average_latency'),
            )
            ->where('chat_usage.user_id', $user->id)
            ->whereNotNull('chat_usage.conversation_id')
            ->groupBy('chat_usage.conversation_id', 'conversations.title')
            ->orderByDesc('total_tokens')
            ->get();
    }
}
