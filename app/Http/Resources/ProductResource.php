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
        if (auth()->user()) {
            $isLiked = $this->likes()->where('user_id', auth()->user()->id)->exists();
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'images' => ImageResource::collection($this->images),
            'main_image' => $this->images[0]->url ?? 'not found',
            'categories' => CategoryResource::collection($this->categories),
            'likes_count' => $this->likes_count ?? $this->likes->count(),
            // 'avg_rating' => round($this->avg_rating, 1) ?? round($this->avgRating(), 1) ?? 0,
            'avg_rating' => $this->avg_rating ?? $this->avgRating() ?? 0,
            'discussions' => DiscussionResource::collection($this->discussions),
            'reviews' => ReviewResource::collection($this->reviews),
            'weight' => $this->weight,
            'deleted_at' => $this->deleted_at,
            'isLiked' => $isLiked,
            'type' => 'product'
            // 'avg_rating' => $this->avgRating(),
            // 'rate_test' => $this->reviews_count,
            // 'likes_count' => $this->likes->count(),
        ];
    }
}
