<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BaseController
{
    public function register(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'pharmacy_name' => 'required',
            'phone_number' => 'required',
            'password' => 'required|min:8',
        ]);

        if($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $input['password'] = Hash::make($input['password']);
        $input['role_id'] = 2 ;

        $user = User::create($input);
        // just to send it to the API
        $user['accessToken'] =  $user->createToken('Personal Access Token')->accessToken;
        return $this->sendResponse($user);
    }
}
