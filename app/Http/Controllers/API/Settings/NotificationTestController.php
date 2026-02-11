<?php

namespace App\Http\Controllers\API\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class NotificationTestController extends Controller
{
    /**
     * Send a test notification
     */
    public function sendTest(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'nullable|exists:users,id',
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'type' => 'nullable|in:toast,notification',
            ]);

            // Use provided user_id or current authenticated user
            $userId = $validatedData['user_id'] ?? $request->user()->id;
            $user = User::findOrFail($userId);


            // Send notification
            $test = $user->notify(new GeneralNotification(
                $user,
                $validatedData['title'],
                $validatedData['message'],
                $validatedData['url_action'] ?? 'tes',
                $validatedData['parameter_action'] ?? [],
                $validatedData['type'] ?? 'toast'
            ));

            Log::info('Test notification sent', [
                'user_id' => $user->id,
                'title' => $validatedData['title'],
                'type' => $validatedData['type'] ?? 'toast',
                'message' => $test,
                'channel' => "App.Models.User.{$user->id}",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test notification sent successfully',
                'data' => [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'channel' => "App.Models.User.{$user->id}",
                ],
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error sending test notification: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
