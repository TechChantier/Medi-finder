<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    public $table = 'medical_facilty_units';
    /**
     * The medical facilities that belong to this unit.
     */
    public function medicalFacilities()
    {
        return $this->belongsToMany(MedicalFacility::class);
    }
}
