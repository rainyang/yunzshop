<?php

namespace app\frontend\controllers;

use app\backend\modules\member\models\MemberRelation;
use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\helpers\Cache;
use app\common\repositories\OptionRepository;
use app\common\services\goods\VideoDemandCourseGoods;
use app\common\services\PluginManager;
use app\frontend\models\Member;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\shop\controllers\IndexController;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Yunshop\Designer\models\Designer;
use Yunshop\Designer\models\DesignerMenu;

class HomePageController extends ApiController
{
    protected $publicAction = ['index', 'defaultDesign', 'defaultMenu', 'defaultMenuStyle', 'bindMobile', 'wxapp'];
    protected $ignoreAction = ['index', 'defaultDesign', 'defaultMenu', 'defaultMenuStyle', 'bindMobile', 'wxapp'];

    /**
     * 该接口需要前后段配合一起优化一下，明确参数、返回值
     *
     *
     * @return \Illuminate\Http\JsonResponse 当路由不包含page_id参数时,提供商城首页数据; 当路由包含page_id参数时,提供装修预览数据
     */
    public function index()
    {
        $result = $this->getWeChatPageData();


        //增加验证码功能
        $status = Setting::get('shop.sms.status');
        if (extension_loaded('fileinfo')) {
            $captcha = self::captchaTest();
            if ($status == 1) {
                $result['captcha'] = $captcha;
                $result['captcha']['status'] = $status;
            }
        }
        return $this->successJson('ok', $result);
    }


    private function getWeChatPageData()
    {
        $result = [
            'item' => $this->getPageInfo(),         //装修信息
            'applet' => $this->getApplet(),         //小程序默认装修数据
            'system' => $this->getSystem(),         //系统设置 todo 与 mailInfo 数据重复
            //'default' => self::defaultDesign(),     //店铺装修插件关闭时使用
            'mailInfo' => $this->getMailInfo(),     //系统设置 todo 与 system 数据重复
            'memberinfo' => $this->getMemberInfo(), //会员信息 todo 不知道做什么使用
        ];
        if(!app('plugins')->isEnabled('designer')) {
            $result['default'] =>  self::defaultDesign();
        }

    }


    private function getPageInfo()
    {
        if(app('plugins')->isEnabled('designer')) {

            $page_info = $this->getDesignerPageInfo();
        } else {
            $page_info = $this->getDefaultPageInfo();
        }
        return array(
            'page' => $page_info['page'],
            'data' => $page_info['data'],
            'menus' => $page_info['menus'],
            'share' => $page_info['share'],
            'params' => $page_info['params'],
            'pageinfo' => $page_info['pageinfo'],
            'menustyle' => $page_info['menustyle'],
            'footertype' => $page_info['footertype'],
            'footermenu' => $page_info['footermenu'],
        );
    }


    /**
     * todo 还需要进一步优化
     */
    private function getDesignerPageInfo()
    {

        $i = \YunShop::request()->i;
        $mid = \YunShop::request()->mid;
        $type = \YunShop::request()->type;
        $pageId = \YunShop::request()->page_id ?:0;
        $member_id = \YunShop::app()->getMemberId();

        //装修数据, 原来接口在 plugin.designer.home.index.page
        if(empty($pageId)){ //如果是请求首页的数据
            if(!Cache::has('desiginer_page_0')) {
                $page = Designer::getDefaultDesigner();
                Cache::put('desiginer_page_0', $page, 4200);
            } else {
                $page = Cache::get('desiginer_page_0');
            }
        } else{
            $page = Designer::getDesignerByPageID($pageId);
        }

        if ($page) {

            if (Cache::has($member_id.'_desiginer_default_0')) {
                $designer = Cache::get($member_id.'_desiginer_default_0');
            } else {
                $designer = (new \Yunshop\Designer\services\DesignerService())->getPageForHomePage($page->toArray());
                Cache::put($member_id.'_desiginer_default_0', $designer,180);
            }

            $store_goods = null;
            if (app('plugins')->isEnabled('store-cashier')) {
                $store_goods = new \Yunshop\StoreCashier\common\models\StoreGoods();
            }

            //课程商品判断
            $videoDemand = new VideoDemandCourseGoods();
            $video_open  = $videoDemand->whetherEnabled();

            foreach ($designer['data'] as &$value) {
                if ($value['temp'] == 'goods') {
                    foreach ($value['data'] as &$info) {
                        $info['is_course'] = 0;

                        if ($video_open) {
                            $info['is_course'] = $videoDemand->isCourse($info['goodid']);
                        }

                        $info['goods_type'] = 0;
                        $info['store_id'] = 0;

                        if (!is_null($store_goods)) {
                            $store_id = $store_goods->where('goods_id', $info['goodid'])->value('store_id');

                            if ($store_id) {
                                $info['goods_type'] = 1;
                                $info['store_id'] = $store_id;
                            }
                        }

                        if ($info['is_course']) {
                            $info['goods_type'] = 2;
                        }
                    }
                }
            }
            $footerMenuType = $designer['footertype'];
            $footerMenuId = $designer['footermenu'];

            //底部菜单: 0 - 不显示, 1 - 显示系统默认, 2 - 显示选中的自定义菜单
            switch ($footerMenuType){
                case 1:
                    $designer['menus'] = self::defaultMenu($i, $mid, $type);
                    $designer['menustyle'] = self::defaultMenuStyle();
                    break;
                case 2:
                    /*
                     * 如果自定义菜单ID不存在，使用自定义菜单中默认启用的菜单
                     */
                    if ($footerMenuId) {
                        $menustyle = DesignerMenu::getMenuById($footerMenuId);
                    } else {
                        $menustyle = DesignerMenu::getDefaultMenu();
                    }

                    if(!empty($menustyle->menus) && !empty($menustyle->params)){
                        $designer['menus'] = json_decode($menustyle->toArray()['menus'], true);
                        $designer['menustyle'] = json_decode($menustyle->toArray()['params'], true);
                    } else{
                        $designer['menus'] = $this->defaultMenu($i, $mid, $type);
                        $designer['menustyle'] = $this->defaultMenuStyle();
                    }

                    break;
                default:
                    $designer['menus'] = false;
                    $designer['menustyle'] = false;
            }
        } else {
            $designer = $this->getDefaultPageInfo();
        }
        return $designer;
    }


    /**
     * 未开启店铺装修插件 首页默认数据
     *
     * @return array
     */
    private function getDefaultPageInfo()
    {
        $i = \YunShop::request()->i;
        $mid = \YunShop::request()->mid;
        $type = \YunShop::request()->type;

        return array(
            'data' => '',//前端需要该字段
            'menu' => $this->defaultMenu($i, $mid, $type),
            'menustyle' => $this->defaultMenuStyle(),
        );
    }


    /**
     * 商城主设置信息
     *
     * @return array
     */
    private function getMailInfo()
    {
        $setting = $this->getSetting();

        return [
            "name" => $setting['name'],
            "logo" => replace_yunshop(yz_tomedia($setting['logo'])),
            "agent" => $this->getRelationSetStatus(),
            "credit" => $setting['credit'],
            "credit1" => $setting['credit1'],
            "signimg" => replace_yunshop(yz_tomedia($setting['signimg'])),
            "diycode" => html_entity_decode($setting['diycode']),
            "cservice" => $setting['cservice'],
            "copyright" => $setting['copyright'],
            "is_bind_mobile" => $this->isBindMobile()
        ];
    }


    /**
     * 登陆会员信息
     *
     * @return array
     */
    private function getMemberInfo()
    {
        //todo 不知道为什么首页获取会员信息、判断逻辑也不明确，暂时不删除，防止出错 YiTian 2018-06-26
        //用户信息, 原来接口在 member.member.getUserInfo
        $pageId = \YunShop::request()->page_id ?: 0;
        $member_id = \YunShop::app()->getMemberId();
        if (empty($pageId)) {
            if (!empty($member_id)) {
                $member_info = MemberModel::getUserInfos($member_id)->first();

                if (!empty($member_info)) {
                    $member_info = $member_info->toArray();
                    $data = MemberModel::userData($member_info, $member_info['yz_member']);
                    $data = MemberModel::addPlugins($data);

                    return $data;
                }
            }
        }
        return [];
    }


    /**
     * todo 不知道为什么重复赋值
     *
     * @return array
     */
    private function getSystem()
    {
        return $this->getMailInfo();
    }


    /**
     * 增加小程序默认装修数据
     *
     * @return array
     */
    private function getApplet()
    {
        return self::defaultDesign();
    }


    /**
     * 系统设置：商城设置
     *
     * @return mixed
     */
    private function getSetting()
    {
        $key = \YunShop::request()->setting_key ? \YunShop::request()->setting_key : 'shop';

        if (!Cache::has('shop_setting')) {
            $setting = Setting::get('shop.' . $key);

            if (!is_null($setting)) {
                Cache::put('shop_setting', $setting, 3600);
            }
        } else {
            $setting = Cache::get('shop_setting');
        }
        return $setting;
    }


    /**
     * 会员：关系链开启\关闭状态
     *
     * @return bool
     */
    private function getRelationSetStatus()
    {
        if (!Cache::has('member_relation')) {
            $relation = MemberRelation::getSetInfo()->first();

            if (!is_null($relation)) {
                Cache::put('member_relation', $relation, 3600);
            }
        } else {
            $relation = Cache::get('member_relation');
        }

        if ($relation) {
            return $relation->status ? true : false;
        }
        return false;
    }


    /**
     * 是否需要绑定手机号
     *
     * @return int
     */
    private function isBindMobile()
    {
        $shop_member_set = $this->getShopMemberSet();

        if (!is_null($shop_member_set)) {
            $member_bind_mobile_status = $this->memberBindMobileStatus();

            return ($shop_member_set['is_bind_mobile'] && !$member_bind_mobile_status) ? 1 : 0;
        }

        return 0;
    }


    /**
     * 系统设置：会员设置
     *
     * @return array
     */
    private function getShopMemberSet()
    {
        if (Cache::has('shop_member')) {
            $member_set = Cache::get('shop_member');
        } else {
            $member_set = Setting::get('shop.member');

            if (!is_null($member_set)) {
                Cache::put('shop_member', $member_set, 4200);
            }
        }

        return $member_set;
    }


    /**
     * 会员绑定手机号状态，1 已绑定、 0 未绑定
     *
     * @return int
     */
    private function memberBindMobileStatus()
    {
        $member_id = \YunShop::app()->getMemberId();

        if (Cache::has($member_id . '_member_info')) {
            $member_model = Cache::get($member_id . '_member_info');
        } else {
            $member_model = Member::getMemberById($member_id);
            if (!is_null($member_model)) {
                Cache::put($member_id . '_member_info', $member_model, 4200);
            }
        }

        return ($member_model && $member_model->mobile) ? 1 : 0;
    }


    //增加验证码功能
    public function captchaTest()
    {
        $captcha = app('captcha');
        $captcha_base64 = $captcha->create('default', true);

        return $captcha_base64;
    }

    public function wxapp()
    {
        $i = \YunShop::request()->i;
        $mid = \YunShop::request()->mid;
        $type = \YunShop::request()->type;
        $pageId = \YunShop::request()->page_id;
        $member_id = \YunShop::app()->getMemberId();

        //商城设置, 原来接口在 setting.get
        $key = \YunShop::request()->setting_key ? \YunShop::request()->setting_key : 'shop';
        if (!empty($key)) {
            $setting = Setting::get('shop.' . $key);
        } else {
            $setting = Setting::get('shop');
        }

        if ($setting) {
            $setting['logo'] = replace_yunshop(yz_tomedia($setting['logo']));

            $relation = MemberRelation::getSetInfo()->first();

            if ($relation) {
                $setting['agent'] = $relation->status ? true : false;
            } else {
                $setting['agent'] = false;
            }

            //强制绑定手机号
            $member_set = Setting::get('shop.member');

            if ((1 == $member_set['is_bind_mobile']) && $member_id && $member_id > 0) {
                $member_model = Member::getMemberById($member_id);

                if ($member_model && $member_model->mobile) {
                    $setting['is_bind_mobile'] = 0;
                } else {
                    $setting['is_bind_mobile'] = 1;
                }
            } else {
                $setting['is_bind_mobile'] = 0;
            }
            $setting['diycode'] = html_entity_decode($setting['diycode']);
            $result['mailInfo'] = $setting;

        } else {
            $result['mailInfo']['is_bind_mobile'] = 0;
        }

        //用户信息, 原来接口在 member.member.getUserInfo
        if (empty($pageId)) { //如果是请求首页的数据
            if (!empty($member_id)) {
                $member_info = MemberModel::getUserInfos($member_id)->first();

                if (!empty($member_info)) {
                    $member_info = $member_info->toArray();
                    $data = MemberModel::userData($member_info, $member_info['yz_member']);
                    $data = MemberModel::addPlugins($data);

                    $result['memberinfo'] = $data;
                }
            }
        }

        //插件信息, 原来接口在 plugins.get-plugin-data
        $plugins = new PluginManager(app(), new OptionRepository(), new Dispatcher(), new Filesystem());
        $enableds = $plugins->getEnabledPlugins()->toArray();

        //如果安装了装修插件并开启插件
        if (array_key_exists('designer', $enableds)) {

            //系统信息
            $result['system'] = (new \Yunshop\Designer\services\DesignerService())->getSystemInfo();

            //装修数据, 原来接口在 plugin.designer.home.index.page
            if (empty($pageId)) { //如果是请求首页的数据
                $page = Designer::getDefaultDesigner(9);
            } else {
                $page = Designer::getDesignerByPageID($pageId);
            }
            if ($page) {
                $designer = (new \Yunshop\Designer\services\DesignerService())->getPageForHomePage($page->toArray());
                $result['item'] = $designer;
                $footerMenuType = $designer['footertype']; //底部菜单: 0 - 不显示, 1 - 显示系统默认, 2 - 显示选中的自定义菜单
                $footerMenuId = $designer['footermenu'];
            } else { //如果是请求首页的数据, 提供默认值
                $result['default'] = self::defaultDesign();
                $result['item']['data'] = ''; //前端需要该字段
                $footerMenuType = 1;
            }

        } else { //如果是请求首页的数据, 但是没有安装"装修插件"或者"装修插件"没有开启, 则提供默认值
            $result['default'] = self::defaultDesign();
            $result['item']['menus'] = self::defaultMenu($i, $mid, $type);
            $result['item']['menustyle'] = self::defaultMenuStyle();
            $result['item']['data'] = ''; //前端需要该字段
        }

        return $this->successJson('ok', $result);
    }

    /**
     * @return array 默认的首页元素(轮播图 & 商品 & 分类 & 商城设置)
     */
    public static function defaultDesign()
    {
        if (!Cache::has('shop_category')) {
            $set = Setting::get('shop.category');

            Cache::put('shop_category', $set, 4200);
        } else {
            $set = Cache::get('shop_category');
        }

        $set['cat_adv_img'] = replace_yunshop(yz_tomedia($set['cat_adv_img']));
//        $category = (new IndexController())->getRecommentCategoryList();
//        foreach ($category  as &$item){
//            $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
//            $item['adv_img'] = replace_yunshop(yz_tomedia($item['adv_img']));
//        }
        return Array(
            'ads' => (new IndexController())->getAds(),
            'advs' => (new IndexController())->getAdv(),
            'brand' => (new IndexController())->getRecommentBrandList(),
            'category' => (new IndexController())->getRecommentCategoryList(),
            'time_goods' => (new IndexController())->getTimeLimitGoods(),
            'set' => $set,
            'goods' => (new IndexController())->getRecommentGoods(),
        );
    }


    /**
     * 默认的底部菜单数据
     * $i 公众号ID $mid 上级的uid
     *
     * @param $i
     * @param $mid
     * @param $type
     * @return array
     */
    private function defaultMenu($i, $mid, $type)
    {
        $defaultMenu = Array(
            Array(
                "id" => 1,
                "title" => "首页",
                "icon" => "fa fa-home",
                "url" => "/addons/yun_shop/?#/home?i=" . $i . "&mid=" . $mid . "&type=" . $type,
                "name" => "home",
                "subMenus" => [],
                "textcolor" => "#70c10b",
                "bgcolor" => "#24d7e6",
                "bordercolor" => "#bfbfbf",
                "iconcolor" => "#666666"
            ),
            Array(
                "id" => "menu_1489731310493",
                "title" => "分类",
                "icon" => "fa fa-th-large",
                "url" => "/addons/yun_shop/?#/category?i=" . $i . "&mid=" . $mid . "&type=" . $type,
                "name" => "category",
                "subMenus" => [],
                "textcolor" => "#70c10b",
                "bgcolor" => "#24d7e6",
                "iconcolor" => "#666666",
                "bordercolor" => "#bfbfbf"
            ),
            Array(
                "id" => "menu_1489735163419",
                "title" => "购物车",
                "icon" => "fa fa-cart-plus",
                "url" => "/addons/yun_shop/?#/cart?i=" . $i . "&mid=" . $mid . "&type=" . $type,
                "name" => "cart",
                "subMenus" => [],
                "textcolor" => "#70c10b",
                "bgcolor" => "#24d7e6",
                "iconcolor" => "#666666",
                "bordercolor" => "#bfbfbf"
            ),
            Array(
                "id" => "menu_1491619644306",
                "title" => "会员中心",
                "icon" => "fa fa-user",
                "url" => "/addons/yun_shop/?#/member?i=" . $i . "&mid=" . $mid . "&type=" . $type,
                "name" => "member",
                "subMenus" => [],
                "textcolor" => "#70c10b",
                "bgcolor" => "#24d7e6",
                "iconcolor" => "#666666",
                "bordercolor" => "#bfbfbf"
            ),
        );


        if ($this->getRelationSetStatus()) {
            $promoteMenu = Array(
                "id" => "menu_1489731319695",
                "classt" => "no",
                "title" => "推广",
                "icon" => "fa fa-send",
                "url" => "/addons/yun_shop/?#/member/extension?i=" . $i . "&mid=" . $mid . "&type=" . $type,
                "name" => "extension",
                "subMenus" => [],
                "textcolor" => "#666666",
                "bgcolor" => "#837aef",
                "iconcolor" => "#666666",
                "bordercolor" => "#bfbfbf"
            );
            $defaultMenu[4] = $defaultMenu[3]; //第 5 个按钮改成"会员中心"
            $defaultMenu[3] = $defaultMenu[2]; //第 4 个按钮改成"购物车"
            $defaultMenu[2] = $promoteMenu; //在第 3 个按钮的位置加入"推广"
        }

        return $defaultMenu;
    }

    /**
     * 默认的底部菜单样式
     *
     * @return array
     */
    private function defaultMenuStyle()
    {
        return Array(
            "previewbg" => "#ef372e",
            "height" => "49px",
            "textcolor" => "#666666",
            "textcolorhigh" => "#ff4949",
            "iconcolor" => "#666666",
            "iconcolorhigh" => "#ff4949",
            "bgcolor" => "#FFF",
            "bgcolorhigh" => "#FFF",
            "bordercolor" => "#010101",
            "bordercolorhigh" => "#bfbfbf",
            "showtext" => 1,
            "showborder" => "0",
            "showicon" => 2,
            "textcolor2" => "#666666",
            "bgcolor2" => "#fafafa",
            "bordercolor2" => "#1856f8",
            "showborder2" => 1,
            "bgalpha" => ".5",
        );
    }



    public function bindMobile()
    {
        $result['is_bind_mobile'] = $this->isBindMobile();

        return $this->successJson('ok', $result);
    }

}