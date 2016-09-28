<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/9/27
 * Time: 下午7:54
 */
namespace app\api\controller\member;
@session_start();

use app\api\YZ;

class Account extends YZ
{
    private $_json_datas;

    public function __construct()
    {
        parent::__construct();

        $this->_json_datas = $this->callMobile('member/center');
    }

    public function index()
    { //echo '<pre>';print_r($this->_json_datas);exit;
        if (!empty($this->_json_datas) && !empty($this->_json_datas['json']['member'])) {
              $res = array(
                  'id'     => $this->_json_datas['json']['member']['id'],
                  'avatar'     => $this->_json_datas['json']['member']['avatar'],
                  'nickname'   => $this->_json_datas['json']['member']['nickname'],
                  'levelname'   => $this->_json_datas['json']['level']['levelname'],
                  'levelurl'   => $this->_json_datas['json']['set']['shop']['levelurl'],
                  'isreferrer'   => $this->_json_datas['json']['shop_set']['shop']['isreferrer'],
                  'referrer_realname'   => $this->_json_datas['json']['referrer']['realname'],
              );

            $this->returnSuccess($res);
        } else {
            $this->returnError("请重新登录!");
        }


    }

}