<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/9/19
 * Time: 下午5:09
 */
return array(
    \app\frontend\models\Goods::class=>[
        \Yunshop\Love\Frontend\Models\Expansions\GoodsExpansions::class,
        \Yunshop\AreaDividend\models\expansions\GoodsExpansions::class,
        \Yunshop\Supplier\common\models\expansions\GoodsExpansions::class
    ]
);