<?php

namespace App\Http\Controllers\API\Settings;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Menu::with('application', 'menuInduk');

            $limit = $request->query('limit', null);
            $offset = $request->query('offset', null);

            if ($limit !== null) {
                $query->limit(max(1, (int) $limit));
            }

            if ($offset !== null) {
                $query->offset(max(0, (int) $offset));
            }

            $data = $query->get();

            return response()->json([
                'data' => $data,
                'message' => 'Menu fetched successfully',
                'count' => Menu::count(),
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error fetching menus: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve menu',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'application_id' => 'required|uuid',
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'icon' => 'nullable|string',
            'description' => 'nullable',
            'order' => 'nullable|integer',
            'menu_id' => 'nullable|exists:menus,id|uuid',
        ]);

        try {
            if (empty($validatedData['icon'])) {
                $validatedData['icon'] = '<svg class="w-6 h-6 transition duration-75" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M8.737 8.737a21.49 21.49 0 0 1 3.308-2.724m0 0c3.063-2.026 5.99-2.641 7.331-1.3 1.827 1.828.026 6.591-4.023 10.64-4.049 4.049-8.812 5.85-10.64 4.023-1.33-1.33-.736-4.218 1.249-7.253m6.083-6.11c-3.063-2.026-5.99-2.641-7.331-1.3-1.827 1.828-.026 6.591 4.023 10.64m3.308-9.34a21.497 21.497 0 0 1 3.308 2.724m2.775 3.386c1.985 3.035 2.579 5.923 1.248 7.253-1.336 1.337-4.245.732-7.295-1.275M14 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/></svg>';
            }

            $menu = Menu::create($validatedData);

            return response()->json([
                'data' => $menu,
                'message' => 'Menu created successfully',
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error('Error creating menu: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to create menu',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $menu = Menu::with('application', 'menuInduk')->findOrFail($id);

            return response()->json([
                'data' => $menu,
                'message' => 'Menu fetched successfully',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error fetching menu: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve menu',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'application_id' => 'required|uuid',
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'icon' => 'nullable|string',
            'description' => 'nullable',
            'order' => 'nullable|integer',
            'menu_id' => 'nullable|exists:menus,id|uuid',
        ]);

        try {
            $menu = Menu::findOrFail($id);

            $menu->update($validatedData);

            return response()->json([
                'data' => $menu,
                'message' => 'Menu updated successfully',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error updating menu: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to update menu',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
