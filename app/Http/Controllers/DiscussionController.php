<?php

namespace App\Http\Controllers;

use App\Http\Resources\DiscussionResource;
use App\Models\Bundle;
use App\Models\Discussion;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    // public function productDiscussions($id) //$id untuk Discussionable ID
    // {
    //     $discussion = Discussion::where([
    //         ['discussionable_id', $id],
    //         ['discussionable_type', 'App\\Models\\Product']
    //     ])->with(['discussionable', 'user', 'replies'])->get();

    //     return DiscussionResource::collection($discussion);
    // }

    // public function bundleDiscussions($id) //$id untuk Discussionable ID
    // {
    //     $discussion = Discussion::where([
    //         ['discussionable_id', $id],
    //         ['discussionable_type', 'App\\Models\\Bundle']
    //     ])->with(['discussionable', 'user', 'replies'])->get();

    //     return DiscussionResource::collection($discussion);
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $discussions = Discussion::latest();
        return DiscussionResource::collection($discussions->paginate(12));
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
                'body' => 'string|required'
            ]);

            $discussion = new Discussion([
                'user_id' => Auth::user()->id,
                'body' => $validated['body']
            ]);

            if ($request->input('type') == 'product') {
                $model = Product::findOrFail($request->input('id'));
            } else {
                $model = Bundle::findOrFail($request->input('id'));
            }
            $model->discussions()->save($discussion);
            return response()->json(['success' => 'Discussion posted'], 201);
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
            $discussion = Discussion::findOrFail($id);
            if ($discussion->user_id == $user) {
                $discussion->delete();
                return response()->json(['message' => 'Successfully deleted'], 204);
            } else {
                return response()->json(['error' => 'Forbidden Not Yours'], 403);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getmessage()], 404);
        }
    }
}
