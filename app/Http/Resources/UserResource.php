<?php

namespace App\Http\Resources;

use App\Models\Bundle;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $detail = null;
        // $cart = null;
        // $liked_products = null;
        // $liked_bundles = null;
        if (auth()->user()) {
            if (auth()->user()->id == $this->id) {
                $detail = new UserDetailResource($this->userDetail);
                // $cart = new CartResource($this->cart);

                // $liked_products = Product::whereHas('likes', function ($query) {
                //     $query->where('user_id', auth()->user()->id);
                // })->latest()->get();

                // $liked_bundles = Bundle::whereHas('likes', function ($query) {
                //     $query->where('user_id', auth()->user()->id);
                // })->latest()->get();
            }
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'detail' => $detail,
            'type' => $this->userDetail->type ?? 'customer',
            'created_at' => $this->created_at->diffForHumans(),
            // 'cart' => $cart,
            // 'liked_products' => $liked_products ? ProductResource::collection($liked_products) : null,
            // 'liked_bundles' => $liked_bundles ? BundleResource::collection($liked_bundles) : null,
        ];
    }
}
