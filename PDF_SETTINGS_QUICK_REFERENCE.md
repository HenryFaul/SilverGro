# 📋 PDF Settings - Quick Reference Card

## 🚀 Getting Started (3 Steps)

```bash
# 1. Run migration
php artisan migrate

# 2. Access settings page
http://your-domain/pdf-settings

# 3. Test by generating a PDF
# Go to any deal ticket and click "View PDF"
```

---

## 📍 Important URLs

| Feature         | URL                             |
|-----------------|---------------------------------|
| Settings Page   | `/pdf-settings`                 |
| Update Settings | `POST /pdf-settings`            |
| Reset Logo      | `POST /pdf-settings/reset-logo` |

---

## 🗂️ File Locations

| Type            | Path                                                                  |
|-----------------|-----------------------------------------------------------------------|
| Model           | `app/Models/PdfSetting.php`                                           |
| Controller      | `app/Http/Controllers/PdfSettingController.php`                       |
| Vue Component   | `resources/js/Pages/PdfSettings/Index.vue`                            |
| Migration       | `database/migrations/2025_11_21_000001_create_pdf_settings_table.php` |
| Routes          | `routes/web.php` (lines with pdf-settings)                            |
| Sample Template | `resources/views/pdf_reports/deal_ticket_v7.blade.php`                |

---

## 🎯 Key Code Snippets

### Get Settings in Controller

```php
$pdfSettings = \App\Models\PdfSetting::getActive();
$logo = $pdfSettings ? $pdfSettings->logo_full_path : public_path('images/pdflogo.jpg');
```

### Use in Blade Template

```blade
@if(isset($pdfSettings))
    {{ $pdfSettings->company_name }}<br>
    @if($pdfSettings->po_box){{ $pdfSettings->po_box }}<br>@endif
    @if($pdfSettings->phone){{ $pdfSettings->phone }}<br>@endif
@endif
```

### Access Logo

```blade
<img src="{{ $logo }}" alt="Company Logo" />
```

---

## 🔍 Quick Debugging

### Check if settings exist:

```bash
php artisan tinker
>>> App\Models\PdfSetting::count()
>>> App\Models\PdfSetting::first()
```

### Check routes:

```bash
php artisan route:list | grep pdf-settings
```

### Clear caches:

```bash
php artisan cache:clear && php artisan config:clear && php artisan view:clear
```

---

## ⚡ Common Tasks

### Update Company Name

1. Go to `/pdf-settings`
2. Change "Company Name" field
3. Click "Save Settings"

### Upload New Logo

1. Go to `/pdf-settings`
2. Click "Choose File" under Logo section
3. Select image (JPG/PNG, max 2MB)
4. Click "Save Settings"

### Reset to Default Logo

1. Go to `/pdf-settings`
2. Click "Reset to Default Logo" button
3. Confirm action

### Add Settings to New PDF Template

1. Update controller to pass `$pdfSettings` and `$logo`
2. Add conditional blocks in blade template
3. Test PDF generation

---

## 📊 Database Fields

| Field          | Type    | Purpose           |
|----------------|---------|-------------------|
| company_name   | string  | Company name      |
| po_box         | string  | PO Box address    |
| street_address | string  | Street address    |
| city           | string  | City name         |
| postal_code    | string  | Postal/ZIP code   |
| country        | string  | Country name      |
| phone          | string  | Phone number      |
| fax            | string  | Fax number        |
| email          | string  | Email address     |
| website        | string  | Website URL       |
| logo_path      | string  | Path to logo file |
| is_active      | boolean | Active status     |

---

## 🛠️ Updated Controllers

- ✅ DealTicketController (viewPDF, generatePDF)
- ✅ SalesOrderController (viewPDF, viewConfirmationPDF, viewConfirmationPDFSplit)
- ✅ PurchaseOrderController (viewPDF, viewConfirmationPDF)
- ✅ TransportOrderController (viewConfirmationPDF)

---

## ❓ FAQ

**Q: Where are logos stored?**  
A: `public/images/` directory. Custom logos named `pdflogo_[timestamp].[ext]`

**Q: What happens to old logos?**  
A: Automatically deleted when new logo uploaded

**Q: What if I delete all settings?**  
A: System falls back to hardcoded default values

**Q: Can I have multiple company profiles?**  
A: Not in current version. Only one active setting at a time.

**Q: What image formats are supported?**  
A: JPG, JPEG, and PNG. Max 2MB file size.

**Q: Do I need to rebuild PDFs?**  
A: No. All new PDFs automatically use latest settings.

---

## 🚨 Troubleshooting

| Problem              | Solution                                          |
|----------------------|---------------------------------------------------|
| Logo shows envelope  | Check `public_path()` is used, file exists        |
| Settings not saving  | Check file permissions, database connection       |
| 404 on settings page | Run `php artisan route:clear`                     |
| Upload fails         | Check `public/images/` is writable                |
| Changes not in PDF   | Clear caches, check `$pdfSettings` passed to view |

---

## 📚 Full Documentation

- **Implementation Details:** `PDF_SETTINGS_IMPLEMENTATION.md`
- **Testing Guide:** `PDF_SETTINGS_TESTING_GUIDE.md`
- **This Quick Reference:** Keep handy for daily use!

---

**Need Help?** Check the full documentation or application logs.

**Last Updated:** November 21, 2025

