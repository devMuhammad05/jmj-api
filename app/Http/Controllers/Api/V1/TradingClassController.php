<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TradingClassResource;
use App\Models\TradingClass;
use Illuminate\Http\JsonResponse;

class TradingClassController extends Controller
{
    /**
     * Display a listing of the published trading classes.
     */
    public function index(): JsonResponse
    {
        $classes = TradingClass::query()
            ->where('is_published', true)
            ->orderBy('scheduled_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Trading classes retrieved successfully',
            'data' => TradingClassResource::collection($classes),
        ]);
    }

    /**
     * Display the specified trading class.
     */
    public function show(TradingClass $tradingClass): JsonResponse
    {
        if (! $tradingClass->is_published) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Class not found',
                ],
                404,
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Trading class details retrieved successfully',
            'data' => new TradingClassResource($tradingClass),
        ]);
    }
}
