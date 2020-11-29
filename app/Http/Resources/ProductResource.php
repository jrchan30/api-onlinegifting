<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
        // if (auth()->user()) {
        //     $isLiked = $this->likes()->where('user_id', auth()->user()->id)->exists();
        // }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'images' => ImageResource::collection($this->images),
            'categories' => CategoryResource::collection($this->categories),
            'likes_count' => $this->likes->count(),
            'discussions' => DiscussionResource::collection($this->discussions),
            'reviews' => ReviewResource::collection($this->reviews),
            'weight' => $this->weight,
            'avg_rating' => $this->avgRating(),
            'deleted_at' => $this->deleted_at,
            'isLiked' => $isLiked
        ];
    }
}
