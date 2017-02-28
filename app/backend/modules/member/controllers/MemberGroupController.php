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
    /**
     * 会员分组列表
     * @Author::yitian 2017-02-24 qq:751818588
     * @access public
     **/
    public function index()
    {
        $uniacid = \YunShop::app()->uniacid;
        $groups_list = MemberGroup::getMemberGroupList($uniacid);
        //所在会员组会员人数
        //echo '<pre>'; print_r($groups_list); exit;
        $this->render('member/group', [
            'operation' => 'display',
            'groups_list' => $groups_list
        ]);
    }
    /**
     * 更新会员分组数据
     * @Author::yitian 2017-02-24 qq:751818588
     * @access public
     **/
    public function updateMemberGroup()
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
    /**
     * 添加会员分组列表【增】
     * @Author::yitian 2017-02-24 qq:751818588
     * @access public
     **/
    public function createMemberGroup()
    {
        $group = \YunShop::request()->group;
        $result = MemberGroup::createMembergroup($group);

        return $this->sendMessage($result);
    }
    /**
     * 删除会员分组【删】
     * @Author::yitian 2017-02-24 qq:751818588
     * @access public
     **/
    public function deleteMemberGroup()
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
     * 反馈结果
     * @Author::yitian 2017-02-24 qq:751818588
     * @access protected
     **/
    protected function sendMessage($result)
    {
        if($result) {
            Header("Location: ".$this->createWebUrl('member.membergroup.index'));exit;
        }
    }
}
