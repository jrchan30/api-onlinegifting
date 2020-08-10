<?php

namespace App\Http\Controllers;

use App\Http\Resources\BoxResource;
use App\Models\Box;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $box = Box::where('user_id', auth()->user()->id)->latest()->paginate(5);

        return BoxResource::collection($box);
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


        $user = auth()->user()->id;

        $calculatedPrice = Product::whereIn('id', $validated['products'])->sum('price');

        $box = Box::create([
            'user_id' => $user,
            'name' => $validated['name'],
            'price' => $calculatedPrice,
        ]);

        $box->products()->attach($validated['products']);

        return new BoxResource($box);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Box  $box
     * @return \Illuminate\Http\Response
     */
    public function show(Box $box)
    {
        if ($box->user_id == auth()->user()->id) {
            return new BoxResource($box);
        } else {
            return response()->json(['error' => 'Forbidden'], 403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Box  $box
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Box $box)
    {
        $validated = $this->validate($request, [
            'name' => 'sometimes',
            'products' => 'sometimes|array',
        ]);

        $user = auth()->user()->id;

        if ($request->has('products')) {

            // $validated['price'] = Product::whereIn('id', $validated['products'])->sum('price');
            $box->products()->sync($validated['products']);
        }


        if ($box->user_id == $user) {
            $box->update($validated);
            return new BoxResource($box);
        } else {
            return response(['error' => 'Forbidden Not Your Box'], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Box  $box
     * @return \Illuminate\Http\Response
     */
    public function destroy(Box $box)
    {
        $own = $box->user_id == auth()->user()->id;

        if ($own) {
            $box->delete();
            return response('', 204);
        } else {
            return response()->json(['error' => 'Forbidden Not Your Box'], 403);
        }
    }
}
