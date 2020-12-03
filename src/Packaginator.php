<?php

namespace AwemaPL\Packaginator;

use Illuminate\Database\Migrations\Migrator;
use Illuminate\Routing\Router;
use AwemaPL\Packaginator\Contracts\Packaginator as PackaginatorContract;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class Packaginator implements PackaginatorContract
{
    /** @var Router $router */
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Routes
     */
    public function routes()
    {
        if ($this->isActiveRoutes()) {
            if ($this->canInstallation()) {
                $this->installationRoutes();
            }
            if ($this->isActiveCreatorRoutes()) {
                $this->creatorRoutes();
            }
            if ($this->isActiveExampleRoutes()) {
                $this->exampleRoutes();
            }
        }
    }

    /**
     * Installation routes
     */
    protected function installationRoutes()
    {
        $prefix = config('packaginator.routes.installation.prefix');
        $namePrefix = config('packaginator.routes.installation.name_prefix');
        $this->router->prefix($prefix)->name($namePrefix)->group(function () {
            $this->router
                ->get('/', '\AwemaPL\Packaginator\Sections\Installations\Http\Controllers\InstallationController@index')
                ->name('index');
            $this->router->post('/', '\AwemaPL\Packaginator\Sections\Installations\Http\Controllers\InstallationController@store')
                ->name('store');
        });

    }

    /**
     * Creator routes
     */
    protected function creatorRoutes()
    {

        $prefix = config('packaginator.routes.creator.prefix');
        $namePrefix = config('packaginator.routes.creator.name_prefix');
        $middleware = config('packaginator.routes.creator.middleware');
        $this->router->prefix($prefix)->name($namePrefix)->middleware($middleware)->group(function () {
            $this->router
                ->get('/', '\AwemaPL\Packaginator\Sections\Creators\Http\Controllers\CreatorController@index')
                ->name('index');
            $this->router
                ->post('/', '\AwemaPL\Packaginator\Sections\Creators\Http\Controllers\CreatorController@store')
                ->name('store');
            $this->router
                ->get('/histories', '\AwemaPL\Packaginator\Sections\Creators\Http\Controllers\CreatorController@scope')
                ->name('scope');
            $this->router
                ->get('/download/{filename}', '\AwemaPL\Packaginator\Sections\Creators\Http\Controllers\CreatorController@download')
                ->name('download');
        });
    }

    /**
     * Example routes
     */
    protected function exampleRoutes()
    {

        $prefix = config('packaginator.routes.example.prefix');
        $namePrefix = config('packaginator.routes.example.name_prefix');
        $middleware = config('packaginator.routes.example.middleware');
        $this->router->prefix($prefix)->name($namePrefix)->middleware($middleware)->group(function () {
            $this->router
                ->get('/', '\AwemaPL\Packaginator\Sections\Examples\Http\Controllers\ExampleController@index')
                ->name('index');
            $this->router
                ->get('/', '\AwemaPL\Packaginator\Sections\Examples\Http\Controllers\ExampleController@index')
                ->name('index');
            $this->router
                ->get('/virtual-tour-from-beginning', '\AwemaPL\Packaginator\Sections\Examples\Http\Controllers\ExampleController@virtualTourFromBeginning')
                ->name('virtual_tour_from_beginning');
        });
    }

    /**
     * Can installation
     *
     * @return bool
     */
    public function canInstallation()
    {
        $canForPermission = $this->canInstallForPermission();
        return $this->isActiveRoutes()
            && $this->isActiveInstallationRoutes()
            && $canForPermission
            && !$this->isMigrated();
    }

    /**
     * Is migrated
     *
     * @return bool
     */
    public function isMigrated()
    {
        $tablesInDb = array_map('reset', \DB::select('SHOW TABLES'));

        $tables = array_values(config('packaginator.database.tables'));
        foreach ($tables as $table){
            if (!in_array($table, $tablesInDb)){
                return false;
            }
        }
        return true;
    }

    /**
     * Is active routes
     *
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public function isActiveRoutes()
    {
        return config('packaginator.routes.active');
    }

    /**
     * Is active packaginator routes
     *
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public function isActiveCreatorRoutes()
    {
        return config('packaginator.routes.creator.active');
    }

    /**
     * Is active installation routes
     *
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function isActiveInstallationRoutes()
    {
        return config('packaginator.routes.installation.active');
    }

    /**
     * Is active example routes
     *
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function isActiveExampleRoutes()
    {
        return config('packaginator.routes.example.active');
    }

    /**
     * Include lang JS
     */
    public function includeLangJs()
    {
        $lang = config('indigo-layout.frontend.lang', []);
        $lang = array_merge_recursive($lang, app(\Illuminate\Contracts\Translation\Translator::class)->get('packaginator::js'));
        app('config')->set('indigo-layout.frontend.lang', $lang);
    }

    /**
     * Can install for permission
     *
     * @return bool
     */
    private function canInstallForPermission()
    {
        $userClass = config('auth.providers.users.model');
        if (!method_exists($userClass, 'hasRole')) {
            return true;
        }

        if ($user = request()->user() ?? null){
            return $user->can(config('packaginator.installation.auto_redirect.permission'));
        }

        return false;
    }

    /**
     * Menu merge in navigation
     */
    public function menuMerge()
    {
        if ($this->canMergeMenu()){
            $packaginatorMenu = config('packaginator-menu.navs', []);
            $navTemp = config('temp_navigation.navs', []);
            $nav = array_merge_recursive($navTemp, $packaginatorMenu);
            config(['temp_navigation.navs' => $nav]);
        }
    }

    /**
     * Can merge menu
     *
     * @return boolean
     */
    private function canMergeMenu()
    {
        return !!config('packaginator-menu.merge_to_navigation') && self::isMigrated();
    }

    /**
     * Execute package migrations
     */
    public function migrate()
    {
         Artisan::call('migrate', ['--force' => true, '--path'=>'vendor/awema-pl/module-packaginator/database/migrations']);
    }

    /**
     * Install package
     */
    public function install()
    {
        $this->migrate();
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
    }

    /**
     * Add permissions for module permission
     */
    public function mergePermissions()
    {
       if ($this->canMergePermissions()){
           $packaginatorPermissions = config('packaginator.permissions');
           $tempPermissions = config('temp_permission.permissions', []);
           $permissions = array_merge_recursive($tempPermissions, $packaginatorPermissions);
           config(['temp_permission.permissions' => $permissions]);
       }
    }

    /**
     * Can merge permissions
     *
     * @return boolean
     */
    private function canMergePermissions()
    {
        return !!config('packaginator.merge_permissions');
    }
}
