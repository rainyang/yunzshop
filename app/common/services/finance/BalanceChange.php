<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/24
 * Time: 下午5:08
 */

namespace app\common\services\finance;


use app\common\exceptions\AppException;
use app\common\models\finance\Balance;
use app\common\models\Member;
use app\common\models\notice\MessageTemp;
use app\common\services\credit\ConstService;
use app\common\services\credit\Credit;
use app\common\services\MessageService as Message;

class BalanceChange extends Credit
{

    private $new_value;


    /**
     * 实现基类中的抽象方法
     * 通过基类 data 中的 member_id 获取会员信息
     * @return mixed
     */
    public function getMemberModel()
    {
        return $this->memberModel = Member::select('uid', 'avatar', 'nickname', 'realname', 'credit2')->where('uid', $this->data['member_id'])->first() ?: false;
    }

    /**
     * 实现基类中的抽象方法
     * 记录数据写入
     * @return bool|\Illuminate\Support\MessageBag|string
     */
    public function recordSave()
    {
        $recordModel = new Balance();

        $recordModel->fill($this->getRecordData());
        $validator = $recordModel->validator();
        if ($validator->fails()) {
            return $validator->messages();
        }
        return $recordModel->save() ? true : '明细记录写入出错';
    }

    /**
     * 实现基类中的抽项方法
     * @return bool|string
     */
    public function updateMemberCredit()
    {
        $this->memberModel->credit2 = $this->new_value;

        if ($this->memberModel->save()) {
            $this->notice();
            return true;
        }
        return '写入会员余额失败';
        //return $this->memberModel->save() ? true : '写入会员余额失败';
    }


    /**
     * @return string
     */
    public function validatorData()
    {
        $this->new_value = $this->memberModel->credit2 + $this->change_value;
        if ($this->new_value < 0) {
            throw new AppException('余额不足');
        }
        if (!$this->relation()) {
            throw new AppException('该订单已经提交过，不能重复使用');
        }

        return true;
    }

    public function transfer(array $data)
    {
        if (!$data['recipient']) {
            throw new AppException('被转让者不存在');
        }

        $result = parent::transfer($data);

        $data['member_id'] = $data['recipient'];
        return $result === true ? $this->addition($data) : $result;
    }

    /**
     * 检测单号是否可用，为空则生成唯一单号
     * @return bool|string
     */
    private function relation()
    {
        if ($this->data['relation']) {
            $result = Balance::ofOrderSn($this->data['relation'])->ofSource($this->source)->ofMemberId($this->data['member_id'])->first();
            //dd($result);
            if ($result) {
                return false;
            }
            return $this->data['relation'];
        }
        return $this->createOrderSN();
    }

    /**
     * 生成唯一单号
     * @return string
     */
    public function createOrderSN()
    {
        $ordersn = createNo('BC', true);
        while (1) {
            if (!Balance::ofOrderSn($ordersn)->first()) {
                break;
            }
            $ordersn = createNo('BC', true);
        }
        return $ordersn;
    }

    /**
     * 明细记录 data 数组
     * @return array
     */
    private function getRecordData()
    {
        return [
            'uniacid'       => \YunShop::app()->uniacid,
            'member_id'     => $this->memberModel->uid,
            'old_money'     => $this->memberModel->credit2 ?: 0,
            'change_money'  => $this->change_value,
            'new_money'     => $this->new_value,
            'type'          => $this->type,
            'service_type'  => $this->source,
            'serial_number' => $this->relation(),
            'operator'      => $this->data['operator'],
            'operator_id'   => $this->data['operator_id'],
            'remark'        => $this->data['remark']
        ];
    }

    /**
     * 余额变动消息通知
     */
    private function notice()
    {
        $noticeMember = Member::getMemberByUid($this->memberModel->uid)->with('hasOneFans')->first();
        if (!$noticeMember->hasOneFans->openid) {
            return;
        }

        $temp_id = \Setting::get('shop.notice')['balance_change'];
        if (!$temp_id) {
            return;
        }
        $params = [
            ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
            ['name' => '昵称', 'value' => $this->memberModel->nickname],
            ['name' => '时间', 'value' => date('Y-m-d H:i', time())],
            ['name' => '余额变动金额', 'value' => $this->change_value],
            ['name' => '余额变动类型', 'value' => (new ConstService(''))->sourceComment()[$this->source]],
            ['name' => '变动后余额数值', 'value' => $this->new_value]
        ];
        $msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$msg) {
            return;
        }

        //todo 临时增加余额变动之为零时不发送消息通知，后期需要修改
        if ($noticeMember->hasOneFans->follow && $this->change_value > 0) {
            Message::notice(MessageTemp::$template_id, $msg, $noticeMember->uid);
        }
    }


}