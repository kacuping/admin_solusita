<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the active payment methods.
     */
    public function index()
    {
        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return response()->json([
            'status' => 'success',
            'data' => $paymentMethods
        ]);
    }
}
