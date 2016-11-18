<?php
namespace app\api\controller\commission;
@session_start();
use app\api\YZ;
use yii\helpers\ArrayHelper;

class Register extends YZ
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $result = $this->callPlugin('commission/register');
        $result['json']['title'] = "申请分销商";
        $result['json']['images'] = $result['json']['set']['regbg'] ? $result['json']['set']['regbg'] : "../addons/sz_yi/plugin/commission/template/mobile/default/static/images/bg.png";
        $result['json']['footer']['title'] = $result['json']['set']['texts']['agent'] . "特权";
        $result['json']['footer']['vshop'] = "独立微店";
        $result['json']['footer']['vshop_desc'] = "拥有自己的微店及推广二维码；";
        $result['json']['footer']['commission'] = "销售拿" . $result['json']['set']['texts']['commission'];
        $result['json']['footer']['vshop_commission'] = "微店卖出商品，您可以获得" . $result['json']['set']['texts']['commission'];
        $result['json']['footer']['centent'] = $result['json']['set']['texts']['agent'] . "的商品销售统一由厂家直接收款、直接发货，并提供产品的售后服务，" . $result['json']['set']['texts']['commission'] . "由厂家统一设置。";

        if ($result['json']['set']['become'] == 2) {
            $result['json']['centent'] = "本店累计消费满 " . $result['json']['status'] . " 次，
                       才可成为<" . $result['json']['shop_set']['name']  . ">购物中心" . $result['json']['set']['texts']['agent'] . "，您已消费 " . $result['json']['order'] . " 次，请继续努力！";
            $result['json']['button'] = "继续去购物";
        } elseif ($result['json']['set']['become'] == 3) {
            $result['json']['centent'] = "本店累计消费满 " . $result['json']['moneycount'] . " 元，
                       才可成为<" . $result['json']['shop_set']['name']  . ">购物中心" . $result['json']['set']['texts']['agent'] . "，您已消费 " . $result['json']['money'] . " 元，请继续努力！";
            $result['json']['button'] = "继续去购物";
        } elseif ($result['json']['set']['become'] == 4) {
            $result['json']['centent'] = "本店需购买商品【" . $result['json']['goods']['title']  . "】才可成为<" . $result['json']['shop_set']['name']  . ">;购物中心" . $result['json']['set']['texts']['agent'] . "，请现在去购买吧！";
            $result['json']['button'] = "现在就去购买";
            //$result['json']['goods']['id'] = "现在就去购买";
        } elseif ($result['json']['set']['become'] == 1) {
            $result['json']['centent']['title'] = "欢迎加入" . $result['json']['shop_set']['name']  . "，请填写申请信息";
            $yname = $result['json']['agent'] ? $result['json']['agent']['nickname'] : "总店";
            $result['json']['centent']['desc'] = "邀请人：" . $yname . " (请核对)";
            $result['json']['button'] = "申请成为" . $result['json']['set']['texts']['agent'];
        }

        $this->returnSuccess($result);
    }

    public function post(){
        global $_W;
        $_W["ispost"] = 1;
        $result = $this->callPlugin('commission/register');
        if(is_array($result)){
            $this->returnSuccess("提交成功，请等待审核");
        }
    }
}