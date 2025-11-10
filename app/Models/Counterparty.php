<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, Relations\BelongsTo};

class Counterparty extends Model
{
    protected $fillable = [
        'name',
        'address',
        'user_id',
        'ogrn'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(related: User::class);
    }
}
