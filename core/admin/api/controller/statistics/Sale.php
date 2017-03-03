<?php
/**
 * 管理后台APP API销售数据统计接口
 *
 * PHP version 5.6.15
 *
 * @package   统计模块
 * @author    shenyang <shenyang@yunzshop.com>
 * @version   v1.0
 */
//$api->validate('username','password');
//$this->ca('statistics.view.sale');
namespace admin\api\controller\statistics;
use api\model\commission;

class Sale extends \admin\api\YZ
{
    private $member_model;
    public function __construct()
    {
        parent::__construct();
        $this->member_model = new \admin\api\model\member();
        //$api->validate('username','password');
    }
    public function index(){
        global $_W;
        $first_time_of_today = strtotime(date('Y-m-d',time()));
        $sale['all'] = $this->getSaleData('sum(price)',array(
            ':uniacid' => $_W['uniacid'])
        );
        $sale['month'] = $this->getSaleData('sum(price)', array(
            ':uniacid' => $_W['uniacid'],
            ':starttime' => strtotime("-1 month",$first_time_of_today),
            ':endtime' => time()
        ));
        $count['yesterday_order'] = $this->getSaleData('count(*)', array(
            ':uniacid' => $_W['uniacid'],
            ':starttime' => strtotime("-1 day",$first_time_of_today),
            ':endtime' => $first_time_of_today,

        ));
        $count['new_member'] = $this->member_model->getCount(array(
            'uniacid' => $_W['uniacid'],
            'createtime' => $first_time_of_today,
        ));
        $count['week_order'] = $this->getSaleData('count(*)', array(
            ':uniacid' => $_W['uniacid'],
            ':starttime' => strtotime("-1 week",$first_time_of_today),
            ':endtime' => time()
        ));
        $rse = array(
            'sale'=>$sale,
            'count'=>$count
        );
        $this->returnSuccess($rse);
    }
    private function getSaleData($countfield, $map = array())
    {
        $condition = '1';
        if(isset($map[':uniacid'])){
            $condition .= ' AND uniacid=:uniacid';
        }
        if(isset($map[':starttime'])){
            $condition .= ' AND createtime >=:starttime';
        }
        if(isset($map[':endtime'])){
            $condition .= ' AND createtime <=:endtime';
        }
        return pdo_fetchcolumn("SELECT ifnull({$countfield},0) as cnt FROM " . tablename('sz_yi_order') . " WHERE {$condition} AND status>=1 ", $map);
    }

}
