<?php

namespace App\Http\Controllers\API\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Models\Organization;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Organization::query();

            if ($request->has('type')) {
                $types = $request->type;
                if (! is_array($types)) {
                    $types = [$types];
                }
                $query->whereIn('type', $types);
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%");
                });
            }

            if ($request->has('limit')) {
                $limit = max(1, (int) $request->query('limit'));
                $query->limit($limit);
            }

            if ($request->has('offset')) {
                $offset = max(0, (int) $request->query('offset'));
                $query->offset($offset);
            }

            // Load parent organization relationship
            $query->with('parent:id,name');

            $data = $query->orderBy('name')->get();

            return response()->json([
                'data' => $data,
                'message' => 'Organization fetched successfully',
                'count' => Organization::count(),
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error fetching organization: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve organization',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganizationRequest $request)
    {
        try {
            DB::beginTransaction();

            $organization = Organization::create([
                'id' => Str::uuid(),
                'name' => $request->input('name'),
                'code' => $request->input('code'),
                'type' => $request->input('type'),
                'city' => $request->input('city'),
                'address' => $request->input('address'),
                'longitude' => $request->input('longitude'),
                'latitude' => $request->input('latitude'),
                'organization_id' => $request->input('organization_id'),
                'is_active' => $request->input('is_active', true),
            ]);

            DB::commit();

            return response()->json([
                'data' => $organization->load('parent:id,name'),
                'message' => 'Organization created successfully',
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating organization: '.$e->getMessage());
            Log::error('Request data: ', $request->all());

            return response()->json([
                'message' => 'Failed to create organization.',
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
            $organization = Organization::with('parent:id,name', 'children:id,name,organization_id')
                ->findOrFail($id);

            return response()->json([
                'data' => $organization,
                'message' => 'Organization fetched successfully',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error fetching organization: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve organization',
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrganizationRequest $request, string $id)
    {
        try {
            DB::beginTransaction();

            $organization = Organization::findOrFail($id);

            $organization->update([
                'name' => $request->input('name'),
                'code' => $request->input('code'),
                'type' => $request->input('type'),
                'city' => $request->input('city'),
                'address' => $request->input('address'),
                'longitude' => $request->input('longitude'),
                'latitude' => $request->input('latitude'),
                'organization_id' => $request->input('organization_id'),
                'is_active' => $request->input('is_active', true),
            ]);

            DB::commit();

            return response()->json([
                'data' => $organization->load('parent:id,name'),
                'message' => 'Organization updated successfully',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating organization: '.$e->getMessage());
            Log::error('Request data: ', $request->all());

            return response()->json([
                'message' => 'Failed to update organization.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $organization = Organization::findOrFail($id);

            // Check if organization has children
            if ($organization->children()->count() > 0) {
                return response()->json([
                    'message' => 'Cannot delete organization that has child organizations.',
                ], Response::HTTP_CONFLICT);
            }

            // Check if organization has users
            if ($organization->users()->count() > 0) {
                return response()->json([
                    'message' => 'Cannot delete organization that has users assigned.',
                ], Response::HTTP_CONFLICT);
            }

            $organization->delete();

            DB::commit();

            return response()->json([
                'message' => 'Organization deleted successfully',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting organization: '.$e->getMessage());

            return response()->json([
                'message' => 'Failed to delete organization.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
