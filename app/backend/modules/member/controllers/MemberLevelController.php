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
use app\common\facades\Setting;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;

class MemberLevelController extends BaseController
{
    /*
     * Member level pager list
     * 17.3,31 restructure
     *
     * @autor yitian */
    public function index()
    {
        $pageSize = 10;
        $levelList = MemberLevel::getLevelPageList($pageSize);
        $pager = PaginationHelper::show($levelList->total(), $levelList->currentPage(), $levelList->perPage());

        return view('member.level.list', [
            'levelList' => $levelList,
            'pager' => $pager,
            'shopSet' => Setting::get('shop.member')
        ])->render();

    }

    /*
     * Add member level
     *
     * @autor yitian */
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
            $validator = $levelModel->validator($levelModel->getAttributes());
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

        return view('member.level.form', [
            'level' => $levelModel,
            'shopSet' => Setting::get('shop.member')
        ])->render();
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
            $validator = $levelModel->validator($levelModel->getAttributes());
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
        return view('member.level.form', [
            'levelModel' => $levelModel,
            'shopSet' => Setting::get('shop.member')
        ])->render();
    }
    /*
     * Delete membership
     *
     * @author yitain */
    public function destroy()
    {
        $levelModel = MemberLevel::getMemberLevelById(\YunShop::request()->id);
        if(!$levelModel) {
            return $this->message('未找到记录或已删除','','error');
        }
        if($levelModel->delete()) {
            return $this->message('删等级成功',Url::absoluteWeb('member.member-level.index'));
        }else{
            return $this->message('删除等级失败','','error');
        }
    }



}