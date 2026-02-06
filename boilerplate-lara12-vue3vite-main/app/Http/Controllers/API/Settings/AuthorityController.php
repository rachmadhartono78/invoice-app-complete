<?php

namespace App\Http\Controllers\API\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuthorityRequest;
use App\Http\Requests\UpdateAuthorityRequest;
use App\Models\Authority;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthorityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Authority::query();

            if ($request->has('limit')) {
                $limit = max(1, (int) $request->query('limit'));
                $query->limit($limit);
            }

            if ($request->has('offset')) {
                $offset = max(0, (int) $request->query('offset'));
                $query->offset($offset);
            }

            $data = $query->get();

            return response()->json([
                'data' => $data,
                'message' => 'Auhtority fetched successfully',
                'count' => Authority::count(),
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error fetching auhtority: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve auhtority',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorityRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create new Authority with UUID
            $authority = Authority::create([
                'id' => Str::uuid(),
                'application_id' => $request->input('application_id'),
                'code' => $request->input('code'),
                'name' => $request->input('name'),
            ]);

            // Get menu data from request
            $menus = collect($request->input('menus', []));

            // Prepare data for batch inserts
            $menuAuthorityData = [];
            $actionUsesData = [];

            foreach ($menus as $menu) {
                // Skip menus without valid ID
                if (empty($menu['id'])) {
                    continue;
                }

                $menuId = $menu['id'];
                $menuAuthorityId = Str::uuid(); // Generate UUID for pivot table

                $menuAuthorityData[] = [
                    'id' => $menuAuthorityId,
                    'menu_id' => $menuId,
                    'authority_id' => $authority->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // If actions exist, prepare data for batch insert
                if (! empty($menu['actions'])) {
                    foreach ($menu['actions'] as $act) {
                        // Skip actions with invalid data
                        if (empty($act['action_id']) || !isset($act['value'])) {
                            continue;
                        }

                        $actionUsesData[] = [
                            'id' => Str::uuid(),
                            'menu_authority_id' => $menuAuthorityId,
                            'value' => (int) $act['value'],
                            'action_id' => $act['action_id'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            // Batch insert menu_authorities
            if (! empty($menuAuthorityData)) {
                DB::table('menu_authorities')->insert($menuAuthorityData);
            }

            // Batch insert action_uses
            if (! empty($actionUsesData)) {
                DB::table('action_uses')->insert($actionUsesData);
            }

            DB::commit();

            return response()->json([
                'data' => $authority->load('menuAuthorities.menu', 'menuAuthorities.actions.action'),
                'message' => 'Authority created successfully',
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating authority: '.$e->getMessage());
            Log::error('Request data: ', $request->all());

            return response()->json([
                'message' => 'Failed to create authority.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $authority = Authority::with([
                'menuAuthorities.menu',
                'menuAuthorities.actions.action'
            ])->findOrFail($id);

            // Transform the data structure for frontend
            $authority->menus = $authority->menuAuthorities->map(function ($menuAuthority) {
                // Skip if menu is null (broken relationship)
                if (!$menuAuthority->menu) {
                    return null;
                }
                
                return [
                    'id' => $menuAuthority->menu->id,
                    'name' => $menuAuthority->menu->name,
                    'url' => $menuAuthority->menu->url,
                    'actions' => $menuAuthority->actions->map(function ($actionUse) {
                        // Skip if action is null (broken relationship)
                        if (!$actionUse->action) {
                            return null;
                        }
                        
                        return [
                            'action' => $actionUse->action,
                            'action_id' => $actionUse->action_id,
                            'value' => $actionUse->value
                        ];
                    })->filter() // Remove null values
                ];
            })->filter(); // Remove null values

            // Remove the menuAuthorities relation from response
            unset($authority->menuAuthorities);

            return response()->json([
                'data' => $authority,
                'message' => 'Authority fetched successfully',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error fetching authority: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve authority',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateAuthorityRequest $request, string $id)
    {
        try {
            DB::beginTransaction();

            // Find authority or fail
            $authority = Authority::findOrFail($id);

            // Update authority details
            $authority->update($request->only(['application_id', 'code', 'name']));

            // Get menu data from request
            $menus = collect($request->input('menus', []));

            // Delete existing menu authorities and their action uses
            $existingMenuAuthorityIds = $authority->menuAuthorities()->pluck('id');
            if ($existingMenuAuthorityIds->isNotEmpty()) {
                DB::table('action_uses')->whereIn('menu_authority_id', $existingMenuAuthorityIds)->delete();
                $authority->menuAuthorities()->delete();
            }

            // Prepare pivot data for batch insert
            $menuAuthorityData = [];
            $actionUsesData = [];

            foreach ($menus as $menu) {
                // Skip menus without valid ID
                if (empty($menu['id'])) {
                    continue;
                }

                $menuId = $menu['id'];
                $menuAuthorityId = Str::uuid(); // Generate unique UUID for pivot table

                $menuAuthorityData[] = [
                    'id' => $menuAuthorityId,
                    'menu_id' => $menuId,
                    'authority_id' => $authority->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // If actions exist, prepare data for batch insert
                if (! empty($menu['actions'])) {
                    foreach ($menu['actions'] as $act) {
                        // Skip actions with invalid data
                        if (empty($act['action_id']) || !isset($act['value'])) {
                            continue;
                        }

                        $actionUsesData[] = [
                            'id' => Str::uuid(),
                            'menu_authority_id' => $menuAuthorityId,
                            'value' => (int) $act['value'],
                            'action_id' => $act['action_id'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            // Batch insert menu_authorities
            if (! empty($menuAuthorityData)) {
                DB::table('menu_authorities')->insert($menuAuthorityData);
            }

            // Batch insert action_uses
            if (! empty($actionUsesData)) {
                DB::table('action_uses')->insert($actionUsesData);
            }

            DB::commit();

            return response()->json([
                'data' => $authority->load('menuAuthorities.menu', 'menuAuthorities.actions.action'),
                'message' => 'Authority updated successfully',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating authority: '.$e->getMessage());
            Log::error('Request data: ', $request->all());

            return response()->json([
                'message' => 'Failed to update authority.',
                'error' => $e->getMessage(),
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
