<?php

namespace app\common\providers;

use App;
use app\common\models\AccountWechats;
use app\common\repositories\OptionRepository;
use app\common\services\mews\captcha\src\Captcha;
use app\common\services\Utils;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //require_once $this->app->path('helpers.php');
        \Cron::setDisablePreventOverlapping();

        //微信接口不输出错误
        if (strpos(request()->getRequestUri(), '/api.php') >= 0) {
            error_reporting(0);
            //strpos(request()->get('route'),'setting.key') !== 0 && Check::app();
        }

        $this->globalParamsHandle();

        //设置uniacid
        Setting::$uniqueAccountId = \YunShop::app()->uniacid;
        //设置公众号信息
        AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));

        //开发模式下记录SQL
        if ($this->app->environment() !== 'production') {
            DB::listen(
                function ($sql) {
                    // $sql is an object with the properties:
                    //  sql: The query
                    //  bindings: the sql query variables
                    //  time: The execution time for the query
                    //  connectionName: The name of the connection

                    // To save  the executed queries to file:
                    // Process the sql and the bindings:
                    foreach ($sql->bindings as $i => $binding) {
                        if ($binding instanceof \DateTime) {
                            $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                        } else {
                            if (is_string($binding)) {
                                $sql->bindings[$i] = "'$binding'";
                            }
                        }
                    }

                    // Insert bindings into query
                    $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);

                    $query = vsprintf($query, $sql->bindings);

                    // Save the query to file
                    $logFile = fopen(
                        storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_query.log'),
                        'a+'
                    );
                    //echo storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_query.log');exit;
                    fwrite($logFile, date('Y-m-d H:i:s') . ': ' . $query . PHP_EOL);
                    fclose($logFile);
                }
            );
        }


    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Orangehill\Iseed\IseedServiceProvider::class);
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            $this->app->register(\Way\Generators\GeneratorsServiceProvider::class);
            $this->app->register(\Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider::class);
        }

        //增加模板扩展tpl
        \View::addExtension('tpl', 'blade');
        //配置表
        $this->app->singleton('options',  OptionRepository::class);

        /**
         * 设置
         */
        App::bind('setting', function()
        {
            return new Setting();
        });

        // Bind captcha
        $this->app->bind('captcha', function($app)
        {
            return new Captcha(
                $app['Illuminate\Filesystem\Filesystem'],
                $app['Illuminate\Config\Repository'],
                $app['Intervention\Image\ImageManager'],
                $app['Illuminate\Hashing\BcryptHasher'],
                $app['Illuminate\Support\Str']
            );
        });
    }

    private function globalParamsHandle()
    {
        if (env('APP_Framework') == 'platform') {
            $uniacid = 0;
            $cfg = \config::get('app.global');

            if (!empty(request('uniacid')) && request('uniacid') > 0) {
                $uniacid = request('uniacid');
                Utils::addUniacid();
            }

            if (empty($uniacid) && isset($_COOKIE['uniacid'])) {
                $uniacid = $_COOKIE['uniacid'];
            }

            $account = AccountWechats::getAccountByUniacid($uniacid);

            $cfg['uniacid'] = $uniacid;
            $cfg['account'] = $account ? $account->toArray() : '';

            \config::set('app.global', $cfg);
            global $_W;
            $_W = $cfg;
            \config::set('app.sys_global', array_merge(app('request')->input(), $_COOKIE));
        }
    }
}
