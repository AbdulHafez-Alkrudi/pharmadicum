<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends BaseController
{
    public function login(Request $request)
    {
        if (Auth::attempt(['phone_number' => $request->phone_number, 'password' => $request->password])) {
            $accessToken = $request->user()->createToken('Personal Access Token')->accessToken ;
            $user = Auth::user();
            $user['accessToken'] = $accessToken;
            return $this->sendResponse($user , 'user');
        }
        return $this->sendError( ['error' => 'Unauthorised']);
    }

    public function userInfo()
    {
        $user = auth()->user();
        return response()->json(['user' => $user], 200);
    }
}
