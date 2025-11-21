# PDF Settings - Testing & Verification Guide

## Quick Start

### 1. Run Migration

```bash
php artisan migrate
```

This will:

- Create the `pdf_settings` table
- Insert default settings with current SilverGro information

### 2. Access the Settings Page

Navigate to: `http://your-domain/pdf-settings`

Or add a link to your navigation menu.

## Testing Checklist

### ✅ Database Setup

- [ ] Migration runs successfully
- [ ] `pdf_settings` table exists in database
- [ ] Default record is created with SilverGro data
- [ ] Verify default logo path points to `images/pdflogo.jpg`

### ✅ Settings Page Functionality

- [ ] Page loads without errors at `/pdf-settings`
- [ ] All form fields display with default values
- [ ] Current logo displays in preview area
- [ ] Form validation works (try submitting empty company name)
- [ ] All fields are editable

### ✅ Update Business Information

- [ ] Change company name
- [ ] Update address fields (PO Box, Street, City, Postal Code)
- [ ] Modify contact information (Phone, Fax, Email, Website)
- [ ] Click "Save Settings"
- [ ] Success message appears
- [ ] Refresh page - changes persist

### ✅ Logo Upload

- [ ] Click "Choose File" button
- [ ] Select a JPG/PNG image (< 2MB)
- [ ] Preview appears before saving
- [ ] Click "Save Settings"
- [ ] New logo is saved
- [ ] Check `public/images/` directory for new logo file (named `pdflogo_[timestamp].jpg`)
- [ ] Old custom logo is deleted (if existed)

### ✅ Reset Logo

- [ ] Upload a custom logo
- [ ] Click "Reset to Default Logo" button
- [ ] Confirm the action
- [ ] Logo reverts to `pdflogo.jpg`
- [ ] Custom logo file is deleted from `public/images/`

### ✅ PDF Generation - Deal Tickets

- [ ] Generate a deal ticket PDF
- [ ] Verify logo displays correctly (not an envelope icon)
- [ ] Check company name appears (if customized)
- [ ] Verify address information is correct
- [ ] Check contact details (phone, fax, email) display
- [ ] Generate as "Working Document"
- [ ] Generate as "Final Version"
- [ ] Test with split loads
- [ ] Test with non-split loads

### ✅ PDF Generation - Other Documents

- [ ] Generate Sales Order PDF
- [ ] Generate Sales Order Confirmation
- [ ] Generate Purchase Order PDF
- [ ] Generate Purchase Order Confirmation
- [ ] Generate Transport Order Confirmation
- [ ] Verify all show updated logo and business info

### ✅ Edge Cases

- [ ] What happens if no PDF settings exist? (Should fall back to hardcoded values)
- [ ] Upload very large image (should reject > 2MB)
- [ ] Upload non-image file (should reject)
- [ ] Try uploading unsupported format (e.g., .gif, .bmp) (should reject)
- [ ] Test with empty optional fields (should not break PDFs)
- [ ] Long company name (should display properly)
- [ ] Long address lines (should wrap correctly)

### ✅ Permission & Security

- [ ] Non-authenticated users cannot access `/pdf-settings`
- [ ] Settings page requires `auth` middleware
- [ ] Logo uploads are validated for file type and size
- [ ] File paths are sanitized
- [ ] Old logos are properly cleaned up

## Common Issues & Solutions

### Logo Shows Envelope Icon

**Problem:** Logo displays as placeholder envelope instead of actual logo.

**Solution:**

- Ensure `public_path()` is being used (not relative paths)
- Check file exists at the path specified
- Verify file permissions (readable by web server)
- Check DOMPDF configuration allows file access

### Settings Page Not Loading

**Problem:** 404 or blank page at `/pdf-settings`.

**Solutions:**

1. Check routes are registered:
   ```bash
   php artisan route:list | grep pdf-settings
   ```
2. Clear route cache:
   ```bash
   php artisan route:clear
   php artisan cache:clear
   ```
3. Check Vue component is compiled:
   ```bash
   npm run build
   ```

### Logo Upload Fails

**Problem:** Logo upload returns error or doesn't save.

**Solutions:**

1. Check directory permissions:
   ```bash
   chmod 755 public/images
   ```
2. Verify PHP upload settings in `php.ini`:
    - `upload_max_filesize = 2M` or higher
    - `post_max_size = 2M` or higher
3. Check storage is writable

### Changes Don't Appear in PDFs

**Problem:** Updated settings don't show in generated PDFs.

**Solutions:**

1. Clear application cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```
2. Ensure controller passes `$pdfSettings` to view:
   ```php
   $pdfSettings = \App\Models\PdfSetting::getActive();
   ```
3. Check blade template uses `$pdfSettings` variable

### Migration Fails

**Problem:** Migration throws error.

**Solutions:**

1. Check database connection
2. Ensure table doesn't already exist:
   ```bash
   php artisan migrate:status
   ```
3. If table exists but is wrong, rollback and re-run:
   ```bash
   php artisan migrate:rollback --step=1
   php artisan migrate
   ```

## Verification Commands

### Check Database

```bash
# MySQL/MariaDB
mysql -u your_user -p your_database -e "SELECT * FROM pdf_settings;"

# PostgreSQL
psql -U your_user -d your_database -c "SELECT * FROM pdf_settings;"
```

### Check Model Works

```bash
php artisan tinker
>>> $settings = App\Models\PdfSetting::getActive();
>>> $settings->company_name
>>> $settings->logo_full_path
>>> $settings->formatted_address
```

### Check Routes

```bash
php artisan route:list | grep pdf-settings
```

Should show:

```
GET|HEAD  pdf-settings ........................ pdf-settings.index › PdfSettingController@index
POST      pdf-settings ........................ pdf-settings.update › PdfSettingController@update
POST      pdf-settings/reset-logo ............ pdf-settings.reset-logo › PdfSettingController@resetLogo
```

### Check Permissions

```bash
ls -la public/images/
```

Should show readable/writable permissions for web server user.

## Sample Test Data

Use these values for testing:

**Company Info:**

- Company Name: "SilverGro Test Co."
- PO Box: "P.O. Box 12345"
- Street: "123 Test Street"
- City: "Port Elizabeth"
- Postal Code: "6000"
- Country: "South Africa"

**Contact Info:**

- Phone: "Tel: +27 41 123 4567"
- Fax: "Fax: +27 41 765 4321"
- Email: "test@silvergro.co.za"
- Website: "https://www.silvergro.co.za"

## Performance Considerations

- **Logo File Size:** Keep under 500KB for optimal PDF generation speed
- **Image Dimensions:** 200x95px recommended (matches current layout)
- **Database Queries:** Settings are cached per request, no N+1 issues
- **File Cleanup:** Old logos deleted automatically on upload

## Rollback Plan

If you need to revert changes:

1. **Rollback Migration:**
   ```bash
   php artisan migrate:rollback --step=1
   ```

2. **Remove Controller:**
   ```bash
   rm app/Http/Controllers/PdfSettingController.php
   ```

3. **Remove Model:**
   ```bash
   rm app/Models/PdfSetting.php
   ```

4. **Remove View:**
   ```bash
   rm -rf resources/js/Pages/PdfSettings
   ```

5. **Remove Routes:** Edit `routes/web.php` and remove the 3 pdf-settings routes

6. **Revert Controllers:** Use git to restore original controller files:
   ```bash
   git checkout app/Http/Controllers/DealTicketController.php
   git checkout app/Http/Controllers/SalesOrderController.php
   git checkout app/Http/Controllers/PurchaseOrderController.php
   git checkout app/Http/Controllers/TransportOrderController.php
   ```

## Support & Maintenance

### Regular Maintenance

- **Monthly:** Review uploaded logos, delete unused ones
- **Quarterly:** Verify settings accuracy
- **As Needed:** Update default logo if company rebrands

### Monitoring

Watch for:

- Disk space usage in `public/images/`
- Failed uploads in logs
- PDF generation errors
- Slow performance with large logo files

## Success Criteria

✅ **The implementation is successful when:**

1. Settings page loads and displays current values
2. All form fields can be updated and persist
3. Logo upload works with preview
4. Reset logo returns to default
5. All PDF types display updated logo and information
6. No errors in application logs
7. File cleanup works (old logos deleted)
8. Fallback works if settings not found
9. Changes take effect immediately in new PDFs
10. Performance remains acceptable

---

**Last Updated:** November 21, 2025  
**Version:** 1.0  
**Implementation Document:** See `PDF_SETTINGS_IMPLEMENTATION.md`

