<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = ['card_number', 'expiration_date', 'cvv'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
