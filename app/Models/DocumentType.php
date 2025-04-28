<?php

namespace App\Models;

use App\Services\SerialNumberService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'serial_number'];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate serial number when creating a new document type
        static::creating(function ($documentType) {
            $documentType->serial_number = $documentType->serial_number ?? SerialNumberService::forDocumentType();
        });
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
