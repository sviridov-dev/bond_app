<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bonds extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bonds';
    protected $fillable = [
        'code', 
        'price',
        'ticker',
        'ISIN',
        'issuer_information',
        'currency',
        'rating',
        'maturity_date',
        'next_offer_date',
        'additional_info',
        'yield_maturity',
        'coupon_rate',
        'volume',
        'duration',
        'created_at',
        'updated_at'
        
    ];
}
