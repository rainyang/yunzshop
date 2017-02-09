<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 16/10/29
 * Time: 下午12:23
 */
if (!defined('IN_IA')) {
    exit('Access Denied');
}
if (!class_exists('liveModel')) {
    class liveModel extends PluginModel
    {

        /**
         * 获取主播id
         *
         * @param $openid
         * @return integer
         */
        public function getAid($openid)
        {
            global $_W;

            $aid = pdo_fetchcolumn("SELECT `id` FROM " . tablename('sz_yi_live_anchor') . " WHERE openid = :openid 
                AND uniacid = :uniacid AND `status` IN ('1','2') ", array(
                ':openid' => $openid,
                ':uniacid' => $_W['uniacid']
            ));

            return $aid;
        }


        /**
         * 获取主播uid
         *
         * @param integer $id 主播id
         * @return  integer 主播的uid
         */
        public function getUid($id)
        {
            global $_W;

            $uid = pdo_fetchcolumn("SELECT uid FROM " . tablename('sz_yi_live_anchor') . " WHERE id = :id 
                AND uniacid = :uniacid", array(
                ':id' => $id,
                ':uniacid' => $_W['uniacid']
            ));

            return $uid;
        }



        /**
         * 获取主播信息
         *
         * @param $openid
         * @return bool
         */
        public function getAnchorInfo($openid)
        {
            global $_W;

            $info = pdo_fetch("SELECT `id`, `status` FROM " . tablename('sz_yi_live_anchor') . " WHERE openid = :openid 
                AND uniacid = :uniacid  LIMIT 1 ", array(
                ':openid' => $openid,
                ':uniacid' => $_W['uniacid']
            ));

            return $info;
        }

        /**
         * 本地数据库存储主播信息
         *
         * @param $openid
         * @param $member
         */
        public function saveLocalAnchor($openid, $member, $status)
        {
            global $_W;

            pdo_insert('sz_yi_live_anchor', array('uniacid' => $_W['uniacid'], 'uid' => $member['id'], 'openid' => $openid, 'status' => $status,
                'createtime' => date('Y-m-d H:i:s', time())
            ));

            return pdo_insertid();
        }

        /**
         * 获取成为主播条件
         *
         * @return array
         */
        public function getAnchorConditions()
        {
            global $_W;

            $info = pdo_fetch("SELECT `conditions`, `is_check` FROM " . tablename('sz_yi_live_base') . " WHERE uniacid = :uniacid LIMIT 1" , array(
                ':uniacid' => $_W['uniacid']
            ));

            return $info;
        }

        /**
         * 保存主播申请资料
         *
         * @param $data
         */
        public function saveAnchorRemindInfo($data)
        {
            pdo_insert('sz_yi_live_reminder', $data);
        }

        /**
         * 获取主播审核材料
         *
         * @param $uid
         * @return bool
         */
        public function getAnchorApplyInfo($uid)
        {
            global $_W;

            $sql = "SELECT la.uid, lr.mobile, lr.auth_img0, lr.auth_img1  FROM " . tablename('sz_yi_live_reminder') . " lr JOIN " . tablename('sz_yi_live_anchor') . " la ON (la.id = lr.aid) WHERE la.uniacid = :uniacid AND la.uid = " . $uid . " AND la.status = '0' ORDER BY lr.id DESC LIMIT 1";

            $result = pdo_fetch($sql, array(':uniacid'=>$_W['uniacid']));

            return $result;
        }

        /**
         * 更新主播审核状态
         *
         * @param $uid
         * @param $status
         * @return bool
         */
        public function updateStatusAnchor($uid, $status)
        {
            global $_W;

            $data = array('status'=>$status);

            if (pdo_update('sz_yi_live_anchor', $data, array('uniacid'=>$_W['uniacid'], 'uid'=>$uid))) {
                return true;
            } else {
                return false;
            }

        }

        /**
         * 更新主播的云端的主播id和云端的房间id
         *
         * @param string $openid
         * @param $aid 云端的主播id
         * @param $rid 云端的房间id
         * @return bool
         */
        public function updateAnchorCloudData($openid, $cloud_anchor_id, $cloud_room_id)
        {
            global $_W;

            $data = array('cloud_anchor_id'=>$cloud_anchor_id, 'cloud_room_id'=>$cloud_room_id);

            $result = pdo_update('sz_yi_live_anchor', $data, array('openid'=>$openid));

            if ($result) {
                return true;
            } else {
                return false;
            }

        }

        /**
         * 创建主播房间
         *
         * @param $member
         * @return boolean
         */
        public function createRoom($member)
        {
            load()->func('communication');

            //创建主播
            $create_anchor_url = SZ_YI_LIVE_CLOUD_URL . '/admin_live.php?api=anchor/Add';
                
                //主播信息
                $anchor_data = array(
                    'status' => 1, //请求创建房间的前提, 就是已经通过审批, 可以直播
                    'mobile' => $member['mobile'],
                    'nickname' => $member['nickname'],
                    'avatar' => $member['avatar']
                );

            $anchor_result = ihttp_request($create_anchor_url, $anchor_data);
            $anchor_result_array = json_decode($anchor_result['content'], true);
            $anchor_id = $anchor_result_array['data']['anchor_id'];


            //创建直播间
            $domain = $_SERVER['HTTP_HOST'];
            $anchor_room_url = SZ_YI_LIVE_CLOUD_URL . '/shop_live.php?api=room/Add';

            $name = pdo_fetchcolumn('SELECT `name` FROM ' . tablename('uni_account') . " WHERE uniacid = :uniacid LIMIT 1", array(':uniacid' => $member['uniacid']));

                //云端存储主播/房间信息
                $room_data = array(
                    'mobile' => $member['mobile'],
                    'domain' => $domain,
                    'uniacid' => $member['uniacid'],
                    'thumb' => '',
                    'title' => $member['nickname'].'的直播间',
                    'shop_name' => $name,
                    'create_time' => date('Y-m-d H:i:s', time()),
                    'is_hot' => '0',
                    'is_recommand' => '0',
                    'status' => '0'
                );

            $room_result = ihttp_request($anchor_room_url, $room_data);
            $room_result_array = json_decode($room_result['content'], true);
            $room_id = $room_result_array['data']['room_id'];

            if(!empty($anchor_id) && !empty($room_id)){
                return array('cloud_anchor_id'=>$anchor_id, 'cloud_room_id'=>$room_id);
            } else {
                return 66;
            }

        }

        /**
         * 获取主播openid
         *
         * @param $uid
         * @return bool|int
         */
        public function getAnchorOpenid($uid)
        {
            global $_W;

            $openid = pdo_fetchcolumn("SELECT openid FROM " . tablename('sz_yi_live_anchor') . " WHERE uniacid=:uniacid AND uid=:uid", array(':uniacid' => $_W['uniacid'], ':uid' => $uid));

            return $openid ? $openid : 0;
        }

        /**
         * 获取流量统计
         *
         * @param $start_time
         * @param $end_time
         * @return array
         */
        public function getStream($start_time='', $end_time='')
        {
            global $_W;

            $domain = $_SERVER['HTTP_HOST'];
            $uniacid = $_W['uniacid'];
            $getUrl = SZ_YI_LIVE_CLOUD_URL . "/shop_live.php?api=stream_log/Get&domain=".$domain."&uniacid=".$uniacid."&start_time=".$start_time."&end_time=".$end_time;

            $ch = curl_init();  
            curl_setopt($ch, CURLOPT_URL, $getUrl);  
            curl_setopt($ch, CURLOPT_HEADER, 0);  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   
            curl_setopt($ch, CURLOPT_HEADER, 0);

            $data = curl_exec($ch); 
            $response = json_decode($data,1);

            if($response['result'] == 1){
                return $response['data'];
            }else{
                return false;
            }
            
        }
        //字节数转换成带单位的
        /* 原理是利用对数求出欲转换的字节数是1024的几次方。
         * 其实就是利用对数的特性确定单位。
        */
        function size2mb($size,$digits=2){ //digits，要保留几位小数
            $unit= array('','K','M','G','T','P');//单位数组，是必须1024进制依次的哦。
            $base= 1024;//对数的基数
            $i   = floor(log($size,$base));//字节数对1024取对数，值向下取整。
            return round($size/pow($base,$i),$digits).' '.$unit[$i] . 'B';
        }

    }
}

