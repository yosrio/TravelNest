<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Summary of index
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $data = Cache::remember("dashboard_{$user->id}", 60, function () use ($user) {
            $query = $user->role === 'admin'
                ? \App\Models\Trip::query()
                : $user->trips();

            $trips = $query->with('expenses')->get();

            return [
                'total_trips'       => $trips->count(),
                'upcoming_trips'    => $trips->where('start_date', '>', now())->count(),
                'total_expenses'    => $trips->flatMap->expenses->sum('amount')
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => $data
        ]);
    }
}
