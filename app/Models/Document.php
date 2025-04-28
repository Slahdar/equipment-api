<?php

namespace App\Models;

use App\Services\SerialNumberService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'document_type_id', 
        'file_path', 
        'issue_date', 
        'expiry_date', 
        'version', 
        'reference', 
        'archived',
        'serial_number'
    ];
    
    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'archived' => 'boolean'
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate serial number when creating a new document
        static::creating(function ($document) {
            $document->serial_number = $document->serial_number ?? SerialNumberService::forDocument();
            $document->archived = $document->archived ?? false;
        });
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'document_product');
    }
}