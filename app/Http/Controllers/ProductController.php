<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        $products = Product::latest();
        if (Auth::user()->userDetail->type != 'admin') {
            $products = $products->where('stock', '>', 0);
        }
        return ProductResource::collection($products->paginate(12));
    }

    public function latestProducts()
    {
        return ProductResource::collection(Product::latest()->take(5)->get());
    }

    public function lowPrice()
    {
        return ProductResource::collection(Product::latest()->where('price', '<', '2000000')->paginate(10));
    }

    public function trashedProducts()
    {
        $trashed = Product::onlyTrashed()->paginate(12);
        return ProductResource::collection($trashed);
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
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'weight' => 'required|numeric',
            'categories' => 'required|array',
            'categories.*' => 'required|numeric',
            'images' => 'required|array',
            'images.*' => 'required|image',
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'weight' => $validated['weight'],
        ]);

        foreach ($validated['images'] as $image) {
            $file = $image;

            $fileName = sha1(time());
            $fileExtension = $file->getClientOriginalExtension();
            $fullFileName = $fileName . '.' . $fileExtension;
            $storage = env('APP_ENV', 'local') ?  'public' : 's3';
            $path = 'product_pictures/';

            $file->storeAs($path, $fullFileName, ['disk' => $storage]);

            if (env('APP_ENV') == 'local') {
                $imageModel = new Image([
                    'path' => $path . $fullFileName,
                    'url' =>  'http://localhost:8000' . Storage::url($path . $fullFileName)
                ]);
            } else if (env('APP_ENV') == 'production') {
                return response()->json('ERROR', 500);
            }

            $product->images()->save($imageModel);
        }

        foreach ($validated['categories'] as $categoryId) {
            $cat = Category::find($categoryId);

            $product->categories()->attach($cat);
        }

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
