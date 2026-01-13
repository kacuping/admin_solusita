<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Service;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\BriService;

class PaymentController extends Controller
{
    protected $briService;

    public function __construct(BriService $briService)
    {
        $this->briService = $briService;

        // Set Midtrans Configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createTransaction(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'cleaner_id' => 'nullable|exists:users,id',
            'transaction_date' => 'required|date',
            'payment_gateway' => 'nullable|in:midtrans,bri,cash,transfer',
            'address' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'quantity' => 'nullable|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $service = Service::findOrFail($request->service_id);
            $user = $request->user();
            $quantity = $request->input('quantity', 1);
            $total = $service->price * $quantity;

            // Create Transaction
            $code = 'TRX-' . mt_rand(100000, 999999);
            $transaction = Transaction::create([
                'code' => $code,
                'user_id' => $user->id,
                'cleaner_id' => $request->cleaner_id, 
                'service_id' => $request->service_id,
                'transaction_date' => $request->transaction_date,
                'order_address' => $request->address,
                'order_lat' => $request->latitude,
                'order_lng' => $request->longitude,
                'quantity' => $quantity,
                'total' => $total,
                'status' => 'pending',
            ]);

            $paymentGateway = $request->input('payment_gateway', 'midtrans');
            $paymentUrl = null;
            $paymentToken = null;
            $paymentType = null;

            if ($paymentGateway === 'cash') {
                // Payment Method: Cash
                $paymentType = 'cash';
                $transaction->update([
                    'payment_type' => 'cash',
                    'status' => 'pending', // Tetap pending sampai dibayar di tempat
                ]);
            } elseif ($paymentGateway === 'transfer') {
                // Payment Method: Manual Transfer
                $paymentType = 'transfer';
                $transaction->update([
                    'payment_type' => 'transfer',
                    'status' => 'pending', // Pending sampai upload bukti dan dikonfirmasi
                ]);
            } elseif ($paymentGateway === 'bri') {
                // BRI Direct Integration
                $briResponse = $this->briService->createVirtualAccount(
                    $transaction->code, 
                    $transaction->total, 
                    $user->name, 
                    $user->phone
                );
                
                // Assuming Mock Success for now as per BriService
                $paymentToken = $briResponse['trxId']; // Or any reference
                $paymentType = 'bri_va';
                // No URL for Direct VA usually, just the VA Number
                $transaction->update([
                    'payment_token' => $paymentToken,
                    'payment_type' => 'bri_va',
                    // Store VA Number in payment_url temporarily or add a new column
                    'payment_url' => $briResponse['virtualAccountNo'], 
                ]);

            } else {
                // Default: Midtrans
                // Prepare Midtrans Params
                $params = [
                    'transaction_details' => [
                        'order_id' => $transaction->code,
                        'gross_amount' => (int) $transaction->total,
                    ],
                    'customer_details' => [
                        'first_name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'billing_address' => [
                            'address' => $request->address,
                        ],
                    ],
                    'item_details' => [
                        [
                            'id' => $service->id,
                            'price' => (int) $service->price,
                            'quantity' => (int) $quantity,
                            'name' => $service->name,
                        ]
                    ],
                ];

                // Get Snap Token
                $paymentUrl = Snap::createTransaction($params)->redirect_url;
                $paymentToken = Snap::getSnapToken($params);

                // Update Transaction
                $transaction->update([
                    'payment_token' => $paymentToken,
                    'payment_url' => $paymentUrl,
                    'payment_type' => 'midtrans',
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => $transaction,
                'payment_url' => $paymentUrl,
                'payment_token' => $paymentToken,
                'payment_gateway' => $paymentGateway
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function notification(Request $request)
    {
        try {
            $notif = new Notification();

            $transaction = $notif->transaction_status;
            $type = $notif->payment_type;
            $order_id = $notif->order_id;
            $fraud = $notif->fraud_status;

            $trx = Transaction::where('code', $order_id)->first();

            if (!$trx) {
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            if ($transaction == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $trx->update(['status' => 'pending']);
                    } else {
                        $trx->update(['status' => 'process']);
                    }
                }
            } else if ($transaction == 'settlement') {
                $trx->update(['status' => 'process']);
            } else if ($transaction == 'pending') {
                $trx->update(['status' => 'pending']);
            } else if ($transaction == 'deny') {
                $trx->update(['status' => 'cancelled']);
            } else if ($transaction == 'expire') {
                $trx->update(['status' => 'cancelled']);
            } else if ($transaction == 'cancel') {
                $trx->update(['status' => 'cancelled']);
            }

            // Save payment type if available
            $trx->update(['payment_type' => $type]);

            return response()->json(['message' => 'Payment status updated']);

        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing notification'], 500);
        }
    }
}
