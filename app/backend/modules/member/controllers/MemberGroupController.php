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
use app\common\helpers\Url;

class MemberGroupController extends BaseController
{
    /**
     *  Member group list
     */
    public function index()
    {
        $groupsList = MemberGroup::getMemberGroupList();
        $this->render('member/group', [
            'groups_list' => $groupsList
        ]);
    }
    /*
     * Add member group
     * */
    public function store()
    {
        $groupModel = new MemberGroup();

        $requestGroup = \YunShop::request()->group;
        if ($requestGroup) {
            $groupModel->setRawAttributes($requestGroup);
            $groupModel->uniacid = \YunShop::app()->uniacid;

            $validator = MemberGroup::validator($groupModel->getAttributes());
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($groupModel->save()) {
                    return $this->message("添加会员分组成功",Url::absoluteWeb('member.membergroup.index'));
                } else {
                    $this->error("添加会员分组失败");
                }
            }
        }

        $this->render('member/edit_group', ['group' => $requestGroup]);
    }
    /*
     *  Update member group
     * */
    public function update()
    {
        $groupModel = MemberGroup::getMemberGroupByGroupID(\YunShop::request()->id);
        if(!$groupModel) {
            return $this->message('未找到会员分组或已删除', Url::absoluteWeb('member.member-group.index'));
        }
        $requestGroup = \YunShop::request()->group;
        if ($requestGroup) {
            $groupModel->setRawAttributes($requestGroup);

            $validator = MemberGroup::validator($requestGroup);
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($groupModel->save()) {
                    return $this->message('修改会员分组信息成功。', Url::absoluteWeb('member.member-group.index'));
                } else {
                    $this->error('修改会员分组信息失败！！！');
                }
            }
        }
        $this->render('member/edit_group', [
            'group'     => $groupModel
        ]);
    }
    /*
     * Destory member group
     * */
    public function destroy()
    {
        $requestGroup = MemberGroup::getMemberGroupByGroupID(\YunShop::request()->id);
        if (!$requestGroup) {
            $this->error('未找到会员分组或已删除', Url::absoluteWeb('member.membergroup.index'));
        }
        $result = MemberGroup::deleteMemberGroup(\YunShop::request()->id);
        if ($result) {
            return $this->message("删除会员分组成功。", Url::absoluteWeb('member.membergroup.index'));
        } else {
            $this->error("删除会员分组失败");
        }
    }
}
