<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CleanerController extends Controller
{
    /**
     * Display a listing of the cleaners.
     */
    public function index()
    {
        // Get users with role 'cleaner'
        $cleaners = User::where('role', 'cleaner')
            ->withAvg('cleanerTransactions as rating', 'rating')
            ->withCount(['cleanerTransactions as reviews_count' => function ($query) {
                $query->whereNotNull('rating');
            }])
            ->get();
            
        // Transform data to include full avatar URL and only necessary fields
        $data = $cleaners->map(function ($cleaner) {
            return [
                'id' => $cleaner->id,
                'name' => $cleaner->name,
                'avatar' => $cleaner->avatar ? asset('storage/' . $cleaner->avatar) : null,
                'rating' => round($cleaner->rating, 1) ?? 0, // 4.5
                'reviews_count' => $cleaner->reviews_count,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Display the specified cleaner.
     */
    public function show(string $id)
    {
        $cleaner = User::where('role', 'cleaner')
            ->withAvg('cleanerTransactions as rating', 'rating')
            ->with(['cleanerTransactions' => function($q) {
                $q->whereNotNull('rating')->with('user:id,name,avatar')->latest()->limit(5);
            }])
            ->find($id);

        if (!$cleaner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cleaner not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $cleaner->id,
                'name' => $cleaner->name,
                'email' => $cleaner->email,
                'phone' => $cleaner->phone,
                'avatar' => $cleaner->avatar ? asset('storage/' . $cleaner->avatar) : null,
                'rating' => round($cleaner->rating, 1) ?? 0,
                'reviews' => $cleaner->cleanerTransactions->map(function($t) {
                    return [
                        'id' => $t->id,
                        'customer_name' => $t->user->name ?? 'Anonymous',
                        'rating' => $t->rating,
                        'review' => $t->review,
                        'date' => $t->created_at->format('Y-m-d'),
                    ];
                })
            ]
        ]);
    }
}
