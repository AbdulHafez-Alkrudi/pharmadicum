<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends BaseController
{

    public function __invoke(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();
        return response()->json([
           'status' => 'success'
        ]);
    }
}
