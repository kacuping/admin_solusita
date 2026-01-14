<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\FcmService;

class SettingController extends Controller
{
    protected $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    public function index()
    {
        // Ambil user yang memiliki device_token
        $usersWithToken = User::whereNotNull('device_token')->where('role', 'pelanggan')->get();
        return view('settings.index', compact('usersWithToken'));
    }

    public function sendTestNotification(Request $request)
    {
        $request->validate([
            'target_type' => 'required|in:specific_user,manual_token',
            'user_id' => 'required_if:target_type,specific_user',
            'manual_token' => 'required_if:target_type,manual_token',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $token = null;

        if ($request->target_type === 'specific_user') {
            $user = User::find($request->user_id);
            if (!$user || !$user->device_token) {
                return back()->with('error', 'User tidak ditemukan atau tidak memiliki token.');
            }
            $token = $user->device_token;
        } else {
            $token = $request->manual_token;
        }

        try {
            $this->fcmService->sendToToken(
                $token,
                $request->title,
                $request->body,
                ['type' => 'test_notification']
            );
            return back()->with('success', 'Notifikasi berhasil dikirim ke FCM!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim notifikasi: ' . $e->getMessage());
        }
    }
}
