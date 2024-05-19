<?php

namespace Corals\Modules\Messaging\Providers;

use Corals\Modules\Messaging\Listeners\ClientEventReceived;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Laravel\Reverb\Events\MessageReceived;

class MessagingEventServiceProvider extends ServiceProvider
{

    /**
     * @var \string[][]
     */
    protected $listen = [
        MessageReceived::class => [
            ClientEventReceived::class
        ]
    ];

    /**
     *
     */
    public function boot()
    {
        //
    }
}
