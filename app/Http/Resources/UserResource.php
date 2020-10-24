<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => Str::of($this->name)->title()->__toString(),
            'email' => $this->email,
            'photo' => $this->profile_photo_path ? Str::of(env('APP_URL'))->append($this->profile_photo_path)->__toString() : null
        ];
    }
}
