<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramSession extends Model
{
    protected $fillable = ['chat_id', 'user_id'];

    public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
}
