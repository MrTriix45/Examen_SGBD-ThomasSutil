<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreferenceIa extends Model
{
    protected $table = 'user_preference_ia';
    protected $fillable = [
        'user_id',
        'humour_level',
        'sarcasm_level',
        'pedagogy_level',
        'patience_level',
        'anger_level',
        'web_plugin_enabled',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // FUNCTION //

    // Get the user's preferences as an array
    public static function getUserPreferences(User $user): array
    {
        $preferences = self::firstOrCreate(
            ['user_id' => $user->id],
            [
                'humour_level'       => 5,
                'sarcasm_level'      => 5,
                'pedagogy_level'     => 5,
                'patience_level'     => 5,
                'anger_level'        => 5,
                'web_plugin_enabled' => false,
            ]
        );

        return $preferences->only([
            'humour_level',
            'sarcasm_level',
            'pedagogy_level',
            'patience_level',
            'anger_level',
            'web_plugin_enabled',
        ]);
    }
}
