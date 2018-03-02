<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/16 下午7:03
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\charts\modules\member\controllers;


use app\backend\modules\charts\modules\member\models\Member;
use app\backend\modules\charts\modules\member\models\YzMember;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;

class OfflineCountController extends BaseController
{
    protected $page_size = 10;

    protected $yzMember;


    public function __construct()
    {
        parent::__construct();

        $this->yzMember = new YzMember();
    }


    public function index()
    {

        $data = $this->getData($this->getAllMembers());

        //分页
        $page_index = \YunShop::request()->page ?: 1;
        $page = PaginationHelper::show(sizeof($data) - $this->page_size, $page_index, $this->page_size);

        $start = $page_index * $this->page_size - $this->page_size;
        $end = $start + $this->page_size;

        $data = array_where($data, function ($value, $key) use($start,$end) {
            return $key >= $start && $key < $end;
        });

        return view('charts.member.offline_count',['data' => $data, 'page' => $page, 'search' => \YunShop::request()->search])->render();
    }


    private function getData(array $members)
    {
        $data = [];
        foreach ($members as $key => $member) {

            $lv1 = count($this->getLv1Offline($member->uid));
            $lv2 = count($this->getLv2Offline($member->uid));
            $lv3 = count($this->getLv3Offline($member->uid));

            $data[] = [
                'member_id'         => $member->uid,
                'member_name'       => $member->realname ?: $member->nickname,
                'avatar'            => $member->avatar,
                'lv1_offline_count' => $lv1,
                'lv2_offline_count' => $lv2,
                'lv3_offline_count' => $lv3,
                'offline_count'     => $lv1 + $lv2 + $lv3

            ];
        }

        //dd($data);
        return $this->arraySort($data, 'offline_count');
    }


    protected function arraySort(array $data, $field)
    {
        $data = array_values(array_sort($data, function ($value) use ($field) {
            return $value[$field];
        }));
        krsort($data);
        return  array_values($data);
    }



    protected function getLv1Offline($member_id)
    {
        return $this->yzMember->getLv1Offline($member_id);

    }

    protected function getLv2Offline($member_id)
    {
        return $this->yzMember->getLv2Offline($member_id);

    }

    protected function getLv3Offline($member_id)
    {
        return $this->yzMember->getLv3Offline($member_id);
    }



    protected function getAllMembers()
    {
        $query = Member::select('uid','nickname','realname','avatar')
            ->with(['yzMember' => function($query) {
                $query->select('member_id','parent_id','relation');
            }]);


        $search = \YunShop::request()->search;
        if ($search['member_id']) {
            $query = $query->where('uid',$search['member_id']);
        }
        if ($search['member_info']) {
            $query = $query->where('nickname', 'like', '%' . $search['member_info'] . '%')
                ->orWhere('realname', 'like', '%' . $search['member_info'] . '%')
                ->orWhere('mobile', 'like', $search['member_info'] . '%');
        }
        $members = $query->get();
        return empty($members) ? [] : $members;
    }


}
