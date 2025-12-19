<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentMethods = PaymentMethod::latest()->paginate(10);
        $transactions = Transaction::with(['user', 'cleaner', 'service'])->latest()->paginate(10, ['*'], 'transactions_page');
        return view('payment-methods.index', compact('paymentMethods', 'transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('payment-methods.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
        ]);

        PaymentMethod::create($request->all());

        return redirect()->route('payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentMethod $paymentMethod)
    {
        return redirect()->route('payment-methods.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentMethod $paymentMethod)
    {
        return view('payment-methods.edit', compact('paymentMethod'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $paymentMethod->update($request->merge([
            'is_active' => $request->has('is_active')
        ])->all());

        return redirect()->route('payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return redirect()->route('payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil dihapus');
    }
}
