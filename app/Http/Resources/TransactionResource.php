<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'delivery_fee' => $this->delivery_fee,
            'total_price' => $this->total_price,
            'receiver_location' => $this->receiver_location,
            'arrival_date' => $this->arrival_date,
            'user' => new UserResource($this->user),
            'bundles' => BundleResource::collection($this->bundles),
            'boxes' => BoxResource::collection($this->boxes()->with('products')),
        ];
    }
}
