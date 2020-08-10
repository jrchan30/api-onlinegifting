<?php

namespace App\Http\Controllers;

use App\Http\Resources\BundleResource;
use App\Http\Resources\UserResource;
use App\Models\Bundle;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BundleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BundleResource::collection(Bundle::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (auth()->user()->userDetail->type == 'admin') {
            $validated = $this->validate($request, [
                'name' => 'required',
                'products' => 'required|array',
            ]);

            $calculatedPrice = Product::whereIn('id', $validated['products'])->sum('price');

            $id = auth()->user()->id;

            $bundle = Bundle::create([
                'user_id' => $id,
                'name' => $validated['name'],
                'price' => $calculatedPrice,
            ]);

            $bundle->products()->attach($validated['products']);

            return new BundleResource($bundle);
        } else {
            return response(['Error' => 'Forbidden'], 403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bundle  $bundle
     * @return \Illuminate\Http\Response
     */
    public function show(Bundle $bundle)
    {
        return new BundleResource($bundle);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bundle  $bundle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bundle $bundle)
    {
        $validated = $this->validate($request, [
            'name' => 'sometimes',
            'products' => 'sometimes|array',
        ]);

        $user = auth()->user()->id;

        if ($request->has('products')) {
            // $validated['price'] = Product::whereIn('id', $validated['products'])->sum('price');
            $bundle->products()->sync($validated['products']);
        }

        if ($bundle->user_id == $user) {
            $bundle->update($validated);
            return new BundleResource($bundle);
        } else {
            return response()->json(['Error' => 'Forbidden Not Your Bundle'], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bundle  $bundle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bundle $bundle)
    {
        $own = $bundle->user_id == auth()->user()->id;

        if ($own) {
            $bundle->delete();
            return response('', 204);
        } else {
            return response()->json(['Error' => 'Forbidden Not Your Bundle'], 403);
        }
    }
}
