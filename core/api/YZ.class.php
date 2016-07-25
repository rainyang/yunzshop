<?php
/**
 * 管理后台APP API接口基类
 *
 * @package   管理后台APP API
 * @author    shenyang<shenyang@yunzshop.com>
 * @version   v1.0
 */

namespace api;
require_once __API_ROOT__ . '/base.class.php';
spl_autoload_register(function ($class_name) {
    $name_space = substr($class_name,0,strrpos($class_name,'\\'));
    $parts = explode('\\', $class_name);
    if($name_space=='model\api'){
        array_unshift($parts,__API_ROOT__.'/..');
        $dir = implode('/',$parts);
        require $dir.'.php';
    }
});

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
        parent::__construct();
        $this->uid = $this->para['uid'];
        if (isset($this->uniacid)) {
            $this->uniacid = $this->para['uniacid'];
        }
        $this->set_WAnd_GPC();
    }
    /**
     * 返回解密的参数
     *
     *
     * @return array 写入属性默认值和全球变量_W _GPC
     */
    protected function set_WAnd_GPC()
    {
        global $_W, $_GPC;
        $this->_W = $_W;
        $this->_GPC = $_GPC;
        $_GPC = array_merge($_GPC, $this->para);
        $_W['uid'] = $this->para['uid'];
        if (isset($this->para['uniacid'])) {
            $_W['uniacid'] = $this->para['uniacid'];
        }
    }
    /**
     * 判断管理员是否为正版用户
     *
     * @return bool
     */
    public function isFonder()
    {
        $founders = explode(',', $this->_W['config']['setting']['founder']);
        return in_array($this->para['uid'], $founders);
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
    public function isSupplier($uid = false)
    {
        if (!$uid) {
            $uid = $this->uid;
        }
        if (!p('supplier'))
            return false;
        if (!p('supplier')->verifyUserIsSupplier($uid))
            return false;
        return true;
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