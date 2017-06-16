<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/28
 * Time: 上午10:49
 */

namespace app\common\components;


use app\backend\modules\member\models\MemberRelation;
use app\common\exceptions\AppException;
use app\common\helpers\Client;
use app\common\helpers\Url;
use app\common\models\Member;
use app\common\models\MemberShopInfo;
use app\common\models\UniAccount;
use app\common\services\Session;
use app\frontend\modules\member\services\MemberService;

class ApiController extends BaseController
{
    protected $publicController = [];
    protected $publicAction = [];
    protected $ignoreAction = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function preAction()
    {
        parent::preAction();

        if(!UniAccount::checkIsExistsAccount(\YunShop::app()->uniacid)){
            return $this->errorJson('无此公众号', ['login_status' => -2]);
        }

        $relaton_set = MemberRelation::getSetInfo()->first();
        $type  = \YunShop::request()->type;

        if (!MemberService::isLogged()) {
            if (($relaton_set->status == 1 && !in_array($this->action,$this->ignoreAction))
                || ($relaton_set->status == 0 && !in_array($this->action,$this->publicAction))
            ) {
                \Log::debug('api mid', $mid);

                $this->jumpUrl($type, $mid);
            }
        } else {
            if (!MemberShopInfo::getMemberShopInfo(\YunShop::app()->getMemberId())) {
                Session::clear('member_id');

                if (($relaton_set->status == 1 && !in_array($this->action, $this->ignoreAction))
                    || ($relaton_set->status == 0 && !in_array($this->action, $this->publicAction))
                ) {
                    \Log::debug('api mid', $mid);

                    $this->jumpUrl($type, $mid);
                }
            }

            return $this->errorJson('',['login_status'=> 0,'login_url'=>Url::absoluteApi('member.login.index', $queryString)]);
        } else {
            if (!MemberShopInfo::getMemberShopInfo(\YunShop::app()->getMemberId())) {
                Session::clear('member_id');
            }

            if (MemberShopInfo::isBlack(\YunShop::app()->getMemberId())) {
                return $this->errorJson('黑名单用户，请联系管理员', ['login_status' => -1]);
            }

            $mid = Member::getMid();
            \Log::debug('Logined mid', $mid);

            //发展下线
            Member::chkAgent(\YunShop::app()->getMemberId(), $mid);
        }
    }

    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);
        //$validator->errors();
        if ($validator->fails()) {
            throw new AppException(current($this->formatValidationErrors($validator)));
        }
    }

/*    private function jumpUrl($type, $data)
    {
        if (5 == $type) {
            return $this->errorJson('',['login_status'=> 1,'login_url'=>'', 'type'=>$type,'session_id'=>session_id(), 'i'=>\YunShop::app()->uniacid, $data['mid']]);
        }

        return $this->errorJson('',['login_status'=> 0,'login_url'=>Url::absoluteApi('member.login.index', $data['$queryString'])]);
    }*/

}