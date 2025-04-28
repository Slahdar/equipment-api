<?php

namespace App\Models;

use App\Services\SerialNumberService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'serial_number'];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate serial number when creating a new brand
        static::creating(function ($brand) {
            $brand->serial_number = $brand->serial_number ?? SerialNumberService::forBrand();
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}