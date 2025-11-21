<?php

namespace App\Http\Controllers;

use App\Models\PdfSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PdfSettingController extends Controller
{
    /**
     * Display the PDF settings page
     */
    public function index(): Response
    {
        $settings = PdfSetting::getActive();

        return Inertia::render('PdfSettings/Index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update the PDF settings
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'po_box' => 'nullable|string|max:255',
            'street_address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'fax' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $settings = PdfSetting::getActive();

        if (!$settings) {
            $settings = new PdfSetting();
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($settings->logo_path && $settings->logo_path !== 'images/pdflogo.jpg') {
                $oldPath = public_path($settings->logo_path);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // Store new logo
            $file = $request->file('logo');
            $filename = 'pdflogo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $validated['logo_path'] = 'images/' . $filename;
        }

        $settings->fill($validated);
        $settings->is_active = true;
        $settings->save();

        return redirect()->back()->with('flash.banner', 'PDF settings updated successfully');
    }

    /**
     * Reset logo to default
     */
    public function resetLogo(): RedirectResponse
    {
        $settings = PdfSetting::getActive();

        if ($settings && $settings->logo_path !== 'images/pdflogo.jpg') {
            // Delete custom logo
            $oldPath = public_path($settings->logo_path);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }

            $settings->logo_path = 'images/pdflogo.jpg';
            $settings->save();
        }

        return redirect()->back()->with('flash.banner', 'Logo reset to default successfully');
    }
}

