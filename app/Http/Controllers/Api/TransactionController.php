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
    public function show(Request $request, string $id)
    {
        $user = $request->user();

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

    public function assign(Request $request, string $id)
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'cleaner_id' => 'required|exists:users,id',
        ]);

        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found'
            ], 404);
        }

        $transaction->update([
            'cleaner_id' => $request->cleaner_id,
            'status' => 'process'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cleaner assigned',
            'data' => $transaction->fresh(['service', 'cleaner'])
        ]);
    }
}
