<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/6/20
 * Time: 上午10:07
 */

namespace app\common\providers;

use app\backend\modules\supervisord\services\Supervisor;
use app\common\facades\Setting;
use app\common\helpers\SettingCache;
use app\common\modules\express\KDN;
use app\common\managers\ModelExpansionManager;
use app\common\models\BaseModel;
use app\common\modules\status\StatusContainer;
use app\framework\Log\Log;
use app\frontend\modules\coin\CoinManager;
use app\frontend\modules\deduction\DeductionManager;
use app\frontend\modules\goods\services\GoodsManager;
use app\frontend\modules\order\services\OrderManager;
use app\frontend\modules\payment\managers\PaymentManager;
use Illuminate\Support\ServiceProvider;

class ShopProvider extends ServiceProvider
{
    public function boot()
    {
        BaseModel::setExpansions(config('shop-foundation.model-expansions'));

        $this->app->singleton('SettingCache',function() {
            return new SettingCache();
        });
        $this->app->singleton('supervisor',function() {
            return new Supervisor('https://127.0.0.1', 9001);
        });
        $this->app->singleton('ModelExpansionManager',function(){
            return new ModelExpansionManager();

        });
        $this->app->singleton('CoinManager',function(){
            return new CoinManager();
        });
        $this->app->singleton('DeductionManager',function(){
            return new DeductionManager();
        });
        $this->app->singleton('PaymentManager',function(){
            return new PaymentManager();
        });
        $this->app->singleton('GoodsManager',function(){
            return new GoodsManager();
        });

        $this->app->singleton('OrderManager',function(){
            return new OrderManager();
        });

        $this->app->singleton('StatusContainer', function (){
            return new StatusContainer();
        });
        $this->app->singleton('express', function (){
            return new KDN(Setting::get('shop.express_info.KDN.eBusinessID'),Setting::get('shop.express_info.KDN.appKey'),config('app.express.KDN.reqURL'));
        });
        $this->app->singleton('Log.debug', function (){
            return new Log();
        });
    }
}