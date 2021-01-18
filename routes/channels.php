<?php

use App\Events\WebsocketDemoEvent;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{room}', function ($user, Room $room) {
    // $current_user = User::find($user->id);
    // if ($current_user->userDetail->type == 'customer') {
    //     return (int) $user->id === $room->user_id;
    // } else {
    //     return true;
    // }

    // return true;
    return $user;
});
