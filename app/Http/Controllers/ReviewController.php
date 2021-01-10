<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Bundle;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ReviewResource;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reviews = Review::all();
        return ReviewResource::collection($reviews);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validated = $this->validate($request, [
                'id' => 'required',
                'type' => 'string|required',
                'body' => 'string|required',
                'paid_product_id' => 'sometimes',
                'paid_bundle_id' => 'sometimes',
                'rating' => 'numeric|required|between:1,5'
            ]);


            $review = Review::firstOrNew([
                'user_id' => Auth::user()->id,
                'paid_product_id' => $request->paid_product_id ? $validated['paid_product_id'] : null,
                'paid_bundle_id' => $request->paid_bundle_id ? $validated['paid_bundle_id'] : null,

            ], [
                'body' => $validated['body'],
                'rating' => $validated['rating'],
            ]);

            // if ($request->paid_product_id) {
            //     $review->paid_product_id = $validated['paid_product_id'];
            // }

            // if ($request->paid_bundle_id) {
            //     $review->paid_bundle_id = $validated['paid_bundle_id'];
            // }

            // $review->save();

            if ($request->input('type') == 'product') {
                $model = Product::findOrFail($request->input('id'));
            } else {
                $model = Bundle::findOrFail($request->input('id'));
            }
            $res = $model->reviews()->save($review);
            // return response()->json(['success' => 'Review posted'], 201);
            return new ReviewResource($review);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
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
            $review = Review::findOrFail($id);
            if ($review->user_id == $user) {
                $review->delete();
                return response()->json(['message' => 'Successfully deleted'], 204);
            } else {
                return response()->json(['error' => 'Forbidden Not Yours'], 403);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getmessage()], 404);
        }
    }
}
