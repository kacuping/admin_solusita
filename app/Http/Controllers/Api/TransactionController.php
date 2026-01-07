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

        $query = Transaction::with(['service', 'cleaner']);

        // Filter based on role
        if ($user->role === 'cleaner') {
            // Cleaner sees tasks assigned to them
            $query->where('cleaner_id', $user->id);
            // Optional: Sort by transaction date for schedule view
            $query->orderBy('transaction_date', 'asc'); 
        } else {
            // Customer sees their own orders
            $query->where('user_id', $user->id);
            $query->latest('created_at');
        }

        $transactions = $query->paginate(10);

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

        $query = Transaction::with(['service', 'cleaner'])->where('id', $id);

        // Security check: Ensure user owns the transaction or is the assigned cleaner
        if ($user->role === 'cleaner') {
            $query->where('cleaner_id', $user->id);
        } elseif ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $transaction = $query->first();

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

    public function complete(Request $request, string $id)
    {
        $user = $request->user();

        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found'
            ], 404);
        }

        // Only admin or assigned cleaner can mark completed
        if ($user->role === 'cleaner') {
            if ($transaction->cleaner_id !== $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 403);
            }
        } elseif ($user->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $transaction->update([
            'status' => 'completed'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction completed',
            'data' => $transaction->fresh(['service', 'cleaner'])
        ]);
    }

    public function rate(Request $request, string $id)
    {
        $user = $request->user();

        $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ]);

        $transaction = Transaction::find($id);
        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found'
            ], 404);
        }

        if ($transaction->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($transaction->status !== 'completed') {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not completed'
            ], 400);
        }

        if (!is_null($transaction->rating)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction already rated'
            ], 400);
        }

        $transaction->update([
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Rating submitted',
            'data' => $transaction->fresh(['service', 'cleaner'])
        ]);
    }
}
