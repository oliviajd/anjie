<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapFileRoutes();

        $this->mapWorkflowRoutes();

        $this->mapAuthRoutes();

        $this->mapWorkplatformRoutes();

        $this->mapWorkRoutes();

        $this->mapRoleRoutes();

        $this->mapSystemRoutes();

        $this->mapJcrRoutes();

        $this->mapJcdRoutes();

        $this->mapCarRoutes();

        $this->mapAdvanceUserRoutes();

        $this->mapAdvanceManagerRoutes();

        $this->mapUserManagerRoutes();

        $this->mapShellRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
    /**
     * 文件
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapFileRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web/fileRoutes.php'));
    }
    /**
     * 工作流
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapWorkflowRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web/workflowRoutes.php'));
    }
    /**
     * 用户管理
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapAuthRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web/authRoutes.php'));
    }
    /**
     * 工作台
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapWorkplatformRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web/workplatformRoutes.php'));
    }
    /**
     * 工作台展示页
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapWorkRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web/workRoutes.php'));
    }
    /**
     * 角色
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapRoleRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web/roleRoutes.php'));
    }
    /**
     * 系统接口
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapSystemRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web/systemRoutes.php'));
    }
    /**
     * 林润万车app接口 
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapJcrRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web/jcrRoutes.php'));
    }
    /**
     * 林润万车后台web接口 
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapJcdRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web/jcdRoutes.php'));
    }
    /**
     * 林润万车后台web接口 
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapCarRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web/carRoutes.php'));
    }


    protected function mapAdvanceUserRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/AdvanceUserRoutes.php'));
    }

    protected function mapAdvanceManagerRoutes(){
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/AdvanceManagerRoutes.php'));
    }
    protected function mapUserManagerRoutes(){
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/UserRoutes.php'));
    }

    protected function mapShellRoutes(){
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/ShellRoutes.php'));
    }
}
