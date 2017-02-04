/**
 * 本页面的参考文档： https://www.qcloud.com/document/product/454/8046
 */

/**
 * 变量说明：为该网页提供视频播放信息的业务服务器
 * 参考文档：https://www.qcloud.com/document/product/454/8046#step4.3A-web.E5.90.8E.E5.8F.B0.E6.90.AD.E5.BB.BA
 * @type {string}
 */
var SERVER = window.document.location.href.substring(0, window.document.location.href.indexOf(window.document.location.pathname));//这里需要改成您实际搭建的 PHP 后台服务地址


/**
 * IM Web SDK 参数配置，可以参考IM Web SDK Demo指引 https://www.qcloud.com/document/product/269/4105
 * ******************************* Start *************************************
 */

/**
 * 变量说明：IM SDK 的账号集成模式
 * 参考文档：https://www.qcloud.com/document/product/454/7980
 * 简单说明：这里的配置，要跟您的小直播IM SDK的配置保持一致。
 * 参数说明：0-表示独立模式，1-表示托管模式
 * @type {number}
 */
var accountMode = 0;//请根据您的情况自行配置

/**
 * 变量说明：IM SDK 的应用配置信息
 * 参考文档：sdkappid:    https://www.qcloud.com/document/product/454/7953#3.2-im-sdk-appid
 * 参考文档：accountType：https://www.qcloud.com/document/product/454/7953#3.3-im-sdk-.E8.B4.A6.E5.8F.B7.E7.B1.BB.E5.9E.8B
 * 简单说明：这里的配置，要跟您的小直播IM SDK的配置保持一致。 (选填)
 * @type {number}
 */
var sdkAppID    = 1400018359; //请根据您的情况自行配置，这里可以不填，分享链接会带上这个参数，页面可以从链接中获取。
var accountType = 9036; //请根据您的情况自行配置，这里可以不填，分享链接会带上这个参数，页面可以从链接中获取。

/**
 * 变量说明 ：IM SDK 的应用配置信息
 * 简单说明 : 房间群ID (选填)
 * @type {string}
 */
var avChatRoomId = '@TGS#aY4UISKE6'; //这里可以不填，分享链接会带上这个参数，页面可以从链接中获取。
if(webim.Tool.getQueryString("chat_room_id")){
    avChatRoomId=webim.Tool.getQueryString("chat_room_id");//用户自定义房间群id
}

/**
 * 变量说明 ：IM SDK 的应用配置信息
 * 简单说明 : 全局配置 (选填)
 * @type {string}
 */
var selType = webim.SESSION_TYPE.GROUP;
var selToID = avChatRoomId;//当前选中聊天id（当聊天类型为私聊时，该值为好友帐号，否则为群号）
var selSess = null;//当前聊天会话
var selSessHeadUrl = '../addons/sz_yi/plugin/live/template/mobile/default/static/img/2017.jpg';//默认群组头像(选填)

/**
 * 变量说明 ：IM SDK 的应用配置信息
 * 简单说明 : 标识当前用户身份，只需声明，会在initParams()中进行赋值
 * @type {string}
 */
var loginInfo = {
    'sdkAppID': '', //用户所属应用id
    'appIDAt3rd': '', //用户所属应用id
    'accountType': '', //用户所属应用帐号类型
    'identifier': '', //当前用户ID,必须是否字符串类
    'identifierNick': '', //当前用户昵称
    'userSig': '', //当前用户身份凭证，必须是字符串类型
    'headurl': ''//当前用户默认头像
};

//监听（多终端同步）群系统消息方法，方法都定义在base.js文件中
//注意每个数字代表的含义，比如，
//1表示监听申请加群消息，2表示监听申请加群被同意消息，3表示监听申请加群被拒绝消息等
var onGroupSystemNotifys = {
    //"1": onApplyJoinGroupRequestNotify, //申请加群请求（只有管理员会收到,暂不支持）
    //"2": onApplyJoinGroupAcceptNotify, //申请加群被同意（只有申请人能够收到,暂不支持）
    //"3": onApplyJoinGroupRefuseNotify, //申请加群被拒绝（只有申请人能够收到,暂不支持）
    //"4": onKickedGroupNotify, //被管理员踢出群(只有被踢者接收到,暂不支持)
    "5": onDestoryGroupNotify, //群被解散(全员接收)
    //"6": onCreateGroupNotify, //创建群(创建者接收,暂不支持)
    //"7": onInvitedJoinGroupNotify, //邀请加群(被邀请者接收,暂不支持)
    //"8": onQuitGroupNotify, //主动退群(主动退出者接收,暂不支持)
    //"9": onSetedGroupAdminNotify, //设置管理员(被设置者接收,暂不支持)
    //"10": onCanceledGroupAdminNotify, //取消管理员(被取消者接收,暂不支持)
    "11": onRevokeGroupNotify, //群已被回收(全员接收)
    "255": onCustomGroupNotify//用户自定义通知(默认全员接收)
};


//监听连接状态回调变化事件
var onConnNotify=function(resp){
    switch(resp.ErrorCode){
        case webim.CONNECTION_STATUS.ON:
            //webim.Log.warn('连接状态正常...');
            break;
        case webim.CONNECTION_STATUS.OFF:
            webim.Log.warn('连接已断开，无法收到新消息，请检查下你的网络是否正常');
            break;
        default:
            webim.Log.error('未知连接状态,status='+resp.ErrorCode);
            break;
    }
};

//监听事件
var listeners = {
    "onConnNotify": onConnNotify, //选填
    "jsonpCallback": jsonpCallback, //IE9(含)以下浏览器用到的jsonp回调函数,移动端可不填，pc端必填
    "onBigGroupMsgNotify": onBigGroupMsgNotify, //监听新消息(大群)事件，必填
    "onMsgNotify": onMsgNotify,//监听新消息(私聊(包括普通消息和全员推送消息)，普通群(非直播聊天室)消息)事件，必填
    "onGroupSystemNotifys": onGroupSystemNotifys, //监听（多终端同步）群系统消息事件，必填
    "onGroupInfoChangeNotify": onGroupInfoChangeNotify//监听群资料变化事件，选填
};

var isAccessFormalEnv=true;//是否访问正式环境

if(webim.Tool.getQueryString("isAccessFormalEnv")=="false"){
    isAccessFormalEnv=false;//访问测试环境
}

var isLogOn = false;//是否在浏览器控制台打印 im sdk日志

//其他对象，选填
var options = {
    'isAccessFormalEnv': isAccessFormalEnv, //是否访问正式环境，默认访问正式，选填
    'isLogOn': isLogOn//是否开启控制台打印日志,默认开启，选填
};
var openEmotionFlag = false;//是否打开表情，目前小直播IM SDK暂不支持发送表情
/**
 * IM Web SDK 参数配置
 * ******************************* End *************************************
 */