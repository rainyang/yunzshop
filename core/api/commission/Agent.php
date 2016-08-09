<?php
/**
 * 管理后台APP API分销商
 *
 * PHP version 5.6.15
 *
 * @package
 * @author    shenyang <shenyang@yunzshop.com>
 * @version   v1.0
 */
namespace controller\api\commission;
class Agent extends \api\YZ
{
    public function __construct()
    {
        parent::__construct();
        //$this->validate('username','password');
    }

    public function index()
    {
        $this->ca('commission.agent.view');
        $para = $this->getPara();
        $commission_model = new \model\api\commission();
        $list = $commission_model->getList(
            array(
                'uniacid' => $para['uniacid'],
                'id' => $para['member_id'],
                'status' => $para['status'],
            )
        );
        dump($list);
        $this->returnSuccess($list);
    }
}
