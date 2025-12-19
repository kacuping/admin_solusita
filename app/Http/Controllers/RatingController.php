<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index()
    {
        $cleanerRatings = Transaction::whereNotNull('rating')
            ->select('cleaner_id')
            ->selectRaw('ROUND(AVG(rating), 2) as avg_rating')
            ->selectRaw('COUNT(*) as ratings_count')
            ->with(['cleaner'])
            ->groupBy('cleaner_id')
            ->orderByDesc('avg_rating')
            ->paginate(10);

        return view('rating.index', compact('cleanerRatings'));
    }

    public function show($cleanerId)
    {
        $cleaner = User::where('role', 'cleaner')->findOrFail($cleanerId);
        $transactions = Transaction::whereNotNull('rating')
            ->where('cleaner_id', $cleanerId)
            ->with(['user', 'service'])
            ->latest()
            ->get();

        return response()->json([
            'cleaner' => [
                'id' => $cleaner->id,
                'name' => $cleaner->name,
                'email' => $cleaner->email,
            ],
            'avg_rating' => round($transactions->avg('rating'), 2),
            'ratings_count' => $transactions->count(),
            'reviews' => $transactions->map(function ($t) {
                return [
                    'id' => $t->id,
                    'date' => $t->transaction_date,
                    'customer' => $t->user ? $t->user->name : '-',
                    'service' => $t->service ? $t->service->name : '-',
                    'rating' => $t->rating,
                    'review' => $t->review,
                ];
            }),
        ]);
    }
}
