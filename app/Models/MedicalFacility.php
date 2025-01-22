<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class MedicalFacility extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',       // Add this line
        'name',
        'address',
        'whatsapp_number',
        'email',
        'description',
        'operating_hours',
        'services',
        'image',
        'status',
        'units'
    ];

    
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function medical_facility_units()
    {
        return $this->belongsToMany(Unit::class);
    }
}




