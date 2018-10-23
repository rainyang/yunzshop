<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/5/8 下午2:11
 * Email: livsyitian@163.com
 */

namespace app\frontend\modules\finance\controllers;


use app\common\components\ApiController;
use app\common\helpers\ImageHelper;
use app\common\models\Income;
use app\frontend\models\Member;
use app\frontend\models\MemberRelation;
use app\frontend\modules\finance\factories\IncomePageFactory;
use app\frontend\modules\finance\services\PluginSettleService;
use app\frontend\modules\member\models\MemberModel;

class IncomePageController extends ApiController
{
    private $relationSet;


    public function __construct()
    {
        parent::__construct();
        $this->relationSet = $this->getRelationSet();
    }

    /**
     * 收入页面接口
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        list($available, $unavailable) = $this->getIncomeInfo();

        $data = [
            'info' => $this->getPageInfo(),
            'parameter' => $this->getParameter(),
            'available' => $available,
            'unavailable' => $unavailable,
        ];

        return $this->successJson('ok', $data);
    }
    
    /**
     * 页面信息
     *
     * @return array
     */
    private function getPageInfo()
    {
        $member_id = \YunShop::app()->getMemberId();

        $memberModel = Member::select('nickname', 'avatar', 'uid')->whereUid($member_id)->first();

        //IOS时，把微信头像url改为https前缀
        $avatar = ImageHelper::iosWechatAvatar($memberModel->avatar);
        return [
            'avatar' => $avatar,
            'nickname' => $memberModel->nickname,
            'member_id' => $memberModel->uid,
            'grand_total' => $this->getGrandTotal(),
            'usable_total' => $this->getUsableTotal()
        ];
    }


    private function getParameter()
    {
        return [
            'share_page' => $this->getSharePageStatus(),
            'plugin_settle_show' => PluginSettleService::doesIsShow(),  //领取收益 开关是否显示
        ];
    }


    /**
     * 收入信息
     * @return array
     * @throws \app\common\exceptions\AppException
     */
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


    /**
     * 获取商城中的插件名称自定义设置
     *
     * @return mixed
     */
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
        if (!is_null($this->relationSet) && 1 == $this->relationSet->status) {
            return true;
        }
        return false;
    }


    private function getSharePageStatus()
    {
        if (!is_null($this->relationSet) && 1 == $this->relationSet->share_page) {
            return true;
        }
        return false;
    }


    private function getRelationSet()
    {
        return MemberRelation::uniacid()->first();
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
