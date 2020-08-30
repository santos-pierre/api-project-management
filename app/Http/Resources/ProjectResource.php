<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'slug' => $this->slug,
            'title' => Str::of($this->title)->title()->__toString(),
            'author' => $this->owner->name,
            'description' => $this->description,
            'deadline' => Carbon::create($this->deadline)->unix(),
            'repository_url' => $this->repository_url,
            'status' => $this->status->name,
        ];
    }
}
