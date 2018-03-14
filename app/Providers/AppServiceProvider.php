<?php

namespace App\Providers;

use DB;
use Log;
use App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Request;
use App\Http\Models\common\Pdo;
use App\Http\Models\common\Common;
use Illuminate\Database\Events\QueryExecuted;

class AppServiceProvider extends ServiceProvider
{
    protected $_pdo = null;
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->_pdo = new Pdo();
        if(isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])){
          $path = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
        } else {
            $path = 'http://';
        }
        $arr = parse_url($path);
        $uri = $arr['path'];
        $urlarr = explode('/', $uri);
        $controller = isset($urlarr['2']) ? $urlarr['2'] : '';
        $action = isset($urlarr['3']) ?$urlarr['3'] : '';
        $sql = "select * from anjie_method where path = ?";
        $methodinfo = $this->_pdo->fetchOne($sql, array('/' . $controller . '/' . $action));
        if (empty($methodinfo)) {
            $methodinfo['method_id'] = '';
            $methodinfo['cid'] = '';
        }
        if(isset($methodinfo['is_bar']) && $methodinfo['is_bar'] == '0') {
            $sql = "select * from anjie_method where method_id = ?";
            $methodinfo = $this->_pdo->fetchOne($sql, array($methodinfo['cid']));
        }
        view()->share('methodinfo', $methodinfo);
        Schema::defaultStringLength(191);
        session_start();
        $method = array();
        $category = array();
        if (isset($_SESSION['user_id'])) {
            $sql = "select * from anjie_users where id = ?";
            $userinfo = $this->_pdo->fetchOne($sql, array($_SESSION['user_id']));
            view()->share('user_type', $userinfo['type']);
            $sql = "select * from v1_user_role where user_id = ?";
            $rs = $this->_pdo->fetchAll($sql, array($_SESSION['user_id']));
            $privilege = array();
            foreach ($rs as $key => $value) {
                if ($value['role_id'] == '1') {
                    $sql = "select * from anjie_category where is_menu = 1 order by sort";
                    $category = $this->_pdo->fetchAll($sql, array());
                    $sql = "select * from anjie_method where status = 1 and is_bar= 1 order by sort";
                    $method = $this->_pdo->fetchAll($sql, array());
                    break;
                } else {
                    $sql = "select * from v1_role_privilege where role_id = ?";
                    $privilege = array_merge($this->_pdo->fetchAll($sql, array($value['role_id'])), $privilege);
                }
            }
            if (empty($method) && empty($category)) {
                $module_id = array_unique(array_column($privilege, 'module_id'));
                $mudule_id_str = "'".implode("','", $module_id)."'";
                $method_id = array_unique(array_column($privilege, 'method_id'));
                $method_id_str = "'".implode("','", $method_id)."'";
                foreach ($privilege as $key => $value) {
                    if ($value['method_id'] == '0') {
                        $sql = "select * from anjie_category where id in(".$mudule_id_str.") and is_menu = 1  order by sort";
                        $category = $this->_pdo->fetchAll($sql, array());
                    } else {
                        $sql = "select * from anjie_method where status = 1 and is_bar= 1 and method_id in(".$method_id_str.")";
                        $method = $this->_pdo->fetchAll($sql, array());
                    }
                }
            }
            foreach ($method as $module_id => $value) {
                    $path = ltrim($value['path'], '/');
                    $methodurlarr = explode('/', $path);
                    if(isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])){
                        $method[$module_id]['url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/Admin' . $value['path'];
                    } else {
                        $method[$module_id]['url'] = 'http://';
                    }
            }
            view()->share('category', $category);
            view()->share('method', $method);
        }
        if (isset($_SESSION['name'])) {
            view()->share('username', $_SESSION['name']);
        }
        if (isset($_SESSION['head_portrait']) && $_SESSION['head_portrait'] !== '') {
            view()->share('head_portrait', $_SESSION['head_portrait']);
        }
        if (isset($_SESSION['account']) && $_SESSION['account'] !== '') {
            view()->share('account', $_SESSION['account']);
        }


        if (App::environment('local','testing')) {
            DB::enableQueryLog();
            DB::listen(function (QueryExecuted $event) {
                Log::info($event->sql);
                Log::info($event->bindings);
            });
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
