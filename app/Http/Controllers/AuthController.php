<?php

namespace App\Http\Controllers;

use App\Events\UserRegistered;
use App\Mail\WelcomeMail;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    // Register user
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Memicu event UserRegistered
        event(new UserRegistered($user));

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    // Login user
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // Cek apakah email sudah diverifikasi
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'message' => 'Email not verified. Please check your inbox for the verification link.'
            ], 403);
        }

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    // Logout user
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    // Request reset password link
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Create token
        $token = Str::random(60);

        // Save token to password_resets table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($token), 'created_at' => Carbon::now()]
        );

        // Send reset password email
        Mail::send('emails.reset-password', ['token' => $token], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password Notification');
        });

        return response()->json(['message' => 'Reset password link sent to your email.']);
    }

    // Reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check if the token is valid
        $passwordReset = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return response()->json(['message' => 'Invalid token'], 400);
        }

        // Update password for the user
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete password reset token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password has been reset successfully.']);
    }
}

