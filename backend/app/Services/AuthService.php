<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Events\UserLoggedIn;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;

class AuthService
{
    // ---------------- REGISTER ----------------
    public function register(array $data)
    {
        if (User::where('email', $data['email'])->exists()) {
            return $this->fail('البريد الإلكتروني مستخدم بالفعل');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'] ?? UserRole::USER, // default role
            'password' => Hash::make($data['password']),
        ]);
        
             $user->sendEmailVerificationNotification();


        return $this->tokenResponse($user);
    }

    // ---------------- LOGIN ----------------
    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return $this->fail('البريد او كلمة السر غير صحيحة');
        }

        $user->last_login = now();
        $user->save();

        return $this->tokenResponse($user);
    }

    // ---------------- REFRESH TOKEN ----------------
    public function refresh($userId, $refreshToken)
    {
        $user = User::find($userId);

        if (! $user ||
            $user->refresh_token !== $refreshToken ||
            $user->refresh_token_expiration_at < now()
        ) {
            return $this->fail('Refresh token غير صالح');
        }

        return $this->tokenResponse($user);
    }

    // ---------------- LOGOUT ----------------
  public function logout()
{
    // 1️⃣ Get the refresh token from the request cookie
    $refreshToken = request()->cookie('refreshToken');

    // 2️⃣ If the token exists, find the user and clear it
    if ($refreshToken) {
        $user = User::where('refresh_token', $refreshToken)->first();
        if ($user) {
            $user->refresh_token = null;
            $user->refresh_token_expiration_at = null;
            $user->save();
        }
    }

    // 3️⃣ Delete the refresh token cookie
    Cookie::queue(Cookie::forget('refreshToken'));

    return [
        'isSuccess' => true,
        'information' => 'Logged out successfully'
    ];
}

   private function tokenResponse(User $user)
{
    // Generate refresh token first
    $refreshToken = Str::random(64);

    $user->refresh_token = $refreshToken;
    $user->refresh_token_expiration_at = now()->addDays(30);
    $user->save(); // <-- save BEFORE JWTAuth

    // Generate access token AFTER updating refresh token
    $accessToken = JWTAuth::fromUser($user);

    // Set refresh token cookie
    // Cookie::queue(
    // 'refreshToken',        // name
    // $refreshToken,         // value
    // 60*24*30,              // minutes = 30 days
    // '/',                   // path
    // null,                  // domain
    // false,                 // secure (true if HTTPS)
    // false,                 // httpOnly
    // false,                 // raw
    // 'Lax'                  // SameSite
    // );

        Cookie::queue(
        'refreshToken',
        $refreshToken,
        60*24*30,
        '/',
        null,
        false,
        false,
        false,
        'Strict'
    );

    return $this->success([
        'accessToken' => $accessToken,
        'user' => $user
    ]);
}



    // ---------------- PRIVATE: SUCCESS RESPONSE ----------------
    private function success($data)
    {
        return ['isSuccess'=>true, 'data'=>$data];
    }

    // ---------------- PRIVATE: FAIL RESPONSE ----------------
    private function fail($message)
    {
        return ['isSuccess'=>false, 'information'=>$message];
    }
}
