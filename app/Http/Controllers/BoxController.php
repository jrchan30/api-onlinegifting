<?php

namespace App\Http\Controllers;

use App\Http\Resources\BoxResource;
use App\Models\Box;
use App\Models\Product;
use App\Models\User;
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
        $box = Box::where('user_id', auth()->user()->id)->latest()->get();

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
            'name' => 'required|string',
            'colour' => 'sometimes|string',
            'products' => 'sometimes|array',
            'design' => 'sometimes|string'
        ]);

        $user_id = auth()->user()->id;
        $user = User::find($user_id);

        $box = $user->boxes()->create([
            'name' => $validated['name'],
        ]);

        $box->detail()->create();

        if ($request->has('colour')) {
            $box->detail()->update([
                'colour' => $validated['colour'],
            ]);
        }

        if ($request->has('design')) {
            $box->detail()->update([
                'design' => $validated['design'],
            ]);
        }

        // $box->detail()->create([
        //     'colour' => $validated['colour'],
        // ]);

        if ($request->has('products')) {
            $box->products()->attach($validated['products']);
        }
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
        $userId = auth()->user()->id;

        if ($box->user_id != $userId) {
            return response(['error' => 'Forbidden Not Your Box'], 403);
        }

        $validated = $this->validate($request, [
            'name' => 'sometimes|string',
            'colour' => 'sometimes|string',
            'design' => 'sometimes|string',
            'allProducts' => 'sometimes|json',
        ]);

        // $validatedProducts = $this->validate($request, [
        //     'allProducts' => 'sometimes|json'
        // ]);

        if ($request->has('allProducts')) {
            $allProducts = json_decode($validated['allProducts'], true);
            $box->products()->sync($allProducts);
        }

        if ($request->has('name')) {
            $box->update([
                'name' => $validated['name']
            ]);
        }

        if ($request->has('colour')) {
            $box->detail->update([
                'colour' => $validated['colour']
            ]);
        }

        if ($request->has('design')) {
            $box->detail()->update([
                'design' => $validated['design'],
            ]);
        }

        // $box->name = $validated['name'];
        // $box->detail->colour = $validated['colour'];
        // $box->save();
        return new BoxResource($box);
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
            $box->products()->detach();
            $box->delete();
            return response('', 204);
        } else {
            return response()->json(['error' => 'Forbidden Not Your Box'], 403);
        }
    }
}
