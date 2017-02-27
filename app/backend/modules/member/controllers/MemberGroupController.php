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
            'groups_list' => $groups_list,
            'aaaa' => 'aaa'
        ]);
    }
    /**
     * 跳转更新会员分组页面
     * @Author::yitian 2017-02-24 qq:751818588
     * @access public
     **/
    public function addMemberGroup()
    {

        $this->render('member/addgroup', []);
    }
    /**
     * 添加会员分组列表
     * @Author::yitian 2017-02-24 qq:751818588
     * @access public
     **/
    public function createMemberGroup()
    {
        $data = $this->getData();
        $result = MemberGroup::createMembergroup($data);
        return $this->sendMessage($result);
    }
    /**
     * 修改会员分组
     * @Author::yitian 2017-02-24 qq:751818588
     * @access public
     **/
    public function updateMemberGroup()
    {

    }
    /**
     * 删除会员分组
     * @Author::yitian 2017-02-24 qq:751818588
     * @access public
     **/
    public function deleteMemberGroup()
    {
        $post = \YunShop::request()->get();
        $result = MemberGroup::deleteMemberGroup($post['id']);
        return $this->sendMessage($result);
    }
    /**
     * 获取提交值
     * @Author::yitian 2017-02-24 qq:751818588
     * @access protected
     **/
    protected function getData()
    {
        $post = \YunShop::request()->get();
        $data = array(
            'uniacid' => \YunShop::app()->uniacid,
            'group_name' => $post['group_name']
        );
        return $data;
    }
    /**
     * 数据处理
     * @Author::yitian 2017-02-24 qq:751818588
     * @access protected
     **/
    protected function dataFiltering($post)
    {
        $data = array(
            'group_name'    => $post['group_name'],
            'uniacid'       => $post['uniacid']
        );
        return $data;
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
