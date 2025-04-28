<?php

namespace App\Services;

use Illuminate\Support\Str;

class SerialNumberService
{
    /**
     * Generate a unique serial number for different models
     * 
     * @param string $prefix The prefix to use for the serial number (e.g. 'DOM', 'PRD')
     * @param int $length The length of the random part of the serial number
     * @return string
     */
    public static function generate(string $prefix, int $length = 8): string
    {
        return strtoupper($prefix) . '-' . Str::random($length);
    }

    /**
     * Generate a unique serial number for Domain model
     * 
     * @return string
     */
    public static function forDomain(): string
    {
        return self::generate('DOM');
    }

    /**
     * Generate a unique serial number for Family model
     * 
     * @return string
     */
    public static function forFamily(): string
    {
        return self::generate('FAM');
    }

    /**
     * Generate a unique serial number for Brand model
     * 
     * @return string
     */
    public static function forBrand(): string
    {
        return self::generate('BRD');
    }

    /**
     * Generate a unique serial number for EquipmentType model
     * 
     * @return string
     */
    public static function forEquipmentType(): string
    {
        return self::generate('EQT');
    }

    /**
     * Generate a unique serial number for DocumentType model
     * 
     * @return string
     */
    public static function forDocumentType(): string
    {
        return self::generate('DCT');
    }

    /**
     * Generate a unique serial number for Product model
     * 
     * @return string
     */
    public static function forProduct(): string
    {
        return self::generate('PRD');
    }

    /**
     * Generate a unique serial number for Document model
     * 
     * @return string
     */
    public static function forDocument(): string
    {
        return self::generate('DOC');
    }

    /**
     * Generate a unique serial number for Inventory model
     * 
     * @return string
     */
    public static function forInventory(): string
    {
        return self::generate('INV');
    }
}