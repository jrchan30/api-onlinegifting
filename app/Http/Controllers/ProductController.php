<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CategoryResource;

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
    public function index(Request $request)
    {
        // $products = Product::where('name', 'LIKE', $search)->leftJoin('reviews', function ($join) {
        //     $join->on('reviews.reviewable_id', '=', 'products.id')->where('reviews.reviewable_type', '=', 'App\\Models\\Product');
        // })->select('products.*', DB::raw('AVG(rating) as avg_rating'))
        //         ->groupBy('products.id', 'products.name', 'products.description', 'products.price', 'stock', 'weight', 'created_at', 'deleted_at', 'updated_at')
        //         ->withCount('likes')->orderBy($orderBy, $orderDir);

        // $products = Product::where('name', 'LIKE', $search)->withCount('likes')->orderBy($orderBy, $orderDir);
        // $products->with(['categories' => function($query) use ($categories){
            //         return $query->whereIn('categories.id', $categories);
            // }]);
            // DB::enableQueryLog();
        // $products->join('categories')->on('categoriable', 'categoriable_id', '=', 'products.id')->whereIn('categories.id', $categories);
        // dd(DB::getQueryLog());

        $s = $request->get('search') ?? '';
        $orderBy = $request->get('orderBy') ?? 'created_at';
        $orderDir = $request->get('orderDir') ?? 'desc';
        $search = '%' . $s . '%';
        $categories = $request->get('categories') ? explode(',', $request->get('categories')) : 'all';
        
        // return $categories;

        $products = Product::where('products.name', 'LIKE', $search)->when($categories !== 'all', function($q) use($categories){
            return $q->whereHas('categories', function($q) use ($categories){
                $q->whereIn('categories.id', $categories);
            });
        })->leftJoin('reviews', function ($join) {
            $join->on('reviews.reviewable_id', '=', 'products.id')->where('reviews.reviewable_type', '=', 'App\\Models\\Product');
        })->select('products.*', DB::raw('AVG(rating) as avg_rating'))
                ->groupBy('id', 'products.name', 'description', 'price', 'stock', 'weight', 'created_at', 'deleted_at', 'updated_at')
                ->withCount('likes')->orderBy($orderBy, $orderDir);
        
        if (Auth::user()) {
            if (Auth::user()->userDetail->type != 'admin') {
                $products = $products->where('stock', '>', 0);
            } else {
                $products = $products->where('stock', '>=', 0);
            }
        } else {
            $products = $products->where('stock', '>', 0);
        }

        return ProductResource::collection($products->paginate(12));
    }

    public function allProducts()
    {
        $products = Product::get(['id', 'name']);
        return $products;
    }

    public function latestProducts()
    {

        if (Auth::user()) {
            if (Auth::user()->userDetail->type != 'admin') {
                $products = Product::where('stock', '>', 0);
            } else {
                $products = Product::where('stock', '>=', 0);
            }
        } else {
            $products = Product::where('stock', '>', 0);
        }
        return ProductResource::collection($products->latest()->take(5)->get());
    }

    public function lowPrice()
    {
        if (Auth::user()) {
            if (Auth::user()->userDetail->type != 'admin') {
                $products = Product::where('stock', '>', 0);
            } else {
                $products = Product::where('stock', '>=', 0);
            }
        } else {
            $products = Product::where('stock', '>', 0);
        }
        return ProductResource::collection($products->latest()->where('price', '<', '200000')->take(12)->get());
    }

    public function trashedProducts(Request $request)
    {
        $s = $request->get('search') ?? '';
        $orderBy = $request->get('orderBy') ?? 'created_at';
        $orderDir = $request->get('orderDir') ?? 'desc';
        $search = '%' . $s . '%';
        $trashed = Product::onlyTrashed()->where('name', 'LIKE', $search)->orderBy($orderBy, $orderDir);
        return ProductResource::collection($trashed->paginate(12));
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

            $upload = $file->store("product_images/{$product->id}", 's3');
            Storage::disk('s3')->setVisibility($upload, 'public');
            $imageModel = new Image([
                'path' => basename($upload),
                'url' =>  Storage::url($upload)
            ]);


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
        $validated = $this->validate($request, [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'weight' => 'required|numeric',
            'categories' => 'required|array',
            'categories.*' => 'required|numeric',
            'delete_image' => 'sometimes|array',
            'delete_image.*' => 'sometimes|numeric',
            'new_images' => 'sometimes|array',
            'new_images.*' => 'sometimes|image',
        ]);

        if (array_key_exists("new_images", $validated)) {
            foreach ($validated['new_images'] as $image) {
                $file = $image;

                $upload = $file->store("product_images/{$product->id}", 's3');
                Storage::disk('s3')->setVisibility($upload, 'public');
                $imageModel = new Image([
                    'path' => basename($upload),
                    'url' =>  Storage::url($upload)
                ]);

                $product->images()->save($imageModel);
            }
        }

        if (array_key_exists("delete_image", $validated)) {
            if ($product->images()->count() > count($validated['delete_image'])) {
                $product->images()->whereIn('id', $validated['delete_image'])->delete();
            } else {
                return response()->json(['message' => 'error, image must be greater or equal to one'], 400);
            }
        }


        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'weight' => $validated['weight'],
        ]);
        $product->categories()->sync($validated['categories']);

        return new ProductResource($product);
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
            return response()->json(['error' => 'Forbidden Not Admin'], 403);
        }
    }

    public function restoreProduct($id)
    {
        if (auth()->user()->userDetail->type == 'admin') {
            Product::withTrashed()->where('id', $id)->restore();
            return response()->json('Successfully Restored', 204);
        } else {
            return response()->json(['error' => 'Forbidden Not Admin'], 403);
        }
    }
}
