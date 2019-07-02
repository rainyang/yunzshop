<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/15
 * Time: 1:51 PM
 */

namespace app\http\Middleware;

use app\common\services\Check;
use Closure;
use app\common\helpers\WeSession;
use app\common\models\Modules;
use Illuminate\Http\Request;

/**
 * 前端cookie
 * Class Cookie
 * @package app\http\Middleware
 */
class Cookie
{
    const COOKIE_EXPIRE = 864000;
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->setCookie();

        $modules = Modules::getModuleName('yun_shop');

        \Config::set('module.name', $modules->title);
        strpos(request()->getBaseUrl(), '/web/index.php') === 0 && Check::setKey();
        return $next($request);

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
            $pieces     = explode('-', \YunShop::request()->state);
            $session_id = $pieces[1];
            unset($pieces);
        }

        if (isset($_COOKIE[session_name()])) {
            $session_id_1 = $_COOKIE[session_name()];
            session_id($session_id_1);
        }

        //h5 app
        if (!empty($_REQUEST['uuid'])) {
            $session_id_2 = md5($_REQUEST['uuid']);
            session_id($session_id_2);
            setcookie(session_name(), $session_id_2);
        }

        if (empty($session_id) && \YunShop::request()->session_id
            && \YunShop::request()->session_id != 'undefined' && \YunShop::request()->session_id != 'null'
        ) {
            $session_id = \YunShop::request()->session_id;
            session_id($session_id);
            setcookie(session_name(), $session_id);
        }

        WeSession::start(\YunShop::app()->uniacid, CLIENT_IP, self::COOKIE_EXPIRE);
    }

}