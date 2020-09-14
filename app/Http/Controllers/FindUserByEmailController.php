<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class FindUserByEmailController extends Controller
{
    public function __invoke(Request $request)
    {
        return response([
            'alreadyTaken' => User::where('email', $request->email)->exists()
        ], 200);
    }
}
