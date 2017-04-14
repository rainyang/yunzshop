<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/4/2
 * Time: 下午2:25
 */

namespace app\common\models\finance;


use app\common\models\BaseModel;

/*
 * 余额转让记录
 *
 * */
class BalanceTransfer extends BaseModel
{
    public $table = 'yz_balance_transfer';

    protected $guarded = [''];

    /*
     * 关联会员数据表，一对一
     * @Author yitian */
    public function transferorInfo()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'transferor');
    }

    /*
     * 关联会员数据表，一对一
     * @Author yitian */
    public function recipientInfo()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'recipient');
    }

    /*
     * 获取余额转让记录分页列表，后台使用
     *
     * @return objece */
    public static function getTransferPageList($pageSize)
    {
        return self::uniacid()
            ->with(['transferorInfo' => function($transferorInfo) {
                return $transferorInfo->select('uid', 'nickname', 'realname', 'avatar', 'mobile');
            }])
            ->with(['recipientInfo' => function($recipientInfo) {
                return $recipientInfo->select('uid', 'nickname', 'realname', 'avatar', 'mobile');
            }])
            ->orderBy('created_at')->paginate($pageSize);
    }

    public static function getSearchPageList($pageSize, $search)
    {
        $query = static::uniacid();
        if ($search['keyword']) {
            $query = $query->whereHas('transferorInfo', function ($transferorInfo)use($search) {
                $transferorInfo->select('uid', 'nickname', 'realname', 'avatar', 'mobile')
                    ->where('nickname', 'like', $search['keyword']);
            });
        }
        return $query->orderBy('created_at')->paginate($pageSize);
    }

    /**
     * @param $recordId
     *
     * @return mixed */
    public static function getTransferRecordByRecordId($recordId)
    {
        return self::where('id', $recordId)->first();
    }

    /*
     * 获取会员余额转让记录
     *
     * @params int $transferId
     *
     * @return object
     * @Author yitian */
    public static function getMemberTransferRecord($transferId) {
        return self::uniacid()
            ->select('recipient', 'money', 'created_at', 'status')
            ->where('transferor', $transferId)
            ->with(['recipientInfo' => function($query) {
                return $query->select('uid', 'nickname', 'realname');
            }])
            ->get();
    }

    /*
     * 获取会员被转让记录
     *
     * @params int $recipientId
     *
     * @return object
     * @Author yitian */
    public static function getMemberRecipientRecord($recipientId) {
        return self::uniacid()
            ->select('transferor', 'money', 'created_at', 'status')
            ->where('recipient', $recipientId)
            ->with(['transferorInfo' => function($query) {
                return $query->select('uid', 'nickname', 'realname');
            }])
            ->get();
    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'uniacid'   => "公众号ID不能为空",
            'transferor'=> "转让者ID不能为空",
            'recipient' => '被转让者ID不能为空',
            'money'     => '充值金额必须是有效的数字，允许两位小数',
            'status'    => '状态不能为空'
        ];
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'uniacid'   => "required",
            'transferor'=> "required",
            'recipient' => 'required',
            'money'     => 'numeric|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            'status'    => 'required'
        ];
    }

}
