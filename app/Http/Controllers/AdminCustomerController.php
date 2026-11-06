<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCustomerController extends Controller
{
    /**
     * Display customer membership list.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $tier = $request->string('tier')->trim()->toString();

        $query = Customer::withCount('orders')->latest();

        if ($search) {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('member_code', 'like', "%{$search}%");
            });
        }

        if ($tier && in_array($tier, ['bronze', 'silver', 'gold'])) {
            $query->where('tier', $tier);
        }

        $customers = $query->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers', 'search', 'tier'));
    }

    /**
     * Search customers for POS terminal autocomplete (JSON).
     */
    public function search(Request $request): JsonResponse
    {
        $q = $request->string('q')->trim()->toString();

        if (strlen($q) < 2) {
            return response()->json(['customers' => []]);
        }

        $customers = Customer::where(function ($query) use ($q): void {
            $query->where('name', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%")
                ->orWhere('member_code', 'like', "%{$q}%");
        })->take(8)->get(['id', 'member_code', 'name', 'phone', 'tier', 'points', 'store_credit', 'total_spent']);

        return response()->json([
            'customers' => $customers->map(fn ($c) => [
                'id' => $c->id,
                'member_code' => $c->member_code,
                'name' => $c->name,
                'phone' => $c->phone,
                'tier' => $c->tier,
                'tier_label' => $c->tier_label,
                'points' => $c->points,
                'store_credit' => (float) $c->store_credit,
                'formatted_store_credit' => $c->formatted_store_credit,
                'total_spent' => $c->total_spent,
            ]),
        ]);
    }

    /**
     * Quick register customer from POS terminal modal (JSON).
     */
    public function quickCreate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'unique:customers,phone'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $customer = Customer::create([
            ...$validated,
            'tier' => 'bronze',
            'points' => 10, // Bonus 10 poin pendaftaran member baru
        ]);

        return response()->json([
            'success' => true,
            'message' => "Member {$customer->name} ({$customer->member_code}) berhasil didaftarkan!",
            'customer' => [
                'id' => $customer->id,
                'member_code' => $customer->member_code,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'tier' => $customer->tier,
                'tier_label' => $customer->tier_label,
                'points' => $customer->points,
            ],
        ]);
    }

    /**
     * Adjust customer store credit.
     */
    public function adjustCredit(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validated['amount'] >= 0) {
            $customer->addStoreCredit((float) $validated['amount'], $validated['notes'] ?? 'Penyesuaian kasir');
        } else {
            $customer->deductStoreCredit(abs((float) $validated['amount']));
        }

        return back()->with('success', "Saldo dompet toko {$customer->name} berhasil diperbarui.");
    }

    /**
     * Destroy customer.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return back()->with('success', 'Data pelanggan berhasil dihapus.');
    }
}
