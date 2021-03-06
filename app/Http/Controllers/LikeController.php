<?php

namespace App\Http\Controllers;

use App\Http\Resources\BundleResource;
use Exception;
use App\Models\Like;
use App\Models\Bundle;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\LikeResource;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $user = auth()->user()->id;
        // $likes = Like::where('user_id', $user)->with(['likeable'])->get();
        // return response()->json($likes);
        $liked_products = Product::whereHas('likes', function ($query) {
            $query->where('user_id', auth()->user()->id);
        })->latest()->get();

        $liked_bundles = Bundle::whereHas('likes', function ($query) {
            $query->where('user_id', auth()->user()->id);
        })->latest()->get();

        return [
            'liked_products' => ProductResource::collection($liked_products),
            'liked_bundles' => BundleResource::collection($liked_bundles)
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Keknya masih bisa dibagusin (firstOrCreate)
        try {
            if ($request->input('type') == 'product') {
                $model = Product::find($request->input('id'));
            } else {
                $model = Bundle::find($request->input('id'));
            }

            $mess = "Liked";
            $like = $model->likes()->where('user_id', Auth::user()->id);
            if ($like->exists()) {
                $like->delete();
                return response()->json([
                    'message' => 'Successfully unliked', 'status' => 204
                ], 200);
            } else {
                $newLike = new Like([
                    'user_id' => Auth::user()->id
                ]);
                $model->likes()->save($newLike);
                return response()->json(['success' => $mess, 'status' => 201], 201);
            }
            return '';
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user()->id;
        try {
            $like = Like::FindOrFail($id);
            if ($like->user_id == $user) {
                $like->delete();
                return response()->json(['Message' => 'Successfully deleted'], 204);
            } else {
                return response()->json(['Error' => 'Forbidden Not Yours'], 403);
            }
        } catch (Exception $errors) {
            return response()->json(['Error' => $errors->getmessage()], 404);
        }

        return '';
    }
}
