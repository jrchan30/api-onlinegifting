<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'room_id' => $this->room_id,
            'user_id' => $this->user_id,
            'message' => $this->message,
            // 'user' => new UserResource($this->whenLoaded('user'))
            'user' => new UserResource($this->user),
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
