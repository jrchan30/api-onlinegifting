<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'body' => $this->body,
            'rating' => $this->rating,
            'user_id' => $this->user_id,
            'paid_product_id' => $this->paid_product_id,
            'paid_bundle_id' => $this->paid_bundle_id,
            'user' => new UserResource($this->user),
            'created_at' => $this->created_at->diffForHumans(),
            'type' => $this->reviewable_type,
            'item_name' => $this->reviewable->name
        ];
    }
}
