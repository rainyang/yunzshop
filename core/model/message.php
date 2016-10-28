<?php
/*=============================================================================
#     FileName: message.php
#         Desc: 消息类
#       Author: Yunzhong - http://www.yunzshop.com
#        Email: 913768135@qq.com
#     HomePage: http://www.yunzshop.com
#      Version: 0.0.1
#   LastChange: 2016-02-05 02:33:25
#      History:
=============================================================================*/
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class Sz_DYi_Message
{
    public function sendTplNotice($touser, $template_id, $postdata, $url = '', $account = null)
    {
        if (!$account) {
            $account = m('common')->getAccount();
        }
        if (!$account) {
            return;
        }

        $setdata = m("cache")->get("sysset");
        $set     = unserialize($setdata['sets']);

        $app = $set['app']['base'];

        if (is_app() && !empty($app['leancloud']['switch']) && !empty($app['wx']['switch'])) {
            $this->sendAppNotice($account, $touser, $template_id, $postdata);
        } else {
            return $account->sendTplNotice($touser, $template_id, $postdata, $url);
        }

    }
    public function sendAppNotice($account, $touser, $template_id, $postdata){
		$token = $account->getAccessToken();
		if (is_error($token)) {
            return $token;
        }

        $post_url = "https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token={$token}";
        $response = ihttp_request($post_url);

        if(is_error($response)) {
            return error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
        }

        if ($response['status'] == 'OK') {
            $template_list = json_decode($response['content'],true);

            foreach ($template_list['template_list'] as $tp_list) {
                if (trim($template_id) == $tp_list['template_id']) {

                    if(preg_match_all("/{{[^}}]*}}/", $tp_list['content'] ,$match)) {
                        foreach ($match[0] as $v) {
                           $match_content[] = substr($v,2,-7);
                        }

                        foreach ($match_content as $filter) {
                            $tp_list['content'] = str_replace("{{". $filter .".DATA}}", $postdata[$filter]["value"] . "\n", $tp_list['content']);
                        }

                    } else {
                        echo "不匹配.";
                    }

                    /**
                     * app消息通知
                     */
                    $setdata = m("cache")->get("sysset");
                    $set     = unserialize($setdata['sets']);

                    $app = $set['app']['base'];

                    if (!empty($app['leancloud']['switch'])) {
                        $this->appSendContent($touser, $postdata, $tp_list['content']);
                    }
                }
            }
        }


    }
    public function sendCustomNotice($openid, $msg, $url = '', $account = null)
    {
        if (!$account) {
            $account = m('common')->getAccount();
        }
        if (!$account) {
            return;
        }
        $content = "";
        if (is_array($msg)) {
            foreach ($msg as $key => $value) {
                if (!empty($value['title'])) {
                    $content .= $value['title'] . ":" . $value['value'] . "\n";
                } else {
                    $content .= $value['value'] . "\n";
                    if ($key == 0) {
                        $content .= "\n";
                    }
                }
            }
        } else {
            $content = $msg;
        }

        if (!empty($url)) {
            $content .= "<a href='{$url}'>点击查看详情</a>";
        }
        return $account->sendCustomNotice(array(
            "touser" => $openid,
            "msgtype" => "text",
            "text" => array(
                'content' => urlencode($content)
            )
        ));

    }

    public function sendCustomAppNotice($openid, $msg, $url = '', $account = null)
    {
        /**
         * app消息通知
         */

        $setdata = m("cache")->get("sysset");
        $set     = unserialize($setdata['sets']);

        $app = $set['app']['base'];

        if (!empty($app['switch']) && !empty($app['leancloud']['switch'])) {
            if (!$account) {
                $account = m('common')->getAccount();
            }
            if (!$account) {
                return;
            }
            $content = "";
            if (is_array($msg)) {
                foreach ($msg as $key => $value) {
                    if (!empty($value['title'])) {
                        $content .= $value['title'] . ":" . $value['value'] . "\n";
                    } else {
                        $content .= $value['value'] . "\n";
                        if ($key == 0) {
                            $content .= "\n";
                        }
                    }
                }
            } else {
                $content = $msg;
            }


            $this->appSendContent($openid, $msg, $content);
        }
    }
    public function sendImage($openid, $mediaid)
    {
        $account = m('common')->getAccount();
        return $account->sendCustomNotice(array(
            "touser" => $openid,
            "msgtype" => "image",
            "image" => array(
                'media_id' => $mediaid
            )
        ));
    }
	public function sendNews($openid, $articles, $account = null)
	{
		if (!$account) {
			$account = m('common')->getAccount();
		}
		return $account->sendCustomNotice(array('touser' => $openid, 'msgtype' => 'news', 'news' => array('articles' => $articles)));
	}

    public function appSendContent($openid, $msg, $content) {
            pdo_insert('sz_yi_message',array('openid'=>$openid,'title'=>$msg['first']['value'],
                'contents'=>$content));
            sent_message(array($openid),$msg['first']['value']);
    }

    //群发消息
    public function sendmsg($filesn, $uniacid){
        $_W['uniacid'] = $uniacid;
        $data = array();
        $file_path = IA_ROOT . "/addons/sz_yi/data/message/" . $filesn . ".log";
        if(file_exists($file_path)){
            $data = unserialize(file_get_contents(IA_ROOT . "/addons/sz_yi/data/message/" . $filesn . ".log"));
        }

        if($data){
            //获取公众号函数
            load()->model('account');
            if (!empty($_W['acid'])) {
                $account = WeAccount::create($_W['acid']);
            } else {
                $acid = pdo_fetchcolumn("SELECT acid FROM " . tablename('account_wechats') . " WHERE `uniacid`=:uniacid LIMIT 1", array(
                    ':uniacid' => $_W['uniacid']
                ));
                $account = WeAccount::create($acid);
            }
            foreach ($data as $value) {
                if (!empty($value['templateid'])) {
                    m('message')->sendTplNotice($value['openid'], $value['templateid'], $value['msg'], '', $account);
                } else {
                    m('message')->sendCustomNotice($value['openid'], $value['msg'], "", $account);
                }
            }
        }
    }
}
