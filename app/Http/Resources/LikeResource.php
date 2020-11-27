<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class LikeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // $products = Product::where('userid', )
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'items' => $this->likeable,
            // 'bundles' => $this->bundles,
        ];
    }
}
