<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
    exit('Access Denied');
}
if (!class_exists('DesignerModel')) {
    class DesignerModel extends PluginModel
    {
        public function getPage($type = 1)
        {
            global $_W, $_GPC;
            $page = pdo_fetch("SELECT * FROM " . tablename('sz_yi_designer') . " WHERE uniacid= :uniacid and pagetype=:type and setdefault=:default", array(
                ':uniacid' => $_W['uniacid'],
                ':type' => $type,
                ':default' => '1'
            ));
            if (empty($page)) {
                return false;
            }
            return $this->getData($page);
        }
        public function change(&$d, $cdata)
        {
            $d[$b['k1']][$b['k2']]['name']     = $cdata['title'];
            $d[$b['k1']][$b['k2']]['priceold'] = $cdata['productprice'];
            $d[$b['k1']][$b['k2']]['pricenow'] = $cdata['marketprice'];
            $d[$b['k1']][$b['k2']]['img']      = $cdata['thumb'];
            $d[$b['k1']][$b['k2']]['sales']    = $cdata['sales'];
            $d[$b['k1']][$b['k2']]['unit']     = $cdata['unit'];
        }

        public function getData($page)
        {
            global $_W;
            $data     = htmlspecialchars_decode($page['datas']);
            $d        = json_decode($data, true);
            $goodsids = array();
            foreach ($d as $k1 => &$dd) {
                if ($dd['temp'] == 'goods') {
                    if($dd['params']['style']=='hotel'){                       
                         if(empty($_SESSION['data'])){
                            $btime = strtotime(date('Y-m-d'));
                            $day=1;
                            $etime = $btime + $day * 86400;
                            $weekarray = array("日", "一", "二", "三", "四", "五", "六");
                            $arr['btime'] = $btime;
                            $arr['etime'] = $etime;
                            $arr['bdate'] = date('Y-m-d');
                            $arr['edate'] = date('Y-m-d', $etime);
                            $arr['bweek'] = '星期' . $weekarray[date("w", $btime)];
                            $arr['eweek'] = '星期' . $weekarray[date("w", $etime)];
                            $arr['day'] = $day; 
                            $_SESSION['data']=$arr;                           
                         }
                        $d[$k1]['session'] = $_SESSION['data'];
                        $d[$k1]['sessionurl'] ="/app/index.php?i=".$_W['uniacid']."&c=entry&method=date&p=designer&op=date&m=sz_yi&do=plugin";

                        //$d[$k1]['sessionurl'] ="/app/index.php?i=".$_W['uniacid']."&c=entry&op=date&p=hotel&do=shop&m=sz_yi";
                         foreach ($dd['data'] as $k2 => $ddd) {
                            //选择时间内是否有房
                            $btime =  $_SESSION['data']['btime'];
                            $bdate =  $_SESSION['data']['bdate'];
                            // 住几天
                            $days =intval( $_SESSION['data']['day']);
                            // 离店
                            $etime =  $_SESSION['data']['etime'];
                            $edate =  $_SESSION['data']['edate'] ;
                            $date_array = array();
                            $date_array[0]['date'] = $bdate;
                            $date_array[0]['day'] = date('j', $btime);
                            $date_array[0]['time'] = $btime;
                            $date_array[0]['month'] = date('m',$btime);    
                            if ($days > 1) {
                                for($i = 1; $i < $days; $i++) {
                                $date_array[$i]['time'] = $date_array[$i-1]['time'] + 86400;
                                $date_array[$i]['date'] = date('Y-m-d', $date_array[$i]['time']);
                                $date_array[$i]['day'] = date('j', $date_array[$i]['time']);
                                $date_array[$i]['month'] = date('m', $date_array[$i]['time']);
                                }
                            }
                            $sql2 = 'SELECT * FROM ' . tablename('sz_yi_hotel_room') . ' WHERE `goodsid` = :goodsid';
                            $params2 = array(':goodsid' =>$ddd['goodid']);
                            $room = pdo_fetch($sql2, $params2);
                            $r_sql = 'SELECT * FROM ' . tablename('sz_yi_hotel_room_price') .
                            ' WHERE `roomid` = :roomid AND `roomdate` >= :btime AND ' .
                            ' `roomdate` < :etime';
                            $params = array(':roomid' => $room['id'],':btime' => $btime, ':etime' => $etime);

                            $price_list = pdo_fetchall($r_sql, $params);
                            if ($price_list) {
                                $dd['data'][$k2]['has'] =0;
                                foreach($price_list as $k => $v) {     
                                    if ($v['status'] == 0 || $v['num'] == 0 ) {
                                       $dd['data'][$k2]['has'] +=1 ;   //不可预约              
                                    } 
                                }
                            }
                            // 当天房价
                            $today = date('Y-m-d') ;
                            $today= strtotime($today);           
                            $sql2 = "SELECT * FROM " . tablename('sz_yi_hotel_room_price') . " as p";
                            $sql2 .= " WHERE 1 = 1";
                            $sql2 .= " AND status = 1";
                            $sql2 .= " AND roomid = ". $room['id'];
                            $sql2 .= " AND roomdate =" . $today;
                            $todayprice = pdo_fetch($sql2);  
                            if($todayprice['oprice']=='0.00' || $todayprice['oprice']==''){
                            $dd['data'][$k2]['todayoprice'] = $room['oprice'];
                            }else{
                              $dd['data'][$k2]['todayoprice'] =$todayprice['oprice'];
                            }
                            if($todayprice['cprice']=='0.00' || $todayprice['cprice']==''){
                              $dd['data'][$k2]['todaycprice'] =  $room['cprice'];
                            }else{
                              $dd['data'][$k2]['todaycprice'] =$todayprice['cprice'];
                            }
                            $condition2 = ' and `uniacid` = :uniacid AND `goodsid` = :goodsid';
                            $params2    = array(
                                ':goodsid' => $ddd['goodid'],
                                ':uniacid' => $_W['uniacid']
                            );
                            $sql2 = "SELECT * FROM " . tablename('sz_yi_goods_param') . " where 1 {$condition2} ";
                            $dd['data'][$k2]['pram'] = pdo_fetchall($sql2, $params2);
                            //button url
                            $dd['data'][$k2]['url'] = "/app/index.php?i=".$_W['uniacid']."&c=entry&p=confirm&do=order&m=sz_yi&id=".$ddd['goodid'];
                            $dd['data'][$k2]['href'] = "/app/index.php?i=".$_W['uniacid']."&c=entry&p=detail&do=shop&m=sz_yi&id=".$ddd['goodid'];
                            $goodsids[] = array(
                                'id' => $ddd['goodid'],
                                'k1' => $k1,
                                'k2' => $k2
                            );
                        }
                    }else{
                        foreach ($dd['data'] as $k2 => $ddd) {
                        $goodsids[] = array(
                            'id' => $ddd['goodid'],
                            'k1' => $k1,
                            'k2' => $k2
                        );
                    }
                }
                
                } elseif ($dd['temp'] == 'richtext') {
                    $dd['content'] = $this->unescape($dd['content']);
                }
            }
            unset($dd);
            $arr = array();
            foreach ($goodsids as $a) {
                $arr[] = $a['id'];
            }
            if (count($arr) > 0) {
                $goodinfos = pdo_fetchall("SELECT id,title,productprice,marketprice,thumb,sales,unit FROM " . tablename('sz_yi_goods') . " WHERE id in ( " . implode(',', $arr) . ") and uniacid= :uniacid ", array(
                    ':uniacid' => $_W['uniacid']
                ), 'id');
                $goodinfos = set_medias($goodinfos, 'thumb');
                foreach ($d as $k1 => &$dd) {
                    if ($dd['temp'] == 'goods') {
                        foreach ($dd['data'] as $k2 => &$ddd) {
                            $cdata           = $goodinfos[$ddd['goodid']];
                            $ddd['name']     = $cdata['title'];
                            $ddd['priceold'] = $cdata['productprice'];
                            $ddd['pricenow'] = $cdata['marketprice'];
                            $ddd['img']      = $cdata['thumb'];
                            $ddd['sales']    = $cdata['sales'];
                            $ddd['unit']     = $cdata['unit'];
                        }
                        unset($ddd);
                    }
                }
                unset($dd);
            }
            $data           = json_encode($d);
            $data           = rtrim($data, "]");
            $data           = ltrim($data, "[");
            $pageinfo       = htmlspecialchars_decode($page['pageinfo']);
            $p              = json_decode($pageinfo, true);
            $page_title     = empty($p[0]['params']['title']) ? "未设置页面标题" : $p[0]['params']['title'];
            $page_desc      = empty($p[0]['params']['desc']) ? "未设置页面简介" : $p[0]['params']['desc'];
            $page_img       = empty($p[0]['params']['img']) ? "" : tomedia($p[0]['params']['img']);
            $page_keyword   = empty($p[0]['params']['kw']) ? "" : $p[0]['params']['kw'];
            $shopset        = m('common')->getSysset(array(
                'shop',
                'share'
            ));
            $system         = $shopset;
            $system['shop'] = set_medias($system['shop'], 'logo');
            $system         = json_encode($system);
            $pageinfo       = rtrim($pageinfo, "]");
            $pageinfo       = ltrim($pageinfo, "[");
            $ret            = array(
                'page' => $page,
                'pageinfo' => $pageinfo,
                'data' => $data,
                'share' => array(
                    'title' => $page_title,
                    'desc' => $page_desc,
                    'imgUrl' => $page_img
                ),
                'footertype' => intval($p[0]['params']['footer']),
                'footermenu' => intval($p[0]['params']['footermenu']),
                'system' => $system
            );
            if ($p[0]['params']['footer'] == 2) {
                $menuid = intval($p[0]['params']['footermenu']);
                $menu   = pdo_fetch('select * from ' . tablename('sz_yi_designer_menu') . ' where id=:id and uniacid=:uniacid limit 1', array(
                    ':id' => $menuid,
                    ':uniacid' => $_W['uniacid']
                ));
                if (!empty($menu)) {
                    $ret['menus']  = json_decode($menu['menus'], true);
                    $ret['params'] = json_decode($menu['params'], true);
                }
            }
            return $ret;
        }
        public function escape($str)
        {
            preg_match_all("/[\xc2-\xdf][\x80-\xbf]+|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}|[\x01-\x7f]+/e", $str, $r);
            $str = $r[0];
            $l   = count($str);
            for ($i = 0; $i < $l; $i++) {
                $value = ord($str[$i][0]);
                if ($value < 223) {
                    $str[$i] = rawurlencode(utf8_decode($str[$i]));
                } else {
                    $UCS2 = "UCS-2";
                    if(PATH_SEPARATOR == ':'){
                        // 如果是linux服务器使用UCS-2BE,防止乱码
                        $UCS2 = "UCS-2BE";
                    }
                    $str[$i] = "%u" . strtoupper(bin2hex(iconv("UTF-8", $UCS2, $str[$i])));
                }
            }
            return join("", $str);
        }
        public function unescape($str)
        {
            $ret = '';
            $len = strlen($str);
            for ($i = 0; $i < $len; $i++) {
                if ($str[$i] == '%' && $str[$i + 1] == 'u') {
                    $val = hexdec(substr($str, $i + 2, 4));
                    if ($val < 0x7f)
                        $ret .= chr($val);
                    else if ($val < 0x800)
                        $ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f));
                    else
                        $ret .= chr(0xe0 | ($val >> 12)) . chr(0x80 | (($val >> 6) & 0x3f)) . chr(0x80 | ($val & 0x3f));
                    $i += 5;
                } else if ($str[$i] == '%') {
                    $ret .= urldecode(substr($str, $i, 3));
                    $i += 2;
                } else
                    $ret .= $str[$i];
            }
            return $ret;
        }
        public function getGuide($system, $pageinfo)
        {
            global $_W, $_GPC;
            if (!empty($_GPC['preview'])) {
                $guide['followed'] = '0';
            } else {
                $guide['openid2']  = m('user')->getOpenid();
                $guide['followed'] = m('user')->followed($guide['openid2']);
            }
            if ($guide['followed'] != '1') {
                $system         = json_decode($system, true);
                $system['shop'] = set_medias($system['shop'], 'logo');
                $pageinfo       = json_decode($pageinfo, true);
                if (!empty($_GPC['mid'])) {
                    $guide['member1'] = pdo_fetch("SELECT id,nickname,openid,avatar FROM " . tablename('sz_yi_member') . " WHERE id=:mid and uniacid= :uniacid limit 1 ", array(
                        ':uniacid' => $_W['uniacid'],
                        ':mid' => $_GPC['mid']
                    ));
                    $guide['member2'] = pdo_fetch("SELECT id,nickname,openid FROM " . tablename('sz_yi_member') . " WHERE openid=:openid and uniacid= :uniacid limit 1 ", array(
                        ':uniacid' => $_W['uniacid'],
                        ':openid' => $guide['openid2']
                    ));
                }
                $guide['followurl'] = $system['share']['followurl'];
                if (empty($guide['member1'])) {
                    $guide['title1'] = $pageinfo['params']['guidetitle1'];
                    $guide['title2'] = $pageinfo['params']['guidetitle2'];
                    $guide['logo']   = $system['shop']['logo'];
                } else {
                    $pageinfo['params']['guidetitle1s'] = str_replace("[邀请人]", $guide['member1']['nickname'], $pageinfo['params']['guidetitle1s']);
                    $pageinfo['params']['guidetitle2s'] = str_replace("[邀请人]", $guide['member1']['nickname'], $pageinfo['params']['guidetitle2s']);
                    $pageinfo['params']['guidetitle1s'] = str_replace("[访问者]", $guide['member2']['nickname'], $pageinfo['params']['guidetitle1s']);
                    $pageinfo['params']['guidetitle2s'] = str_replace("[访问者]", $guide['member2']['nickname'], $pageinfo['params']['guidetitle2s']);
                    $guide['title1']                    = $pageinfo['params']['guidetitle1s'];
                    $guide['title2']                    = $pageinfo['params']['guidetitle2s'];
                    $guide['logo']                      = $guide['member1']['avatar'];
                }
            }
            return $guide;
        }
        public function getMenu($menuid = 0)
        {
            if (empty($menuid)) {
            }
        }
        public function getDefaultMenuID()
        {
            global $_W;
            return pdo_fetchcolumn('select id from ' . tablename('sz_yi_designer_menu') . ' where isdefault=1 and uniacid=:uniacid limit 1', array(
                ':uniacid' => $_W['uniacid']
            ));
        }
        public function getDefaultMenu()
        {
            global $_W;
            return pdo_fetch('select * from ' . tablename('sz_yi_designer_menu') . ' where isdefault=1 and uniacid=:uniacid limit 1', array(
                ':uniacid' => $_W['uniacid']
            ));
        }
        public function perms()
        {
            return array(
                'designer' => array(
                    'text' => $this->getName(),
                    'isplugin' => true,
                    'child' => array(
                        'page' => array(
                            'text' => '页面设置',
                            'view' => '浏览',
                            'edit' => '添加修改-log',
                            'delete' => '删除-log',
                            'setdefault' => '设置默认-log'
                        ),
                        'menu' => array(
                            'text' => '菜单设置',
                            'view' => '浏览',
                            'edit' => '添加修改-log',
                            'delete' => '删除-log',
                            'setdefault' => '设置默认-log'
                        )
                    )
                )
            );
        }

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
    }
}
