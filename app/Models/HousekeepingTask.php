<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HousekeepingTask extends Model
{
    protected $fillable = [
        'reservation_id',
        'property_id',
        'guest_name',
        'checkout_date',
        'status',
        'notes',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}