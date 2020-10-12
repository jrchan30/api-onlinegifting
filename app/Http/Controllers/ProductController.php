<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.admin', ['except' => ['index', 'show', 'latestProducts', 'lowPrice']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ProductResource::collection(Product::latest()->paginate(10));
    }

    public function latestProducts()
    {
        return ProductResource::collection(Product::latest()->take(5)->get());
    }

    public function lowPrice()
    {
        return ProductResource::collection(Product::latest()->where('price', '<', '2000000')->paginate(10));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $image = $request->file('images');
        $filename = time() . "_" . preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));
        $image->storeAs('public/cv/', $filename, 'local');

        $validated = $this->validate($request, [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        $validatedImages = $this->validate($request, [
            'images' => 'required|array',
        ]);

        $validatedCategories = $this->validate($request, [
            'category' => 'required|numeric'
        ]);

        $product = Product::create($validated);
        $product->images()->attach($validatedImages);
        $product->category()->attach($validatedCategories);

        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if (auth()->user()->userDetail->type == 'admin') {
            $product->delete();
            return response()->json('Successfully Deleted', 204);
        } else {
            return response()->json(['Error' => 'Forbidden Not Admin'], 403);
        }
    }
}
