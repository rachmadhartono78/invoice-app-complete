<?php

namespace App\Http\Controllers\API\Settings;

use App\Http\Controllers\Controller;
use App\Models\Actions;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ActionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Actions::query();

            if ($request->has('limit')) {
                $limit = max(1, (int) $request->query('limit'));
                $query->limit($limit);
            }

            if ($request->has('offset')) {
                $offset = max(0, (int) $request->query('offset'));
                $query->offset($offset);
            }

            $actions = $query->get();

            return response()->json([
                'data' => $actions,
                'message' => 'Actions fetched successfully',
                'count' => Actions::count(),
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error fetching action: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve action',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $action = Actions::findOrFail($id);

            return response()->json([
                'data' => $action,
                'message' => 'Action fetched successfully',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error fetching action: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve action',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
