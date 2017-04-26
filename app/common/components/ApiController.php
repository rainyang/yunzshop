<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/28
 * Time: 上午10:49
 */

namespace app\common\components;


use app\backend\modules\member\models\MemberRelation;
use app\common\exceptions\AppException;
use app\common\helpers\Client;
use app\common\helpers\Url;
use app\common\models\UniAccount;
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
            return $this->errorJson('无此公众号', ['status' => -2]);
        }

        $relaton_set = MemberRelation::getSetInfo()->first();

        if (!MemberService::isLogged()
            && (($relaton_set->status == 1 && !in_array($this->action,$this->ignoreAction))
                || ($relaton_set->status == 0 && !in_array($this->action,$this->publicAction))
               )
        ) {
            $type  = \YunShop::request()->type;
            $mid   = \YunShop::request()->mid ? \YunShop::request()->mid : 0;

            \Log::debug('api mid', $mid);

            if (empty($type) || $type == 'undefined') {
                $type = Client::getType();
            }

            $queryString = ['type'=>$type,'session_id'=>session_id(), 'i'=>\YunShop::app()->uniacid, 'mid'=>$mid];

            if (5 == $type) {
                return $this->errorJson('',['login_status'=> 1,'login_url'=>'', 'type'=>$type,'session_id'=>session_id(), 'i'=>\YunShop::app()->uniacid, 'mid'=>$mid]);
            }

            return $this->errorJson('',['login_status'=> 0,'login_url'=>Url::absoluteApi('member.login.index', $queryString)]);
        } else {

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

}