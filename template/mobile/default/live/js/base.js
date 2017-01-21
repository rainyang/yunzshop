//获取在线人数
function getMemberNumTotal(){
    var groupData = {
        "GroupIdList": [
            avChatRoomId
        ],
        "GroupBaseInfoFilter": [
            "MemberNum"
        ]
    };
    webim.getGroupInfo(groupData, function(resp){
        //更新在线人数
        var memberNum = parseInt(resp.GroupInfo[0].MemberNum)-1;

        $('#user-icon-fans').html(memberNum);

        window.setTimeout(function () {
            //在进群之后才能发送登录消息，不然有可能自己收不到
            sendCustomLoginMsg();
        },1000);

    },function (err){
        console.log('err:'+JSON.stringify(err));
    });
}

//更新在线人数
function refreshNum(){
    var memberNum = window.setTimeout(getMemberNumTotal,5000);
    var refresh = function(){
        $('#user-icon-fans').html(parseInt(memberNum));
    }
    window.setTimeout(refresh,5000);

}


//IE9(含)以下浏览器用到的jsonp回调函数
function jsonpCallback(rspData) {
    //设置接口返回的数据
    webim.setJsonpLastRspData(rspData);
}

//监听大群新消息（普通，点赞，提示，红包）
function onBigGroupMsgNotify(msgList) {
    if(msgList.length > 0){
        //console.log('onBigGroupMsgNotify',msgList);
    }
    for (var i = msgList.length - 1; i >= 0; i--) {//遍历消息，按照时间从后往前
        var msg = msgList[i];
        //console.warn(msg);
        webim.Log.warn('receive a new avchatroom group msg: ' + msg.getFromAccountNick());
        //显示收到的消息
        showMsg(msg);
    }
}

//监听新消息(私聊(包括普通消息、全员推送消息)，普通群(非直播聊天室)消息)事件
//newMsgList 为新消息数组，结构为[Msg]
function onMsgNotify(newMsgList) {
    console.log('onMsgNotify', newMsgList);
    var newMsg;
    for (var j in newMsgList) {//遍历新消息
        newMsg = newMsgList[j];
        handlderMsg(newMsg);//处理新消息
    }
}

//处理消息（私聊(包括普通消息和全员推送消息)，普通群(非直播聊天室)消息）
function handlderMsg(msg) {
    var fromAccount, fromAccountNick, sessType, subType,contentHtml;

    fromAccount = msg.getFromAccount();
    if (!fromAccount) {
        fromAccount = '';
    }
    fromAccountNick = msg.getFromAccountNick();
    if (!fromAccountNick) {
        fromAccountNick = fromAccount;
    }

    //解析消息
    //获取会话类型
    //webim.SESSION_TYPE.GROUP-群聊，
    //webim.SESSION_TYPE.C2C-私聊，
    sessType = msg.getSession().type();
    //获取消息子类型
    //会话类型为群聊时，子类型为：webim.GROUP_MSG_SUB_TYPE
    //会话类型为私聊时，子类型为：webim.C2C_MSG_SUB_TYPE
    subType = msg.getSubType();

    switch (sessType) {
        case webim.SESSION_TYPE.C2C://私聊消息
            switch (subType) {
                case webim.C2C_MSG_SUB_TYPE.COMMON://c2c普通消息
                    //业务可以根据发送者帐号fromAccount是否为app管理员帐号，来判断c2c消息是否为全员推送消息，还是普通好友消息
                    //或者业务在发送全员推送消息时，发送自定义类型(webim.MSG_ELEMENT_TYPE.CUSTOM,即TIMCustomElem)的消息，在里面增加一个字段来标识消息是否为推送消息
                    contentHtml = convertMsgtoHtml(msg);
                    webim.Log.warn('receive a new c2c msg: fromAccountNick=' + fromAccountNick+", content="+contentHtml);
                    //c2c消息一定要调用已读上报接口
                    var opts={
                        'To_Account':fromAccount,//好友帐号
                        'LastedMsgTime':msg.getTime()//消息时间戳
                    };
                    webim.c2CMsgReaded(opts);
                    alert('收到一条c2c消息(好友消息或者全员推送消息): 发送人=' + fromAccountNick+", 内容="+contentHtml);
                    break;
            }
            break;
        case webim.SESSION_TYPE.GROUP://普通群消息，对于直播聊天室场景，不需要作处理
            break;
    }
}

//sdk登录
function sdkLogin(callback) {
    console.log('sdkLogin', loginInfo);
    //web sdk 登录
    webim.login(loginInfo, listeners, options,
            function (data) {
                /**
                 * data :
                 * ActionStatus
                 * ErrorCode
                 * ErrorInfo
                 * identifierNick
                 */
                //identifierNick为登录用户昵称(没有设置时，为帐号)，无登录态时为空
                loginInfo.identifierNick = data.identifierNick ? data.identifierNick : loginInfo.identifierNick;
                webim.Log.info('webim登录成功');
                console.log('sdkLogin webim登录成功', data);
                applyJoinBigGroup(avChatRoomId);//加入大群
                //hideDiscussForm();//隐藏评论表单
                //initEmotionUL();//初始化表情
                callback && callback.call();
            },
            function (err) {
                // alert('sdkLogin' + err.ErrorInfo); //因为报错信息非常长,暂替换成更友好的提示
                alert('无法进入直播间,请尝试重新登录');
            }
    );//
}

//进入大群
function applyJoinBigGroup(groupId) {
    var options = {
        'GroupId': groupId//群id
    };
    webim.applyJoinBigGroup(
            options,
            function (resp) {
                //JoinedSuccess:加入成功; WaitAdminApproval:等待管理员审批
                if (resp.JoinedStatus && resp.JoinedStatus == 'JoinedSuccess') {
                    console.log('applyJoinBigGroup 进群成功');
                    webim.Log.info('进群成功');
                    selToID = groupId;

                    refreshNum();
                } else {
                    alert('进群失败');
                }
            },
            function (err) {
                console.log('applyJoinBigGroup: 进群失败', err);
                if(10010 == err.ErrorCode){
                    alert('直播已结束('+err.ErrorCode+')');
                    hideLoginForm();
                }else  if(10013 == err.ErrorCode){
                    alert('进群失败 : ' + err.ErrorInfo);
                }else{
                    alert('进群失败 : ' + err.ErrorInfo);
                }
            }
    );
}

//显示消息（群普通+点赞+提示+红包）
function showMsg(msg) {
    var isSelfSend, fromAccount, fromAccountNick, sessType, subType;
    var ul, li, paneDiv, textDiv, nickNameSpan, contentSpan;

    fromAccount = msg.getFromAccount();
    if (!fromAccount) {
        fromAccount = '';
    }
    fromAccountNick = msg.getFromAccountNick();
    if (!fromAccountNick) {
        fromAccountNick = '未知用户';
    }
    ul = document.getElementById("video_sms_list");
    var maxDisplayMsgCount = 5;
    //var opacityStep=(1.0/4).toFixed(2);
    var opacityStep = 0.2;
    var opacity;
    var childrenLiList = $("#video_sms_list").children();
    if (childrenLiList.length == maxDisplayMsgCount) {
        $("#video_sms_list").children(":first").remove();
        for (var i = 0; i < maxDisplayMsgCount; i++) {
            opacity = opacityStep * (i + 1) + 0.2;
            $('#video_sms_list').children().eq(i).css("opacity", opacity);
        }
    }
    li = document.createElement("li");
    paneDiv = document.createElement("div");
    paneDiv.setAttribute('class', 'video-sms-pane');
    textDiv = document.createElement("div");
    textDiv.setAttribute('class', 'video-sms-text');
    nickNameSpan = document.createElement("span");

    var colorList = ['red', 'green', 'blue', 'org'];
    var index = Math.round(fromAccount.length % colorList.length) || 0;
    var color = colorList[index];

    nickNameSpan.setAttribute('class', 'user-name-' + color);

    nickNameSpan.innerHTML = fromAccountNick;
    contentSpan = document.createElement("span");

    //解析消息
    //获取会话类型，目前只支持群聊
    //webim.SESSION_TYPE.GROUP-群聊，
    //webim.SESSION_TYPE.C2C-私聊，
    sessType = msg.getSession().type();
    //获取消息子类型
    //会话类型为群聊时，子类型为：webim.GROUP_MSG_SUB_TYPE
    //会话类型为私聊时，子类型为：webim.C2C_MSG_SUB_TYPE
    subType = msg.getSubType();

    isSelfSend = msg.getIsSend();//消息是否为自己发的

    var _firstMsg = msg.getElems()[0];
    if(subType == webim.GROUP_MSG_SUB_TYPE.COMMON && _firstMsg.getType() == webim.MSG_ELEMENT_TYPE.TEXT && _firstMsg.getContent().isAppMsg ) {
        //过滤出来自app的消息
        convertAppMsg(nickNameSpan, contentSpan, msg);
    }else{
        //原始消息格式
        switch (subType) {

            case webim.GROUP_MSG_SUB_TYPE.COMMON://群普通消息
                var msgHTML = convertMsgtoHtml(msg);
                if(msgHTML){
                    // 由于app 自定义了消息格式，fromAccountNick 不一定是用户的nick，实际nick存放在 消息体里
                    var appData = msg.getElems()[0].getContent().getData();
                    if(appData && appData.nickName){
                        nickNameSpan.innerHTML = appData.nickName;
                    }
                    contentSpan.innerHTML = msgHTML;
                }else {
                    return;
                }
                break;
            case webim.GROUP_MSG_SUB_TYPE.REDPACKET://群红包消息
                return;
                contentSpan.innerHTML = "[群红包消息]" + convertMsgtoHtml(msg);
                break;
            case webim.GROUP_MSG_SUB_TYPE.LOVEMSG://群点赞消息
                //业务自己可以增加逻辑，比如展示点赞动画效果
                contentSpan.innerHTML = "[群点赞消息]" + convertMsgtoHtml(msg);
                //展示点赞动画
                showLoveMsgAnimation();
                break;
            case webim.GROUP_MSG_SUB_TYPE.TIP://群提示消息
                var msgHTML = convertMsgtoHtml(msg);
                if(msgHTML){
                    contentSpan.innerHTML = "[群提示消息]" + msgHTML;
                }else {
                    return;
                }
                //contentSpan.innerHTML = "[群提示消息]" + convertMsgtoHtml(msg);
                break;
        }
    }
    if(nickNameSpan.innerHTML){
        textDiv.appendChild(nickNameSpan);
    }
    if(contentSpan.innerHTML) {
        textDiv.appendChild(contentSpan);
    }

    paneDiv.appendChild(textDiv);
    li.appendChild(paneDiv);
    ul.appendChild(li);
}

//把消息转换成Html
function convertMsgtoHtml(msg) {
    var html = "", elems, elem, type, content;
    elems = msg.getElems();//获取消息包含的元素数组
    for (var i in elems) {
        elem = elems[i];
        type = elem.getType();//获取元素类型
        content = elem.getContent();//获取元素对象
        switch (type) {
            case webim.MSG_ELEMENT_TYPE.TEXT:
                html += convertTextMsgToHtml(content);
                break;
            case webim.MSG_ELEMENT_TYPE.FACE:
                html += convertFaceMsgToHtml(content);
                break;
            case webim.MSG_ELEMENT_TYPE.IMAGE:
                html += convertImageMsgToHtml(content);
                break;
            case webim.MSG_ELEMENT_TYPE.SOUND:
                html += convertSoundMsgToHtml(content);
                break;
            case webim.MSG_ELEMENT_TYPE.FILE:
                html += convertFileMsgToHtml(content);
                break;
            case webim.MSG_ELEMENT_TYPE.LOCATION://暂不支持地理位置
                //html += convertLocationMsgToHtml(content);
                break;
            case webim.MSG_ELEMENT_TYPE.CUSTOM:
                return ;
                html += convertCustomMsgToHtml(content);
                break;
            case webim.MSG_ELEMENT_TYPE.GROUP_TIP:
                return ;
                html += convertGroupTipMsgToHtml(content);
                break;
            default:
                webim.Log.error('未知消息元素类型: elemType=' + type);
                break;
        }
    }
    return html;
}

//解析文本消息元素
function convertTextMsgToHtml(content) {
    return content.getText();

/*
    var _data,
        _text = content.getText();
    console.log('convertTextMsgToHtml' ,_text);
    if(content.getData()){
        _data = content.getData();
        _convertAppMsg(_data);
        return _data.msg;
    }else{
        //原文本消息格式,这里会显示无昵称的情况
        //return _text;
        return ;
    }
*/

    /*if(/^\{.*\}$/.test(_text)){
        //小直播的自定义格式，text为json数据
        try{
            _json = JSON.parse(_text);
            console.log('convertTextMsgToHtml' ,_json);
            return converAppMsg(_json);
        }catch (e){

        }
    }else{
        return _text;
    }*/
}

function _convertAppMsg(data){
    switch (data.userAction){
        case 1:
            //文本消息
            break;
        case 2:
            //用户加入直播
            data.msg='进入了房间';
            //房间成员数加1
            memberCount = $('#user-icon-fans').html();
            $('#user-icon-fans').html(parseInt(memberCount) + 1);
            break;
        case 3:
            //用户退出直播
            data.msg='离开了房间';
            memberCount = parseInt($('#user-icon-fans').html());
            if (memberCount > 0) {
                $('#user-icon-fans').html(memberCount - 1);
            }
            break;
        case 4:
            //点赞消息
            data.msg='点了个赞';
            showLoveMsgAnimation();
            break;
        case 5:
            //弹幕消息
            break;
    }
}

function convertAppMsg(nickSpan, contentSpan, msg){
    var elems, elem, type, content,data,memberCount;
    elems = msg.getElems();//获取消息包含的元素数组
    for (var i in elems) {
        elem = elems[i];
        type = elem.getType();//获取元素类型
        content = elem.getContent();//获取元素对象
        data = content.getData();
        switch (data.userAction) {
            case 1:
                //文本消息
                nickSpan.innerHTML = data.nickName ;
                contentSpan.innerHTML = data.msg ;
                break;
            case 2:
                //用户加入直播
                nickSpan.innerHTML = '通知 <i>'+ data.nickName + '</i> 加入直播';
                nickSpan.setAttribute('class', 'user-name-red');
                contentSpan.innerHTML = '';
                //房间成员数加1
                memberCount = $('#user-icon-fans').html();
                $('#user-icon-fans').html(parseInt(memberCount) + 1);
                break;
            case 3:
                //用户退出直播
                nickSpan.innerHTML = '通知 <i>'+ data.nickName + '</i> 退出直播';
                nickSpan.setAttribute('class', 'user-name-red');
                contentSpan.innerHTML = '';
                memberCount = parseInt($('#user-icon-fans').html());
                if (memberCount > 0) {
                    $('#user-icon-fans').html(memberCount - 1);
                }
                break;
            case 4:
                //点赞消息
                nickSpan.innerHTML = '通知 '+ data.nickName + ' 点了个赞' ;
                nickSpan.setAttribute('class', 'user-name-red');
                contentSpan.innerHTML = '' ;
                showLoveMsgAnimation();
                break;
            case 5:
                //弹幕消息
                //文本消息
                nickSpan.innerHTML = data.nickName ;
                contentSpan.innerHTML = data.msg ;
                break;
            case 6:
                //商品置顶
                var li = $(".lw-goods-list li[data-id="+data.msg+"]");

                var top_img_src = li.find('img').attr('src');
                var top_href = li.find('a').attr('href');
                var top_goods_price = li.find('.p1').text();
                $("#top_img").attr('src', top_img_src);
                $("#top_href").attr('href', top_href);
                $("#top_goods_price").text(top_goods_price);
                break;
        }
    }
}
//解析表情消息元素
function convertFaceMsgToHtml(content) {
    var index = content.getIndex();
    var data = content.getData();
    var faceUrl = null;
    var emotion = webim.Emotions[index];
    if (emotion && emotion[1]) {
        faceUrl = emotion[1];
    }
    if (faceUrl) {
        return  "<img src='" + faceUrl + "'/>";
    } else {
        return data;
    }
}
//解析图片消息元素
function convertImageMsgToHtml(content) {
    var smallImage = content.getImage(webim.IMAGE_TYPE.SMALL);//小图
    var bigImage = content.getImage(webim.IMAGE_TYPE.LARGE);//大图
    var oriImage = content.getImage(webim.IMAGE_TYPE.ORIGIN);//原图
    if (!bigImage) {
        bigImage = smallImage;
    }
    if (!oriImage) {
        oriImage = smallImage;
    }
    return  "<img src='" + smallImage.getUrl() + "#" + bigImage.getUrl() + "#" + oriImage.getUrl() + "' style='CURSOR: hand' id='" + content.getImageId() + "' bigImgUrl='" + bigImage.getUrl() + "' onclick='imageClick(this)' />";
}
//解析语音消息元素
function convertSoundMsgToHtml(content) {
    var second = content.getSecond();//获取语音时长
    var downUrl = content.getDownUrl();
    if (webim.BROWSER_INFO.type == 'ie' && parseInt(webim.BROWSER_INFO.ver) <= 8) {
        return '[这是一条语音消息]demo暂不支持ie8(含)以下浏览器播放语音,语音URL:' + downUrl;
    }
    return '<audio src="' + downUrl + '" controls="controls" onplay="onChangePlayAudio(this)" preload="none"></audio>';
}
//解析文件消息元素
function convertFileMsgToHtml(content) {
    var fileSize = Math.round(content.getSize() / 1024);
    return '<a href="' + content.getDownUrl() + '" title="点击下载文件" ><i class="glyphicon glyphicon-file">&nbsp;' + content.getName() + '(' + fileSize + 'KB)</i></a>';

}
//解析位置消息元素
function convertLocationMsgToHtml(content) {
    return '经度=' + content.getLongitude() + ',纬度=' + content.getLatitude() + ',描述=' + content.getDesc();
}
//解析自定义消息元素
function convertCustomMsgToHtml(content) {
    var data = content.getData();
    var desc = content.getDesc();
    var ext = content.getExt();
    return "data=" + data + ", desc=" + desc + ", ext=" + ext;
}
//解析群提示消息元素
function convertGroupTipMsgToHtml(content) {
    var WEB_IM_GROUP_TIP_MAX_USER_COUNT = 10;
    var text = "";
    var maxIndex = WEB_IM_GROUP_TIP_MAX_USER_COUNT - 1;
    var opType, opUserId, userIdList;
    var memberCount;
    opType = content.getOpType();//群提示消息类型（操作类型）
    opUserId = content.getOpUserId();//操作人id
    switch (opType) {
        case webim.GROUP_TIP_TYPE.JOIN://加入群
            userIdList = content.getUserIdList();
            // console.log(content);
            //text += opUserId + "邀请了";
            for (var m in userIdList) {
                text += userIdList[m] + ",";
                if (userIdList.length > WEB_IM_GROUP_TIP_MAX_USER_COUNT && m == maxIndex) {
                    text += "等" + userIdList.length + "人";
                    break;
                }
            }
            text = text.substring(0, text.length - 1);
            text += "进入房间";
            //房间成员数加1
            memberCount = $('#user-icon-fans').html();
            $('#user-icon-fans').html(parseInt(memberCount) + 1);
            break;
        case webim.GROUP_TIP_TYPE.QUIT://退出群
            text += opUserId + "离开房间";
            //房间成员数减1
            memberCount = parseInt($('#user-icon-fans').html());
            if (memberCount > 0) {
                $('#user-icon-fans').html(memberCount - 1);
            }
            break;
        case webim.GROUP_TIP_TYPE.KICK://踢出群
            text += opUserId + "将";
            userIdList = content.getUserIdList();
            for (var m in userIdList) {
                text += userIdList[m] + ",";
                if (userIdList.length > WEB_IM_GROUP_TIP_MAX_USER_COUNT && m == maxIndex) {
                    text += "等" + userIdList.length + "人";
                    break;
                }
            }
            text += "踢出该群";
            break;
        case webim.GROUP_TIP_TYPE.SET_ADMIN://设置管理员
            text += opUserId + "将";
            userIdList = content.getUserIdList();
            for (var m in userIdList) {
                text += userIdList[m] + ",";
                if (userIdList.length > WEB_IM_GROUP_TIP_MAX_USER_COUNT && m == maxIndex) {
                    text += "等" + userIdList.length + "人";
                    break;
                }
            }
            text += "设为管理员";
            break;
        case webim.GROUP_TIP_TYPE.CANCEL_ADMIN://取消管理员
            text += opUserId + "取消";
            userIdList = content.getUserIdList();
            for (var m in userIdList) {
                text += userIdList[m] + ",";
                if (userIdList.length > WEB_IM_GROUP_TIP_MAX_USER_COUNT && m == maxIndex) {
                    text += "等" + userIdList.length + "人";
                    break;
                }
            }
            text += "的管理员资格";
            break;

        case webim.GROUP_TIP_TYPE.MODIFY_GROUP_INFO://群资料变更
            text += opUserId + "修改了群资料：";
            var groupInfoList = content.getGroupInfoList();
            var type, value;
            for (var m in groupInfoList) {
                type = groupInfoList[m].getType();
                value = groupInfoList[m].getValue();
                switch (type) {
                    case webim.GROUP_TIP_MODIFY_GROUP_INFO_TYPE.FACE_URL:
                        text += "群头像为" + value + "; ";
                        break;
                    case webim.GROUP_TIP_MODIFY_GROUP_INFO_TYPE.NAME:
                        text += "群名称为" + value + "; ";
                        break;
                    case webim.GROUP_TIP_MODIFY_GROUP_INFO_TYPE.OWNER:
                        text += "群主为" + value + "; ";
                        break;
                    case webim.GROUP_TIP_MODIFY_GROUP_INFO_TYPE.NOTIFICATION:
                        text += "群公告为" + value + "; ";
                        break;
                    case webim.GROUP_TIP_MODIFY_GROUP_INFO_TYPE.INTRODUCTION:
                        text += "群简介为" + value + "; ";
                        break;
                    default:
                        text += "未知信息为:type=" + type + ",value=" + value + "; ";
                        break;
                }
            }
            break;

        case webim.GROUP_TIP_TYPE.MODIFY_MEMBER_INFO://群成员资料变更(禁言时间)
            text += opUserId + "修改了群成员资料:";
            var memberInfoList = content.getMemberInfoList();
            var userId, shutupTime;
            for (var m in memberInfoList) {
                userId = memberInfoList[m].getUserId();
                shutupTime = memberInfoList[m].getShutupTime();
                text += userId + ": ";
                if (shutupTime != null && shutupTime !== undefined) {
                    if (shutupTime == 0) {
                        text += "取消禁言; ";
                    } else {
                        text += "禁言" + shutupTime + "秒; ";
                    }
                } else {
                    text += " shutupTime为空";
                }
                if (memberInfoList.length > WEB_IM_GROUP_TIP_MAX_USER_COUNT && m == maxIndex) {
                    text += "等" + memberInfoList.length + "人";
                    break;
                }
            }
            break;
        default:
            text += "未知群提示消息类型：type=" + opType;
            break;
    }
    return text;
}

//tls登录
function tlsLogin() {
    //跳转到TLS登录页面
    TLSHelper.goLogin({
        sdkappid: loginInfo.sdkAppID,
        acctype: loginInfo.accountType,
        url: window.location.href
    });
}

/**
 * 匿名登录流程
 * TLSHelper.anologin -> 回调tlsAnoLogin -> 跳转链接带上tmpsig -> get tmpsig and call TLSHelper.fetchUserSig()
 * -> call tlsGetUserSig() -> get usersig and prepare loginInfo -> call sdkLogin()
 * @param sdkAppID
 */
//匿名登录
function anoLogin(sdkAppID) {
    TLSHelper.anologin({
        sdkappid: sdkAppID,
        url: window.location.href
    });
}


window.tlsAnoLogin = function(res) {
    switch (parseInt(res.ErrorCode)) {
        case 0:
            // 跳转到成功地址，自行拼接上必须的参数
            var params = {
                sdkappid: loginInfo.sdkAppID,
                identifier: res.Identifier,
                tmpsig: res.TmpSig
            };
            var url = res.Url;
            for (var p in params) {
                if (params.hasOwnProperty(p))
                    url = TLSHelper.setQuery(url, p, params[p]);
            }
            location.href = url;
            break;
        default:
            alert(res.ErrorCode + "=>" + res.ErrorInfo);
    }
};


//第三方应用需要实现这个函数，并在这里拿到UserSig
function tlsGetUserSig(res) {
    //成功拿到凭证
    if (res.ErrorCode == webim.TLS_ERROR_CODE.OK) {
        //从当前URL中获取参数为identifier的值
        loginInfo.identifier = webim.Tool.getQueryString("identifier");
        //拿到正式身份凭证
        //console.log(res); //需要判断userSig是否生效
        loginInfo.userSig = res.UserSig;
        //console.log(loginInfo);
        //从当前URL中获取参数为sdkappid的值
        loginInfo.sdkAppID = loginInfo.appIDAt3rd = Number(webim.Tool.getQueryString("sdkappid"));
        //从cookie获取accountType
        //debugger;
        var accountType = webim.Tool.getCookie('accountType');
        if (accountType) {
            loginInfo.accountType = accountType;
            //带上usersig登录
            sdkLogin(function(){
                //window.history.pushState(null, null, window.location.pathname);
                window.setTimeout(function () {
                    //在进群之后才能发送登录消息，不然有可能自己收不到
                    sendCustomLoginMsg();
                },1000);
                showLogoutForm();

                sendData({
                    Action  :'EnterGroup',
                    userid  :webim.Tool.getQueryString("identifier"),// string  用户id
                    liveuserid  :webim.Tool.getQueryString('userid'),// string  主播id
                    flag    :webim.Tool.getQueryString('type'), // 0:直播 1:点播
                    groupid: webim.Tool.getQueryString('type') == 0 ? avChatRoomId : webim.Tool.getQueryString('fileid'),
                    nickname: loginInfo.identifierNick,
                    headpic: '',
                    fileid  :webim.Tool.getQueryString('fileid') || '' // 点播的情况下使用，用于区分是哪个视频
                });

                var indexUrl = window.location.search.substr(1);
                indexUrl = delUrlParam(delUrlParam(indexUrl,'tmpsig'),'identifier');
                window.history.pushState(null, null, window.location.pathname+'?'+indexUrl);

                //login success save userSig identifier to cookie

                //webim.Tool.setCookie('userSig', loginInfo.userSig, 3600 * 24);
                webim.Tool.setCookie('identifier', loginInfo.identifier, 3600 * 24);
                webim.Tool.setCookie('sdkappid', loginInfo.sdkappid, 3600 * 24);
                webim.Tool.setCookie('identifierNick', loginInfo.identifierNick, 3600 * 24);
                webim.Tool.setCookie('avChatRoomId', avChatRoomId, 3600 * 24);

            });//sdk登录
        } else {
            console.error('accountType非法');
            //alert('accountType非法');
        }
    } else {
        //签名过期，需要重新登录
        if (res.ErrorCode == webim.TLS_ERROR_CODE.SIGNATURE_EXPIRATION) {
            //tlsLogin();
            //tls匿名登录，获取tmpsig
            anoLogin(loginInfo.sdkAppID);
        } else {
            alert("tlsGetUserSig [" + res.ErrorCode + "]" + res.ErrorInfo);
        }
    }
}

//单击图片事件
function imageClick(imgObj) {
    var imgUrls = imgObj.src;
    var imgUrlArr = imgUrls.split("#"); //字符分割
    var smallImgUrl = imgUrlArr[0];//小图
    var bigImgUrl = imgUrlArr[1];//大图
    var oriImgUrl = imgUrlArr[2];//原图
    webim.Log.info("小图url:" + smallImgUrl);
    webim.Log.info("大图url:" + bigImgUrl);
    webim.Log.info("原图url:" + oriImgUrl);
}


//切换播放audio对象
function onChangePlayAudio(obj) {
    if (curPlayAudio) {//如果正在播放语音
        if (curPlayAudio != obj) {//要播放的语音跟当前播放的语音不一样
            curPlayAudio.currentTime = 0;
            curPlayAudio.pause();
            curPlayAudio = obj;
        }
    } else {
        curPlayAudio = obj;//记录当前播放的语音
    }
}

//单击评论图片
function smsPicClick() {
    if (!loginInfo.identifier) {//未登录
        if (accountMode == 1) {//托管模式
            //调用tls登录服务
            //tlsLogin();
            //tls匿名登录，获取tmpsig
            anoLogin(loginInfo.sdkAppID);
        } else {//独立模式
            alert('请重新登录');
        }
        return;
    } else {
        hideDiscussTool();//隐藏评论工具栏
        showDiscussForm();//显示评论表单
    }
}

//发送消息(普通消息)
function onSendMsg() {

    if (!loginInfo.identifier) {//未登录
        if (accountMode == 1) {//托管模式
            //调用tls登录服务
            //tlsLogin();

            //tls匿名登录，获取tmpsig
            anoLogin(loginInfo.sdkAppID);
        } else {//独立模式
            alert('请重新登录');
        }
        return;
    }

    if (!selToID) {
        alert("您还没有进入房间，暂不能聊天");
        $("#send_msg_text").val('');
        return;
    }
    //获取消息内容
    var msgtosend = $("#send_msg_text").val();
    var msgLen = webim.Tool.getStrBytes(msgtosend);
    if (msgtosend.length < 1) {
        alert("发送的消息不能为空!");
        return;
    }

    var maxLen, errInfo;
    if (selType == webim.SESSION_TYPE.GROUP) {
        maxLen = webim.MSG_MAX_LENGTH.GROUP;
        errInfo = "消息长度超出限制(最多" + Math.round(maxLen / 3) + "汉字)";
    } else {
        maxLen = webim.MSG_MAX_LENGTH.C2C;
        errInfo = "消息长度超出限制(最多" + Math.round(maxLen / 3) + "汉字)";
    }
    if (msgLen > maxLen) {
        alert(errInfo);
        return;
    }

    if (!selSess) {
        selSess = new webim.Session(selType, selToID, selToID, selSessHeadUrl, Math.round(new Date().getTime() / 1000));
    }
    var isSend = true;//是否为自己发送
    var seq = -1;//消息序列，-1表示sdk自动生成，用于去重
    var random = Math.round(Math.random() * 4294967296);//消息随机数，用于去重
    var msgTime = Math.round(new Date().getTime() / 1000);//消息时间戳
    var subType;//消息子类型
    if (selType == webim.SESSION_TYPE.GROUP) {
        //群消息子类型如下：
        //webim.GROUP_MSG_SUB_TYPE.COMMON-普通消息,
        //webim.GROUP_MSG_SUB_TYPE.LOVEMSG-点赞消息，优先级最低
        //webim.GROUP_MSG_SUB_TYPE.TIP-提示消息(不支持发送，用于区分群消息子类型)，
        //webim.GROUP_MSG_SUB_TYPE.REDPACKET-红包消息，优先级最高
        subType = webim.GROUP_MSG_SUB_TYPE.COMMON;

    } else {
        //C2C消息子类型如下：
        //webim.C2C_MSG_SUB_TYPE.COMMON-普通消息,
        subType = webim.C2C_MSG_SUB_TYPE.COMMON;
    }
    var msg = new webim.Msg(selSess, isSend, seq, random, msgTime, loginInfo.identifier, subType, loginInfo.identifierNick);
    //解析文本和表情
    var expr = /\[[^[\]]{1,3}\]/mg;
    var emotions = msgtosend.match(expr);
    var text_obj, face_obj, tmsg, emotionIndex, emotion, restMsgIndex;
    if (!emotions || emotions.length < 1) {
        //发送文本消息改成app自定义的格式
        msgtosend = JSON.stringify({
            'userAction': 1,
            'userId': loginInfo.identifier,
            'nickName': loginInfo.identifierNick,
            'headPic': '',
            'msg': msgtosend
        });
        text_obj = new webim.Msg.Elem.Text(msgtosend);
        msg.addText(text_obj);
    } else {//有表情

        for (var i = 0; i < emotions.length; i++) {
            tmsg = msgtosend.substring(0, msgtosend.indexOf(emotions[i]));
            if (tmsg) {
                text_obj = new webim.Msg.Elem.Text(tmsg);
                msg.addText(text_obj);
            }
            emotionIndex = webim.EmotionDataIndexs[emotions[i]];
            emotion = webim.Emotions[emotionIndex];
            if (emotion) {
                face_obj = new webim.Msg.Elem.Face(emotionIndex, emotions[i]);
                msg.addFace(face_obj);
            } else {
                text_obj = new webim.Msg.Elem.Text(emotions[i]);
                msg.addText(text_obj);
            }
            restMsgIndex = msgtosend.indexOf(emotions[i]) + emotions[i].length;
            msgtosend = msgtosend.substring(restMsgIndex);
        }
        if (msgtosend) {
            text_obj = new webim.Msg.Elem.Text(msgtosend);
            msg.addText(text_obj);
        }
    }

    webim.sendMsg(msg, function (resp) {
        if (selType == webim.SESSION_TYPE.C2C) {//私聊时，在聊天窗口手动添加一条发的消息，群聊时，长轮询接口会返回自己发的消息
            showMsg(msg);
        }
        webim.Log.info("发消息成功");
        $("#send_msg_text").val('');

        hideDiscussForm();//隐藏评论表单
        showDiscussTool();//显示评论工具栏
        hideDiscussEmotion();//隐藏表情
    }, function (err) {
        if(10010 == err.ErrorCode) {
            alert('直播已结束(' + err.ErrorCode + ')');
        }else{
            //webim.Log.error("发消息失败:" + err.ErrorInfo);
            alert("发消息失败:" + err.ErrorInfo);

        }
    });
}

//发送消息(群点赞消息)
function sendGroupLoveMsg() {

    if (!loginInfo.identifier) {//未登录
        if (accountMode == 1) {//托管模式
            //调用tls登录服务
            //tlsLogin();
            //tls匿名登录，获取tmpsig
            anoLogin(loginInfo.sdkAppID);
        } else {//独立模式
            alert('请重新登录');
        }
        return;
    }

    if (!selToID) {
        alert("您还没有进入房间，暂不能点赞");
        return;
    }

    if (!selSess) {
        selSess = new webim.Session(selType, selToID, selToID, selSessHeadUrl, Math.round(new Date().getTime() / 1000));
    }
    var isSend = true;//是否为自己发送
    var seq = -1;//消息序列，-1表示sdk自动生成，用于去重
    var random = Math.round(Math.random() * 4294967296);//消息随机数，用于去重
    var msgTime = Math.round(new Date().getTime() / 1000);//消息时间戳
    //群消息子类型如下：
    //webim.GROUP_MSG_SUB_TYPE.COMMON-普通消息,
    //webim.GROUP_MSG_SUB_TYPE.LOVEMSG-点赞消息，优先级最低
    //webim.GROUP_MSG_SUB_TYPE.TIP-提示消息(不支持发送，用于区分群消息子类型)，
    //webim.GROUP_MSG_SUB_TYPE.REDPACKET-红包消息，优先级最高

    //var subType = webim.GROUP_MSG_SUB_TYPE.LOVEMSG;
    var subType = webim.GROUP_MSG_SUB_TYPE.COMMON;

    var msg = new webim.Msg(selSess, isSend, seq, random, msgTime, loginInfo.identifier, subType, loginInfo.identifierNick);
    var msgtosend = 'love_msg';
    msgtosend = JSON.stringify({
        'userAction': 4,
        'userId': loginInfo.identifier,
        'nickName': loginInfo.identifierNick,
        'headPic': '',
        'msg': ''
    });

    var text_obj = new webim.Msg.Elem.Text(msgtosend);
    msg.addText(text_obj);

    webim.sendMsg(msg, function (resp) {
        if (selType == webim.SESSION_TYPE.C2C) {//私聊时，在聊天窗口手动添加一条发的消息，群聊时，长轮询接口会返回自己发的消息
            showMsg(msg);
        }
        webim.Log.info("点赞成功");

        sendData({
            Action  :'ChangeCount',
            userid  :webim.Tool.getQueryString('userid'),// string  用户id
            type    :1, // 0:修改观看数量 1：修改点赞数量
            optype  :0, // 0:增加 1:减少
            flag    :webim.Tool.getQueryString('type'), // 0:直播 1:点播
            fileid  :webim.Tool.getQueryString('fileid') || '' // 点播的情况下使用，用于区分是哪个视频
        });
    }, function (err) {
        webim.Log.error("发送点赞消息失败:" + err.ErrorInfo);
        alert("发送点赞消息失败:" + err.ErrorInfo);
    });
}
//发送自定义登录消息
function sendCustomLoginMsg(type, callback){
    console.log('sendCustomLoginMsg',type);
    if (!selSess) {
        selSess = new webim.Session(selType, selToID, selToID, selSessHeadUrl, Math.round(new Date().getTime() / 1000));
    }
    var isSend = true;//是否为自己发送
    var seq = -1;//消息序列，-1表示sdk自动生成，用于去重
    var random = Math.round(Math.random() * 4294967296);//消息随机数，用于去重
    var msgTime = Math.round(new Date().getTime() / 1000);//消息时间戳
    //群消息子类型如下：
    //webim.GROUP_MSG_SUB_TYPE.COMMON-普通消息,
    //webim.GROUP_MSG_SUB_TYPE.LOVEMSG-点赞消息，优先级最低
    //webim.GROUP_MSG_SUB_TYPE.TIP-提示消息(不支持发送，用于区分群消息子类型)，
    //webim.GROUP_MSG_SUB_TYPE.REDPACKET-红包消息，优先级最高

    //var subType = webim.GROUP_MSG_SUB_TYPE.LOVEMSG;
    var subType = webim.GROUP_MSG_SUB_TYPE.COMMON;

    var msg = new webim.Msg(selSess, isSend, seq, random, msgTime, loginInfo.identifier, subType, loginInfo.identifierNick);
    var msgtosend = 'login_msg';
    msgtosend = JSON.stringify({
        'userAction': type || 2,
        'userId': loginInfo.identifier,
        'nickName': loginInfo.identifierNick,
        'headPic': '',
        'msg': ''
    });

    var text_obj = new webim.Msg.Elem.Text(msgtosend);
    msg.addText(text_obj);

    webim.sendMsg(msg, function (resp) {
        if (selType == webim.SESSION_TYPE.C2C) {//私聊时，在聊天窗口手动添加一条发的消息，群聊时，长轮询接口会返回自己发的消息
            showMsg(msg);
        }
        console.log('sendCustomLoginMsg success',resp);
        callback && callback.call();
    }, function (err) {
        //alert("发送消息失败:" + err.ErrorInfo);
    });
}
//切换文本框和工具栏
function switchForm(){
    if($("#video-discuss-form").is(':visible')){
        hideDiscussForm();
        showDiscussTool();
    }
}
//显示登出框
function showLogoutForm(){
    $(".j-btn-logout").show();
}
function hideLogoutForm(){
    $(".j-btn-logout").hide();
}
//显示登录框
function showLoginForm(){
    $("#j-pane-login").show();
}
function hideLoginForm(){
    $("#j-pane-login").hide();
}
//隐藏评论文本框
function hideDiscussForm() {
    $("#video-discuss-form").hide();
}
//显示评论文本框
function showDiscussForm() {
    $("#video-discuss-form").show();
}
//隐藏评论工具栏
function hideDiscussTool() {
    $("#video-discuss-tool").hide();
}
//显示评论工具栏
function showDiscussTool() {
    $("#video-discuss-tool").show();
}
//隐藏表情框
function hideDiscussEmotion() {
    $(".video-discuss-emotion").hide();
    //$(".video-discuss-emotion").fadeOut("slow");
}
//显示表情框
function showDiscussEmotion() {
    $(".video-discuss-emotion").show();
    //$(".video-discuss-emotion").fadeIn("slow");

}
//展示点赞动画
function showLoveMsgAnimation() {
    //点赞数加1
    var loveCount = $('#user-icon-like').html();
    $('#user-icon-like').html(parseInt(loveCount) + 1);
    var toolDiv = document.getElementById("video-discuss-tool");
    var loveSpan = document.createElement("span");
    var colorList = ['red', 'green', 'blue'];
    var max = colorList.length - 1;
    var min = 0;
    var index = parseInt(Math.random() * (max - min + 1) + min, max + 1);
    var color = colorList[index];
    loveSpan.setAttribute('class', 'like-icon zoomIn ' + color);
    toolDiv.appendChild(loveSpan);
}

//初始化表情
function initEmotionUL() {
    for (var index in webim.Emotions) {
        var emotions = $('<img>').attr({
            "id": webim.Emotions[index][0],
            "src": webim.Emotions[index][1],
            "style": "cursor:pointer;"
        }).click(function () {
            selectEmotionImg(this);
        });
        $('<li>').append(emotions).appendTo($('#emotionUL'));
    }
}

//打开或显示表情
function showEmotionDialog() {
    if (openEmotionFlag) {//如果已经打开
        openEmotionFlag = false;
        hideDiscussEmotion();//关闭
    } else {//如果未打开
        openEmotionFlag = true;
        showDiscussEmotion();//打开
    }
}
//选中表情
function selectEmotionImg(selImg) {
    $("#send_msg_text").val($("#send_msg_text").val() + selImg.id);
}

//退出大群
function quitBigGroup() {
    var options = {
        'GroupId': avChatRoomId//群id
    };
    webim.quitBigGroup(
            options,
            function (resp) {
                webim.Log.info('退群成功');
                $("#video_sms_list").find("li").remove();
                //webim.Log.error('进入另一个大群:'+avChatRoomId2);
                //applyJoinBigGroup(avChatRoomId2);//加入大群
            },
            function (err) {
                alert(err.ErrorInfo);
            }
    );
}

function sendData(data){
    return $.ajax({
        type: "POST",
        url: SERVER,
        data: JSON.stringify(data),
        crossDomain: true ,
        dataType: 'json'
    }).done(function (data, textStatus, jqXHR) {
        console.log('done', data);
    }).fail(function (jqXHR, textStatus, errorThrown) {

    });
}

function delUrlParam(url,delParam){
    var reg = new RegExp("(^|&)" + delParam + "=([^&]*)(&|$)", "i"); //构造一个含有目标参数的正则表达式对象
    return url.replace(reg,'');
}

//登出
function logout() {
    sendData({
        Action  :'QuitGroup',
        userid  :webim.Tool.getCookie('identifier'),// string   用户id
        liveuserid  :webim.Tool.getQueryString('userid'),// string  主播id
        flag    :webim.Tool.getQueryString('type'), // 0:直播 1:点播
        groupid: webim.Tool.getQueryString('type') == 0 ? avChatRoomId : webim.Tool.getQueryString('fileid') // flag为0情况下填群组id，为1情况下填fileid
    });

    webim.Tool.delCookie('sdkappid');
    webim.Tool.delCookie('userSig');
    webim.Tool.delCookie('identifier');
    webim.Tool.delCookie('accountType');

    //发送退出消息
    sendCustomLoginMsg(3, function(){

        webim.logout(
            function (resp) {
                webim.Log.info('登出成功');
                loginInfo.identifier = null;
                loginInfo.userSig = null;
                $("#video_sms_list").find("li").remove();

                var indexUrl = window.location.search.substr(1);
                indexUrl = delUrlParam(delUrlParam(indexUrl,'tmpsig'),'identifier');
                console.log(indexUrl);

                //关闭当前窗口
                wx.ready(function () {
                    wx.closeWindow();
                });

                //  window.location.search = indexUrl;
            }
        );

    });
}


//监听 申请加群 系统消息
function onApplyJoinGroupRequestNotify(notify) {
    webim.Log.warn("执行 加群申请 回调："+JSON.stringify(notify));
    var timestamp = notify.MsgTime;
    var reportTypeCh = "[申请加群]";
    var content = notify.Operator_Account + "申请加入你的群";
    showGroupSystemMsg(notify.ReportType, reportTypeCh, notify.GroupId, notify.GroupName, content, timestamp);
}

//监听 申请加群被同意 系统消息
function onApplyJoinGroupAcceptNotify(notify) {
    webim.Log.warn("执行 申请加群被同意 回调："+JSON.stringify(notify));
    var reportTypeCh = "[申请加群被同意]";
    var content = notify.Operator_Account + "同意你的加群申请，附言：" + notify.RemarkInfo;
    showGroupSystemMsg(notify.ReportType, reportTypeCh, notify.GroupId, notify.GroupName, content, notify.MsgTime);
}
//监听 申请加群被拒绝 系统消息
function onApplyJoinGroupRefuseNotify(notify) {
    webim.Log.warn("执行 申请加群被拒绝 回调："+JSON.stringify(notify));
    var reportTypeCh = "[申请加群被拒绝]";
    var content = notify.Operator_Account + "拒绝了你的加群申请，附言：" + notify.RemarkInfo;
    showGroupSystemMsg(notify.ReportType, reportTypeCh, notify.GroupId, notify.GroupName, content, notify.MsgTime);
}
//监听 被踢出群 系统消息
function onKickedGroupNotify(notify) {
    webim.Log.warn("执行 被踢出群  回调："+JSON.stringify(notify));
    var reportTypeCh = "[被踢出群]";
    var content = "你被管理员" + notify.Operator_Account + "踢出该群";
    showGroupSystemMsg(notify.ReportType, reportTypeCh, notify.GroupId, notify.GroupName, content, notify.MsgTime);
}
//监听 解散群 系统消息
function onDestoryGroupNotify(notify) {
    webim.Log.warn("执行 解散群 回调："+JSON.stringify(notify));
    var reportTypeCh = "[群被解散]";
    var content = "群主" + notify.Operator_Account + "已解散该群";
    showGroupSystemMsg(notify.ReportType, reportTypeCh, notify.GroupId, notify.GroupName, content, notify.MsgTime);
}
//监听 创建群 系统消息
function onCreateGroupNotify(notify) {
    webim.Log.warn("执行 创建群 回调："+JSON.stringify(notify));
    var reportTypeCh = "[创建群]";
    var content = "你创建了该群";
    showGroupSystemMsg(notify.ReportType, reportTypeCh, notify.GroupId, notify.GroupName, content, notify.MsgTime);
}
//监听 被邀请加群 系统消息
function onInvitedJoinGroupNotify(notify) {
    webim.Log.warn("执行 被邀请加群  回调: "+JSON.stringify(notify));
    var reportTypeCh = "[被邀请加群]";
    var content = "你被管理员" + notify.Operator_Account + "邀请加入该群";
    showGroupSystemMsg(notify.ReportType, reportTypeCh, notify.GroupId, notify.GroupName, content, notify.MsgTime);
}
//监听 主动退群 系统消息
function onQuitGroupNotify(notify) {
    webim.Log.warn("执行 主动退群  回调： "+JSON.stringify(notify));
    var reportTypeCh = "[主动退群]";
    var content = "你退出了该群";
    showGroupSystemMsg(notify.ReportType, reportTypeCh, notify.GroupId, notify.GroupName, content, notify.MsgTime);
}
//监听 被设置为管理员 系统消息
function onSetedGroupAdminNotify(notify) {
    webim.Log.warn("执行 被设置为管理员  回调："+JSON.stringify(notify));
    var reportTypeCh = "[被设置为管理员]";
    var content = "你被群主" + notify.Operator_Account + "设置为管理员";
    showGroupSystemMsg(notify.ReportType, reportTypeCh, notify.GroupId, notify.GroupName, content, notify.MsgTime);
}
//监听 被取消管理员 系统消息
function onCanceledGroupAdminNotify(notify) {
    webim.Log.warn("执行 被取消管理员 回调："+JSON.stringify(notify));
    var reportTypeCh = "[被取消管理员]";
    var content = "你被群主" + notify.Operator_Account + "取消了管理员资格";
    showGroupSystemMsg(notify.ReportType, reportTypeCh, notify.GroupId, notify.GroupName, content, notify.MsgTime);
}
//监听 群被回收 系统消息
function onRevokeGroupNotify(notify) {
    webim.Log.warn("执行 群被回收 回调："+JSON.stringify(notify));
    var reportTypeCh = "[群被回收]";
    var content = "该群已被回收";
    showGroupSystemMsg(notify.ReportType, reportTypeCh, notify.GroupId, notify.GroupName, content, notify.MsgTime);
}
//监听 用户自定义 群系统消息
function onCustomGroupNotify(notify) {
    webim.Log.warn("执行 用户自定义系统消息 回调："+JSON.stringify(notify));
    var reportTypeCh = "[用户自定义系统消息]";
    var content = notify.UserDefinedField;//群自定义消息数据
    showGroupSystemMsg(notify.ReportType, reportTypeCh, notify.GroupId, notify.GroupName, content, notify.MsgTime);
}

//监听 群资料变化 群提示消息
function onGroupInfoChangeNotify(groupInfo) {
    webim.Log.warn("执行 群资料变化 回调： "+JSON.stringify(groupInfo));
    var groupId = groupInfo.GroupId;
    var newFaceUrl = groupInfo.GroupFaceUrl;//新群组图标, 为空，则表示没有变化
    var newName = groupInfo.GroupName;//新群名称, 为空，则表示没有变化
    var newOwner = groupInfo.OwnerAccount;//新的群主id, 为空，则表示没有变化
    var newNotification = groupInfo.GroupNotification;//新的群公告, 为空，则表示没有变化
    var newIntroduction = groupInfo.GroupIntroduction;//新的群简介, 为空，则表示没有变化

    if (newName) {
        //更新群组列表的群名称
        //To do
        webim.Log.warn("群id="+groupId+"的新名称为："+newName);
    }
}

//显示一条群组系统消息
function showGroupSystemMsg(type, typeCh, group_id, group_name, msg_content, msg_time) {

    var sysMsgStr="收到一条群系统消息: type="+type+", typeCh="+typeCh+",群ID="+group_id+", 群名称="+group_name+", 内容="+msg_content+", 时间="+webim.Tool.formatTimeStamp(msg_time);
    webim.Log.warn(sysMsgStr);
    if(5 == type){
        alert('直播已结束');//执行退群操作

        hideDiscussForm();
        hideDiscussTool();
        hideLogoutForm();
        hideLoginForm();
    }
    //alert(sysMsgStr);

}