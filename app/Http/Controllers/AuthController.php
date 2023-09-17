<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\SentVerificationCodeMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public  function register(Request $request)
    {

        $request->validate([
            'email' => 'required|email:unique:users'
        ]);
        $randomCode = Str::random(6);
        $expiresAt = Carbon::now()->addMinutes(Config::get('const.otp_valid_till'));
        User::create([
            'email' => $request->email,
            'verification_code' => $randomCode,
            'expires_at' =>  $expiresAt
        ]);

        Mail::to($request->email)->send(new SentVerificationCodeMail($randomCode));

        return response()->json(['message' => 'Email sent successfully']);
    }

    function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'code' => 'required|exists:users,verification_code',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        //code expires
        if (Carbon::now()->gt($user->expires_at)) {
            return response()->json(['error' => 'Verification code has expired'], 400);
        }

        User::where('email', $request->email)->update([
            'is_verified' => 1
        ]);
        return response()->json(['message' => 'User verified successfully']);
    }
}
