<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bundle;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BundleResource;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

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
    public function index(Request $request)
    {
        $s = $request->get('search') ?? '';
        $orderBy = $request->get('orderBy') ?? 'created_at';
        $orderDir = $request->get('orderDir') ?? 'desc';
        $search = '%' . $s . '%';
        $bundles = Bundle::where('name', 'LIKE', $search)->leftJoin('reviews', function ($join) {
            $join->on('reviews.reviewable_id', '=', 'bundles.id')->where('reviews.reviewable_type', '=', 'App\\Models\\Bundle');
        })->select('bundles.*', DB::raw('AVG(rating) as avg_rating'))->groupBy('id')->withCount('likes')->orderBy($orderBy, $orderDir);
        return BundleResource::collection($bundles->paginate(12));
    }

    public function trashedBundles(Request $request)
    {
        $s = $request->get('search') ?? '';
        $orderBy = $request->get('orderBy') ?? 'created_at';
        $orderDir = $request->get('orderDir') ?? 'desc';
        $search = '%' . $s . '%';
        $trashed = Bundle::onlyTrashed()->where('name', 'LIKE', $search)->orderBy($orderBy, $orderDir);
        return BundleResource::collection($trashed->paginate(12));
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
            'colour' => 'required|string',
            'description' => 'required|string',
            'products' => 'required|array',
            'products.*' => 'required|numeric',
            'categories' => 'required|array',
            'categories.*' => 'required|numeric',
            'image' => 'required|image',
        ]);

        $id = auth()->user()->id;

        $bundle = Bundle::create([
            'user_id' => $id,
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        $detail = $bundle->detail()->create([
            'colour' => $validated['colour'],
        ]);

        $file = $validated['image'];

        $upload = $file->store("bundle_images/{$bundle->id}", 's3');
        Storage::disk('s3')->setVisibility($upload, 'public');
        $imageModel = new Image([
            'path' => basename($upload),
            'url' =>  Storage::url($upload)
        ]);

        $detail->image()->save($imageModel);

        foreach ($validated['products'] as $productId) {
            $prod = Product::find($productId);
            $bundle->products()->attach($prod);
        }

        foreach ($validated['categories'] as $categoryId) {
            $cat = Category::find($categoryId);
            $bundle->detail->categories()->attach($cat);
        }



        // $bundle->detail()->categories()->attach($cat);
        // $bundle->products()->attach($productId);
        // $calculatedPrice = Product::whereIn('id', $validated['products'])->sum('price');
        // $bundle->products()->attach($validated['products']);

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
            'name' => 'required|string',
            'colour' => 'required|string',
            'description' => 'required|string',
            'products' => 'required|array',
            'products.*' => 'required|numeric',
            'categories' => 'required|array',
            'categories.*' => 'required|numeric',
            'new_image' => 'sometimes|image',
            'delete_image' => 'sometimes|numeric'
        ]);

        $bundle->update([
            'name' => $validated['name'],
            'colour' => $validated['colour'],
            'description' => $validated['description'],
        ]);

        $bundle->products()->sync($validated['products']);
        $bundle->categories()->sync($validated['categories']);

        if ($request->has('new_image')) {
            $file = $validated['new_image'];

            $upload = $file->store("bundle_images/{$bundle->id}", 's3');
            Storage::disk('s3')->setVisibility($upload, 'public');
            $imageModel = new Image([
                'path' => basename($upload),
                'url' =>  Storage::url($upload)
            ]);

            $bundle->detail()->image()->save($imageModel);
            $bundle->detail()->image()->where('id', $validated['delete_image'])->delete();
        }

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
        if (auth()->user()->userDetail->type == 'admin') {
            $bundle->delete();
            return response()->json('Successfully Deleted', 204);
        } else {
            return response()->json(['error' => 'Forbidden Not Admin'], 403);
        }
    }

    public function restoreBundle($id)
    {
        if (auth()->user()->userDetail->type == 'admin') {
            Bundle::withTrashed()->where('id', $id)->restore();
            return response()->json('Successfully Restored', 204);
        } else {
            return response()->json(['error' => 'Forbidden Not Admin'], 403);
        }
    }
}
