<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{

    protected $fillable = [
        'facility_id',
        'name',
        'description',
        'specialization',
        'status'
    ];


    public $table = 'medical_facility_units';
    /**
     * The medical facilities that belong to this unit.
     */
    public function medicalFacilities()
    {
        return $this->belongsToMany(MedicalFacility::class);
    }
}
