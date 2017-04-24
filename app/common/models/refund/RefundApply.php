<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/4/12
 * Time: 下午1:38
 */

namespace app\common\models\refund;

use app\common\models\BaseModel;
use app\common\observers\refund\RefundApplyObserver;
use app\frontend\modules\order\models\Order;

class RefundApply extends BaseModel
{
    protected $table = 'yz_order_refund';
    protected $hidden = ['updated_at', 'created_at', 'uniacid', 'uid', 'order_id'];
    protected $fillable = [];
    protected $guarded = ['id'];

    protected $appends = ['refund_type_name', 'status_name', 'button_models'];
    protected $attributes = [
        'images' => '[]',
        'refund_proof_imgs' => '[]',
        'content' => '',
        'reply' => '',
        'remark' => '',
        'refund_address' => '',
    ];
    protected $casts = [
        'images' => 'json',
        'refund_proof_imgs' => 'json'
    ];
    const REFUND_TYPE_MONEY = 0;
    const REFUND_TYPE_RETURN = 1;
    const REFUND_TYPE_GOODS = 2;
    const CLOSE = '-3';//关闭
    const CANCEL = '-2';//用户取消
    const REJECT = '-1';//驳回
    const WAIT_CHECK = '0';//待审核
    const WAIT_RETURN_GOODS = '1';//待退货
    const WAIT_RECEIVE_RETURN_GOODS = '2';//待收货
    const WAIT_REFUND = '3';//待打款
    const COMPLETE = '4';//已完成
    const CONSENSUS = '5';//手动退款

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        // todo 转移到前端
        if (!isset($this->uniacid)) {
            $this->uniacid = \YunShop::app()->uniacid;
        }
        if (!isset($this->uid)) {
            $this->uid = \YunShop::app()->getMemberId();
        }
    }

    /**
     * 前端获取退款按钮 todo 转移到前端的model
     * @return array
     */
    public function getButtonModelsAttribute()
    {
        $result = [];
        if ($this->status == self::WAIT_CHECK) {
            $result[] = [
                'name' => '修改申请',
                'api' => 'refund.edit',
                'value' => 1
            ];
            $result[] = [
                'name' => '取消申请',
                'api' => 'refund.cancel',
                'value' => 3
            ];
        }
        if ($this->status == self::WAIT_RETURN_GOODS) {
            $result[] = [
                'name' => '填写快递',
                'api' => 'refund.send',
                'value' => 2
            ];
        }
        return $result;
    }

    public function getDates()
    {
        return ['create_time', 'refund_time', 'operate_time', 'send_time', 'return_time', 'end_time', 'cancel_pay_time', 'cancel_send_time'] + parent::getDates();
    }

    public function scopeDefaults($query)
    {
        return $query->where('uid', \YunShop::app()->getMemberId())->with([
            'order' => function ($query) {
                $query->orders();
            }
        ])->orderBy('id','desc');
    }

    public function getRefundTypeNameAttribute()
    {
        $mapping = [
            self::REFUND_TYPE_MONEY => '退款',
            self::REFUND_TYPE_RETURN => '退货',
            self::REFUND_TYPE_GOODS => '换货',

        ];
        return $mapping[$this->refund_type];
    }

    protected function getStatusNameMapping()
    {
        return [
            self::CANCEL => '用户取消',
            self::REJECT => '驳回',
            self::WAIT_CHECK => '待审核',
            self::WAIT_RETURN_GOODS => '待退货',
            self::WAIT_RECEIVE_RETURN_GOODS => '商家待收货',
            self::WAIT_REFUND => '待退款',
            self::COMPLETE => '完成',
            self::CONSENSUS => '手动退款',
        ];

    }

    public function isCompleted()
    {
        return in_array($this->status, [self::COMPLETE, self::CONSENSUS]);
    }

    public function getStatusNameAttribute()
    {

        return $this->getStatusNameMapping()[$this->status];
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

//    public static function boot()
//    {
//        parent::boot();
//
//        static::observe(new RefundApplyObserver());
//    }


}