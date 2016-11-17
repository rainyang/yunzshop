<?php
namespace app\api\controller\commission;
@session_start();
use app\api\YZ;
use yii\helpers\ArrayHelper;

class Shares extends YZ
{

    private $json;
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $result = $this->callPlugin('commission/shares');
        $this->json =$result['json'];
        $result['json']['share_title'] = "如何赚钱";
        $result['json']['content'] = array(
            array('number' => '第一步', 'text' => '转发商品链接或商品图片给微信好友；'),
            array('number' => '第二步', 'text' => '从您转发的链接或图片进入商城的好友，' . $result['json']['set']['become_child'] == 1 ? '如果您的好友下单，' : '' . $result['json']['set']['become_child'] == 2 ? '如果您的好友下单并付款，' : '' . '系统将自动锁定成为您的客户, 他们在微信商城中购买任何商品，您都可以获得' . $result['json']['set']['texts']['commission1'] . '；'),
            array('number' => '第三步', 'text' => '您可以在' . $result['json']['set']['texts']['center'] . '查看【' . $result['json']['set']['texts']['myteam'] . '】和【' . $result['json']['set']['texts']['order'] . '】，好友确认收货后' . $result['json']['set']['texts']['commission'] . '方可' . $result['json']['set']['texts']['withdraw'] . '。'),
            );

        $result['json']['desc'] = "说明：分享后会带有独有的推荐码，您的好友访问之后，系统会自动检测并记录客户关系。如果您的好友已被其他人抢先发展成了客户，他就不能成为您的客户，以最早发展成为客户为准。";
        $result['json']['share'] = $this->_getShareInfo();
        $this->returnSuccess($result);
    }
    private function _getShareInfo()
    {
        global $_W;
        $result = array(
            'title' => $_W['shopshare']['title'],
            'webUrl' => $_W['shopshare']['link'] . '&access=app',
            'imageUrl' => $this->json['img'],
            'content' => $_W['shopshare']['desc']
        );
        return $result;
    }
}