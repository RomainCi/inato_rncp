<?php

namespace App\Http\Controllers\Api;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Notifications\InvitationNotif;
use Illuminate\Notifications\Notifiable;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();
        return response()->json([
            "message" => "succes",
            "nombreNotif" => $user->unreadNotifications->count()
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = auth()->user();
        $request->validate([
            "id" => 'integer|required'
        ]);
        foreach ($user->unreadNotifications as  $value) {
            if ($value->data["invitation"][0]["id"] == $request->id) {
                $value->markAsRead();
                $readAt = $value->read_at;
            }
        };
        return response()->json([
            "message" => "succes",
            "id" => $request->id,
            "readAt" => $readAt
        ]);
    }
}
