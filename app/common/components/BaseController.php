<?php

namespace app\common\components;

use app\common\exceptions\ShopException;
use app\common\helpers\WeSession;
use app\common\models\Modules;
use app\common\services\Check;
use app\common\traits\JsonTrait;
use app\common\traits\MessageTrait;
use app\common\traits\PermissionTrait;
use app\common\traits\TemplateTrait;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Validator;

/**
 * controller基类
 *
 * Author: 芸众商城 www.yunzshop.com
 * Date: 21/02/2017
 * Time: 21:20
 */
class BaseController extends Controller
{
    use DispatchesJobs, MessageTrait, ValidatesRequests, TemplateTrait, PermissionTrait, JsonTrait;

    const COOKIE_EXPIRE = 10 * 24 * 3600;

    /**
     * controller中执行报错需要回滚的action数组
     * @var array
     */
    public $transactionActions = [];

    public function __construct()
    {
        $this->setCookie();

        $modules = Modules::getModuleName('yun_shop');

        \Config::set('module.name', $modules->title);
    }

    /**
     * 前置action
     */
    public function preAction()
    {
        //strpos(request()->get('route'),'setting.key')!== 0 && Check::app();

        //是否为商城后台管理路径
        strpos(request()->getBaseUrl(), '/web/index.php') === 0 && Check::setKey();
    }

    protected function formatValidationErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }


    /**
     * 后台url参数验证
     * @param \Request $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     */
    public function validate(array $rules, \Request $request = null, array $messages = [], array $customAttributes = [])
    {
        if (!isset($request)) {
            $request = request();
        }
        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            throw new ShopException($validator->errors()->first());
        }
    }

    /**
     * 设置Cookie存储
     *
     * @return void
     */
    private function setCookie()
    {
        $session_id = '';
        if (isset(\YunShop::request()->state) && !empty(\YunShop::request()->state) && strpos(\YunShop::request()->state, 'yz-')) {
            $pieces = explode('-', \YunShop::request()->state);
            $session_id = $pieces[1];
            unset($pieces);
        }

        if (empty($session_id) && \YunShop::request()->session_id &&
            \YunShop::request()->session_id != 'undefined'
        ) {
            $session_id = \YunShop::request()->session_id;
        }

        if (empty($session_id)) {
            $session_id = $_COOKIE[session_name()];
        }

        if (empty($session_id)) {
            $session_id = "{".\YunShop::app()->uniacid."}-" . str_random(20) ;

            $session_id = md5($session_id);

            setcookie(session_name(), $session_id);
        }

        session_id($session_id);

        load()->classs('wesession'); 
        WeSession::start(\YunShop::app()->uniacid, CLIENT_IP, self::COOKIE_EXPIRE);
    }

    /**
     * 需要回滚
     * @param $action
     * @return bool
     */
    public function needTransaction($action)
    {
        return in_array($action, $this->transactionActions) || in_array('*', $this->transactionActions) || $this->transactionActions == '*';
    }


}
