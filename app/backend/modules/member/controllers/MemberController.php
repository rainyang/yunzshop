<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/2
 * Time: 下午2:03
 */

namespace app\backend\modules\member\controllers;


use app\backend\modules\member\models\McMappingFans;
use app\backend\modules\member\models\Member;
use app\backend\modules\member\models\MemberGroup;
use app\backend\modules\member\models\MemberLevel;
use app\backend\modules\member\models\MemberRecord;
use app\backend\modules\member\models\MemberShopInfo;
use app\backend\modules\member\models\MemberUnique;
use app\backend\modules\member\services\MemberServices;
use app\common\components\BaseController;
use app\common\events\member\MemberRelationEvent;
use app\common\events\member\RegisterByAgent;
use app\common\helpers\PaginationHelper;
use app\common\services\ExportService;
use Yunshop\Commission\models\Agents;


class MemberController extends BaseController
{
    private $pageSize = 20;

    /**
     * 列表
     *
     */
    public function index()
    {
        $groups = MemberGroup::getMemberGroupList();
        $levels = MemberLevel::getMemberLevelList();

        $parames = \YunShop::request();

        if (strpos($parames['search']['searchtime'], '×') !== FALSE) {
            $search_time = explode('×', $parames['search']['searchtime']);

            if (!empty($search_time)) {
                $parames['search']['searchtime'] = $search_time[0];

                $start_time = explode('=', $search_time[1]);
                $end_time = explode('=', $search_time[2]);

                $parames->times = [
                    'start' => $start_time[1],
                    'end' => $end_time[1]
                ];
            }
        }

        $list = Member::searchMembers($parames)
            ->orderBy('uid', 'desc')
            ->paginate($this->pageSize)
            ->toArray();
        $set = \Setting::get('shop.member');

        if (empty($set['level_name'])) {
            $set['level_name'] = '普通会员';
        }

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        $starttime = strtotime('-1 month');
        $endtime = time();

        if (isset($parames['searchtime']) &&  $parames['searchtime'] == 1) {
            if ($parames['times']['start'] != '请选择' && $parames['times']['end'] != '请选择') {
                $starttime = strtotime($parames['times']['start']);
                $endtime = strtotime($parames['times']['end']);
            }
        }

        return view('member.index', [
            'list' => $list,
            'levels' => $levels,
            'groups' => $groups,
            'endtime' => $endtime,
            'starttime' => $starttime,
            'total' => $list['current_page'] <= $list['last_page'] ? $list['total'] : 0,
            'pager' => $pager,
            'request' => \YunShop::request(),
            'set' => $set,
            'opencommission' => 1
        ])->render();
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

        if (!empty($member)) {
            $member = $member->toArray();

            if (1 == $member['yz_member']['is_agent'] && 2 == $member['yz_member']['status']) {
                $member['agent'] = 1;
            } else {
                $member['agent'] = 0;
            }

            $myform = json_decode($member['yz_member']['member_form']);
        }

        $set = \Setting::get('shop.member');

        if (empty($set['level_name'])) {
            $set['level_name'] = '普通会员';
        }

        if (0 == $member['yz_member']['parent_id']) {
            $parent_name = '总店';
        } else {
            $parent = Member::getMemberById($member['yz_member']['parent_id']);

            $parent_name = $parent->nickname;
        }

        return view('member.detail', [
            'member' => $member,
            'levels' => $levels,
            'groups' => $groups,
            'set'    => $set,
            'myform' => $myform,
            'parent_name' => $parent_name
        ])->render();
    }

    /**
     * 更新
     *
     */
    public function update()
    {
        $uid = \YunShop::request()->id ? intval(\YunShop::request()->id) : 0;

        $shopInfoModel = MemberShopInfo::getMemberShopInfo($uid) ?: new MemberShopInfo();

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
            'member_id' => $uid,
            'wechat' => $parame->data['wechat'],
            'parent_id' => $parame->data['parent_id'],
            'uniacid' => \YunShop::app()->uniacid,
            'level_id' => $parame->data['level_id'] ?: 0,
            'group_id' => $parame->data['group_id'],
            'alipayname' => $parame->data['alipayname'],
            'alipay' => $parame->data['alipay'],
            'is_black' => $parame->data['is_black'],
            'content' => $parame->data['content'],
            'custom_value' => $parame->data['custom_value'],
            'validity' => $parame->data['validity'] ? $parame->data['validity'] : 0,
        );

        if ($parame->data['agent']) {
            $yz['is_agent'] = 1;
            $yz['status'] = 2;

            if ($shopInfoModel->inviter == 0) {
                $shopInfoModel->inviter = 1;
                $shopInfoModel->parent_id = 0;
            }

        } else {
            $yz['is_agent'] = 0;
            $yz['status'] =  0;
        }

        $shopInfoModel->fill($yz);
        $validator = $shopInfoModel->validator();
        if ($validator->fails()) {
            $this->error($validator->messages());
        } else {
            if ($shopInfoModel->save()) {

                if ($parame->data['agent']) {
                    $member = Member::getMemberByUid($uid)->with('hasOneFans')->first();

                    event(new MemberRelationEvent($member));
                }

                return $this->message("用户资料更新成功", yzWebUrl('member.member.detail', ['id' => $uid]));
            }
        }
        return $this->message("用户资料更新失败", yzWebUrl('member.member.detail', ['id' => $uid]),'error');
    }

    /**
     * 删除
     *
     */
    public function delete()
    {
        $uid = \YunShop::request()->id ? intval(\YunShop::request()->id) : 0;

        if ($uid == 0 || !is_int($uid)) {
            return $this->message('参数错误', '', 'error');
        }

        $member = Member::getMemberInfoById($uid);

        if (empty($member)) {
            return $this->message('用户不存在', '', 'error');
        }

        if (MemberShopInfo::deleteMemberInfoById($uid)) {
            MemberUnique::deleteMemberInfoById($uid);

            return $this->message('用户删除成功', yzWebUrl('member.member.index'));
        } else {
            return $this->message('用户删除失败', yzWebUrl('member.member.index'), 'error');
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
            return $this->message('黑名单设置成功', yzWebUrl('member.member.index'));
        } else {
            return $this->message('黑名单设置失败', yzWebUrl('member.member.index'), 'error');
        }
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
        return view('member.query', [
            'members' => $member->toArray(),
        ])->render();
    }

    /**
     * 推广下线
     *
     * @return mixed
     */
    public function agent()
    {
        $request = \YunShop::request();

        $member_info = Member::getUserInfos($request->id)->first();

        if (empty($member_info)) {
            return $this->message('会员不存在','', 'error');
        }

        $list = Member::getAgentInfoByMemberId($request)
            ->paginate($this->pageSize)
            ->toArray();

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('member.agent', [
            'member' => $member_info,
            'list'  => $list,
            'pager' => $pager,
            'total' => $list['total'],
            'request' => $request
        ])->render();
    }

    /**
     * 数据导出
     *
     */
    public function export()
    {
        $member_builder = Member::searchMembers(\YunShop::request());
        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($member_builder, $export_page);

        $file_name = date('Ymdhis', time()) . '会员导出';

        $export_data[0] = ['会员ID', '粉丝', '姓名', '手机号', '等级', '分组', '注册时间', '积分', '余额', '订单', '金额', '关注', '提现手机号'];

        foreach ($export_model->builder_model->toArray() as $key => $item) {
            if (!empty($item['yz_member']) && !empty($item['yz_member']['group'])) {
                $group = $item['yz_member']['group']['group_name'];

            } else {
                $group = '';
            }

            if (!empty($item['yz_member']) && !empty($item['yz_member']['level'])) {
                $level = $item['yz_member']['level']['level_name'];

            } else {
                $level = '';
            }

            $order = $item['has_one_order']['total']?:0;
            $price = $item['has_one_order']['sum']?:0;

            if (!empty($item['has_one_fans'])) {
                if ($item['has_one_fans']['followed'] == 1) {
                    $fans = '已关注';
                } else {
                    $fans = '未关注';
                }
            } else {
                $fans = '未关注';
            }
            if (substr($item['nickname'], 0, strlen('=')) === '=') {
                $item['nickname'] = '，' . $item['nickname'];
            }

            $export_data[$key + 1] = [$item['uid'], $item['nickname'], $item['realname'], $item['mobile'],
                $level, $group, date('YmdHis', $item['createtime']), $item['credit1'], $item['credit2'], $order,
                $price, $fans, $item['yz_member']['withdraw_mobile']];
        }

        $export_model->export($file_name, $export_data, \Request::query('route'));
    }

    public function search_member()
    {
        $members    = [];
        $parent_id = \YunShop::request()->parent;

        if (is_numeric($parent_id)) {
            $member = Member::getMemberById($parent_id);

            if (!is_null($member)) {
                $members[] = $member->toArray();
            }

            if (0 == $parent_id) {
                $members = 0;
            }
        }

        return view('member.query', [
            'members' => $members
        ])->render();
    }

    public function change_relation()
    {
        $parent_id = \YunShop::request()->parent;
        $uid       = \YunShop::request()->member;

        if (is_numeric($parent_id)) {
            if (Member::setMemberRelation($uid, $parent_id)) {
                $member = MemberShopInfo::getMemberShopInfo($uid);

                $record = new MemberRecord();
                $record->uniacid   = \YunShop::app()->uniacid;
                $record->uid       = $uid;
                $record->parent_id = $member->parent_id;

                $member->parent_id = $parent_id;
                $member->save();
                $record->save();

                if (app('plugins')->isEnabled('commission')) {
                   $agents = Agents::uniacid()->where('member_id', $uid)->first();

                   if (!is_null($agents)) {
                       $agents->parent_id = $parent_id;
                       $agents->parent    = $member->relation;

                       $agents->save();
                   }
                }

                $agent_data = [
                    'member_id' => $uid,
                    'parent_id' => $parent_id,
                    'parent'   => $member->relation
                ];

                event(new RegisterByAgent($agent_data));

                return response(['status' => 1])->send();
            } else {
                return response(['status' => 0])->send();
            }
        }
    }

    public function member_record()
    {
        $records = MemberRecord::getRecord(\YunShop::request()->member);

        return view('member.record', [
            'records' => $records
        ])->render();
    }
}