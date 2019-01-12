<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/1/12
 * Time: 15:05
 */

namespace app\Jobs;


use app\backend\modules\charts\modules\team\models\MemberMonthOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrderMemberMonthJob implements  ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct($order)
    {
        $this->orderId = $order;
    }

    public function handle()
    {

        $time = time();
        $year = date('Y',$time);
        $month = date('n',$time)-1;
        if($month == 0){
            $year -= 1;
            $month = 12;
        }
        $finder = MemberMonthOrder::uniacid()->where(['member_id'=>$this->order->uid,'year'=>$year,'month'=>$month])->first();
        if($finder){
            $finder->order_num += 1;
            $finder->order_price = bcadd($finder->order_price ,$this->order->price,2);
            $finder->save();
        }else{
            $data=[];
            $data['member_id'] = $this->order->uid;
            $data['year'] = $year;
            $data['month'] = $month;
            $data['order_num'] = 1;
            $data['order_price'] = $this->order->price;
            MemberMonthOrder::create($data);
        }

    }
}