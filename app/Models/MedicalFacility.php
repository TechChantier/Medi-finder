<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalFacility extends Model
{
    use HasFactory;
    /**
     * Status constants for facility
     */
    const STATUS = [
        'Open' => 'Open',
        'Closed' => 'Closed'
    ];
    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'user_id',
        'address',
        'description',
        'emergency_contact',
        'google_map_url',
        'operating_hours', // JSON array of operating hours
        'status',         // Open/Closed
        'units'          // JSON array of units
    ];
    /**
     * Cast fields to appropriate types
     */
    protected $casts = [
        'units' => 'array',           // Cast units to PHP array
        'operating_hours' => 'array'  // Cast operating hours to PHP array
    ];
    /**
     * Get the user that owns this facility
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
