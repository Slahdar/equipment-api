<?php

namespace App\Models;

namespace App\Models;

use App\Services\SerialNumberService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'brand_id', 'equipment_type_id', 'serial_number'];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate serial number when creating a new product
        static::creating(function ($product) {
            $product->serial_number = $product->serial_number ?? SerialNumberService::forProduct();
        });
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function equipmentType()
    {
        return $this->belongsTo(EquipmentType::class);
    }

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'document_product');
    }
    
    public function associatedProducts()
    {
        return $this->belongsToMany(
            Product::class,
            'product_associated',
            'product_id',
            'associated_product_id'
        );
    }
    
    public function associatedByProducts()
    {
        return $this->belongsToMany(
            Product::class,
            'product_associated',
            'associated_product_id',
            'product_id'
        );
    }
    
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}