<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BundleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $isLiked = false;
        if (auth()->user()) {
            $isLiked = $this->likes()->where('user_id', auth()->user()->id)->exists();
        }

        return [
            'id' => $this->id,
            'type' => 'bundle',
            'price' => $this->calculatePrice(),
            'weight' => $this->calculateWeight(),
            'description' => $this->description,
            'name' => $this->name,
            'detail' => new DetailResource($this->detail),
            'products' => ProductResource::collection($this->products),
            'likes_count' => $this->likes->count(),
            'discussions' => DiscussionResource::collection($this->discussions),
            'reviews' => ReviewResource::collection($this->reviews),
            'isLiked' => $isLiked,
        ];
    }
}
