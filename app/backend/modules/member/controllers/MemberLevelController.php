<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/2/27
 * Time: 上午10:44
 */

namespace app\backend\modules\member\controllers;


use app\backend\modules\member\models\MemberLevel;
use app\common\components\BaseController;
use app\common\helpers\Url;

class MemberLevelController extends BaseController
{
    public $shopset;

    public function __construct()
    {
        $this->shopset = m('common')->getSysset('shop');
    }
    /**
     *  Membership list
     */
    public function index()
    {
        $level_list = MemberLevel::getMemberLevelList();

        $this->render('member/level', [
            'level_list' => $level_list,
            'shopset' => $this->shopset
        ]);
    }
    /**
     * Add member level
     */
    public function store()
    {
        $levelModel = new memberLevel();

        $requestLevel = \YunShop::request()->level;
        if($requestLevel) {
            //将数据赋值到model
            $levelModel->setRawAttributes($requestLevel);
            //其他字段赋值
            $levelModel->uniacid = \YunShop::app()->uniacid;

            //字段检测
            $validator = MemberLevel::validator($levelModel->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($levelModel->save()) {
                    //显示信息并跳转
                    return $this->message('添加会员等级成功', Url::absoluteWeb('member.member-level.index'));
                }else{
                    $this->error('添加会员等级失败');
                }
            }
        }
        $this->render('member/edit_level', [
            'level' => $levelModel,
            'shopset' => $this->shopset
        ]);
    }
    /**
     * Modify membership level
     */
    public function update()
    {
        $levelModel = MemberLevel::getMemberLevelById(\YunShop::request()->id);
        if(!$levelModel){
            return $this->message('无此记录或已被删除','','error');
        }
        $requestLevel = \YunShop::request()->level;
        if($requestLevel) {
            $levelModel->setRawAttributes($requestLevel);
            $validator = MemberLevel::validator($levelModel->getAttributes());
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                if ($levelModel->save()) {
                    return $this->message('修改会员等级信息成功', Url::absoluteWeb('member.member-level.index'));
                }else{
                    $this->error('修改会员等级信息失败');
                }
            }
        }

        $this->render('member/edit_level', [
            'level'     => $levelModel,
            'shopset'   => $this->shopset
        ]);
    }
    /**
     * Delete membership
     */
    public function destroy()
    {
        $level = MemberLevel::getMemberLevelById(\YunShop::request()->id);
        if(!$level) {
            return $this->message('无此品牌或已经删除','','error');
        }

        $result = MemberLevel::deleteMemberLevel(\YunShop::request()->id);
        if($result) {
            return $this->message('删除品牌成功',Url::absoluteWeb('member.member-level.index'));
        }else{
            return $this->message('删除品牌失败','','error');
        }
    }
}