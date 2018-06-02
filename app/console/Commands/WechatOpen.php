<?php

namespace app\Console\Commands;

use app\backend\modules\charts\models\Member;
use app\common\models\AccountWechats;
use app\frontend\modules\member\models\MemberUniqueModel;
use Illuminate\Console\Command;

class WechatOpen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syn:wechatUnionid {uniacid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '微信开发平台同步Unionid';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $uniacid = $this->argument('uniacid');

        $this->synRun($uniacid);
    }


    private function synRun($uniacid)
    {
        $member_info = Member::getMembers()->get();
\Log::debug('----member----', count($member_info));
        $account = AccountWechats::getAccountByUniacid($uniacid);
        $appId = $account->key;
        $appSecret = $account->secret;
\Log::debug('-----account----', [$account]);
        $global_access_token_url = $this->_getAccessToken($appId, $appSecret);

        $global_token = \Curl::to($global_access_token_url)
            ->asJsonResponse(true)
            ->get();

        $this->requestWechatApi($uniacid, $member_info, $global_token);
    }

    private function requestWechatApi($uniacid, $member_info, $global_token)
    {
        $member_total = 0;
        $update_total = 0;

        if (!is_null($member_info)) {
            $member_total = count($member_info);

            $time = time();
            $path = 'logs/' . $time . '_member_openid.log';
            $upgrade_path = 'logs/' . $time . '_upgrade_member_openid.log';

            $result = collect($member_info)->each(function($item) use ($uniacid, $global_token, $path, $upgrade_path, &$update_total) {
                $this->printLog($path, $item->hasOneFans->openid);
                \Log::debug('-----into----');
                $global_userinfo_url = $this->_getInfo($global_token['access_token'], $item->hasOneFans->openid);
                \Log::debug('-----global_userinfo_url----', [$global_userinfo_url]);
                $user_info = \Curl::to($global_userinfo_url)
                    ->asJsonResponse(true)
                    ->get();
                \Log::debug('-----user_info----', [$user_info]);
                if (isset($user_info['errcode'])) {
                    \Log::debug('----error---');
                    return ['error' => 1, 'msg' => $user_info['errmsg']];
                }
                \Log::debug('-----error_info----');
                if (isset($user_info['unionid'])) {
                    $UnionidInfo = MemberUniqueModel::getUnionidInfo($uniacid, $user_info['unionid'])->first();

                    if (is_null($UnionidInfo)) {
                        MemberUniqueModel::insertData(array(
                            'uniacid' => $uniacid,
                            'unionid' => $user_info['unionid'],
                            'member_id' => $item->hasOneFans->uid,
                            'type' => 1
                        ));

                        $this->printLog($upgrade_path, $item->hasOneFans->openid);

                        $update_total++;
                    } else {
                        //TODO UPDATE
                    }

                }
            });
\Log::debug('----step1-----');
            if (isset($result)) {
                \Log::debug('-----step2----');
                return $result;
            }
        }

        return [
            'error' => 0,
            'data' =>[
                'total'   => $member_total,
                'upgrade' => $update_total
            ],
            'msg' => 'ok'
        ];
    }

    private function printLog($path, $openid)
    {
        file_put_contents(storage_path($path), $openid . "\r\n", FILE_APPEND);
    }

    /**
     * 获取全局ACCESS TOKEN
     * @return string
     */
    private function _getAccessToken($appId, $appSecret)
    {
        return 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appId . '&secret=' . $appSecret;
    }

    /**
     * 获取用户信息
     *
     * 是否关注公众号
     *
     * @param $accesstoken
     * @param $openid
     * @return string
     */
    private function _getInfo($accesstoken, $openid)
    {
        return 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $accesstoken . '&openid=' . $openid;
    }
}
