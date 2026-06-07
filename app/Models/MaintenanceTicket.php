<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceTicket extends Model
{
    protected $fillable = [
        'property_id',
        'title',
        'description',
        'priority',
        'status',
        'reported_by',
        'assigned_to',
        'reported_date',
        'completed_date',
        'cost',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}