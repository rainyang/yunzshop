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

    const CANCEL = '-2';
    const REJECT = '-1';
    const WAIT_CHECK = '0';
    const WAIT_SEND = '1';
    const WAIT_RECEIVE = '2';
    const WAIT_REFUND = '3';
    const COMPLETE = '4';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if (!isset($this->uniacid)) {
            $this->uniacid = \YunShop::app()->uniacid;
        }
        if (!isset($this->uid)) {
            $this->uid = \YunShop::app()->getMemberId();
        }
    }

    public function getButtonModelsAttribute()
    {
        $result = [
                [
                    'name' => '查看详情',
                    'api' => 'refund.detail',
                    'value' => 1
                ],
            ];
        if($this->status == self::WAIT_SEND){
            $result[] = [
                'name' => '填写快递',
                'api' => 'dispatch.refundExpress',
                'value' => 2 //todo
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
        ]);
    }

    public function getRefundTypeNameAttribute()
    {
        $mapping = [
            0 => '退款(仅退款不退货)',
            1 => '退款退货',
            2 => '换货',
        ];
        return $mapping[$this->refund_type];
    }

    public function getStatusNameAttribute()
    {
        $mapping = [
            self::CANCEL => '用户取消',
            self::REJECT => '驳回',
            self::WAIT_CHECK => '待审核',
            self::WAIT_SEND => '待退货',
            self::WAIT_RECEIVE => '待收货',
            self::WAIT_REFUND => '待退款',
            self::COMPLETE => '完成',
        ];
        
        return $mapping[$this->status];
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::observe(new RefundApplyObserver());
    }
}