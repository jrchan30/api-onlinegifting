<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BoxResource extends JsonResource
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
            'user' => new UserResource($this->user),
            'name' => $this->name,
            // 'price' => $this->price,
            'detail' => new DetailResource($this->detail),
            'products' => ProductResource::collection($this->products),
            'productsWithTrashed' => ProductResource::collection(
                $this->whenLoaded('products')->withTrashed()->get()
            ),
        ];
    }
}
