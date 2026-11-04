<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDiscountController extends Controller
{
    /**
     * Display a listing of discount coupons.
     */
    public function index(): View
    {
        $discounts = Discount::latest()->paginate(12);

        return view('admin.discounts.index', compact('discounts'));
    }

    /**
     * Show form for creating a new voucher.
     */
    public function create(): View
    {
        return view('admin.discounts.form');
    }

    /**
     * Store newly created voucher.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Discount::create($data);

        return redirect()->route('admin.discounts.index')
            ->with('success', "Voucher promo {$data['code']} berhasil ditambahkan.");
    }

    /**
     * Show form for editing an existing voucher.
     */
    public function edit(Discount $discount): View
    {
        return view('admin.discounts.form', compact('discount'));
    }

    /**
     * Update voucher.
     */
    public function update(Request $request, Discount $discount): RedirectResponse
    {
        $data = $this->validated($request, $discount);
        $discount->update($data);

        return redirect()->route('admin.discounts.index')
            ->with('success', "Voucher promo {$discount->code} berhasil diperbarui.");
    }

    /**
     * Toggle active status.
     */
    public function toggle(Discount $discount): RedirectResponse
    {
        $discount->update(['is_active' => ! $discount->is_active]);
        $statusStr = $discount->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Voucher {$discount->code} berhasil {$statusStr}.");
    }

    /**
     * Delete voucher.
     */
    public function destroy(Discount $discount): RedirectResponse
    {
        $code = $discount->code;
        $discount->delete();

        return back()->with('success', "Voucher promo {$code} telah dihapus.");
    }

    /**
     * Validate discount payload.
     */
    private function validated(Request $request, ?Discount $discount = null): array
    {
        $id = $discount?->id;

        $data = $request->validate([
            'code' => ['required', 'string', 'max:30', "unique:discounts,code,{$id}"],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'type' => ['required', 'string', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_spend' => ['required', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => ['boolean'],
        ]);

        $data['code'] = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
