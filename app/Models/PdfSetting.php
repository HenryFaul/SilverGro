<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'po_box',
        'street_address',
        'city',
        'postal_code',
        'country',
        'phone',
        'fax',
        'email',
        'website',
        'logo_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the active PDF settings
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first() ?? self::first();
    }

    /**
     * Get the full logo path
     */
    public function getLogoFullPathAttribute(): string
    {
        if ($this->logo_path) {
            return public_path($this->logo_path);
        }
        return public_path('images/pdflogo.jpg');
    }

    /**
     * Get formatted address for PDF display
     */
    public function getFormattedAddressAttribute(): string
    {
        $address = [];

        if ($this->po_box) {
            $address[] = $this->po_box;
        }
        if ($this->street_address) {
            $address[] = $this->street_address;
        }
        if ($this->city) {
            $address[] = $this->city;
        }
        if ($this->postal_code) {
            $address[] = $this->postal_code;
        }
        if ($this->country) {
            $address[] = $this->country;
        }

        return implode(', ', $address);
    }
}

