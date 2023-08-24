<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wage extends Model
{
    protected $fillable = ['wage_amount'];

    public const AMOUNT = 5000;

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
