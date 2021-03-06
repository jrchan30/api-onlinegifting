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
use App\Notifications\NewBundleNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use NotificationChannels\WebPush\PushSubscription;

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
        $categories = $request->get('categories') ? explode(',', $request->get('categories')) : 'all';
        $min = $request->get('min') ?? 0;
        $max = $request->get('max') ?? 10000000;

        $bundles = Bundle::where('name', 'LIKE', $search)
            ->whereHas('products', function ($query) use ($min, $max) {
                $query->select(DB::raw('SUM(price) sumPrice'))
                    ->having('sumPrice', '>=', $min)->having('sumPrice', '<=', $max);
            })
            ->when($categories !== 'all', function ($q) use ($categories) {
                return $q->whereHas('detail', function ($query) use ($categories) {
                    $query->whereHas('categories', function ($q) use ($categories) {
                        $q->whereIn('categories.id', $categories);
                    });
                });
            })
            ->leftJoin('reviews', function ($join) {
                $join->on('reviews.reviewable_id', '=', 'bundles.id')->where('reviews.reviewable_type', '=', 'App\\Models\\Bundle');
            })->select('bundles.*', DB::raw('AVG(rating) as avg_rating'))->groupBy('id', 'bundles.user_id', 'bundles.name', 'bundles.description', 'bundles.created_at', 'bundles.updated_at', 'bundles.deleted_at')->withCount('likes')->with('products')->orderBy($orderBy, $orderDir);
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
            'design' => 'required|string',
            'isNotif' => 'sometimes|boolean',
        ]);

        $id = auth()->user()->id;

        $bundle = Bundle::create([
            'user_id' => $id,
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        $detail = $bundle->detail()->create([
            'colour' => $validated['colour'],
            'design' => $validated['design'],
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

        $subscribed_ids = PushSubscription::all()->pluck('subscribable_id');
        $user = User::whereIn('id', $subscribed_ids)->where('id', '!=', $request->user()->id)->get();

        // return $bundle;
        if ($request->has('isNotif')) {
            if ($validated['isNotif'] == true) {
                Notification::send($user, new NewBundleNotification($bundle));
            }
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
            'delete_image' => 'sometimes|numeric',
            'design' => 'required|string',
        ]);

        $bundle->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        $bundle->detail()->update([
            'colour' => $validated['colour'],
            'design' => $validated['design'],
        ]);

        $bundle->products()->sync($validated['products']);
        $bundle->detail->categories()->sync($validated['categories']);

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
