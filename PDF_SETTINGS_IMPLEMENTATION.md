# PDF Settings Feature - Implementation Summary

## Overview

Created a comprehensive PDF settings management system that allows users to dynamically update business address, contact
details, and company logo for all PDF reports.

## Components Created

### 1. Database Migration

**File:** `database/migrations/2025_11_21_000001_create_pdf_settings_table.php`

- Creates `pdf_settings` table with fields for:
    - Company name
    - Address details (PO Box, street, city, postal code, country)
    - Contact information (phone, fax, email, website)
    - Logo path
    - Active status
- Seeds default data with current SilverGro information

### 2. Model

**File:** `app/Models/PdfSetting.php`

- `getActive()` - Static method to retrieve active settings
- `getLogoFullPathAttribute()` - Accessor for full logo file path
- `getFormattedAddressAttribute()` - Accessor for formatted address string
- All fields mass-assignable with proper casting

### 3. Controller

**File:** `app/Http/Controllers/PdfSettingController.php`
**Methods:**

- `index()` - Display settings page (Inertia)
- `update()` - Update settings and handle logo upload
- `resetLogo()` - Reset logo to default

**Features:**

- Validates all input fields
- Handles logo file upload (JPG, JPEG, PNG, max 2MB)
- Automatically deletes old logo when new one is uploaded
- Stores uploaded logos in `public/images/` directory

### 4. Vue Component

**File:** `resources/js/Pages/PdfSettings/Index.vue`
**Sections:**

- Company Information (name)
- Address Details (PO Box, street, city, postal code, country)
- Contact Information (phone, fax, email, website)
- Logo Upload with preview
- Reset to default button

**Features:**

- Live preview of uploaded logo
- Form validation
- File upload handling
- Responsive design with Tailwind CSS

### 5. Routes

**File:** `routes/web.php`
Added routes:

- `GET /pdf-settings` - Display settings page
- `POST /pdf-settings` - Update settings
- `POST /pdf-settings/reset-logo` - Reset logo

## Controllers Updated

All PDF-generating controllers have been updated to use `PdfSetting::getActive()`:

1. **DealTicketController** (2 methods)
    - `viewPDF()`
    - `generatePDF()`

2. **SalesOrderController** (3 methods)
    - `viewPDF()`
    - `viewConfirmationPDF()`
    - `viewConfirmationPDFSplit()`

3. **PurchaseOrderController** (2 methods)
    - `viewPDF()`
    - `viewConfirmationPDF()`

4. **TransportOrderController** (1 method)
    - `viewConfirmationPDF()`

### Changes Made:

```php
// Before
$logo = public_path('images/pdflogo.jpg');

// After
$pdfSettings = \App\Models\PdfSetting::getActive();
$logo = $pdfSettings ? $pdfSettings->logo_full_path : public_path('images/pdflogo.jpg');

// Added to data array
$data = [
    'logo' => $logo,
    'pdfSettings' => $pdfSettings,
    // ...other data
];
```

## PDF Template Updated

**File:** `resources/views/pdf_reports/deal_ticket_v7.blade.php`

Updated header section to dynamically display:

- Company logo (with alt text)
- PO Box
- Street address
- City and postal code
- Phone number
- Fax number
- Email address

**Template Logic:**

```blade
@if(isset($pdfSettings))
    @if($pdfSettings->po_box){{ $pdfSettings->po_box }}<br>@endif
    @if($pdfSettings->street_address){{ $pdfSettings->street_address }}<br>@endif
    // ...etc
@else
    <!-- Fallback to hardcoded values -->
@endif
```

## Previous Fixes Included

1. **Null Safety for Transport Dates**
    - Added null checks for `transport_date_earliest`
    - Displays "No date Selected" when null
    - Fixed in 16 PDF report files

2. **Logo Display Issues**
    - Changed from base64 encoding to direct file paths
    - Fixed compatibility with DOMPDF

3. **Trade Operation Rules Display**
    - Added "No Trade Operation Rules applicable" message when empty
    - Fixed in all deal ticket versions

## Usage Instructions

### For Administrators:

1. Navigate to `/pdf-settings` in the application
2. Update company information, address, and contact details
3. Upload a new logo (optional)
4. Click "Save Settings"
5. All future PDFs will use the new settings

### For Developers:

To add PDF settings to a new template:

```blade
<!-- In blade template -->
<td>
    <img src="{{ $logo }}" alt="Company Logo" />
</td>
<td>
    @if(isset($pdfSettings))
        {{ $pdfSettings->company_name }}<br>
        @if($pdfSettings->po_box){{ $pdfSettings->po_box }}<br>@endif
        @if($pdfSettings->phone){{ $pdfSettings->phone }}<br>@endif
        @if($pdfSettings->email){{ $pdfSettings->email }}@endif
    @else
        <!-- Fallback content -->
    @endif
</td>
```

```php
// In controller
$pdfSettings = \App\Models\PdfSetting::getActive();
$logo = $pdfSettings ? $pdfSettings->logo_full_path : public_path('images/pdflogo.jpg');

$data = [
    'logo' => $logo,
    'pdfSettings' => $pdfSettings,
    // ...other data
];
```

## Database Schema

```sql
CREATE TABLE pdf_settings
(
    id             BIGINT PRIMARY KEY AUTO_INCREMENT,
    company_name   VARCHAR(255) DEFAULT 'SilverGro',
    po_box         VARCHAR(255),
    street_address VARCHAR(255),
    city           VARCHAR(255),
    postal_code    VARCHAR(255),
    country        VARCHAR(255) DEFAULT 'South Africa',
    phone          VARCHAR(255),
    fax            VARCHAR(255),
    email          VARCHAR(255),
    website        VARCHAR(255),
    logo_path      VARCHAR(255),
    is_active      BOOLEAN      DEFAULT TRUE,
    created_at     TIMESTAMP,
    updated_at     TIMESTAMP
);
```

## Testing Checklist

- [ ] Run migration: `php artisan migrate`
- [ ] Access `/pdf-settings` page
- [ ] Update company information
- [ ] Upload a new logo
- [ ] Generate a deal ticket PDF
- [ ] Verify new logo and information appear
- [ ] Test "Reset to Default Logo" button
- [ ] Generate other PDF types (sales order, purchase order, transport order)
- [ ] Verify all PDFs show updated information

## Notes

- Only one active PDF setting record should exist at a time
- Logo files are stored in `public/images/` directory
- Original default logo remains at `public/images/pdflogo.jpg`
- Custom logos are named with timestamp: `pdflogo_[timestamp].[ext]`
- Old logos are automatically deleted when new ones are uploaded
- System falls back to hardcoded values if PDF settings are not found

## Future Enhancements

Consider adding:

- Multiple logo support (different logos for different document types)
- Logo size/position customization
- Company registration number
- Bank details
- Footer text customization
- Multiple company profiles (for multi-tenant scenarios)

