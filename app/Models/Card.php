<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Card extends Model
{
    use HasFactory;

    protected $fillable = ['card_number', 'expiration_date', 'cvv'];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
