<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DetailResource extends JsonResource
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
            'colour' => $this->colour,
            'design' => $this->design,
            'image' => new ImageResource($this->image),
            'categories' => CategoryResource::collection($this->categories),
        ];
    }
}
