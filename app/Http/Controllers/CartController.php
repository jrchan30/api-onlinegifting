<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Http\Resources\UserResource;
use App\Models\Box;
use App\Models\Bundle;
use App\Models\Cart;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cart = Cart::where('user_id', auth()->user()->id)->latest()->paginate(10);

        return CartResource::collection($cart);
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
            'ids' => 'required|array',
            'type' => 'required|string',
        ]);

        try {
            $user_id = auth()->user()->id;
            $user = User::find($user_id);

            $cart = $user->cart()->first();

            if ($request->input('type') == 'bundle') {
                // $model = Bundle::class;
                $cart->bundles()->sync($validated['ids']);
            } else if ($request->input('type') == 'box') {
                // $model = Box::class;
                foreach ($validated['ids'] as $id) {
                    $box = Box::find($id);
                    if ($box->user_id !== $user_id) {
                        return response()->json(['forbidden' => 'Not your box'], 403);
                    }
                }
                $cart->boxes()->sync($validated['ids']);
            } else {
                return response()->json(['error' => 'type not found'], 500);
            }

            return new CartResource($cart);


            // $cart->$model->sync($validated['ids']);

            // $cart = $model->carts()->where('user_id', Auth::user()->id);
            // if ($like->exists()) {
            //     $like->delete();
            //     return response()->json([
            //         'message' => 'Successfully unliked', 'status' => 204
            //     ], 200);
            // } else {
            //     $newLike = new Like([
            //         'user_id' => Auth::user()->id
            //     ]);
            //     $model->likes()->save($newLike);
            //     return response()->json(['success' => $mess, 'status' => 201], 201);
            // }
            return '';
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function show(Cart $cart)
    {
        if ($cart->user_id == auth()->user()->id) {
            return new CartResource($cart);
        } else {
            return response()->json(['error' => 'Forbidden'], 403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cart $cart)
    {
        // if($transaction->user_id == auth()->user()->id){
        //     $transaction->delete();
        //     return response()->json('Successfully Deleted', 204);
        // }else{
        //     return response()->json(['Error' => 'Forbidden Not Your Transaction'], 403);
        // }
    }
}
