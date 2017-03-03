<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/23
 * Time: 下午6:08
 */

namespace app\backend\modules\member\controllers;


use app\backend\modules\member\models\MemberGroup;
use app\common\components\BaseController;

class MemberGroupController extends BaseController
{
    protected $uniacid;

    public function __construct()
    {
        $this->uniacid = \Yunshop::app()->uniacid;
    }

    public function index()
    {
        $groupsList = MemberGroup::getMemberGroupList($this->uniacid);

        //所在会员组会员人数
        //echo '<pre>'; print_r($groupsList); exit;
        $this->render('member/group', [
            'operation' => 'display',
            'groups_list' => $groupsList
        ]);
    }

    public function update()
    {
        $groupId = \YunShop::request()->id;
        if($groupId) {
            $group = MemberGroup::getMemberGroupByGroupID($groupId);
        }else{
            $group = array(
                'id'        => '',
                'group_name' => '',
                'uniacid'   => ''
            );
        }
        $this->render('member/add_group', [
            'group'     => $group
        ]);
    }
    public function create()
    {
        $group = \YunShop::request()->group;
        $result = MemberGroup::createMembergroup($group);
        echo $result;exit;
        return $this->sendMessage($result);
    }
    /**
     * 删除会员分组【删】
     * @Author::yitian 2017-02-24 qq:751818588
     * @access public
     **/
    public function delete()
    {
        $group_id = \YunShop::request()->id;
        $result = MemberGroup::deleteMemberGroup($group_id);
        return $this->sendMessage($result);
    }
    /**
     * 修改会员分组【改】
     * @Author::yitian 2017-02-24 qq:751818588
     * @access public
     **/
    public function reviseMemberGroup()
    {
        $group = \YunShop::request()->group;
        $result = MemberGroup::updateMemberGroupNameByGroupId($group['id'], $group['group_name']);
        $this->sendMessage($result);
    }
    /**
     * 反馈结果Combined data
     * @Author::yitian 2017-02-24 qq:751818588
     * @access protected
     **/
    protected function sendMessage($result)
    {
        if($result) {
            Header("Location: ".$this->createWebUrl('member.membergroup.index'));exit;
        }
    }
    protected function combinedDate()
    {

    }
}
