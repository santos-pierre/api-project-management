<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FindUserByEmailController extends Controller
{
    public function __invoke(Request $request)
    {
        if (auth()->user()) {
            return response([
                'alreadyTaken' => User::where('id', '!=', auth()->user()->id)->where('email', $request->email)->exists()
            ], 200);
        } else {
            return response([
                'alreadyTaken' => User::where('email', $request->email)->exists()
            ], 200);
        }
    }
}
