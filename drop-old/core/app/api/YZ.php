<?php
/**
 * 管理后台APP API接口基类
 *
 * @package   管理后台APP API
 * @author    shenyang<shenyang@yunzshop.com>
 * @version   v1.0
 */

namespace app\api;
//require_once __DIR__ . '/base.php';


class YZ extends base
{
    /**
     * 来自global变量$_W(各种数据,没什么规律)
     *
     * @var Array
     */
    protected $_W;
    /**
     * 来自global变量$_GPC(post,get等参数混合的数组)
     *
     * @var Array
     */
    protected $_GPC;
    /**
     * 管理员ID
     *
     * @var int
     */
    protected $uid;
    /**
     * 公众号ID
     *
     * @var int
     */
    protected $uniacid;

    /**
     * 写入属性默认值
     */
    public function __construct()
    {
        global $_W, $_GPC;
        parent::__construct();
        $this->set_WAnd_GPC();
        //require IA_ROOT . '/web/common/bootstrap.sys.inc.php';
        require_once __CORE_PATH__ . '/../site.php';
    }

    protected function callMobile($path)
    {
        global $_W, $_GPC;
        $_W['isajax'] = true;
        list($folder_name, $file_name, $action_name, $to) = explode('/', $path);
        $class = new \Sz_yiModuleSite();
        $method = 'doMobile' . ucfirst($folder_name);
        $_GPC['p'] = $file_name;
        $_GPC['op'] = $action_name;
        $_GPC['to'] = $to;
        $result = $class->$method();
        if (!in_array($result['status'], array(1, 2))) {
            $this->returnError($result['json']);
        }
        return $result;
    }

    protected function callPlugin($path)
    {
        global $_GPC, $_W;
        $_W['isajax'] = true;
        list($_GPC['p'], $_GPC['method'], $_GPC['op'], $_GPC['to']) = explode('/', $path);
        $class = new \Sz_yiModuleSite();
        $result = $class->doMobilePlugin();
        if (!in_array($result['status'], array(1, 2))) {
            $this->returnError($result['json']);
        }
        return $result;
    }

    /**
     * 返回解密的参数
     *
     *
     * @return array 写入属性默认值和全球变量_W _GPC
     */
    protected function set_WAnd_GPC()
    {
        $para = $this->getPara();
        global $_W, $_GPC;
        $this->_W = $_W;
        $this->_GPC = $_GPC;
        if (is_array($para)) {
            $_GPC = array_merge($_GPC, $para);
        }
        $_GPC['m'] = 'sz_yi';
        $_W['uid'] = $para['uid'];
        $_GPC['__uid'] = $para['uid'];
        $para['uniacid'] = $_GET['uniacid'];
        $_GPC['directopenid'] = true;
        if (isset($para['uniacid'])) {
            //var_dump( $para['uniacid']);
            $_W['uniacid'] = $_GET['uniacid'];
            $_GPC['__uniacid'] = $para['uniacid'];
        }
    }

    /**
     * 判断管理员是否为正版用户
     *
     * @return bool
     */
    public function isFonder()
    {
        $para = $this->getPara();
        $founders = explode(',', $this->_W['config']['setting']['founder']);
        return in_array($para['uid'], $founders);
    }

    /**
     * 验证用户是否拥有该接口访问权限
     *
     * 详细描述（略）
     * @param array $permtypes 页面权限码
     * @return void
     */
    public function ca($permtypes)
    {
        if (!cv($permtypes)) {
            $this->returnError('您没有权限操作，请联系管理员!');
        }
    }

    /**
     * 获取当前访问管理员的ID
     *
     * 详细描述（略）
     * @return int 管理员的ID
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * 获取当前操作公众号的ID
     *
     * 详细描述（略）
     * @return int 公众号的ID
     */
    public function getUniacid()
    {
        return $this->uniacid;
    }

    /**
     * 管理员是否为供应商
     *
     * 详细描述（略）
     * @return bool
     */
    public function getOpenId()
    {
        $open_id = m('user')->getPerOpenid();
        return $open_id;
    }

    /**
     * 载入指定model
     *
     *
     * @param string $name model名
     * @return object model对象
     */
    public function m($name = '')
    {
        static $_modules = array();
        if (isset($_modules[$name])) {
            return $_modules[$name];
        }
        $model = SZ_YI_CORE . "model/api/" . strtolower($name) . '.php';
        if (!is_file($model)) {
            die(' Model ' . $name . ' Not Found!');
        }
        require $model;
        $class_name = '\api\model\\' . strtolower($name);
        $_modules[$name] = new $class_name();
        return $_modules[$name];

    }
}