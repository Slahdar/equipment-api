<?php

namespace App\Models;

use App\Services\SerialNumberService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'serial_number'];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate serial number when creating a new domain
        static::creating(function ($domain) {
            $domain->serial_number = $domain->serial_number ?? SerialNumberService::forDomain();
        });
    }

    public function families()
    {
        return $this->hasMany(Family::class);
    }
}