<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
define('TM_COMMISSION_AGENT_NEW', 'commission_agent_new');
define('TM_BONUS_ORDER_PAY', 'bonus_order_pay');
define('TM_BONUS_ORDER_FINISH', 'bonus_order_finish');
define('TM_COMMISSION_APPLY', 'commission_apply');
define('TM_COMMISSION_CHECK', 'commission_check');
define('TM_BONUS_PAY', 'bonus_pay');
define('TM_BONUS_GLOBAL_PAY', 'bonus_global_pay');
define('TM_BONUS_UPGRADE', 'bonus_upgrade');
define('TM_COMMISSION_BECOME', 'commission_become');
if (!class_exists('HotelModel')) {
	class HotelModel extends PluginModel
	{
		 public function getSearchArray(){
        $search_array =$this->get_cookie($this->_search_key);
        if (empty($search_array)) {
            //默认搜索参数
            $search_array['order_type'] = 1;
            $search_array['order_name'] = 2;
            $search_array['location_p'] = $this->_set_info['location_p'];
            $search_array['location_c'] = $this->_set_info['location_c'];
            if (strpos($search_array['location_p'], '市') > -1) {
                //直辖市
                $search_array['municipality'] = 1;
                $search_array['city_name'] = $search_array['location_p'];
            } else {
                $search_array['municipality'] = 0;
                $search_array['city_name'] = $search_array['location_c'];
            }
            $search_array['business_id'] = 0;
            $search_array['business_title'] = '';
            $search_array['brand_id'] = 0;
            $search_array['brand_title'] = '';

            $weekarray = array("日", "一", "二", "三", "四", "五", "六");

            $date = date('Y-m-d');
            $time = strtotime($date);
            $search_array['btime'] = $time;
            $search_array['etime'] = $time + 86400;
            $search_array['bdate'] = $date;
            $search_array['edate'] = date('Y-m-d', $search_array['etime']);
            $search_array['bweek'] = '星期' . $weekarray[date("w", $time)];
            $search_array['eweek'] = '星期' . $weekarray[date("w", $search_array['etime'])];
            $search_array['day'] = 1;
            $this->insert_cookie($this->_search_key, $search_array);
        }
        //print_r($search_array);exit;
        return $search_array;
    }

    public function get_cookie($key)
    {
        global $_W;
        $key = $_W['config']['cookie']['pre'] . $key;
        return json_decode(base64_decode($_COOKIE[$key]), true);
    }
    public  function insert_cookie($key, $data)
    {
        global $_W, $_GPC;
        $session = base64_encode(json_encode($data));
        isetcookie($key, $session, !empty($_GPC['rember']) ? 7 * 86400 : 0);
    }

    public function getRoomPrice($hotelid, $roomid, $date) {
        global $_W;
        $btime = strtotime($date);
        $sql = "SELECT * FROM " . tablename('sz_yi_hotel_room_price');
        $sql .= " WHERE 1 = 1";
       // $sql .=" and weid=" . $_W['uniacid'];
       // $sql .= " AND hotelid = " . $hotelid;
        $sql .= " AND roomid = " . $roomid;
        $sql .= " AND roomdate = " . $btime;
        $sql .=" limit 1";
        $roomprice = pdo_fetch($sql);

        if (empty($roomprice)) {
            $room = $this->getRoom($hotelid, $roomid);
            $roomprice = array(
               // "weid" => $_W['uniacid'],
              //  "hotelid" => $hotelid,
                "roomid" => $roomid,
                "oprice" => $room['oprice'],
                "cprice" => $room['cprice'],
                "mprice" => $room['mprice'],
                "status" => $room['status'],
                "roomdate" => strtotime($date),
                "thisdate" => $date,
                "num" => "-1",
                "status" => 1,
            );
        }
        return $roomprice;
    }
    public function getRoom($hotelid, $roomid) {
        $sql = "SELECT * FROM " . tablename('sz_yi_hotel_room');
        $sql .= " WHERE 1 = 1";
        //$sql .= " AND hotelid = " . $hotelid;
        $sql .= " AND id = " . $roomid;
        $sql .=" limit 1";
        return pdo_fetch($sql);
    }

    public  function get_page_array($tcount, $pindex, $psize = 15)
    {
        global $_W;
        $pdata = array(
            'tcount' => 0,
            'tpage' => 0,
            'cindex' => 0,
            'findex' => 0,
            'pindex' => 0,
            'nindex' => 0,
            'lindex' => 0,
            'options' => ''
        );
        $pdata['tcount'] = $tcount;
        $pdata['tpage'] = ceil($tcount / $psize);
        if ($pdata['tpage'] <= 1) {
            $pdata['isshow'] = 0;
            return $pdata;
        }
        $cindex = $pindex;
        $cindex = min($cindex, $pdata['tpage']);
        $cindex = max($cindex, 1);
        $pdata['cindex'] = $cindex;
        $pdata['findex'] = 1;
        $pdata['pindex'] = $cindex > 1 ? $cindex - 1 : 1;
        $pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
        $pdata['lindex'] = $pdata['tpage'];
        if ($pdata['cindex'] == $pdata['lindex']) {
            $pdata['isshow'] = 0;
            $pdata['islast'] = 1;
        } else {
            $pdata['isshow'] = 1;
            $pdata['islast'] = 0;
        }
        return $pdata;
    }
    public function array_sort($arr, $keys, $type = 0)
    {
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v) {
            $keysvalue[$k] = $v[$keys];
        }
        if ($type == 0) {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k => $v) {
            $new_array[$k] = $arr[$k];
        }
        return $new_array;
    }


    public function check_plugin($pluginname = '')
        {
            global $_W, $_GPC;
            $acid = pdo_fetchcolumn("SELECT acid FROM " . tablename('account_wechats') . " WHERE `uniacid`=:uniacid LIMIT 1", array(':uniacid' => $_W['uniacid']));
            $ac_perm = pdo_fetch('select  plugins from ' . tablename('sz_yi_perm_plugin') . ' where acid=:acid limit 1', array(':acid' => $acid));
            if ($_W['role'] == 'founder') {
                return true;
            }
            if (!empty($ac_perm)) {
                $allow_plugins = explode(',', $ac_perm['plugins']);
                if (!in_array($pluginname, $allow_plugins)) {
                    $allow = false;
                }else{
                   $allow = true; 
                }
            } else {
                $allow = true;
            }
            return  $allow;
        }

    public function perms()
        {
            return array(
                'hotel' => array(
                    'text' => $this->getName(),
                    'isplugin' => true,
                    'child' => array(
                        'room_status' => array(
                            'text' => '房量/房态管理',
                            'view' => '浏览',
                            'edit' => '编辑-log',
                            // 'delete' => '删除-log',
                            // 'setdefault' => '设置默认-log'
                        ),
                        'room_price' => array(
                            'text' => '房价管理',
                            'view' => '浏览',
                            'edit' => '编辑-log',
                            // 'delete' => '删除-log',
                            // 'setdefault' => '设置默认-log'
                        ),   
                        'meet' => array(
                            'text' => '会议预约',
                            'view' => '浏览',
                            'edit' => '编辑-log',
                            // 'delete' => '删除-log',
                            // 'setdefault' => '设置默认-log'
                        ),
                        'rest' => array(
                            'text' => '餐饮预约',
                            'view' => '浏览',
                            'edit' => '编辑-log',
                            // 'delete' => '删除-log',
                            // 'setdefault' => '设置默认-log'
                        ),
                        'prints' => array(
                            'text' => '打印机设置',
                            'view' => '浏览',
                            'edit' => '编辑-log',
                            // 'delete' => '删除-log',
                            // 'setdefault' => '设置默认-log'
                        )
                    )
                )
            );
        }

 }
}
