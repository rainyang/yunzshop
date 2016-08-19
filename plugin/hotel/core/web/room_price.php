<?php
if (!defined("IN_IA")) {
    print ("Access Denied");
}
global $_W, $_GPC;
//  START 判断是否当前用户是否供应商
ca('hotel.room_price');
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation =='updatelot_submit') {
            $rooms = $_GPC['rooms'];
            $rooms_arr = explode(",", $rooms);
            $days = $_GPC['days'];
            $days_arr = explode(",", $days);
            $oprices = $_GPC['oprice'];
            $cprices = $_GPC['cprice'];
            $mprices = $_GPC['mprice'];
            $start = strtotime($_GPC['start']);
            $end = strtotime($_GPC['end']);
            foreach ($rooms_arr as $v) {
                for ($time = $start; $time <= $end; $time+=86400) {
                    $week = date('w', $time);
                    if (in_array($week, $days_arr)) {
                        $roomprice =$this->model->getRoomPrice($hotelid, $v, date('Y-m-d', $time));
                        $roomprice['oprice'] = $oprices[$v];
                        $roomprice['cprice'] = $cprices[$v];
                        $roomprice['mprice'] = $mprices[$v];
                        if (empty($roomprice['id'])) {
                            pdo_insert("sz_yi_hotel_room_price", $roomprice);
                        } else {
                           // echo $roomprice['id'];
                           //print_r($roomprice);exit;
                            pdo_update("sz_yi_hotel_room_price", $roomprice, array("id" => $roomprice['id']));
                        }
                    }
                }
            }
            message("批量修改房价成功!", $this->createPluginWebUrl('hotel/room_price'), "success");
    }else if ($operation == 'updatelot_create') {  
            $rooms = $_GPC['rooms'];
            if (empty($rooms)) {
                die("");
            }
            $days = $_GPC['days'];
            $days_arr = implode(",", $days);
            $rooms_arr = implode(",", $rooms);
            $start = $_GPC['start'];
            $end = $_GPC['end'];
            $list = pdo_fetchall("select * from " . tablename("sz_yi_hotel_room") . " where id in (" . implode(",", $rooms) . ")");
            ob_start();
            include $this->template('room_price_lot_list');
            $data['result'] = 1;
            $data['code'] = ob_get_contents();
            ob_clean();
            die(json_encode($data));
   }else if ($operation == 'updatelot') { 
            //print_r($_POST);exit;
            //批量修改房价
            $startime = time();
            $firstday = date('Y-m-01', time());
            //当月最后一天
            $endtime = strtotime(date('Y-m-d', strtotime("$firstday +1 month -1 day")));
            $rooms = pdo_fetchall("select * from " . tablename("sz_yi_hotel_room") ."where uniacid =". $_W['uniacid']);
            include $this->template('room_price_lot');
            exit();

 } else if($operation =='doWebRoom_price'){ 
            // if (empty($_GPC['start']) || empty($_GPC['end'])) {
            //     die(json_encode(array("result" => 0, "error" => "请选择时间")));
            // }
           $ac = $_GPC['ac'];
           if ($ac == "getDate") {
              $start = $_GPC['start'];
            $end = $_GPC['end'];
            $btime = strtotime($start);
            $etime = strtotime($end);
            //日期列
            $days = ceil(($etime - $btime) / 86400);
            $pagesize = 10;
            $totalpage = ceil($days / $pagesize);
            $page = intval($_GPC['page']);
            if ($page > $totalpage) {
                $page = $totalpage;
            } else if ($page <= 1) {
                $page = 1;
            }
            $currentindex = ($page - 1) * $pagesize;
            $start = date('Y-m-d', strtotime(date('Y-m-d') . "+$currentindex day"));
            $btime = strtotime($start);
            $etime = strtotime(date('Y-m-d', strtotime("$start +$pagesize day")));
            $endtime= time()+7200;
            $date_array = array();
            $date_array[0]['date'] = $start;
            $date_array[0]['day'] = date('j', $btime);
            $date_array[0]['time'] = $btime;
            $date_array[0]['month'] = date('m', $btime);

            for ($i = 1; $i <= $pagesize; $i++) {
                $date_array[$i]['time'] = $date_array[$i - 1]['time'] + 86400;
                $date_array[$i]['date'] = date('Y-m-d', $date_array[$i]['time']);
                $date_array[$i]['day'] = date('j', $date_array[$i]['time']);
                $date_array[$i]['month'] = date('m', $date_array[$i]['time']);
            }
            $params = array();
            $sql = "SELECT r.* FROM " . tablename('sz_yi_hotel_room') . "as r";
            $sql .= " WHERE 1 = 1";
            $sql .= " AND uniacid =". $_W['uniacid'];
           // $sql .= " AND r.weid = $weid";
            $list = pdo_fetchall($sql, $params);
            foreach ($list as $key => $value) {
                $sql = "SELECT * FROM " . tablename('sz_yi_hotel_room_price');
                $sql .= " WHERE 1 = 1";
                $sql .= " AND roomid = " . $value['id'];
                $sql .= " AND roomdate >= " . $btime;
                $sql .= " AND roomdate < " . ($etime + 86400);
                $item = pdo_fetchall($sql);
                if ($item) {
                    $flag = 1;
                } else {
                    $flag = 0;
                }
                $list[$key]['price_list'] = array();

                if ($flag == 1) {
                    for ($i = 0; $i <= $pagesize; $i++) {
                        $k = $date_array[$i]['time'];
                        foreach ($item as $p_key => $p_value) {
                            //判断价格表中是否有当天的数据
                            if ($p_value['roomdate'] == $k) {
                                $list[$key]['price_list'][$k]['oprice'] = $p_value['oprice'];
                                $list[$key]['price_list'][$k]['cprice'] = $p_value['cprice'];
                                $list[$key]['price_list'][$k]['mprice'] = $p_value['mprice'];
                                $list[$key]['price_list'][$k]['roomid'] = $value['id'];
                               // $list[$key]['price_list'][$k]['hotelid'] = $hotelid;
                                $list[$key]['price_list'][$k]['has'] = 1;
                                break;
                            }
                        }
                        //价格表中没有当天数据
                        if (empty($list[$key]['price_list'][$k]['oprice'])) {
                            $list[$key]['price_list'][$k]['oprice'] = $value['oprice'];
                            $list[$key]['price_list'][$k]['cprice'] = $value['cprice'];
                            $list[$key]['price_list'][$k]['mprice'] = $value['mprice'];
                            $list[$key]['price_list'][$k]['roomid'] = $value['id'];
                          //  $list[$key]['price_list'][$k]['hotelid'] = $hotelid;
                        }
                    }
                } else {
                    //价格表中没有数据
                    for ($i = 0; $i <= $pagesize; $i++) {
                        $k = $date_array[$i]['time'];
                        $list[$key]['price_list'][$k]['oprice'] = $value['oprice'];
                        $list[$key]['price_list'][$k]['cprice'] = $value['cprice'];
                        $list[$key]['price_list'][$k]['mprice'] = $value['mprice'];
                        $list[$key]['price_list'][$k]['roomid'] = $value['id'];
                       // $list[$key]['price_list'][$k]['hotelid'] = $hotelid;
                    }
                }
            }
            $data = array();
            $data['result'] = 1;
            ob_start();
            include $this->template('room_price_list');
            $data['code'] = ob_get_contents();
            ob_clean();
            die(json_encode($data));          
        }else if ($ac == 'submitPrice') {  //修改价格
            $hotelid = intval($_GPC['hotelid']);
            $roomid = intval($_GPC['roomid']);
            $price = $_GPC['price'];
            $pricetype = $_GPC['pricetype'];
            $date = $_GPC['date'];
            $roomprice =$this->model->getRoomPrice($hotelid, $roomid, $date);
            $roomprice[$pricetype] = $price;
            if (empty($roomprice['id'])) {
                pdo_insert("sz_yi_hotel_room_price", $roomprice);
            } else {
                pdo_update("sz_yi_hotel_room_price", $roomprice, array("id" => $roomprice['id']));
            }
            die(json_encode(array("result" => 1, "hotelid" => $hotelid, "roomid" => $roomid, "pricetype" => $pricetype, "price" => $price)));
        } 
          

}
load()->func('tpl');
include $this->template('room_price');
