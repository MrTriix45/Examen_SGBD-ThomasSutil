<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversations extends Model
{
    protected $fillable = [
        'title',
        'user_id',
        'is_favorite',
    ];

    public static function getListConversationsByUserId(int $userId)
    {
        return self::where('user_id', $userId)->latest()->get();
    }

    public static function getFavoriteConversationsByUserId(User $user)
    {
        return self::where('user_id', $user->id)->where('is_favorite', true)->latest()->get();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Messages::class, 'conversation_id')->orderBy('id');
    }
}
