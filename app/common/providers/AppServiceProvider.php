<?php

namespace app\common\providers;

use Setting;
use Illuminate\Support\ServiceProvider;
use App;
use Illuminate\Support\Facades\DB;
use app\common\repositories\OptionRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //开发模式下记录SQL
        if ($this->app->environment() !== 'production') {
            DB::listen(
                function ($sql) {
                    // $sql is an object with the properties:
                    //  sql: The query
                    //  bindings: the sql query variables
                    //  time: The execution time for the query
                    //  connectionName: The name of the connection

                    // To save the executed queries to file:
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

        //表单设置
        \BootForm::horizontal();
        \BootForm::open([
            'left_column_class' => 'col-xs-12 col-sm-3 col-md-2',
            'left_column_offset_class' => '',
            'right_column_class' => 'col-sm-9 col-xs-12',
            'show_all_errors'=>true
        ]);

        //设置uniacid
        Setting::$uniqueAccountId = \YunShop::app()->uniacid;

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
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
    }
}
