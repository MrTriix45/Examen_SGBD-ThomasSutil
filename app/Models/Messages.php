<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Messages extends Model
{
    protected $fillable = [
        'conversation_id',
        'content',
        'is_user',
    ];

    protected $casts = [
        'is_user' => 'boolean',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversations::class, 'conversation_id');
    }
}
