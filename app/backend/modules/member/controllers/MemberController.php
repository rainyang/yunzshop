<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/3/2
 * Time: 下午2:03
 */

namespace app\backend\modules\member\controllers;


use app\backend\modules\member\models\Member;
use app\backend\modules\member\services\MemberServices;
use app\common\components\BaseController;
use app\backend\modules\member\models\MemberLevel;
use app\backend\modules\member\models\MemberGroup;
use app\common\helpers\PaginationHelper;
use app\backend\modules\member\models\MemberShopInfo;

class MemberController extends BaseController
{
    private $groups;
    private $levels;

    private $pageSize = 20;

    public function __construct()
    {}

    /**
     * 列表
     *
     */
    public function index()
    {
        $groups = MemberGroup::getMemberGroupList();
        $levels = MemberLevel::getMemberLevelList();

        $list = Member::getMembers($this->pageSize);

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        if (empty($starttime) || empty($endtime)) {
            $starttime = strtotime('-1 month');
            $endtime   = time();
        }

        $this->render('member/member_list',[
            'list' => $list,
            'levels' => $levels,
            'groups' => $groups,
            'endtime' => $endtime,
            'starttime' => $starttime,
            'total' => $list['total'],
            'pager' => $pager,
            'opencommission'=>false
        ]);
    }

    /**
     * 详情
     *
     */
    public function detail()
    {
        $groups = MemberGroup::getMemberGroupList();
        $levels = MemberLevel::getMemberLevelList();

        $uid = \YunShop::request()->id ? intval(\YunShop::request()->id) : 0;

        if ($uid == 0 || !is_int($uid)) {
           $this->message('参数错误', '', 'error');
           exit;
        }

        $member = Member::getMemberInfoById($uid);

        $this->render('member/member_detail',[
            'member' => $member,
            'levels' => $levels,
            'groups' => $groups,
        ]);
    }

    /**
     * 更新
     *
     */
    public function update()
    {
        $uid = \YunShop::request()->id ? intval(\YunShop::request()->id) : 0;

        if ($uid == 0 || !is_int($uid)) {
            $this->message('参数错误', '', 'error');
            exit;
        }

        $parame = \YunShop::request();

        $mc = array(
            'realname' => $parame->data['realname']
        );

        Member::updateMemberInfoById($mc, $uid);

        $yz = array(
            'level_id' => $parame->data['level_id'],
            'group_id' => $parame->data['group_id'],
            'alipayname' => $parame->data['alipayname'],
            'alipay' => $parame->data['alipay'],
            'is_black' => $parame->data['is_black'],
            'content' => $parame->data['content']
        );

        MemberShopInfo::updateMemberInfoById($yz, $uid);

        if ($uid == 0 || !is_int($uid)) {
            $this->message('参数错误', '', 'error');
            exit;
        }


        $this->message("用户资料更新成功", $this->createWebUrl('member.member.detail', array('id'=>$uid)));
    }

    /**
     * 删除
     *
     */
    public function delete()
    {
        $uid = \YunShop::request()->id ? intval(\YunShop::request()->id) : 0;

        if ($uid == 0 || !is_int($uid)) {
            $this->message('参数错误', '', 'error');
            exit;
        }

        $member = Member::getMemberInfoById($uid);

        if (empty($member)) {
            $this->message('用户不存在', '', 'error');
            exit;
        }

        if (Member::deleteMemberInfoById($uid)) {
            MemberShopInfo::deleteMemberInfoById($uid);

            $this->message('用户删除成功', $this->createWebUrl('member.member.index'));
        } else {
            $this->message('用户删除失败', $this->createWebUrl('member.member.index'));
        }
    }

    /**
     * 设置黑名单
     *
     */
    public function black()
    {
        $uid = \YunShop::request()->id ? intval(\YunShop::request()->id) : 0;

        if ($uid == 0 || !is_int($uid)) {
            $this->message('参数错误', '', 'error');
            exit;
        }

        $data = array(
            'is_black' => \YunShop::request()->black
        );

        if (MemberShopInfo::setMemberBlack($uid, $data)) {
            $this->message('黑名单设置成功', $this->createWebUrl('member.member.index'));
        } else {
            $this->message('黑名单设置失败', '', 'error');
        }
    }

    public function search()
    {
        $groups = MemberGroup::getMemberGroupList();
        $levels = MemberLevel::getMemberLevelList();

        $parames = \YunShop::request();

        $list = Member::searchMembers($this->pageSize, $parames);

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        if (empty($starttime) || empty($endtime)) {
            $starttime = strtotime('-1 month');
            $endtime   = time();
        }

        $this->render('member/member_list',[
            'list' => $list,
            'levels' => $levels,
            'groups' => $groups,
            'endtime' => $endtime,
            'starttime' => $starttime,
            'total' => $list['total'],
            'pager' => $pager,
            'opencommission'=>false
        ]);

    }

    /**
     * 获取搜索会员
     * @return html
     */
    public function getSearchMember()
    {

        $keyword = \YunShop::request()->keyword;
        $member = Member::getMemberByName($keyword);
        $member = set_medias($member, array('avatar', 'share_icon'));
        return $this->render('web/member/query',['ds'=>$member->toArray()]);

    }
}