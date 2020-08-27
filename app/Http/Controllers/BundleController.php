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
    public function __construct()
    {
        $this->middleware('check.admin', ['except' => ['index', 'show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bundles = Bundle::latest()->paginate(10);
        return BundleResource::collection($bundles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validated = $this->validate($request, [
            'name' => 'required',
            'products' => 'required|array',
        ]);

        $calculatedPrice = Product::whereIn('id', $validated['products'])->sum('price');

        $id = auth()->user()->id;

        $bundle = Bundle::create([
            'user_id' => $id,
            'name' => $validated['name'],
            // 'price' => $calculatedPrice,
        ]);

        $bundle->products()->attach($validated['products']);

        return new BundleResource($bundle);
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

        if ($request->has('products')) {
            // $validated['price'] = Product::whereIn('id', $validated['products'])->sum('price');
            $bundle->products()->sync($validated['products']);
        }

        $bundle->update($validated);
        return new BundleResource($bundle);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bundle  $bundle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bundle $bundle)
    {
        $bundle->delete();
        return response('', 204);
    }
}
