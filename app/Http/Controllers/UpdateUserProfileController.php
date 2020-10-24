<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateUserProfileController extends Controller
{
    public function updateUserInfo(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'photo' => 'nullable|image|max:1024',
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $name = $file->getClientOriginalName();
            $path = $file->storeAs('images/profiles', $name);
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        if ($request->hasFile('photo')) {
            $user->profile_photo_path = $path;
        }


        $user->save();

        return response(new UserResource($user));
    }
}
