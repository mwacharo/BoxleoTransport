<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function riderLogin(Request $request)
    {

        Log::info('Request data:', $request->all());
        // Log::info('Login attempt:', ['email' => $request->email]);
        // dd($request->all());

        // dd($request->all());

        // dd($request);

        // return 0;
        // Log the incoming request data (excluding sensitive information)

        // Validate the request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        // dd($credentials);

        // Log the credentials being used for authentication
        Log::info('Credentials:', $credentials);

        if (Auth::attempt($credentials)) {
            $rider = Auth::user();
            // dd($rider);

            // Log the authenticated user
            Log::info('Authenticated user:', ['id' => $rider->id, 'email' => $rider->email]);

            $token = $rider->createToken('RiderApp')->accessToken;

            // Log the generated token
            Log::info('Generated token:', ['token' => $token]);

            return response()->json([
                'success' => true,
                'token' => $token,
                'rider' => $rider
            ]);
        }

        // Log a failed login attempt
        Log::warning('Login failed:', ['email' => $request->input('email')]);

        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password'
        ], 401);
    }
}
