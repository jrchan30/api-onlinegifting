<?php

namespace App\Http\Resources;

use App\Models\Bundle;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return [
        //     'id' => $this->id,
        //     'name' => $this->name,
        //     'parent_name' => $this->parentCategory ? $this->parentCategory['name'] : null,
        //     // 'subCategory' => count($this->allSubCategories) < 1 ? null : CategoryResource::collection($this->allSubCategories),
        //     'subCategory' => CategoryResource::collection($this->allSubCategories),
        //     // 'testSub' => $this->with($this->subCategories)->whereNull('category_id'),
        // ];

        return [
            'id' => $this->id,
            'label' => $this->name,
            'children' => count($this->allSubCategories) < 1 ? null : CategoryResource::collection($this->allSubCategories),
            // 'subCategory' => CategoryResource::collection($this->allSubCategories),
            // 'testSub' => $this->with($this->subCategories)->whereNull('category_id'),
        ];
    }
}
