<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/11/14 上午10:22
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\finance\controllers;


use app\backend\modules\finance\models\Withdraw;
use app\backend\modules\member\models\MemberBankCard;
use app\backend\modules\member\models\MemberShopInfo;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\services\ExportService;
use Illuminate\Support\Facades\Config;

class WithdrawRecordsController extends BaseController
{
    private $withdrawModel;


    public function __construct()
    {
        parent::__construct();

        $this->withdrawModel = Withdraw::records();
    }


    /**
     * 提现记录
     * @return string
     */
    public function index()
    {
        $records = $this->getRecords();

        $page = PaginationHelper::show($records->total(), $records->currentPage(), $records->perPage());

        return view('finance.withdraw.records', [
            'records' => $records,
            'page' => $page,
            'search' => \YunShop::request()->search,
            'income_type' => Withdraw::getIncomeTypes(),
        ])->render();
    }




    /**
     * 提现记录导出
     */
    public function export()
    {
        $records = $this->getRecords();
        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($records, $export_page);

        $file_name = date('Ymdhis', time()) . '提现记录导出';

        $export_data[0] = [
            '提现编号',
            '粉丝',
            '姓名、手机',
            '收入类型',
            '提现方式',
            '申请金额',
            '申请时间',

            '打款至',

            '打款微信号',

            '支付宝姓名',
            '支付宝账号',

            '开户行',
            '开户行省份',
            '开户行城市',
            '开户行支行',
            '银行卡信息',
            '开户人姓名'
        ];
        foreach ($export_model->builder_model as $key => $item)
        {
            $export_data[$key + 1] = [
                $item->withdraw_sn,
                $item->hasOneMember->nickname,
                $item->hasOneMember->realname.'/'.$item->hasOneMember->mobile,
                $item->type_name,
                $item->pay_way_name,
                $item->amounts,
                $item->created_at->toDateTimeString(),
            ];
            if ($item->pay_way == 'manual') {
                switch ($item->manual_type) {
                    case 2:
                        $export_data[$key + 1][] = '微信';
                        $export_data[$key + 1] = array_merge($export_data[$key + 1], $this->getMemberWeChat($item->member_id));
                        break;
                    case 3:
                        $export_data[$key + 1][] = '支付宝';
                        $export_data[$key + 1] = array_merge($export_data[$key + 1], $this->getMemberAlipay($item->member_id));
                        break;
                    default:
                        $export_data[$key + 1][] = '银行卡';
                        $export_data[$key + 1] = array_merge($export_data[$key + 1], $this->getMemberBankCard($item->member_id));
                        break;
                }
            }
        }
        $export_model->export($file_name, $export_data, \Request::query('route'));
    }



    private function getMemberAlipay($member_id)
    {
        $yzMember = MemberShopInfo::select('alipayname','alipay')->where('member_id',$member_id)->first();
        return $yzMember ? [ '', $yzMember->alipayname ?: '', $yzMember->alipay ?: '' ] : ['', ''];
    }

    private function getMemberWeChat($member_id)
    {
        $yzMember = MemberShopInfo::select('wechat')->where('member_id',$member_id)->first();
        return $yzMember ? [ $yzMember->wechat ?: '' ] : [''];
    }

    private function getMemberBankCard($member_id)
    {
        $bankCard = MemberBankCard::where('member_id',$member_id)->first();
        if ($bankCard) {
            return [
                '', '', '',
                $bankCard->bank_name ?: '',
                $bankCard->bank_province ?: '',
                $bankCard->bank_city ?: '',
                $bankCard->bank_branch ?: '',
                $bankCard->bank_card ? $bankCard->bank_card . ",": '',
                $bankCard->member_name ?: ''
            ];
        }
        return ['','','','','','','','',''];
    }



    private function getRecords()
    {
        $search = \YunShop::request()->search;

        if ($search) {
            $this->withdrawModel->search($search);
        }

        return $this->withdrawModel->orderBy('created_at', 'desc')->paginate();
    }



}
