<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\UploadedFile;

class UpdateUserProfileController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->get('photo')) {
            echo 'Work';
        } else {
            echo 'Not Work';
        }
        // Validator::make($input, [
        //     'name' => ['required', 'string', 'max:255'],
        //     'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore(auth()->user()->id)],
        //     'photo' => ['nullable', 'image', 'max:1024'],
        // ])->validateWithBag('updateProfileInformation');

        // if (isset($input['photo'])) {
        //     auth()->user()->updateProfilePhoto($input['photo']);
        // }

        // auth()->user()->forceFill([
        //     'name' => $input['name'],
        //     'email' => $input['email'],
        // ])->save();
        // return response()->json($input->all());
    }
}
