<?php

namespace App\Models;

use App\Services\SerialNumberService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'domain_id', 'serial_number'];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate serial number when creating a new family
        static::creating(function ($family) {
            $family->serial_number = $family->serial_number ?? SerialNumberService::forFamily();
        });
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function equipmentTypes()
    {
        return $this->hasMany(EquipmentType::class);
    }
}