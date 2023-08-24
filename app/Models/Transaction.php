<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['amount'];

    public function sourceCard()
    {
        return $this->belongsTo(Card::class, 'source_card_id');
    }

    public function destinationCard()
    {
        return $this->belongsTo(Card::class, 'destination_card_id');
    }
}
