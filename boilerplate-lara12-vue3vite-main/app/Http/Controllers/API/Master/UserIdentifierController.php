<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserIdentifier;
use App\Rules\UniqueUserIdentifier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class UserIdentifierController extends Controller
{
    /**
     * Store a new identifier for a user
     */
    public function store(Request $request, string $userId)
    {
        try {
            $validatedData = $request->validate([
                'type' => 'required|in:email,phone,username',
                'value' => ['required', 'string', new UniqueUserIdentifier($request->type)],
            ]);

            $user = User::findOrFail($userId);

            $identifier = UserIdentifier::create([
                'user_id' => $userId,
                'type' => $validatedData['type'],
                'value' => $validatedData['value'],
                'verified_at' => now(), // Auto-verify when creating
            ]);

            return response()->json([
                'data' => $identifier,
                'message' => 'Identifier added successfully',
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error('Error adding identifier: '.$e->getMessage());

            return response()->json([
                'message' => 'An error occurred while adding the identifier.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update an existing identifier
     */
    public function update(Request $request, string $userId, string $identifierId)
    {
        try {
            $identifier = UserIdentifier::where('user_id', $userId)
                ->where('id', $identifierId)
                ->firstOrFail();

            $validatedData = $request->validate([
                'value' => ['required', 'string', new UniqueUserIdentifier($identifier->type, $userId, $identifierId)],
            ]);

            $identifier->update([
                'value' => $validatedData['value'],
            ]);

            return response()->json([
                'data' => $identifier,
                'message' => 'Identifier updated successfully',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error updating identifier: '.$e->getMessage());

            return response()->json([
                'message' => 'An error occurred while updating the identifier.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete an identifier
     */
    public function destroy(string $userId, string $identifierId)
    {
        try {
            $identifier = UserIdentifier::where('user_id', $userId)
                ->where('id', $identifierId)
                ->firstOrFail();

            $identifier->delete();

            return response()->json([
                'message' => 'Identifier deleted successfully',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error deleting identifier: '.$e->getMessage());

            return response()->json([
                'message' => 'An error occurred while deleting the identifier.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Verify an identifier
     */
    public function verify(string $userId, string $identifierId)
    {
        try {
            $identifier = UserIdentifier::where('user_id', $userId)
                ->where('id', $identifierId)
                ->firstOrFail();

            $identifier->markAsVerified();

            return response()->json([
                'data' => $identifier,
                'message' => 'Identifier verified successfully',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error verifying identifier: '.$e->getMessage());

            return response()->json([
                'message' => 'An error occurred while verifying the identifier.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Restore a soft deleted identifier
     */
    public function restore(string $userId, string $identifierId)
    {
        try {
            $identifier = UserIdentifier::where('user_id', $userId)
                ->where('id', $identifierId)
                ->onlyTrashed()
                ->firstOrFail();

            $identifier->restore();

            // Auto-verify when restoring
            $identifier->update(['verified_at' => now()]);

            return response()->json([
                'data' => $identifier->fresh(),
                'message' => 'Identifier restored successfully',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error restoring identifier: '.$e->getMessage());

            return response()->json([
                'message' => 'An error occurred while restoring the identifier.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
