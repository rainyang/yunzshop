<?php
namespace app\common\models;

use app\backend\models\BackendModel;
use app\backend\modules\member\models\MemberRelation;
use app\common\events\member\BecomeAgent;
use app\common\repositories\OptionRepository;
use app\common\services\PluginManager;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\models\MemberWechatModel;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Yunshop\AreaDividend\models\AreaDividendAgent;
use Yunshop\Commission\models\Agents;
use Yunshop\Gold\frontend\services\MemberCenterService;
use Yunshop\Love\Common\Services\SetService;
use Yunshop\Merchant\common\models\Merchant;
use Yunshop\Micro\common\models\MicroShop;
use Yunshop\Micro\common\services\MicroShop\GetButtonService;
use Yunshop\StoreCashier\common\models\Store;
use Yunshop\Supplier\admin\models\Supplier;
use Yunshop\Supplier\common\services\VerifyButton;
use Yunshop\TeamDividend\models\TeamDividendAgencyModel;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 21/02/2017
 * Time: 12:58
 */

/**
 * Class Member
 * @package app\common\models
 * @property int uid
 * @property float credit1
 */
class Member extends BackendModel
{
    public $table = 'mc_members';

    public $timestamps = false;




    protected $guarded = ['credit1', 'credit2', 'credit3', 'credit4', 'credit5'];

    protected $fillable = ['uniacid', 'mobile', 'groupid', 'createtime', 'nickname', 'avatar', 'gender', 'salt', 'password'];

    protected $attributes = ['bio' => '', 'resideprovince' => '', 'residecity' => '', 'nationality' => '', 'interest' => '', 'mobile' => '', 'email' => '', 'credit1' => 0, 'credit2' => 0, 'credit3' => 0, 'credit4' => 0, 'credit5' => 0, 'credit6' => 0, 'realname' => '', 'qq' => '', 'vip' => 0, 'birthyear' => 0, 'birthmonth' => 0, 'birthday' => 0, 'constellation' => '', 'zodiac' => '', 'telephone' => '', 'idcard' => '', 'studentid' => '', 'grade' => '', 'address' => '', 'zipcode' => '', 'residedist' => '', 'graduateschool' => '', 'company' => '', 'education' => '', 'occupation' => '', 'position' => '', 'revenue' => '', 'affectivestatus' => '', 'lookingfor' => '', 'bloodtype' => '', 'height' => '', 'weight' => '', 'alipay' => '', 'msn' => '', 'taobao' => '', 'site' => ''];

    const INVALID_OPENID = 0;

    protected $search_fields = ['mobile', 'uid', 'nickname', 'realname'];

    protected $primaryKey = 'uid';



    public function bankCard()
    {
        return $this->hasOne('app\common\models\member\BankCard', 'member_id', 'uid');
    }


    public function pointLove()
    {
        return $this->hasOne('app\common\models\finance\PointLoveSet', 'member_id', 'uid');
    }
    public function defaultAddress()
    {
        return $this->hasOne(app('MemberAddressRepository')->model(), 'uid', 'uid')->where('isdefault', 1);
    }

    /**
     * 主从表1:1
     *
     * @return mixed
     */
    public function yzMember()
    {
        return $this->hasOne('app\backend\modules\member\models\MemberShopInfo', 'member_id', 'uid');
    }

    /**
     * 会员－粉丝1:1关系
     *
     * @return mixed
     */
    public function hasOneFans()
    {
        return $this->hasOne('app\common\models\McMappingFans', 'uid', 'uid');
    }



    /**
     * 会员－订单1:1关系 todo 会员和订单不是一对多关系吗?
     *
     * @return mixed
     */
    public function hasOneOrder()
    {
        return $this->hasOne('app\common\models\Order', 'uid', 'uid');
    }
    /**
     * 会员－会员优惠券1:多关系
     *
     * @return mixed
     */
    public function hasManyMemberCoupon()
    {
        return $this->hasOne(MemberCoupon::class, 'uid', 'uid');
    }

    /**
     * 角色
     *
     * 会员-分销商
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneAgent()
    {
        return $this->hasOne(Agents::class, 'member_id', 'uid');
    }

    /**
     * 角色
     *
     * 会员-经销商
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneTeamDividend()
    {
        return $this->hasOne(TeamDividendAgencyModel::class, 'uid', 'uid');
    }

    /**
     * 角色
     *
     * 会员-区域代理
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneAreaDividend()
    {
        return $this->hasOne(AreaDividendAgent::class, 'member_id', 'uid');
    }

    /**
     * 角色
     *
     * 会员-招商员
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneMerchant()
    {
        return $this->hasOne(Merchant::class, 'member_id', 'uid');
    }

    /**
     * 角色
     *
     * 会员-招商中心
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneMerchantCenter()
    {
        return $this->hasOne(Merchant::class, 'member_id', 'uid');
    }

    /**
     * 角色
     *
     * 会员-微店店主
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneMicro()
    {
        return $this->hasOne(MicroShop::class, 'member_id', 'uid');
    }

    /**
     * 角色
     *
     * 会员-供应商
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneSupplier()
    {
        return $this->hasOne(Supplier::class, 'member_id', 'uid');
    }

    public function scopeOfUid($query,$uid)
    {
        return $query->where('uid',$uid);
    }

    public function scopeSearchYzMember($query,$search)
    {
        return $query->whereHas('yzMember',function($query)use($search) {
            return $query->search($search);
        });
    }


    public function scopeSearch($query,$search)
    {
        if ($search['member_id']) {
            $query->ofUid($search['member_id']);
        }
        if ($search['realname']) {
            $query->searchLike($search['realname']);
        }
        if ($search['member_level'] || $search['member_group']) {
            $query->searchYzMember($search);
        }
        return $query;
    }

    /**
     * 获取用户信息
     *
     * @param $member_id
     * @return mixed
     */
    public static function getUserInfos($member_id)
    {
        return self::select(['*'])
            ->uniacid()
            ->where('uid', $member_id)
            ->whereHas('yzMember', function($query) use($member_id) {
                $query->where('member_id', $member_id)->whereNull('deleted_at');
            })
            ->with([
                'yzMember' => function ($query) {
                    return $query->select(['*'])->where('is_black', 0)
                        ->with([
                            'group' => function ($query1) {
                                return $query1->select(['id', 'group_name']);
                            },
                            'level' => function ($query2) {
                                return $query2->select(['id', 'level_name']);
                            },
                            'agent' => function ($query3) {
                                return $query3->select(['uid', 'avatar', 'nickname']);
                            }
                        ]);
                },
                'hasOneFans' => function ($query4) {
                    return $query4->select(['uid', 'openid', 'follow as followed']);
                },
                'hasOneOrder' => function ($query5) {
                    return $query5->selectRaw('uid, count(uid) as total, sum(price) as sum')
                        ->uniacid()
                        ->where('status', 3)
                        ->groupBy('uid');
                }
            ]);
    }

    /**
     * 获取该公众号下所有用户的 member ID
     *
     * @return mixed
     */
    public static function getMembersId()
    {
        return static::uniacid()
                    ->select (['uid'])
                    ->get();
    }

    /**
     * 通过id获取用户信息
     *
     * @param $member_id
     * @return mixed
     */
    public static function getMemberById($member_id)
    {
        return self::uniacid()
                ->where('uid', $member_id)
                ->first();
    }
    public static function getMemberByUid($member_id)
    {
        return self::uniacid()
            ->where('uid', $member_id);
    }
    /**
     * 添加评论默认名称
     * @return mixed
     */
    public static function getRandNickName()
    {
        return self::select('nickname')
            ->whereNotNull('nickname')
            ->inRandomOrder()
            ->first();
    }

    /**
     * 添加评论默认头像
     * @return mixed
     */
    public static function getRandAvatar()
    {
        return self::select('avatar')
            ->whereNotNull('avatar')
            ->inRandomOrder()
            ->first();
    }

    public static function getOpenId($member_id){
        $data = self::getUserInfos($member_id)->first();
        if ($data) {
            $info = $data->toArray();

            if (!empty($info['has_one_fans'])) {
                return $info['has_one_fans']['openid'];
            } else {
                return self::INVALID_OPENID;
            }
        }
    }

    /**
     * 触发会员成为下线事件
     *
     * @param $member_id
     */
    public static function chkAgent($member_id, $mid)
    {
        $model = MemberShopInfo::getMemberShopInfo($member_id);

        $relation = new MemberRelation();
        $relation->becomeChildAgent($mid, $model);
    }

    /**
     * 定义字段名
     *
     * @return array
     */
    public function atributeNames()
    {
        return [
            'mobile' => '绑定手机号',
            'realname' => '真实姓名',
            //'avatar' => '头像',
            'telephone' => '联系手机号',
        ];
    }

    /**
     * 字段规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            'mobile' => 'regex:/^1[34578]\d{9}$/',
            'realname' => 'required|between:2,10',
            //'avatar' => 'required',
            'telephone' => 'regex:/^1[34578]\d{9}$/',
        ];
    }


    /**
     * 生成分销关系链
     *
     * @param $member_id
     */
    public static function createRealtion($member_id, $upperMemberId = NULL)
    {
        $model = MemberShopInfo::getMemberShopInfo($member_id);

        if ($upperMemberId) {
            event(new BecomeAgent($upperMemberId, $model));
        } else {
            event(new BecomeAgent(self::getMid(), $model));
        }
    }

    public static function getMid()
    {
      /*
        if (\YunShop::request()->mid) {
            \Log::debug(sprintf('前端获取mid-%d', \YunShop::request()->mid));
            return \YunShop::request()->mid;
        } elseif (Session::get('client_url') && strpos(Session::get('client_url'), 'mid')) {
            preg_match('/.+mid=(\d+).+/', Session::get('client_url'), $matches);
            \Log::debug('截取mid', $matches[1]);
            if (isset($matches) && !empty($matches[1])) {
                return $matches[1];
            }
        }
        */

        $mid = \YunShop::request()->mid;

        return ($mid && ($mid != 'null' || $mid != 'undefined')) ? (int)$mid : 0;
    }

    /**
     * 申请插件
     *
     * @param array $data
     * @return array
     */
    public static function addPlugins(&$data = [])
    {
        $plugin_class = new PluginManager(app(),new OptionRepository(),new Dispatcher(),new Filesystem());

        // todo 后期需要重构
        if ($plugin_class->isEnabled('supplier')) {
            $data['supplier'] = VerifyButton::button();
        } else {
            $data['supplier'] = '';
        }

        // todo 后期需要重构
        if ($plugin_class->isEnabled('micro')) {
            $micro_set = \Setting::get('plugin.micro');
            if ($micro_set['is_open_miceo'] == 0) {
                $data['micro'] = '';
            } else {
                $data['micro'] = GetButtonService::verify(\YunShop::app()->getMemberId());
            }
        } else {
            $data['micro'] = '';
        }

        // todo 后期需要重构
        if ($plugin_class->isEnabled('gold')) {
            $data['gold'] = MemberCenterService::button(\YunShop::app()->getMemberId());
        } else {
            $data['gold'] = '';
        }

        // todo 后期需要重构
        if ($plugin_class->isEnabled('love')) {
            $data['love'] = [
                'status'  => true,
                'love_name'=> SetService::getLoveName(),
            ];
        } else {
            $data['love'] = [
                'status'    => false,
                'love_name' => '0',
            ];
        }

        if ($plugin_class->isEnabled('store-cashier')) {
            $store = Store::getStoreByUid(\YunShop::app()->getMemberId())->first();
            if ($store && $store->hasOneCashier->hasOneCashierGoods->is_open == 1) {
                $data['cashier'] = [
                    'button_name' => '收银台',
                    'api'         => 'plugin.store-cashier.frontend.cashier.center.index'
                ];
            }
        } else {
            $data['cashier'] = '';
        }

        if ($plugin_class->isEnabled('elive')) {
            $data['elive'] = [
                'button_name' => '生活缴费',
                'status'         => true
            ];
        } else {
            $data['elive'] = ['status' => false];
        }

        //获取插件会员中心链接挂件
        $plugins = \Config::get('member_center.plugins');
        if (is_array($plugins)) {
            $data = array_merge($data,$plugins);
        }

        return $data;
    }

    /**
     * 推广提现
     * @return \Illuminate\Http\JsonResponse
     */
    public static function getIncomeCount()
    {
        $incomeModel = Income::getIncomes()->where('member_id', \YunShop::app()->getMemberId())->get();

        if ($incomeModel) {
            return $incomeModel->sum('amount');
        }

        return 0;
    }

    /**
     * 会员3级关系链
     *
     * @param $uid
     * @param string $mid
     * @return bool
     */
    public static function setMemberRelation($uid, $mid='')
    {
        $model = MemberShopInfo::getMemberShopInfo($uid);

        if (empty($mid)) {
            $mid   = 0;
        }

        //生成关系3级关系链
        $member_model = MemberModel::getMyAgentsParentInfo($mid)->first();

        if (!empty($member_model)) {
            \Log::debug('model-生成关系3级关系链');
            $member_data = $member_model->toArray();

            $relation_str = $mid;

            if (!empty($member_data['yz_member'])) {
                $count = count($member_data['yz_member'], 1);

                if ($count > 3) {
                    $relation_str .= ',' . $member_data['yz_member']['parent_id'];
                }

                if ($count > 6) {
                    $relation_str .= ',' . $member_data['yz_member']['has_one_pre_self']['parent_id'];
                }
            }
        } else {
            $relation_str = '0';
        }

        if ($relation_str != '0') {
            $curr_arr = explode(',', rtrim($relation_str, ','));
            $res_arr  = array_unique($curr_arr);

            if (count($res_arr) != count($curr_arr)) {
                return false;
            }

            if (in_array($uid, $curr_arr)) {
                return false;
            }
        }

        $model->relation = $relation_str;

        return $model->save();
    }

    public static function getOpenIdForType($member_id, $type = null){
        switch ($type) {
            case 2:
                $mini_app = MemberMiniAppModel::getFansById($member_id);

                return $mini_app->openid;
                break;
            case 9:
                $mini_app = MemberWechatModel::getFansById($member_id);

                return $mini_app->openid;
                break;
            default:
                $fans = McMappingFans::getFansById($member_id);

                return $fans->openid;
        }
    }
    /**
     * 判断用户是否关注
     * @return bool
     */
    public function isFollow()
    {
        return isset($this->hasOneFans) && $this->hasOneFans->follow && !empty($this->hasOneFans->openid);
    }

    public function getMemberRole($builder)
    {
        $result = $builder;

        if (app('plugins')->isEnabled('commission')) {
            $result = $result->with([
                'hasOneAgent'
            ]);
        }

        if (app('plugins')->isEnabled('team-dividend')) {
            $result = $result->with([
                'hasOneTeamDividend'
            ]);
        }

        if (app('plugins')->isEnabled('area-dividend')) {
            $result = $result->with([
                'hasOneAreaDividend' => function ($query) {
                    return $query->where('status', 1);
                }
            ]);
        }

        if (app('plugins')->isEnabled('merchant')) {
            $result = $result->with([
                'hasOneMerchant',
                'hasOneMerchantCenter'
            ]);
        }

        if (app('plugins')->isEnabled('micro')) {
            $result = $result->with([
                'hasOneMicro'
            ]);
        }

        if (app('plugins')->isEnabled('supplier')) {
            $result = $result->with([
                'hasOneSupplier' => function ($query) {
                    return $query->where('status', 1);
                }
            ]);
        }

        return $result;
    }
}