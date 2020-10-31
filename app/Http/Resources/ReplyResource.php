<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReplyResource extends JsonResource
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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'discussion_id' => $this->discussion_id,
            'body' => $this->body,
            'user' => new UserResource($this->user),
            'created_at' => $this->created_at->diffForHumans()
        ];
    }
}
