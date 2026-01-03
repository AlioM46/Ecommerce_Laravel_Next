<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // ---------------- REGISTER ----------------
    public function register(RegisterRequest $request)
    {

        $data = $request->validated(); 

        // $data = $request->only('name','email','password','role');
        $result = $this->authService->register($data);

        if (!$result['isSuccess'])
            return response()->json($result, 409); // Conflict

        return response()->json($result, 201); // Created
    }

    // ---------------- LOGIN ----------------
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        
        $result = $this->authService->login($data);

        if (!$result['isSuccess'])
            return response()->json($result, 401); // Unauthorized

        return response()->json($result, 200); // OK
    }

    // ---------------- REFRESH TOKEN ----------------
    public function refresh($userId)
{
    $refreshToken = request()->cookie('refreshToken');

    if (!$refreshToken) {
        return response()->json([
            'isSuccess' => false,
            'information' => 'No refresh token'
        ], 401);
    }

    $result = $this->authService->refresh($userId, $refreshToken);

    if (!$result['isSuccess']) {
        return response()->json($result, 401);
    }

    return response()->json($result, 200);
}


    // ---------------- LOGOUT ----------------
    public function logout()
    {
        $this->authService->logout();
        return response()->json(['isSuccess'=>true,'information'=>'Logged out successfully'], 200);
    }
}
