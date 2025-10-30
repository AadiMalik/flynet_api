<?php

namespace App\Http\Controllers;

use App\Enums\ResponseMessage;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ResponseAPI;
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator->errors());
        }

        if (!$token = Auth::attempt($request->only('email', 'password'))) {
            return $this->error(ResponseMessage::INVALID_LOGIN);
        }

        $user = Auth::user()->load('business'); // eager load relation

        if (!$user->business) {
            Auth::logout();
            JWTAuth::invalidate($token);
            return $this->error(ResponseMessage::INVALID_COMPANY_ATTACHED);
        }

        $subscriptionEnd = $user->business->subscription_end_date;

        if ($subscriptionEnd && Carbon::now()->gt(Carbon::parse($subscriptionEnd))) {
            Auth::logout();
            JWTAuth::invalidate($token);
            return $this->error(ResponseMessage::SUBSCRIPTION_EXPIRED);
        }
        $user->token = $token;

        return $this->success($user, ResponseMessage::LOGIN, 200);
    }
}
