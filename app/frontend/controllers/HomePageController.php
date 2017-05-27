<?php

namespace app\frontend\controllers;

use app\common\components\ApiController;
use Yunshop\Designer\models\Designer;
use Yunshop\Designer\models\DesignerMenu;
use Yunshop\Designer\services\DesignerService;
use app\frontend\modules\member\models\MemberModel;
use app\backend\modules\member\models\MemberRelation;
use app\common\facades\Setting;
use app\frontend\models\Member;
use app\common\services\PluginManager;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use app\common\repositories\OptionRepository;
use app\common\models\AccountWechats;
use app\common\models\McMappingFans;
use app\frontend\modules\shop\controllers\IndexController;


class HomePageController extends ApiController
{
    protected $publicAction = ['index', 'defaultDesign', 'defaultMenu', 'defaultMenuStyle'];
    protected $ignoreAction = [];

    public function index()
    {
        $i = \YunShop::request()->i;
        $mid = \YunShop::request()->mid;
        $type = \YunShop::request()->type;


        //商城设置, 原来接口在 setting.get
        $key = \YunShop::request()->setting_key ? \YunShop::request()->setting_key : 'shop';
        if (!empty($key)) {
            $setting = Setting::get('shop.' . $key);
        } else {
            $setting = Setting::get('shop');
        }

        if($setting){
            $setting['logo'] = replace_yunshop(tomedia($setting['logo']));
            $relation = MemberRelation::getSetInfo()->first();

            if ($relation) {
                $setting['agent'] = $relation->status ? true : false;
            } else {
                $setting['agent'] = false;
            }

            //强制绑定手机号
            $member_set = Setting::get('shop.member');

            if ((1 == $member_set['is_bind_mobile']) && \YunShop::app()->getMemberId() && \YunShop::app()->getMemberId() > 0) {
                $member_model = Member::getMemberById(\YunShop::app()->getMemberId());

                if ($member_model && $member_model->mobile) {
                    $setting['is_bind_mobile'] = 0;
                } else {
                    $setting['is_bind_mobile'] = 1;
                }
            } else {
                $setting['is_bind_mobile'] = 0;
            }

            $result['mailInfo'] = $setting;
        }

        //用户信息, 原来接口在 member.member.getUserInfo
        $member_id = \YunShop::app()->getMemberId();
        if (!empty($member_id)) {
            $member_info = MemberModel::getUserInfos($member_id)->first();

            if (!empty($member_info)) {
                $member_info = $member_info->toArray();
                $data = MemberModel::userData($member_info, $member_info['yz_member']);
                $data = MemberModel::addPlugins($data);

                $result['memberinfo'] = $data;
            }
        }


        //用户信息, 原来接口在 member.member.guideFollow
        $set = \Setting::get('shop.share');
        $fans_model = McMappingFans::getFansById(\YunShop::app()->getMemberId());

        if (!empty($set['follow_url']) && 0 == $fans_model->follow) {

            if ($mid != null && $mid != 'undefined' && $mid > 0) {
                $member_model = Member::getMemberById($mid);

                $logo = $member_model->avatar;
                $text = $member_model->nickname;
            } else {
                $setting = Setting::get('shop');
                $account = AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid);

                $logo = replace_yunshop(tomedia($setting['logo']));
                $text = $account->name;
            }

            $result['subscribe'] = [
                'logo' => $logo,
                'text' => $text,
                'url' => $set['follow_url'],
            ];
        }


        //插件信息, 原来接口在 plugins.get-plugin-data
        $plugins = new PluginManager(app(),new OptionRepository(),new Dispatcher(),new Filesystem());
        $enableds = $plugins->getEnabledPlugins()->toArray();

        foreach ($enableds as &$enabled) {
            unset($enabled['path']);
        }

        if($enableds){
            $result['plugins'] = $enableds;
        }


        //如果安装了装修插件并开启插件
        if(array_key_exists('designer', $enableds)){

            //装修, 原来接口在 plugin.designer.home.index.page
            $page = Designer::getDefaultDesigner();
            if ($page) {
                $designer = (new DesignerService())->getPageForHomePage($page->toArray());
                $result['item'] = $designer;
            } else{
                $result['default'] = self::defaultDesign();
                $result['item']['data'] = ''; //前端需要该字段
            }

            $result['system'] = (new DesignerService())->getSystemInfo();

            //菜单背景色, 原来接口在  plugin.designer.home.index.menu
            $menustyle = DesignerMenu::getDefaultMenu();
            if ($menustyle) {
                $result['item']['menus'] = json_decode($menustyle->toArray()['menus'], true);
                $result['item']['menustyle'] = json_decode($menustyle->toArray()['params'], true);
            } else{ //提供默认值
                $result['item']['menus'] = self::defaultMenu($i, $mid, $type);
                $result['item']['menustyle'] = self::defaultMenuStyle();
            }
        } else{ //没有安装装修插件或者没有开启
            $result['default'] = self::defaultDesign();
            $result['item']['menus'] = self::defaultMenu($i, $mid, $type);
            $result['item']['menustyle'] = self::defaultMenuStyle();
            $result['item']['data'] = ''; //前端需要该字段
        }

        return $this->successJson('ok', $result);
    }

    //默认首页样式
    public static function defaultDesign()
    {
        $set = Setting::get('shop.category');
        $set['cat_adv_img'] = replace_yunshop(tomedia($set['cat_adv_img']));
        $category = (new IndexController())->getRecommentCategoryList();
        foreach ($category  as &$item){
            $item['thumb'] = replace_yunshop(tomedia($item['thumb']));
            $item['adv_img'] = replace_yunshop(tomedia($item['adv_img']));
        }
        return  Array(
            'ads' => (new IndexController())->getAds(),
            'category' => (new IndexController())->getRecommentCategoryList(),
            'set' => $set,
            'goods' => (new IndexController())->getRecommentGoods(),
        );
    }

    //默认菜单
    public static function defaultMenu($i, $mid, $type)
    {
        return Array(
            Array(
                "id"=>1,
                "title"=>"首页",
                "icon"=>"fa fa-home",
                "url"=>"/addons/yun_shop/#/home?i=".$i."&mid=".$mid."&type=".$type,
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
                "url"=>"/addons/yun_shop/#/category?i=".$i."&mid=".$mid."&type=".$type,
                "name"=>"category",
                "subMenus"=>[],
                "textcolor"=>"#70c10b",
                "bgcolor"=>"#24d7e6",
                "iconcolor"=>"#666666",
                "bordercolor"=>"#bfbfbf"
            ),
            Array(
                "id"=>"menu_1489731319695",
                "classt"=>"no",
                "title"=>"推广",
                "icon"=>"fa fa-send",
                "url"=>"/addons/yun_shop/#/member/extension?i=".$i."&mid=".$mid."&type=".$type,
                "name"=>"extension",
                "subMenus"=>[],
                "textcolor"=>"#666666",
                "bgcolor"=>"#837aef",
                "iconcolor"=>"#666666",
                "bordercolor"=>"#bfbfbf"
            ),
            Array(
                "id"=>"menu_1489735163419",
                "title"=>"购物车",
                "icon"=>"fa fa-cart-plus",
                "url"=>"/addons/yun_shop/#/cart?i=".$i."&mid=".$mid."&type=".$type,
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
                "url"=>"/addons/yun_shop/#/member?i=".$i."&mid=".$mid."&type=".$type,
                "name"=>"member",
                "subMenus"=>[],
                "textcolor"=>"#70c10b",
                "bgcolor"=>"#24d7e6",
                "iconcolor"=>"#666666",
                "bordercolor"=>"#bfbfbf"
            ),
        );

    }

    public static function defaultMenuStyle()
    {
        return Array(
            "previewbg" => "#ef372e",
            "height" => "49px",
            "textcolor" => "#70c10b",
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

}