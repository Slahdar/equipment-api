<?php

namespace App\Models;

use App\Services\SerialNumberService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'subtitle', 
        'family_id', 
        'inventory_required', 
        'additional_fields',
        'serial_number'
    ];
    
    protected $casts = [
        'additional_fields' => 'array',
        'inventory_required' => 'boolean'
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate serial number when creating a new equipment type
        static::creating(function ($equipmentType) {
            $equipmentType->serial_number = $equipmentType->serial_number ?? SerialNumberService::forEquipmentType();
        });
    }

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}