<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $messages = Message::with('user')->get();
        return MessageResource::collection($messages);
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
            'message' => 'string|required',
            'room_id' => 'sometimes'
        ]);

        $uid = auth()->user()->id;
        $user = User::find($uid);

        // if ($user->room()->exists()) {
        if (!$request->has('room_id')) {
            $room = Room::where('user_id', $uid)->first();
        } else {
            $room = Room::find($validated['room_id']);
        }
        // }
        //  else {
        //     $room = Room::create([
        //         'user_id' => $request->has('room_id') ? $validated[$uid,
        //         'admin_id' => 1,
        //     ]);
        // }

        $message = $room->messages()->create([
            'user_id' => $uid,
            'message' => $validated['message']
        ]);

        $message->load('user');
        // MessageSent::dispatch($message);

        // broadcast(new MessageSent($message->load('user')))->toOthers();
        // broadcast(new MessageSent($message->load('user')));
        broadcast(new MessageSent($message))->toOthers();

        return new MessageResource($message);
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
        //
    }
}
