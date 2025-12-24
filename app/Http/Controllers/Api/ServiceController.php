<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Service::with('category');

        // Filter by Category ID if provided (?category_id=1)
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $services = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $services
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service = Service::with('category')->find($id);

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $service
        ]);
    }
}
