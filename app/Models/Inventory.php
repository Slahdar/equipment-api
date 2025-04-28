<?php

namespace App\Models;

use App\Services\SerialNumberService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 
        'location', 
        'brand_id', 
        'commissioning_date', 
        'additional_fields',
        'serial_number'
    ];
    
    protected $casts = [
        'commissioning_date' => 'date',
        'additional_fields' => 'array'
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate serial number when creating a new inventory
        static::creating(function ($inventory) {
            $inventory->serial_number = $inventory->serial_number ?? SerialNumberService::forInventory();
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}