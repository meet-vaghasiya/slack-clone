<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\SentVerificationCodeMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PhpParser\Node\Stmt\TryCatch;

class AuthController extends Controller
{
    public function index()
    {
        return response()->json(['user' => Auth::user()]);
    }

    public  function signin(Request $request)
    {

        $request->validate([
            'email' => 'required|email'
        ]);
        $randomCode = Str::random(6);
        $expiresAt = Carbon::now()->addMinutes(Config::get('const.otp_valid_till'));

        User::updateOrCreate(['email' => $request->email], [
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
            'otp' => 'required|exists:users,verification_code',
        ]);

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
        return response()->json(['message' => 'User verified successfully', 'token'  => $user->createToken("API TOKEN")->plainTextToken, 'user' => $user]);
    }

    function isValid(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        $isExist =   User::where(['email' => $request->email, 'is_verified' => 1])->exists();
        return response()->json(['exist' => $isExist]);
    }
}
