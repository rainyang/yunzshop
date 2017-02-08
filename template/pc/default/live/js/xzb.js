var identifier = null,
    sig = null,
    nickName = null,
    type = 0;

var getIdentifier = webim.Tool.getQueryString('identifier');
if( getIdentifier && getIdentifier != 0){
    identifier = getIdentifier;
} else {
    // identifier = $('#Identifier').text();
    identifier = document.getElementById("identifier").innerText;
}

var getSig = webim.Tool.getQueryString("sig")
if( getSig ){
    sig = getSig;
} else {
    // sig = $('#sig').text();
    sig = document.getElementById("sig").innerText;
}

var nickName = document.getElementById("nickName").innerText;

/**
 * 功能逻辑
 * Start
 */
(function () {

    //官方20170104的demo遗漏了下面这个方法
    function getParams(name) {
        var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i');
        var r = window.location.search.substr(1).match(reg);
        if (r != null) {
            //return unescape(r[2]);
            return decodeURIComponent(r[2]);
        }
        return null;
    }

    var sUserAgent = navigator.userAgent.toLowerCase()
        , bIsIpad = "ipad" == sUserAgent.match(/ipad/i)
        , bIsIphoneOs = "iphone os" == sUserAgent.match(/iphone os/i)
        , bIsAndroid = "android" == sUserAgent.match(/android/i)
        , bIsPc = !bIsIpad && !bIsIphoneOs && !bIsAndroid
        , weixin = "micromessenger" == sUserAgent.match(/MicroMessenger/i)
        , qq = "QQ/" == navigator.userAgent.match(/QQ\//i)
        , weibo = "weibo" == sUserAgent.match(/WeiBo/i)
        , onClick = "ontouchend"in window ? "touchend" : "click";

    //分享的视频地址
    var renderData={}, //页面渲染所需的数据，JOSN格式
        hlsUrl = '',
        flvUrl = '';

    /**
     * 函数功能：获取url参数，并进一步获取页面所需的参数
     * 1.初始化Web IM SDK 登录参数
     * 2.获取页面渲染数据，包括用户id、昵称，直播名称，直播状态，视频地址等数据
     * 3.获取视频播放地址
     *
     */
    
    function initParams(){
        /**
         * 获取url参数
         * ?userid=xxxx&type=x&fileid=xxx&ts=xxx
         * hls
         * chat_room_id
         * sdkappid
         * acctype
         */

        // sdkAppID = getParams("sdkappid") || sdkAppID;
        sdkAppID = 1400018359;
        // accountType = getParams("acctype") || accountType;
        accountType = 9036;
        avChatRoomId = selToID = getParams("groupid") || avChatRoomId;
        hlsUrl = getParams("hls");


        // var nickName = getParams("identifier");
        // var nickName = identifier,
            // type = getParams("type");
            // type = 0;
        if( /@v_tls/.test(nickName)){ //匿名ID需要自定义昵称
            nickName = prompt("输入您的昵称", "游客");
        }
        loginInfo = {
            'sdkAppID': sdkAppID, //用户所属应用id,必填
            'appIDAt3rd': sdkAppID, //用户所属应用id，必填
            'accountType': accountType, //用户所属应用帐号类型，必填
            'identifier': identifier, //当前用户ID,必须是否字符串类型，选填
            'identifierNick': nickName || '游客', //当前用户昵称，选填
            // 'userSig': getParams('sig'), //当前用户身份凭证，必须是字符串类型，选填
            'userSig': sig, //当前用户身份凭证，必须是字符串类型，选填
            'headurl': '../addons/sz_yi/template/mobile/default/live/img/2016.gif'//当前用户默认头像，选填
        };
        // console.log(loginInfo,'loginfo') //todo 屏蔽, 用于生产环境
        //将account_type保存到cookie中,有效期是1天 , 在登录后tlsGetUserSig回调里会用到
        webim.Tool.setCookie('accountType', loginInfo.accountType, 3600 * 24);
        // console.log('webim.Tool.setCookie') //todo 屏蔽, 用于生产环境
        // var _data = {
        //     // Action: 'GetUserInfo',
        //     api: 'live/LiveInfo',
        //     // userid: getParams('userid'),
        //     room_id: getParams('room_id'),
        //     // type: getParams('type'),
        //     type: 0,
        //     // fileid: getParams('fileid')||''
        // };

        return $.ajax({
            type: "GET",
            url: window.document.location.href.substring(0, window.document.location.href.indexOf(window.document.location.pathname)) + "/app_api.php?api=live/LiveInfo",
            // data: JSON.stringify(_data),
            data: "room_id=" + getParams("room_id"),
            crossDomain: true ,
            /*xhrFields: {
                withCredentials: true
            },*/
            dataType: 'json'
        }).done(function (data, textStatus, jqXHR) {
            if(data.result == 1){
                var defPic = '../addons/sz_yi/template/mobile/default/live/img/user-img.png'; //用户默认头像
                $('.j-user-avatar').html('<img src="'+ (data.data.userinfo.avatar || defPic) +'">');
                $('.j-user-name').text(data.data.userinfo.nickname);
                avChatRoomId = selToID =  data.data.chat_room_id || avChatRoomId; //todo 换了顺序

                //hlsUrl = data.data.hls_downstream_address.replace('.hls','.m3u8');
                renderData = data;
                hlsUrl = data.data.hls_downstream_address;
                flvUrl = data.data.flv_downstream_address;

                //查询在线人数
                // console.log('avChatRoomId:'+avChatRoomId);  
                // var memberNum = window.setTimeout(getMemberNumTotal,2000);
                // var refreshNum = function(){
                //     $('#user-icon-fans').html(parseInt(memberNum));
                // }
                // window.setTimeout(refreshNum,2000);

                //房间成员数加1
                // $('#user-icon-fans').html( memberCount);

                //var loveCount = $('#user-icon-like').html();
                // $('#user-icon-like').html( data.data.likecount);

                //设置封面
                document.querySelector("#PlayerContainer").appendChild(initVideoCover(data));
            }else{
                // alert("接口返回数据错误: " + data.returnMsg +'['+ data.returnValue +']');
                alert("接口返回数据错误: " + data.msg);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            alert("接口返回数据错误");
        });
    }

    function initSwf(url){
        var template = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="" id="FlashPlayer" width="100%" height="100%">' +
            '<param name="movie"  value="http://imgcache.qq.com/open/qcloud/video/player/release/QCPlayer.swf" />' +
            '<param name="quality" value="autohigh" />' +
            '<param name="swliveconnect" value="true" />' +
            '<param name="allowScriptAccess" value="always" />' +
            '<param name="bgcolor" value="#0" />' +
            '<param name="allowFullScreen" value="true" />' +
            '<param name="wmode" value="opaque" />' +
            '<param name="FlashVars" value="url='+url+'" />' +
            '<embed src="http://imgcache.qq.com/open/qcloud/video/player/release/QCPlayer.swf" width="100%" height="100%" name="FlashPlayer"' +
            'quality="autohigh"' +
            'bgcolor="#000000"' +
            'align="middle"' +
            'allowFullScreen="true"' +
            'allowScriptAccess="always"' +
            'type="application/x-shockwave-flash"' +
            'swliveconnect="true"' +
            'wmode="opaque"' +
            'FlashVars="url='+url+'"' +
            'pluginspage="http://www.macromedia.com/go/getflashplayer" >' +
            '</embed></object>';
        return template;
    }
    function initVideoCover(data){
        var coverURL = '../addons/sz_yi/template/mobile/default/live/img/play-button.png';

        var elem = document.createElement('div');
        elem.id = 'PlayerCover';
        elem.style.backgroundImage='url('+coverURL+')';
        elem.style.backgroundSize = '30% auto';
        elem.style.backgroundPosition = '50% 50%';
        elem.style.backgroundRepeat = 'no-repeat';

        elem.classList.add('cover');
        if(data.data.type == 1 || (data.data.type == 0 && data.data.status == 1) ){ //todo data.data.status
            elem.classList.add('cover-play-btn'); 
        }
        return elem;
    }
    function showVideoCover(){
        document.querySelector("#PlayerCover").style.display = '';
    }
    function hideVideoCover(){
        document.querySelector("#PlayerCover").style.display = 'none';
    }
    function addSource(element, src, type){
        var source = document.createElement('source');
        source.src = src;
        source.type = type;
        element.appendChild(source);
    }

    /**
     * 初始化视频播放
     */
    function initPlayer(){
        
        var container = document.getElementById("PlayerContainer");
         
        container.style.height =  (window.innerHeight || document.documentElement.clientHeight)+'px';
        // console.log(container.style); //todo 屏蔽, 用于生产环境
        // if(renderData.data.type == 0 && renderData.data.status == 0){
        if(renderData.data.status == 0){
            //直播分享且直播已结束，不需要进行视频播放
            // console.log('直播分享且直播已结束，不需要进行视频播放');
            hideVideoCover();
            alert('直播已结束，敬请期待下次直播');
            return;
        }
        //PC平台需要Flash播放器
        if(bIsPc){
            container.innerHTML = initSwf(flvUrl);
        }else{
            //移动端播放逻辑
            var _player = document.createElement('video'),
                EventAry = 'loadstart,suspend,abort,error,emptied,stalled,loadedmetadata,loadeddata,canplay,canplaythrough,playing,waiting,seeking,seeked,ended,durationchange,timeupdate,progress,play,pause,ratechange,volumechange'.split(',');
            
            $.each(EventAry, function (_, event) {
                $(_player).on(event, videoEventHandler);
                //_player.addEventListener(event,videoEventHandler);
            });


            _player.id = 'player';

            if(hlsUrl && /myqcloud.com\//.test(hlsUrl)){
                //hlsUrl = 'http://2157.liveplay.myqcloud.com/2157_358556a1088511e6b91fa4dcbef5e35a.m3u8';
                _player.src = hlsUrl;
                //addSource(_player, hlsUrl, 'application/x-mpegURL');
            }

            _player.setAttribute('preload', 'auto');
            _player.setAttribute('webkit-playsinline', 'true');
            _player.setAttribute('playsinline', 'true');
            _player.setAttribute('x-webkit-airplay', 'true');
            _player.setAttribute('x5-video-player-type', 'h5'); //在Android x5内核浏览器下开启同级模式
            _player.setAttribute('x5-video-player-fullscreen', 'true');//在Android x5内核浏览器下开启播放全屏模式


            // if(getParams('type') == 1){
            //     //点播
            // }else{

            // }
            container.appendChild(_player);
            if(bIsIpad || bIsIphoneOs){
                //_player.style.display = 'none';
                
                document.addEventListener('touchstart', function(){ 
                   _player.play();
                }, false);
                
            }
        }
    }

    var isFirstTimePlay = false,
        playOnError = false;
    function videoEventHandler(event){
        var _player = document.querySelector("#player");
        if(_player && ( event.type == 'timeupdate') && !isFirstTimePlay){
            isFirstTimePlay = true;
            hideVideoCover();
            //hideLoading();
            if(bIsIpad || bIsIphoneOs){
                _player.style.height = 'auto';
                //_player.style.display = '';
            }
            if(bIsAndroid){//在android环境下需要延迟，避免页面抖动
                window.setTimeout(function () {
                    _player.style.height = 'auto';
                },500);
            }
            
        }
        if(_player && event.type == 'pause'){
            isFirstTimePlay = false;;
            showVideoCover();
        }
        if(_player && event.type == 'error'){//在Android 微信 x5 模式下 首次播放失败没有error事件
            alert('视频加载失败，请稍后重试或刷新页面');// hls 的直播地址会有30s的延迟, 首次播放需要重试，在Android 微信 x5 模式下系统默认会重试。正式上线可以去掉这里的提示以实现静默重试 //todo
            playOnError = true;
            if(bIsIpad || bIsIphoneOs){ //ios系统手动重试
                reloadVideo();
            }
        }else{
            playOnError = false;
            if(_reloadTimer){
                window.clearTimeout(_reloadTimer);
            }
        }
        // return;
        if(event.type != 'timeupdate'){
            console.log(event.type);
        }
    }
    var _reloadTimer;
    function reloadVideo(){
        if(_reloadTimer){
            window.clearTimeout(_reloadTimer);
        }
        _reloadTimer = window.setTimeout(function(){
            if(playOnError){//未开始播放
                loadVideo(hlsUrl);
                getVideoElem().play();
            }
        }, 3000);//3s后进行重连
    }
    function getVideoElem(){
        return document.querySelector("#player");
    }
    function loadVideo(url){
        getVideoElem().src = url;
    }

    function initLogin(){
        if(accountMode==1){//托管模式
            //判断是否已经拿到临时身份凭证
            if (webim.Tool.getQueryString('tmpsig')) {
                if (loginInfo.identifier == null) {
                    webim.Log.info('start fetchUserSig');
                    //获取正式身份凭证，成功后会回调 tlsGetUserSig(res)函数
                    TLSHelper.fetchUserSig();
                    showDiscussTool();
                }
            } else if(webim.Tool.getCookie('sdkappid') && webim.Tool.getCookie('userSig') && webim.Tool.getCookie('identifier') && webim.Tool.getCookie('accountType')) {
                //已登录模式 check cookie
                loginInfo.sdkappid = loginInfo.appIDAt3rd= webim.Tool.getCookie('sdkappid');
                loginInfo.userSig = webim.Tool.getCookie('userSig');
                loginInfo.identifier = webim.Tool.getCookie('identifier');
                loginInfo.accountType = webim.Tool.getCookie('accountType');
                sdkLogin();
                showDiscussTool();
            } else {
                //未登录, 无登录态模式, 弹出登录框, 可收消息
                // showLoginForm();
                //sdk登录
                sdkLogin();
            }
        }else{//独立模式
            //sdk登录
            sdkLogin();
            showLoginForm();
        }
    }

    function bindEvent(info){

        $(document).on(onClick,'.j-btn-login',function(){
            tlsLogin();
        });
        $(document).on(onClick,'.j-btn-anologin',function(){
            anoLogin(loginInfo.sdkAppID);
        });
        $(document).on(onClick,'.j-btn-sms',function(event){
            smsPicClick();
            event.stopPropagation();//for switchForm()
        });
        $(document).on(onClick,'.j-btn-like',function(){
            sendGroupLoveMsg();
        });
        $(document).on(onClick,'.j-btn-show-emotion',function(){
            showEmotionDialog();
        });
        $(document).on(onClick,'.j-btn-send-msg',function(){
            onSendMsg();
        });
        $(document).on(onClick,'.j-btn-logout',function(){
            logout();
        });
        $(document).on(onClick,'#video-discuss-form',function(event){
            event.stopPropagation();//for switchForm()
        });
        window.addEventListener("orientationchange", function(e) {

        });
        document.addEventListener("DOMContentLoaded", function(event) {
            var videoPage = document.querySelector('#j-video-page');
                //panel = videoPage.querySelector('.video-pane');

            videoPage.style.width =  window.innerWidth +'px';
            videoPage.style.height =  window.innerHeight +'px';
        });
        window.addEventListener("resize", function(e) {
            //alert(window.innerHeight);
            var videoPage = document.querySelector('#j-video-page'),
                //panel = videoPage.querySelector('.video-pane'),
                container = document.querySelector("#PlayerContainer");
            container.style.width = videoPage.style.width =  window.innerWidth +'px';
            container.style.height = videoPage.style.height =  window.innerHeight +'px';
        });

    }

    function bindEventAfterInitParams(data){
        // if(data.data.type == 1 || (data.data.type == 0 && data.data.status == 1) ){
        if(data.data.status == 1){
            // 点播 或者 直播中，绑定点击播放事件,避免一些浏览器不允许自动播放导致播放视频失败
            $(document).on(onClick,'.video-pane-body', function(){
                var _player = document.querySelector("#player");
                if(_player){
                    hideVideoCover();
                    if(playOnError) {// 加载视频出现错误
                        loadVideo(hlsUrl);
                    }
                    _player.play();
                }
                switchForm();
            });
        }
    }

    /**
     * 初始化分享文案
     * @param info
     */
    function setShareInfo(info) {
        var nick = info.data.userinfo.nickname || info.data.userid,
            shareInfo = {
            title: info.data.title,
            desc:  info.data.type ? nick +' 直播精彩回放': nick +' 正在直播',
            imgUrl: info.data.userinfo.frontcover || location.origin+'/open/qcloud/video/share/img/default_cover.jpg',
            link: window.location.href,
            success: function (ret) {
            },
            cancel: function (ret) {
            }
        };
        document.querySelector('[name=description]').setAttribute('content',shareInfo.desc);
        if (window.wx) {
            //分享到朋友圈
            wx.onMenuShareTimeline({
                title: shareInfo.title +' '+shareInfo.desc,
                link: shareInfo.link,
                imgUrl: shareInfo.imgUrl,
                success: function(){
                },
                cancel: function(){
            }
        });
            //分享给朋友
            wx.onMenuShareAppMessage(shareInfo);
            //分享到QQ
            wx.onMenuShareQQ(shareInfo);
            //分享到腾讯微博
            wx.onMenuShareWeibo(shareInfo);

            wx.onMenuShareQZone(shareInfo);
        } else if (window.mqq) {
            mqq.data.setShareInfo({
                title: shareInfo.title,
                desc: shareInfo.desc,
                share_url: shareInfo.link,
                image_url: shareInfo.imgUrl
            });
        }
    }
    /**
     * 初始化微信 QQ分享接口
     * @param info
     */
    function initShareInfo(info){
        if (window.wx) {
            $.ajax({
                type: "POST",
                'url': SERVER,
                'data': JSON.stringify({
                    'Action':'GetWeixinSignature',
                    'url': location.href.split('?')[0]
                }),
                crossDomain: true ,
                dataType: 'json'
            }).done(function (ret) {
                var code = ret.returnValue;
                var data = ret.data.data;
                if (code == 0 && data) {
                    // data.debug = true;
                    data.jsApiList = [
                        'onMenuShareTimeline',
                        'onMenuShareAppMessage',
                        'onMenuShareQQ',
                        'onMenuShareWeibo',
                        'onMenuShareQZone'
                    ];
                    wx.config(data);
                    wx.ready(function() {
                        setShareInfo(info);
                    });
                } else {
                    setShareInfo(info);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                setShareInfo(info);
            });

            //初始化微信分享，需要用户申请微信开放平台公众号分享权限。以下为测试用数据。
            // wx.config({
            //     debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            //     appId: 'wx0051e12508338fb9', // 必填，公众号的唯一标识
            //     timestamp:1478588250 , // 必填，生成签名的时间戳
            //     nonceStr: '', // 必填，生成签名的随机串
            //     signature: '',// 必填，签名，参考微信开放平台文档 http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
            //     jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','onMenuShareQZone'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
            // });
            wx.ready(function() {
                setShareInfo(info);
            });
        } else {
            setShareInfo(info);
        }
    }

    /**
     * 全局初始化函数入口
     */
    function init(){
        //1.调用初始化参数，获取页面渲染所需的所有数据，并返回一个promise对象，在done 的回调中，获取到所需的数据，进行初始化页面逻辑。
        initParams().done(function (data ) {
            //type=0 直播模式需要进行im登录收发消息
            // if(data.data.type == 0 ){
                initLogin();
            // }
            //初始化视频播放
            initPlayer();
            //初始化微信二次分享内容，具体参考微信公众号分享指引 https://mp.weixin.qq.com/wiki
            // initShareInfo(data);
            //获取参数后才能绑定的事件
            bindEventAfterInitParams(data);
        });
        //绑定操作事件
        bindEvent();
    }

    /**
     * 开始调用全局初始化函数
     */
    init();
})();
/**
 * 功能逻辑
 * End
 */





