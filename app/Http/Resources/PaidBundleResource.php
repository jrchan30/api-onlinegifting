<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaidBundleResource extends JsonResource
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
            'bundle_id' => $this->bundle_id,
            'name' => $this->name,
            'path' => $this->path,
            'url' => $this->url,
            'paid_products' => PaidProductResource::collection($this->paidProducts),
        ];
    }
}
