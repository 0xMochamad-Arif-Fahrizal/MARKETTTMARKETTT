<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AddressController extends Controller
{
    /**
     * Display a listing of the user's addresses.
     */
    public function index(Request $request)
    {
        $addresses = $request->user()
            ->addresses()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Profile/Addresses', [
            'addresses' => $addresses,
        ]);
    }

    /**
     * Store a newly created address.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address_line' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_default' => 'boolean',
        ]);

        DB::transaction(function () use ($request, $validated) {
            // If this is set as default, unset other defaults
            if ($validated['is_default'] ?? false) {
                $request->user()->addresses()->update(['is_default' => false]);
            }

            $request->user()->addresses()->create($validated);
        });

        return redirect()->back()->with('success', 'Alamat berhasil ditambahkan');
    }

    /**
     * Update the specified address.
     */
    public function update(Request $request, Address $address)
    {
        // Ensure user owns this address
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address_line' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_default' => 'boolean',
        ]);

        DB::transaction(function () use ($request, $address, $validated) {
            // If this is set as default, unset other defaults
            if ($validated['is_default'] ?? false) {
                $request->user()->addresses()
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }

            $address->update($validated);
        });

        return redirect()->back()->with('success', 'Alamat berhasil diperbarui');
    }

    /**
     * Remove the specified address.
     */
    public function destroy(Request $request, Address $address)
    {
        // Ensure user owns this address
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        $address->delete();

        return redirect()->back()->with('success', 'Alamat berhasil dihapus');
    }

    /**
     * Set address as default.
     */
    public function setDefault(Request $request, Address $address)
    {
        // Ensure user owns this address
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        DB::transaction(function () use ($request, $address) {
            // Unset all defaults
            $request->user()->addresses()->update(['is_default' => false]);
            
            // Set this as default
            $address->update(['is_default' => true]);
        });

        return redirect()->back()->with('success', 'Alamat default berhasil diubah');
    }
}
