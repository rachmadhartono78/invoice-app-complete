<?php

namespace App\Http\Controllers\API\Settings;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Application::with('menus.menuInduk');

            if ($request->has('limit')) {
                $limit = max(1, (int) $request->query('limit'));
                $query->limit($limit);
            }

            if ($request->has('offset')) {
                $offset = max(0, (int) $request->query('offset'));
                $query->offset($offset);
            }

            $applications = $query->get();

            return response()->json([
                'data' => $applications,
                'message' => 'Application fetched successfully',
                'count' => Application::count(),
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error fetching applications: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve applications',
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
        //
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
