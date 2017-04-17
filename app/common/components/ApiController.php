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
use app\common\helpers\Url;
use app\frontend\modules\member\services\MemberService;

class ApiController extends BaseController
{
    protected $publicAction = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function preAction()
    {
        parent::preAction();

        $set = MemberRelation::getSetInfo();

        if (!MemberService::isLogged() && !in_array($this->action,$this->publicAction)) {
            $type  = \YunShop::request()->type;

            if (empty($type) || $type == 'undefined') {
                $type = Client::getType();
            }
            
            if (5 == $type) {
                return $this->errorJson('',['login_status'=>1,'login_url'=>'']);
            }

            return $this->errorJson('',['login_status'=>0,'login_url'=>Url::absoluteApi('member.login.index', ['type'=>$type,'session_id'=>session_id()])]);
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