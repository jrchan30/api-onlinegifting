<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReplyResource;
use App\Models\Discussion;
use App\Models\Reply;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $replies = Reply::all();
        return ReplyResource::collection($replies);
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

            $reply = new Reply([
                'user_id' => Auth::user()->id,
                'body' => $validated['body']
            ]);
            $discussion = Discussion::findOrFail($request->input('discussion_id'));
            $res = $discussion->replies()->save($reply);
            // return response()->json(['success' => 'Reply posted'], 201);
            return new ReplyResource($res);
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
            $reply = Reply::findOrFail($id);
            if ($reply->user_id == $user) {
                $reply->delete();
                return response()->json(['message' => 'Successfully deleted (Reply)'], 204);
            } else {
                return response()->json(['error' => 'Forbidden Not Yours'], 403);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getmessage()], 404);
        }
    }
}
