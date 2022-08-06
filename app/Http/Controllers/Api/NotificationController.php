<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Http\JsonResponse;
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
        // dd($request->id);
        // dd($user->unreadNotifications[0]->data['invitationId']);
        $invitation = Invitation::findOrFail($request->id);
        $invitation->update(['color' => ""]);
        $user->unreadNotifications()->where('data', $request->id)->where('notifiable_id', $user->id)->update(['read_at' => now()]);
        return response()->json([
            "message" => "succes",
            "id" => $request->id,
        ]);
    }
}
