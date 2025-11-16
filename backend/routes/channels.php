<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Domains.Users.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('admin', function ($user) {
    return $user->hasRole('admin');
});

Broadcast::channel('employer.{employerId}', function ($user, $employerId) {
    return $user->id == $employerId || $user->hasRole('admin');
});

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
