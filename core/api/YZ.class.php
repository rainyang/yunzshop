<?php

namespace Api;
require_once __API_ROOT__.'/base.class.php';

class YZ extends base
{
    protected $_W;
    protected $_GPC;

    protected $uid;
    protected $uniacid;

    public function __construct()
    {
        parent::__construct();
        $this->set_WAnd_GPC();
    }
    protected function set_WAnd_GPC(){
        global $_W,$_GPC;
        $this->_W = $_W;
        $this->_GPC = $_GPC;

        $_W['uid'] = $this->para['uid'];
        if(isset($this->para['uniacid'])){
            $_W['uniacid'] = $this->para['uniacid'];
        }
    }

    public function isFonder()
    {
        $founders = explode(',', $this->_W['config']['setting']['founder']);
        return in_array($this->para['uid'], $founders);
    }

    public function ca($permtypes)
    {
        if (!cv($permtypes)) {
            $this->returnError('您没有权限操作，请联系管理员!');
        }
    }

    public function getUid()
    {
        return $this->uid;
    }
    public function getUniacid()
    {
        return $this->uniacid;
    }

}