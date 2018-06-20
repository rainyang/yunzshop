<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/20
 * Time: 下午2:10
 */

namespace app\common\modules\flow;

use app\common\modules\audit\flow\models\AuditFlow;
use app\common\modules\payType\remittance\models\flows\RemittanceFlow;
use Illuminate\Container\Container;

class FlowContainer extends Container
{
    public function __construct()
    {
        collect([
            [
                'key' => 'Remittance','class'=>RemittanceFlow::class,
            ],[
                'key' => 'Audit','class'=>AuditFlow::class,
            ],
        ])->each(function($item){
            $this->singleton('Remittance',function() use($item){
                return new $item['class']();
            });
        });
    }
}