<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['amount'];

    public const MIN = 10_000;
    public const MAX = 500_000_000;

    public function sourceCard(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'source_card_id');
    }

    public function destinationCard(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'destination_card_id');
    }
}
