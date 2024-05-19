<?php

namespace Corals\Modules\Messaging\Providers;

use Illuminate\Support\ServiceProvider;

class MessagingBroadcastServiceProvider extends ServiceProvider
{

    /**
     *
     */
    public function boot()
    {
        require __DIR__ . '/../routes/channels.php';
    }
}
