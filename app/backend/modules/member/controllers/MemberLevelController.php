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
        uniacid();
        $level_list = MemberLevel::getMemberLevelList();
        //echo '<pre>'; print_r($shopset); exit;
        $this->render('member/level', [
            'operation' => 'display',
            'level_list' => $level_list,
            'shopset' => m('common')->getSysset('shop')
        ]);
    }
    /**
     * 跳转修改会员等级页面
     * @Author::yitian 2017-02-28 qq:751818588
     * @access public
     **/
    public function updateMemberLevel()
    {
        $post = \YunShop::request()->get();
        if($post['id']) {
            $level = MemberLevel::getMemberLevelInfoById($post['id']);
        }
        $this->render('member/level',[
            'operation' => 'post',
            'shopset' => m('common')->getSysset('shop'),
            'level' => $level
        ]);
    }
    public function reviseMemberLevel()
    {

        $level = \YunShop::request()->level;
        $level_id = $level['id'];
        unset($level['id']);
        $result = MemberLevel::updateMemberLevelInfoById($level_id, $level);
        if ($result) {
            Header("Location: ".$this->createWebUrl('member.memberlevel.index'));
            exit;
        }

    }
    /**
     * 跳转添加会员等级页面
     * @Author::yitian 2017-02-24 qq:751818588
     * @access public
     **/
    public function addMemberLevel()
    {

        $level = array(
            'id'    => '',
            'level' => '',
            'level_name' => '',
            'order_money' => '',
            'order_count' => '',
            'goods_id' => '',
            'discount' => ''
        );
        //echo '<pre>'; print_r($level); exit;
        $this->render('member/level',[
            'operation' => 'post',
            'shopset' => m('common')->getSysset('shop'),
            'level' => $level
        ]);
    }
    /**
     * 添加会员等级【增】
     * @Author::yitian 2017-02-27 qq:751818588
     * @access public
     **/
    public function createMemberLevel()
    {
        //$shopset = m('common')->getSysset('shop');
        $level = \YunShop::request()->level;
        $level['uniacid'] = \YunShop::app()->uniacid;
        //echo '<pre>'; print_r($level); exit;
        /*if($shopset['leveltype'] == '2') {
            $goodsid = $post['goodsid'];
        } else {
            $goodsid = '0';
        }
        $data = array(
            'uniacid'   => \YunShop::app()->uniacid,
            'level'     => $post['level'],
            'level_name' => $post['level_name'],
            'order_money'=> $post['order_money'],
            'order_count' => $post['order_count'],
            'discount'  => $post['discount'],
            'goodsid'   => $goodsid
        );*/
        $result = MemberLevel::createMemberLevel($level);
        //echo '<pre>'; print_r($result); exit;
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