<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['amount'];

    public const MIN = 10_000;
    public const MAX = 500_000_000;

    public function sourceCard()
    {
        return $this->belongsTo(Card::class, 'source_card_id');
    }

    public function destinationCard()
    {
        return $this->belongsTo(Card::class, 'destination_card_id');
    }
}
