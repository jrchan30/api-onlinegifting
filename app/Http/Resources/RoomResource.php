<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = User::find($this->user_id);
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'admin_id' => $this->admin_id,
            'user' => $user,
            'messages' => MessageResource::collection($this->messages),
        ];
    }
}
