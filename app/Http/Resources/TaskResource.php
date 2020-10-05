<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'body' => Str::of($this->body)->title()->__toString(),
            'owner' => new UserResource($this->owner),
            'project' => new ProjectResource($this->project),
            'done' => (bool) $this->done,
            'id' => $this->id
        ];
    }
}
