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

class MemberLevelController extends BaseController
{

    /**
     * 会员等级列表
     * @Author::yitian 2017-02-27 qq:751818588
     * @access public
     **/
    public function index()
    {
        $level_list = MemberLevel::getMemberLevelList();
        //echo '<pre>'; print_r($shopset); exit;
        $this->render('member/level', [
            'operation' => 'display',
            'level_list' => $level_list,
            'shopset' => m('common')->getSysset('shop')
        ]);
    }
    /**
     * 跳转更新会员等级页面
     * @Author::yitian 2017-02-24 qq:751818588
     * @access public
     **/
    public function addMemberLevel()
    {
        $post = \YunShop::request()->get();
        if($post['op'] == 'post') {
            $this->render('member/level',[
                'operation' => 'post',
                'shopset' => m('common')->getSysset('shop')
            ]);
        }
    }
    /**
     * 添加会员等级【增】
     * @Author::yitian 2017-02-27 qq:751818588
     * @access public
     **/
    public function createMemberLevel()
    {
        $shopset = m('common')->getSysset('shop');
        $post = \YunShop::request()->get();
        if($shopset['leveltype'] == '2') {
            $goodsid = $post['goodsid'];
        } else {
            $goodsid = '0';
        }
        $data = array(
            'uniacid'   => \YunShop::app()->uniacid,
            'level'     => $post['level'],
            'levelname' => $post['levelname'],
            'ordermoney'=> $post['ordermoney'],
            'discount'  => $post['discount'],
            'goodsid'   => $goodsid
        );
        $result = MemberLevel::createMemberLevel($data);
        if($result) {
            Header("Location:" . $this->createWebUrl('member.memberlevel.index'));
            exit;
        }

    }
    /**
     * 删除会员等级【删】
     * @Author::yitian 2017-02-27 qq:751818588
     * @access public
     **/
    public function deleteMemberLevel()
    {
        $post = \YunShop::request()->get();
        if($post['id']) {
            $result = MemberLevel::deleteMemberLevel($post['id']);
            if ($result) {
                Header("Location: ".$this->createWebUrl('member.memberlevel.index'));
                exit;
            }
        }
        exit("删除失败！");
    }
}