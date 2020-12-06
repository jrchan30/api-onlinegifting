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
        $products = [];
        foreach ($this->products as $product) {
            array_push($products, array(
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $product->pivot->quantity,
                'stock' => $product->stock
            ));
        };
        return [
            'id' => $this->id,
            // 'user' => new UserResource($this->user),
            'name' => $this->name,
            'detail' => new DetailResource($this->detail),
            'products' => $products,
            'price' => $this->calculatePrice(),
        ];
    }
}
