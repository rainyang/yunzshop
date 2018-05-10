<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/5/8 下午2:11
 * Email: livsyitian@163.com
 */

namespace app\frontend\modules\finance\controllers;


use app\backend\modules\member\models\MemberRelation;
use app\common\components\ApiController;
use app\common\models\Income;
use app\frontend\models\Member;
use app\frontend\modules\finance\factories\IncomePageFactory;
use app\frontend\modules\member\models\MemberModel;

class IncomePageController extends ApiController
{
    public function index()
    {
        $lang = \Setting::get('shop.lang', ['lang' => 'zh_cn']);
        //$langData = \Setting::get('shop.lang.' . $lang);
        //dd($lang);

        list($available, $unavailable) = $this->getIncomeInfo();

        $data = [
            'info' => $this->getPageInfo(),
            'available' => $available,
            'unavailable' => $unavailable
        ];

        return $this->successJson('ok', $data);
    }


    private function getPageInfo()
    {
        $member_id = \YunShop::app()->getMemberId();

        $memberModel = Member::select('nickname', 'avatar', 'uid')->whereUid($member_id)->first();
        return [
            'avatar' => $memberModel->avatar,
            'nickname' => $memberModel->nickname,
            'member_id' => $memberModel->uid,
            'grand_total' => $this->getGrandTotal(),
            'usable_total' => $this->getUsableTotal()
        ];
    }


    private function getIncomeInfo()
    {
        $lang_set = $this->getLangSet();
        $is_agent = $this->isAgent();
        $is_relation = $this->isOpenRelation();

        $config = $this->getIncomePageConfig();

        $available = [];
        $unavailable = [];
        foreach ($config as $key => $item) {

            $incomeFactory = new IncomePageFactory(new $item['class'], $lang_set, $is_relation, $is_agent);

            if (!$incomeFactory->isShow()) {
                continue;
            }

            $income_data = $incomeFactory->getIncomeData();

            if ($incomeFactory->isAvailable()) {
                $available[] = $income_data;
            } else {
                $unavailable[] = $income_data;
            }

            //unset($incomeFactory);
            //unset($income_data);
        }

        return [$available, $unavailable];
    }


    private function getLangSet()
    {
        $lang = \Setting::get('shop.lang', ['lang' => 'zh_cn']);

        return $lang[$lang['lang']];
    }


    /**
     * 是否开启关系链 todo 应该提出一个公用的服务
     *
     * @return bool
     */
    private function isOpenRelation()
    {
        $relation = MemberRelation::getSetInfo()->first();

        if (!is_null($relation) && 1 == $relation->status) {
            return true;
        }
        return false;
    }


    /**
     * 登陆会员是否是推客
     *
     * @return bool
     */
    private function isAgent()
    {
        return MemberModel::isAgent();
    }


    /**
     * 收入页面配置 config
     *
     * @return mixed
     */
    private function getIncomePageConfig()
    {
        return \Config::get('income_page');
    }


    //累计收入
    private function getGrandTotal()
    {
        return $this->getIncomeModel()->sum('amount');
    }

    //可提现收入
    private function getUsableTotal()
    {
        return $this->getIncomeModel()->where('status', 0)->sum('amount');
    }


    private function getIncomeModel()
    {
        $member_id = \YunShop::app()->getMemberId();

        return Income::uniacid()->where('member_id',$member_id);
    }

}
