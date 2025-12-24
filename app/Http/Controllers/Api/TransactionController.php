<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the user's transactions (History).
     */
    public function index(Request $request)
    {
        // Get logged in user
        $user = $request->user();

        // Get transactions for this user
        $transactions = Transaction::where('user_id', $user->id)
            ->with(['service', 'cleaner']) // Eager load relations
            ->latest('created_at')
            ->paginate(10); // Pagination

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ]);
    }

    /**
     * Display the specified transaction detail.
     */
    public function show(string $id)
    {
        $user = auth()->user();

        // Find transaction ensuring it belongs to the user
        $transaction = Transaction::where('user_id', $user->id)
            ->where('id', $id)
            ->with(['service', 'cleaner'])
            ->first();

        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found or unauthorized'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $transaction
        ]);
    }
}
