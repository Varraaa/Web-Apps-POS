<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingController extends Controller
{
    /**
     * Display the store settings and thermal receipt customizer.
     */
    public function index(): View
    {
        $settings = StoreSetting::allAsMap();

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update store settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:100'],
            'store_tagline' => ['nullable', 'string', 'max:255'],
            'store_address' => ['required', 'string', 'max:255'],
            'store_phone' => ['required', 'string', 'max:30'],
            'store_instagram' => ['nullable', 'string', 'max:50'],
            'store_whatsapp' => ['nullable', 'string', 'max:30'],
            'receipt_header' => ['nullable', 'string', 'max:255'],
            'receipt_footer' => ['nullable', 'string', 'max:500'],
            'enable_tax' => ['nullable', 'boolean'],
            'tax_percent' => ['nullable', 'numeric', 'min:0', 'max:50'],
        ]);

        foreach ($validated as $key => $value) {
            StoreSetting::set($key, $value ?? '');
        }

        return back()->with('success', 'Pengaturan toko dan format struk kasir berhasil diperbarui!');
    }
}
