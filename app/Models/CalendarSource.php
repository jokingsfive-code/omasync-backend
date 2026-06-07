<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarSource extends Model
{
    protected $fillable = [
        'property_id',
        'channel',
        'ical_url',
        'last_synced_at',
        'is_active',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}