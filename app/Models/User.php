<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * User types available in the system
     * @var array
     */
    const USER_TYPES = [
        'medical_facility' => 'medical_facility',
        'finder' => 'finder'
    ];
    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'email',
        'whatsapp_number',
        'password',
        'image',
        'user_type'
    ];
    /**
     * Hidden attributes from serialization
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * Attribute casting
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    /**
     * A user of type medical_facility has one medical facility
     * This establishes a one-to-one relationship
     */
    public function medicalFacility()
    {
        return $this->hasOne(MedicalFacility::class);
    }
    /**
     * Check if user is a medical facility
     */
    public function isMedicalFacility(): bool
    {
        return $this->user_type === self::USER_TYPES['medical_facility'];
    }
    /**
     * Get image URL with fallback to default
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::url($this->image);
        }
        return Storage::url('images/default-image.jpg');
    }
}
