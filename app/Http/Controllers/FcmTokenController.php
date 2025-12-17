<?php

namespace App\Http\Controllers;

use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FcmTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|string',
        ]);

        $request->user()->fcmTokens()->updateOrCreate(
            ['token' => $request->token],
            ['device_type' => $request->device_type]
        );

        return response()->json(['status' => 'ok']);
    }

    public function testNotification(FirebaseNotificationService $firebase) {
        $user = Auth::user();
        foreach ($user->fcmTokens as $token) {
            $firebase->sendToToken($token, "Test", "Hi, This is test\n We'll do it. :)");
        }
        return "Sent Notification";
    }
}
