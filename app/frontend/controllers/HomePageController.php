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
     * @return \Illuminate\Http\JsonResponse 当路由不包含page_id参数时,提供商城首页数据; 当路由包含page_id参数时,提供装修预览数据
     */
    public function index()
    {
        $i = \YunShop::request()->i;
        $mid = \YunShop::request()->mid;
        $type = \YunShop::request()->type;
        $pageId = \YunShop::request()->page_id ?:0;
        $member_id = \YunShop::app()->getMemberId();

        //商城设置, 原来接口在 setting.get
        $key = \YunShop::request()->setting_key ? \YunShop::request()->setting_key : 'shop';

        if(!Cache::has('shop_setting')){
            $setting = Setting::get('shop.' . $key);

            if (!is_null($setting)) {
                Cache::put('shop_setting',$setting,3600);
            }
        }else{
            $setting = Cache::get('shop_setting');
        }

        if($setting){
            $setting['logo'] = replace_yunshop(yz_tomedia($setting['logo']));
            if(!Cache::has('member_relation')){
                $relation = MemberRelation::getSetInfo()->first();

                if (!is_null($relation)) {
                    Cache::put('member_relation',$relation,3600);
                }
            }else{
                $relation = Cache::get('member_relation');
            }

            $setting['signimg'] = replace_yunshop(yz_tomedia($setting['signimg']));
            if ($relation) {
                $setting['agent'] = $relation->status ? true : false;
            } else {
                $setting['agent'] = false;
            }

            $setting['diycode'] = html_entity_decode($setting['diycode']);
            $result['mailInfo'] = $setting;
        }

        //强制绑定手机号
        if(!Cache::has('shop_member')){
            $member_set = Setting::get('shop.member');

            if (!is_null($member_set)) {
                Cache::put('shop_member',$member_set,4200);
            }
        }else{
            $member_set = Cache::get('shop_member');
        }

        $is_bind_mobile = 0;

        if (!is_null($member_set)) {
            if ((1 == $member_set['is_bind_mobile']) && $member_id && $member_id > 0) {
                if(!Cache::has($member_id . '_member_info')){
                    $member_model = Member::getMemberById($member_id);
                    if (!is_null($member_model)) {
                        Cache::put($member_id . '_member_info',$member_model,4200);
                    }
                }else{
                    $member_model = Cache::get($member_id. '_member_info');
                }

                if ($member_model && empty($member_model->mobile)) {
                    $is_bind_mobile = 1;
                }
            }
        }

        $result['mailInfo']['is_bind_mobile'] = $is_bind_mobile;

        //用户信息, 原来接口在 member.member.getUserInfo
        if(empty($pageId)){ //如果是请求首页的数据
            if (!empty($member_id)) {
                // TODO
                $member_info = MemberModel::getUserInfos($member_id)->first();

                if (!empty($member_info)) {
                    $member_info = $member_info->toArray();
                    $data = MemberModel::userData($member_info, $member_info['yz_member']);
                    $data = MemberModel::addPlugins($data);

                    $result['memberinfo'] = $data;
                }
            }
        }


        //如果安装了装修插件并开启插件
        if(app('plugins')->isEnabled('designer')){
            //系统信息
            // TODO
            if(!Cache::has('desiginer_system')){
                $result['system'] = (new \Yunshop\Designer\services\DesignerService())->getSystemInfo();

                Cache::put('desiginer_system',$result['system'],4200);
            }else{
                $result['system'] = Cache::get('desiginer_system');
            }

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
                if (empty($pageId) && Cache::has($member_id.'_desiginer_default_0')) {
                    $designer = Cache::get($member_id.'_desiginer_default_0');
                } else {
                    $designer = (new \Yunshop\Designer\services\DesignerService())->getPageForHomePage($page->toArray());
                }

                if (empty($pageId) && !Cache::has($member_id.'_desiginer_default_0')) {
                    Cache::put($member_id.'_desiginer_default_0', $designer,180);
                }

                $store_goods = null;
                if (app('plugins')->isEnabled('store-cashier')) {
                    $store_goods = new \Yunshop\StoreCashier\common\models\StoreGoods();
                }

                //课程商品判断
                $videoDemand = new VideoDemandCourseGoods();
                $video_open  = $videoDemand->whetherEnabled();

                foreach ($designer['data'] as &$value_one) {
                    if ($value_one['temp'] == 'goods') {
                        foreach ($value_one['data'] as &$info) {
                            $info['is_course'] = 0;
                            $info['img'] = replace_yunshop(yz_tomedia($info['img']));
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
                $result['item'] = $designer;
                $footerMenuType = $designer['footertype']; //底部菜单: 0 - 不显示, 1 - 显示系统默认, 2 - 显示选中的自定义菜单
                $footerMenuId = $designer['footermenu'];
            } elseif(empty($pageId)){ //如果是请求首页的数据, 提供默认值
                $result['default'] = self::defaultDesign();
                $result['item']['data'] = ''; //前端需要该字段
                $footerMenuType = 1;
            } else{ //如果是请求预览装修的数据
                $result['item']['data'] = ''; //前端需要该字段
                $footerMenuType = 0;
            }

            //自定义菜单, 原来接口在  plugin.designer.home.index.menu
            switch ($footerMenuType){
                case 1:
                    $result['item']['menus'] = self::defaultMenu($i, $mid, $type);
                    $result['item']['menustyle'] = self::defaultMenuStyle();
                    break;
                case 2:
                    if(!empty($footerMenuId)){
                        if(!Cache::has("designer_menu_{$footerMenuId}")){
                            $menustyle = DesignerMenu::getMenuById($footerMenuId);
                            Cache::put("designer_menu_{$footerMenuId}", $menustyle,4200);
                        }else{
                            $menustyle = Cache::get("designer_menu_{$footerMenuId}");
                        }

                        if(!empty($menustyle->menus) && !empty($menustyle->params)){
                            $result['item']['menus'] = json_decode($menustyle->toArray()['menus'], true);
                            $result['item']['menustyle'] = json_decode($menustyle->toArray()['params'], true);
                            //判断是否是商城外部链接
                            foreach ($result['item']['menus'] as $key => $value) {
                                if (!strexists($value['url'],'addons/yun_shop/')) {
                                    $result['item']['menus'][$key]['is_shop'] = 1;
                                } else {
                                    $result['item']['menus'][$key]['is_shop'] = 0;
                                }
                            }
                        } else{
                            $result['item']['menus'] = self::defaultMenu($i, $mid, $type);
                            $result['item']['menustyle'] = self::defaultMenuStyle();
                        }
                    } else{
                        $result['item']['menus'] = self::defaultMenu($i, $mid, $type);
                        $result['item']['menustyle'] = self::defaultMenuStyle();
                    }
                    break;
                default:
                    $result['item']['menus'] = false;
                    $result['item']['menustyle'] = false;
            }
        } elseif(empty($pageId)){ //如果是请求首页的数据, 但是没有安装"装修插件"或者"装修插件"没有开启, 则提供默认值
            $result['default'] = self::defaultDesign();
            $result['item']['menus'] = self::defaultMenu($i, $mid, $type);
            $result['item']['menustyle'] = self::defaultMenuStyle();
            $result['item']['data'] = ''; //前端需要该字段
        }

        //增加小程序回去默认装修数据
        $result['applet'] = self::defaultDesign();

        //增加验证码功能
        $status = Setting::get('shop.sms.status');
        if (extension_loaded('fileinfo')) {
            if ($status == 1) {
                $captcha = self::captchaTest();
                $result['captcha'] = $captcha;
                $result['captcha']['status'] = $status;
            }
        }
        return $this->successJson('ok', $result);
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

        if($setting){
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
        if(empty($pageId)){ //如果是请求首页的数据
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
        $enableds = app('plugins')->getEnabledPlugins()->toArray();

        //如果安装了装修插件并开启插件
        if(array_key_exists('designer', $enableds)){

            //系统信息
            $result['system'] = (new \Yunshop\Designer\services\DesignerService())->getSystemInfo();

            //装修数据, 原来接口在 plugin.designer.home.index.page
            if(empty($pageId)){ //如果是请求首页的数据
                $page = Designer::getDefaultDesigner(9);
            } else{
                $page = Designer::getDesignerByPageID($pageId);
            }
            if ($page) {
                $designer = (new \Yunshop\Designer\services\DesignerService())->getPageForHomePage($page->toArray());
                $result['item'] = $designer;
                $footerMenuType = $designer['footertype']; //底部菜单: 0 - 不显示, 1 - 显示系统默认, 2 - 显示选中的自定义菜单
                $footerMenuId = $designer['footermenu'];
            } else{ //如果是请求首页的数据, 提供默认值
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
        if(!Cache::has('shop_category')){
            $set = Setting::get('shop.category');

            Cache::put('shop_category',$set,4200);
        }else{
            $set = Cache::get('shop_category');
        }

        $set['cat_adv_img'] = replace_yunshop(yz_tomedia($set['cat_adv_img']));
//        $category = (new IndexController())->getRecommentCategoryList();
//        foreach ($category  as &$item){
//            $item['thumb'] = replace_yunshop(yz_tomedia($item['thumb']));
//            $item['adv_img'] = replace_yunshop(yz_tomedia($item['adv_img']));
//        }
        return  Array(
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
     * @param $i 公众号ID
     * @param $mid 上级的uid
     * @param $type
     * @return array 默认的底部菜单数据
     */
    public static function defaultMenu($i, $mid, $type)
    {
        $defaultMenu = Array(
            Array(
                "id"=>1,
                "title"=>"首页",
                "icon"=>"fa fa-home",
                "url"=>"/addons/yun_shop/?#/home?i=".$i."&mid=".$mid."&type=".$type,
                "name"=>"home",
                "subMenus"=>[],
                "textcolor"=>"#70c10b",
                "bgcolor"=>"#24d7e6",
                "bordercolor"=>"#bfbfbf",
                "iconcolor"=>"#666666"
            ),
            Array(
                "id"=>"menu_1489731310493",
                "title"=>"分类",
                "icon"=>"fa fa-th-large",
                "url"=>"/addons/yun_shop/?#/category?i=".$i."&mid=".$mid."&type=".$type,
                "name"=>"category",
                "subMenus"=>[],
                "textcolor"=>"#70c10b",
                "bgcolor"=>"#24d7e6",
                "iconcolor"=>"#666666",
                "bordercolor"=>"#bfbfbf"
            ),
            Array(
                "id"=>"menu_1489735163419",
                "title"=>"购物车",
                "icon"=>"fa fa-cart-plus",
                "url"=>"/addons/yun_shop/?#/cart?i=".$i."&mid=".$mid."&type=".$type,
                "name"=>"cart",
                "subMenus"=>[],
                "textcolor"=>"#70c10b",
                "bgcolor"=>"#24d7e6",
                "iconcolor"=>"#666666",
                "bordercolor"=>"#bfbfbf"
            ),
            Array(
                "id"=>"menu_1491619644306",
                "title"=>"会员中心",
                "icon"=>"fa fa-user",
                "url"=>"/addons/yun_shop/?#/member?i=".$i."&mid=".$mid."&type=".$type,
                "name"=>"member",
                "subMenus"=>[],
                "textcolor"=>"#70c10b",
                "bgcolor"=>"#24d7e6",
                "iconcolor"=>"#666666",
                "bordercolor"=>"#bfbfbf"
            ),
        );

        //如果开启了"会员关系链", 则默认菜单里面添加"推广"菜单
        /*
        if(Cache::has('member_relation')){
            $relation = Cache::get('member_relation');
        } else {
            $relation = MemberRelation::getSetInfo()->first();
        }
        */
        //if($relation->status == 1){
            $promoteMenu = Array(
                "id"=>"menu_1489731319695",
                "classt"=>"no",
                "title"=>"推广",
                "icon"=>"fa fa-send",
                "url"=>"/addons/yun_shop/?#/member/extension?i=".$i."&mid=".$mid."&type=".$type,
                "name"=>"extension",
                "subMenus"=>[],
                "textcolor"=>"#666666",
                "bgcolor"=>"#837aef",
                "iconcolor"=>"#666666",
                "bordercolor"=>"#bfbfbf"
            );
            $defaultMenu[4] = $defaultMenu[3]; //第 5 个按钮改成"会员中心"
            $defaultMenu[3] = $defaultMenu[2]; //第 4 个按钮改成"购物车"
            $defaultMenu[2] = $promoteMenu; //在第 3 个按钮的位置加入"推广"
        //}


        return $defaultMenu;

    }

    /**
     * @return array 默认的底部菜单样式
     */
    public static function defaultMenuStyle()
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
        $member_id = \YunShop::app()->getMemberId();

        //强制绑定手机号
        if(Cache::has('shop_member')){
            $member_set = Cache::get('shop_member');
        } else {
            $member_set = Setting::get('shop.member');
        }

        $is_bind_mobile = 0;

        if (!is_null($member_set)) {
            if ((1 == $member_set['is_bind_mobile']) && $member_id && $member_id > 0) {
                if(Cache::has($member_id . '_member_info')){
                    $member_model = Cache::get($member_id . '_member_info');
                } else {
                    $member_model = Member::getMemberById($member_id);
                }

                if ($member_model && empty($member_model->mobile)) {
                    $is_bind_mobile = 1;
                }
            }
        }

        $result['is_bind_mobile'] = $is_bind_mobile;

        return $this->successJson('ok', $result);
    }

}
