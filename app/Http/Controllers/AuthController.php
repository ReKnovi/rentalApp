<?php

// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Services\SmsService;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|unique:users|regex:/^[0-9]{10}$/',
            'password' => 'required|min:6',
            'role' => 'required|in:tenant,landlord,admin', // Ensure valid role
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email ?? null ,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User registered successfully.',
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('phone_number', 'password');


        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = auth()->user();
        $token = JWTAuth::claims(['role' => $user->role])->fromUser($user);

        return response()->json([
            'message' => 'Logged in successfully',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return response()->json(['token' => JWTAuth::refresh(JWTAuth::getToken())]);
    }

    //     public function register(Request $request, SmsService $smsService)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'phone_number' => 'required|unique:users|regex:/^[0-9]{10}$/',
    //         'password' => 'required|min:6',
    //         'role' => 'required|in:tenant,landlord,admin',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 400);
    //     }

    //     // Generate OTP
    //     $otp = rand(100000, 999999);

    //     // Cache OTP for 10 minutes
    //     Cache::put("otp_{$request->phone_number}", $otp, now()->addMinutes(10));

    //     // Send OTP
    //     if (!$smsService->sendOtp($request->phone_number, $otp)) {
    //         return response()->json(['error' => 'Failed to send OTP.'], 500);
    //     }

    //     // Temporarily store user data in the session or cache until OTP is verified
    //     Cache::put("register_data_{$request->phone_number}", $request->all(), now()->addMinutes(10));

    //     return response()->json(['message' => 'OTP sent to your phone number for verification.']);
    // }

    // // Verify OTP for registration
    // public function verifyRegisterOtp(Request $request)
    // {
    //     $request->validate([
    //         'phone_number' => 'required|regex:/^[0-9]{10}$/',
    //         'otp' => 'required|digits:6',
    //     ]);

    //     // Retrieve OTP from cache
    //     $cachedOtp = Cache::get("otp_{$request->phone_number}");

    //     if (!$cachedOtp || $cachedOtp != $request->otp) {
    //         return response()->json(['error' => 'Invalid or expired OTP.'], 400);
    //     }

    //     // Retrieve registration data from cache
    //     $registerData = Cache::get("register_data_{$request->phone_number}");

    //     if (!$registerData) {
    //         return response()->json(['error' => 'Registration data expired. Please try again.'], 400);
    //     }

    //     // Create the user
    //     $user = User::create([
    //         'name' => $registerData['name'] ?? null,
    //         'email' => $registerData['email'] ?? null,
    //         'phone_number' => $registerData['phone_number'],
    //         'password' => Hash::make($registerData['password']),
    //         'role' => $registerData['role'],
    //     ]);

    //     // Clear OTP and registration data from cache
    //     Cache::forget("otp_{$request->phone_number}");
    //     Cache::forget("register_data_{$request->phone_number}");

    //     // Generate JWT token
    //     $token = JWTAuth::fromUser($user);

    //     return response()->json([
    //         'message' => 'User registered successfully.',
    //         'token' => $token,
    //     ], 201);
    // }
    //     public function sendLoginOtp(Request $request, SmsService $smsService)
    // {
    //     $request->validate([
    //         'phone_number' => 'required|regex:/^[0-9]{10}$/',
    //     ]);

    //     // Check if the phone number exists in the database
    //     $user = User::where('phone_number', $request->phone_number)->first();

    //     if (!$user) {
    //         return response()->json(['error' => 'User with this phone number does not exist.'], 404);
    //     }

    //     // Generate OTP
    //     $otp = rand(100000, 999999);

    //     // Cache OTP for 10 minutes
    //     Cache::put("otp_{$request->phone_number}", $otp, now()->addMinutes(10));

    //     // Send OTP
    //     if (!$smsService->sendOtp($request->phone_number, $otp)) {
    //         return response()->json(['error' => 'Failed to send OTP.'], 500);
    //     }

    //     return response()->json(['message' => 'OTP sent to your phone number for login verification.']);
    // }

    // // Verify OTP and Login
    // public function verifyLoginOtp(Request $request)
    // {
    //     $request->validate([
    //         'phone_number' => 'required|regex:/^[0-9]{10}$/',
    //         'otp' => 'required|digits:6',
    //         'password' => 'required|min:6',
    //     ]);

    //     // Retrieve OTP from cache
    //     $cachedOtp = Cache::get("otp_{$request->phone_number}");

    //     if (!$cachedOtp || $cachedOtp != $request->otp) {
    //         return response()->json(['error' => 'Invalid or expired OTP.'], 400);
    //     }

    //     // Authenticate user
    //     $credentials = $request->only('phone_number', 'password');

    //     if (!$token = JWTAuth::attempt($credentials)) {
    //         return response()->json(['error' => 'Invalid credentials.'], 401);
    //     }

    //     $user = auth()->user();

    //     // Clear OTP from cache
    //     Cache::forget("otp_{$request->phone_number}");

    //     return response()->json([
    //         'message' => 'Logged in successfully.',
    //         'token' => $token,
    //         'user' => $user,
    //     ]);
    // }
}
