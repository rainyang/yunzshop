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
namespace admin\api\controller\commission;
class Agent extends \admin\api\YZ
{
    public function __construct()
    {
        parent::__construct();
        //$this->validate('username','password');
    }
    public function check(){
        $this->ca('commission.agent.check');
        $para = $this->getPara();
        $id     = intval($para['member_id']);
        $commission_model = new \admin\api\model\commission();

        $member = $commission_model->getInfo($id, array(
            'total',
            'pay'
        ));
        if (empty($member)) {
            $this->returnError('未找到会员信息，无法进行审核');
        }
        dump($member);
        if ($member['isagent'] == 1 && $member['status'] == 1) {
            $this->returnError('此分销商已经审核通过，无需重复审核!');
        }
        $time = time();
        pdo_update('sz_yi_member', array(
            'status' => 1,
            'agenttime' => $time
        ), array(
            'id' => $member['id'],
            'uniacid' => $para['uniacid']
        ));
        $commission_model->sendMessage($member['openid'], array(
            'nickname' => $member['nickname'],
            'agenttime' => $time
        ), TM_COMMISSION_BECOME);
        if (!empty($member['agentid'])) {
            $commission_model->upgradeLevelByAgent($member['agentid']);
        }
        plog('commission.agent.check', "审核分销商 <br/>分销商信息:  ID: {$member['id']} /  {$member['openid']}/{$member['nickname']}/{$member['realname']}/{$member['mobile']}");
        $this->returnSuccess(array(),'审核分销商成功!');
    }
    public function index()
    {
        $this->ca('commission.agent.view');
        $para = $this->getPara();
        $commission_model = new \admin\api\model\commission();
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
