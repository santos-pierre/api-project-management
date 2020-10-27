<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class UpdateUserProfileController extends Controller
{
    public function updateUserInfo(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'min:8', 'confirmed'],
            'photo' => ['image', 'max:1024'],
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $img = Image::make($file);
            $img->resize(500, 500);
            $destination = public_path('images/profiles/');
            $rand = time() . Str::random(6) . date('h-i-s') . '.' . $file->extension();
            $img->save($destination . $rand);
            $user->profile_photo_path = 'images/profiles/' . $rand;
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        if ($data['password']) {
            $user->password = bcrypt($data['password']);
        }

        $user->save();

        return response(new UserResource($user));
    }
}
