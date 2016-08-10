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
class Apply extends \api\YZ
{
    public function __construct()
    {
        parent::__construct();
        //$this->validate('username','password');
    }

    public function index()
    {
        $para = $this->getPara();
        if ($para['status'] == -1) {
            $this->ca('commission.apply.view_1');
        } else {
            $this->ca('commission.apply.view' . $para['status']);
        }
        $commission_apply_model = new \model\api\commissionApply();

        $list = $commission_apply_model->getList( array(
            'id'=>$para['commission_apply_id'],
            'uniacid'=>$para['uniacid'],
            'status'=>$para['status'],
        ));
        
        
        dump($list);
        $this->returnSuccess($list);
    }
}
