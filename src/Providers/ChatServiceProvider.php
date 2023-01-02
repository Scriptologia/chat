<?php

namespace Scriptologia\Chat\Providers;

use Illuminate\Support\ServiceProvider;

class ChatServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/chat.php', 'chat');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/channels.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'chat');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/chat')
        ], 'chat-views');
        $this->publishes([
            __DIR__.'/../config/chat.php' => config_path('chat.php')
        ], 'chat-config');
        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/chat')
        ], 'chat-public');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'chat');
    }
}
