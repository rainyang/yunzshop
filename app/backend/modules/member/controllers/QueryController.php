<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/3/30
 * Time: 下午2:03
 */

namespace app\backend\modules\member\controllers;


use app\backend\modules\member\models\Member;
use app\common\components\BaseController;


class QueryController extends BaseController
{
    public function index()
    {
        $kwd = \YunShop::request()->keyword;
        if ($kwd) {
            $members = Member::getMemberByName($kwd)->toArray();
            return view('member.query', [
                'members' => $members
            ])->render();
        }
    }
}