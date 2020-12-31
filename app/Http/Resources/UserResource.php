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
            'photo' => $this->parsePhotoUrl($this->profile_photo_path)
        ];
    }

    protected function parsePhotoUrl($url)
    {
        if (is_null($this->profile_photo_path)) {
            return null;
        }

        if (Str::of($url)->contains('github')) {
            return $this->profile_photo_path;
        }

        if ($this->profile_photo_path) {
            return Str::of(env('APP_URL'))->append($this->profile_photo_path)->__toString();
        }
    }
}
