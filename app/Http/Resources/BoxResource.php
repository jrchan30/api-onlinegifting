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
                'stock' => $product->stock,
                'weight' => $product->weight,
                'main_image' => $product->images[0]->url ?? 'not found',
            ));
        };
        return [
            'id' => $this->id,
            'type' => 'box',
            'price' => $this->calculatePrice(),
            'weight' => $this->calculateWeight(),
            'name' => $this->name,
            'detail' => new DetailResource($this->detail),
            'products' => $products,
        ];
    }
}
