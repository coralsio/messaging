<?php

use Corals\Modules\Messaging\Models\Discussion;
use Corals\User\Models\User;
use Corals\User\Transformers\API\SimpleUserPresenter;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('messages.{user}', function (User $user) {
    return true;
});

Broadcast::channel('online', function (User $user) {
    return (new SimpleUserPresenter)->present($user)['data'];
});

Broadcast::channel('openedDiscussion.{discussion}', function (User $user, Discussion $discussion) {
    return true;
});
