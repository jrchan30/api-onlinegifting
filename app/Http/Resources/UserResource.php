<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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
        $detail = null;
        if (auth()->user()) {
            if (auth()->user()->id == $this->id) {
                $detail = new UserDetailResource($this->userDetail);
            }
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            // 'detail' => new UserDetailResource($this->userDetail),
            'detail' => $detail,
            'type' => $this->userDetail->type ?? 'customer'
        ];
    }
}
