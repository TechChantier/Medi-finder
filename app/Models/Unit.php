<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'medical_facility_units';
    protected $fillable = [
        'facility_id',
        'name',
        'description',
        'status'
    ];
    /**
     * Get all medical facilities that have this unit
     */
    public function medicalFacilities()
    {
        return $this->belongsToMany(MedicalFacility::class, 'medical_facility_units')
            ->withTimestamps();
    }
}
