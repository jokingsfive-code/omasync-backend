<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'property_id',
        'guest_name',
        'channel',
        'check_in',
        'check_out',
        'total_price',
        'status',
        'notes',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}