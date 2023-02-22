<?php

namespace Corals\Modules\Messaging;

use Corals\Foundation\Providers\BasePackageServiceProvider;
use Corals\Modules\Messaging\Models\Discussion;
use Corals\Modules\Messaging\Models\Message;
use Corals\Modules\Messaging\Models\Participation;
use Corals\Modules\Messaging\Providers\MessagingAuthServiceProvider;
use Corals\Modules\Messaging\Providers\MessagingObserverServiceProvider;
use Corals\Modules\Messaging\Providers\MessagingRouteServiceProvider;
use Corals\Settings\Facades\Modules;
use Corals\Settings\Facades\Settings;
use Corals\User\Models\User;
use Illuminate\Foundation\AliasLoader;

class MessagingServiceProvider extends BasePackageServiceProvider
{
    /**
     * @var
     */
    protected $defer = true;
    /**
     * @var
     */
    protected $packageCode = 'corals-messaging';

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function bootPackage()
    {
        // Load view
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'Messaging');

        // Load translation
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'Messaging');

        // Load migrations
//        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->registerCustomFieldsModels();
    }

    /**
     * @throws \ReflectionException
     */
    public function registerPackage()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/messaging.php', 'messaging');

        $this->app->register(MessagingRouteServiceProvider::class);
        $this->app->register(MessagingAuthServiceProvider::class);
        $this->app->register(MessagingObserverServiceProvider::class);

        $this->app->booted(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('Discussion', Discussion::class);
            $loader->alias('Message', Message::class);
            $loader->alias('Participation', Participation::class);
        });

        $this->bindModels();

        $messagable = new \Corals\Modules\Messaging\Hooks\Messagable();
        User::mixin($messagable);
    }

    protected function registerCustomFieldsModels()
    {
        Settings::addCustomFieldModel(Discussion::class);
        Settings::addCustomFieldModel(Message::class);
        Settings::addCustomFieldModel(Participation::class);
    }

    /**
     * Bind the models.
     */
    private function bindModels()
    {
        $this->app->bind(Contracts\Discussion::class, Discussion::class);
        $this->app->bind(Contracts\Message::class, Message::class);
        $this->app->bind(Contracts\Participation::class, Participation::class);
    }

    public function registerModulesPackages()
    {
        Modules::addModulesPackages('corals/messaging');
    }
}
