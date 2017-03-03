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

    public function index()
    {
        //echo '<pre>'; print_r('test); exit;
        $level_list = MemberLevel::getMemberLevelList();

        $this->render('member/level', [
            'level_list' => $level_list,
            'shopset' => $this->shopset
        ]);
    }
    public function create()
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
        $this->render('member/edit_level',[
            'shopset' => $this->shopset,
            'level' => $level
        ]);
    }
    public function store()
    {
        $level = \YunShop::request()->level;
        $level['uniacid'] = \YunShop::app()->uniacid;
        $result = MemberLevel::createMemberLevel($level);
        if($result) {
            return $this->message('添加会员等级成功。',Url::absoluteWeb('member.memberlevel.index'));
        }

    }
    public function edit()
    {
        $levelId = \YunShop::request()->id;
        $level = MemberLevel::getMemberLevelInfoById($levelId);

        $this->render('member/edit_level',[
            'shopset' => $this->shopset,
            'level' => $level
        ]);
    }
    public function update()
    {

        $level = \YunShop::request()->level;
        $level_id = $level['id'];
        unset($level['id']);
        $result = MemberLevel::updateMemberLevelInfoById($level_id, $level);
        if ($result) {
            return $this->message('品牌创建成功', Url::absoluteWeb('goods.brand.index'));
        }

    }
    public function destroy()
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