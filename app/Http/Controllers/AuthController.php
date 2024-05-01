<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function handleGoogleSignIn(Request $request): JsonResponse
    {
        $code = $request->code;

        try {
            // Retrieve the access token using the code provided by the client
            $token = Socialite::driver('google')
                ->stateless()
                ->getAccessTokenResponse($code);

            $googleUser = Socialite::driver('google')
                ->stateless()
                ->userFromToken($token['access_token']);

            // Check if the user exists or create a new one
            $existingUser = User::where('email', $googleUser->email)->first();
            if ($existingUser) {
                auth()->login($existingUser, true);
                $user            = auth()->user();
                $user->google_id = $googleUser->id;
                $user->save();
            } else {
                $newUser            = new User;
                $newUser->email     = $googleUser->email;
                $newUser->name      = $googleUser->name;
                $newUser->google_id = $googleUser->id;
                $newUser->save();

                auth()->login($newUser, true);
            }

            $authToken = auth()->user()->createToken('authToken')->plainTextToken;

            return response()->json([
                'token'   => $authToken,
                'message' => 'Login successful',
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Authentication failed: ' . $e->getMessage()], 401);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('authToken')->plainTextToken;

            return response()->json([
                'token'   => $token,
                'message' => 'Login successful',
            ]);
        }

        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }
}
