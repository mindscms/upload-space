<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('notifications.{channelUser}', function ($authUser, \App\Models\User $channelUser) {
    return $authUser->id === $channelUser->id;
});
