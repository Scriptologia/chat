<?php

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

Broadcast::channel('chat.to-user-{user_id}', function ($user, $user_id) {
    if( !\Scriptologia\Chat\Models\ChatIgnore::where('user_id', $user_id)->where('ignore_user_id', $user->id)->first() && !\Scriptologia\Chat\Models\ChatIgnore::where('user_id', $user->id)->where('ignore_user_id', $user_id)->first() ) return $user;
}, ['guards' => ['web']]);
