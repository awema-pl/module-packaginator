<?php

namespace AwemaPL\Packaginator;

use AwemaPL\BaseJS\AwemaProvider;
use AwemaPL\Packaginator\Listeners\EventSubscriber;
use AwemaPL\Packaginator\Sections\Creators\Http\Middleware\StorageDownload;
use AwemaPL\Packaginator\Sections\Creators\Repositories\Contracts\HistoryRepository;
use AwemaPL\Packaginator\Sections\Creators\Repositories\EloquentHistoryRepository;
use AwemaPL\Packaginator\Sections\Installations\Http\Middleware\GlobalMiddleware;
use AwemaPL\Packaginator\Sections\Installations\Http\Middleware\GroupMiddleware;
use AwemaPL\Packaginator\Sections\Installations\Http\Middleware\Installation;
use AwemaPL\Packaginator\Sections\Installations\Http\Middleware\RouteMiddleware;
use AwemaPL\Packaginator\Contracts\Packaginator as PackaginatorContract;
use Illuminate\Support\Facades\Event;

class PackaginatorServiceProvider extends AwemaProvider
{

    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'packaginator');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'packaginator');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->bootMiddleware();
        app('packaginator')->includeLangJs();
        app('packaginator')->menuMerge();
        app('packaginator')->mergePermissions();
        Event::subscribe(EventSubscriber::class);
        parent::boot();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/packaginator.php', 'packaginator');
        $this->mergeConfigFrom(__DIR__ . '/../config/packaginator-menu.php', 'packaginator-menu');
        $this->app->bind(PackaginatorContract::class, Packaginator::class);
        $this->app->singleton('packaginator', PackaginatorContract::class);
        $this->registerRepositories();
        parent::register();
    }


    public function getPackageName(): string
    {
        return 'packaginator';
    }

    public function getPath(): string
    {
        return __DIR__;
    }

    /**
     * Register and bind package repositories
     *
     * @return void
     */
    protected function registerRepositories()
    {
        $this->app->bind(HistoryRepository::class, EloquentHistoryRepository::class);
    }

    /**
     * Boot middleware
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function bootMiddleware()
    {
        $this->bootGlobalMiddleware();
        $this->bootRouteMiddleware();
        $this->bootGroupMiddleware();
    }

    /**
     * Boot route middleware
     */
    private function bootRouteMiddleware()
    {
        $router = app('router');
        $router->aliasMiddleware('packaginator', RouteMiddleware::class);
    }

    /**
     * Boot group middleware
     */
    private function bootGroupMiddleware()
    {
        $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);
        $kernel->appendMiddlewareToGroup('web', GroupMiddleware::class);
        $kernel->appendMiddlewareToGroup('web', Installation::class);
    }

    /**
     * Boot global middleware
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function bootGlobalMiddleware()
    {
        $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);
        $kernel->pushMiddleware(GlobalMiddleware::class);
        $kernel->pushMiddleware(StorageDownload::class);
    }
}
