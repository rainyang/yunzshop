<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/2/13
 * Time: 15:41
 */

namespace app\backend\modules\member\controllers;


use app\common\components\BaseController;
use app\common\models\member\MemberInvitationCodeLog;
use app\common\helpers\PaginationHelper;
use app\common\services\ExportService;
use app\common\models\MemberShopInfo;


class MemberInvitedController extends BaseController
{
    public function index()
    {
//        dd(\Yunshop::request());
        $search = \YunShop::request();
        // $mid = \Yunshop::request()->mid;
        // $member_id = \Yunshop::request()->member_id;
        // $invitation_code = \Yunshop::request()->invitation_code;
        // $member_invitation_model = new MemberInvitationCodeLog;
        $pageSize = 1;

        $list =  MemberInvitationCodeLog::
        // searchLog($search)
        with(['yzMember'=>function($query) {
            
            $query->with(['hasOneMember']);
        }])
        // ->with(['yzMember' => function($q){
        //             $q->with('hasOneMember');
        // }])
        ->paginate()
        // ->get();
        ->toArray();
        // dd($list['data'][0]['yz_member']['has_one_member']);
        foreach ($list['data'] as $k => $v) {
            $list['data'][$k]['mid'] = MemberInvitationCodeLog::uniacid()->where('member_id', $v['mid'])->first();
        }
        // dd($list);

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $pageSize);

        return view('member.invited', ['list'=>$list, 'pager'=>$pager])->render();
    }

    public function export()
    {
        $member_builder = MemberInvitationCodeLog::searchLog(\YunShop::request()->search);
        $export_page = request()->export_page ? request()->export_page : 1; 

        if (!$member_builder) {

        }
            
        $export_model = new ExportService($member_builder, $export_page);
        $file_name = date('Ymdhis', time()) . '邀请码使用情况导出';

        $export_data[0] = ['ID', '邀请人id', '被邀请人id', '邀请码', '注册时间'];

        $list = $export_model->builder_model->toArray();
        
        if ($list) {

            foreach ($list as $key => $item) {
                $export_data[$key + 1] = [$item['id'], $item['member_id'], $item['mid'], $item['invitation_code'],
                   date('YmdHis', $item['createtime'])];
            }
            
            $export_model->export($file_name, $export_data, \Request::query('route'));
        } else {
            return $this->message('暂无数据', yzWebUrl('member.member-invited.index'));
        }
    }
}