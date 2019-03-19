<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 24/03/2017
 * Time: 18:10
 */

namespace app\common\events;


class WechatMessage extends Event
{

    protected $wechatApp;

    protected $keyword;

    public function __construct(\app\common\modules\wechat\WechatApplication $wechatApp, $keyword)
    {
        $this->wechatApp = $wechatApp;
        $this->keyword = $keyword;
    }

    /**
     * 获取微信对象
     * @return \app\common\modules\wechat\WechatApplication
     */
    public function getWechatApp()
    {
        return $this->wechatApp;
    }

    /**
     * 获取关键字
     * @return mixed
     */
    public function getKeyword()
    {
        return $this->keyword;
    }
}