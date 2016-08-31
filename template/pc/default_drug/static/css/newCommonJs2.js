// var userDomain=window.urlConfig.user;
// var pcDomain=window.urlConfig.pc;
// var cartDomain=window.urlConfig.cart;
//弹出层样式
/***
idname:需要作为弹出层目标元素id
closename:关闭按钮的id
showtype:用于做判断生成最外层遮罩层，默认传1
confirmButtonObject：确认按钮对象(可jquery对象或者dom对象)
confirmCallback:确认按钮回调函数,点击确认按钮需要执行的操作
***/
function popShow(idname, closename, showtype, confirmButtonObject, confirmCallback) {
    var isIE = (document.all) ? true : false;
    var isIE6 = isIE && (navigator.userAgent.indexOf('MSIE 6.0') != -1);
    var newbox = document.getElementById(idname);

    newbox.style.zIndex = "9999";
    newbox.style.display = "block"
    newbox.style.position = !isIE6 ? "fixed" : "absolute";
    newbox.style.top = newbox.style.left = "50%";
    newbox.style.marginTop = -newbox.offsetHeight / 2 + "px";
    newbox.style.marginLeft = -newbox.offsetWidth / 2 + "px";
    if (showtype) {
        if (document.getElementById("layer") == undefined) {
            var layer = document.createElement("div");
            layer.id = "layer";
            layer.style.width = layer.style.height = "100%";
            layer.style.position = !isIE6 ? "fixed" : "absolute";
            layer.style.top = layer.style.left = 0;
            layer.style.backgroundColor = "#000";
            layer.style.zIndex = "9998";
            layer.style.opacity = "0.6";
            document.body.appendChild(layer);
        }
        else { layer = document.getElementById("layer"); layer.style.display = "block"; }
    }

    function layer_iestyle() {
        layer.style.width = Math.max(document.documentElement.scrollWidth, document.documentElement.clientWidth)
+ "px";
        layer.style.height = $(document).height();
    }
    function newbox_iestyle() {
        newbox.style.marginTop = document.documentElement.scrollTop - newbox.offsetHeight / 2 + "px";
        newbox.style.marginLeft = document.documentElement.scrollLeft - newbox.offsetWidth / 2 + "px";
    }
    if (isIE) { layer.style.filter = "alpha(opacity=60)"; }
    if (isIE6) {
        if (showtype) { layer_iestyle(); }
        newbox_iestyle();
        window.attachEvent("onscroll", function () {
            newbox_iestyle();
        })
        window.attachEvent("onresize", layer_iestyle)
    }
    if (closename != 0) {
        var closebtn = document.getElementById(closename);
        closebtn.onclick = function () {
            newbox.style.display = "none"; if (showtype) { layer.style.display = "none" };
        }
    }
    if (confirmButtonObject != null && (typeof confirmButtonObject) === "object") {

        if (confirmCallback != null && (typeof confirmCallback) === "function") {
            confirmButtonObject = $(confirmButtonObject);//如果是dom对象将被封装为jquery对象     
            confirmButtonObject.removeAttr("onclick");//既然是走回调的模式则移除掉页面上写的onclick
            confirmButtonObject.click(function () {
                confirmCallback();
            });
        }
    } 
}
//关闭弹出层样式
function popClose(showname, showtype) {
    document.getElementById(showname).style.display = "none";
    if (showtype) { document.getElementById("layer").style.display = "none"; }
}


//登录
function GetLogin() {

    jQuery.ajax({
        type: "Get",
        url: window.urlConfig.multiDomain.pc() +"/User/GetUserInfo",
        cache: false,
        dataType: "jsonp",
		json: "callback",
        success: function (data) {         
            var userName = data.userName || ""
            var isLogin = data.isLogin || false;
            if (isLogin) {
                GetHNavList();
                GetTopCartList();              
                var YUserName = "<a href=\"" + userDomain + "/user\"  rel=\"nofollow\">" + userName + "</a>";
                var Yout = "<a href=\"" + userDomain + "/User/Logout?ReturnUrl=" + pcDomain + "\" rel=\"nofollow\">退出</a>";                                
                $(".YUserName").html(YUserName)
                $(".Yout").html(Yout)
                $(".YnewYesLogin").show();
                $(".YnewNoLogin").hide();
            }
			else{
				$('.YnewYesLogin').hide();
				$('.YnewNoLogin').show();
			}

        }
    });

}
//判断是否登录
function IsLogin() {   
    var result = false;
    jQuery.ajax({
        type: "Get",
        url:  "/User/GetUserInfo/",    
        cache: false,
		async:false, 
		dataType: "json",
        success: function (msg) {
            result = msg.isLogin || false;         
        }
    });
    return result;
}



//页头交互
// jQuery(document).ready(function () { 
//     GetTopCartList();
//         //顶部下拉菜单
//     $(".hNavList > li").hover(function(){
//         $(this).addClass("Yhover");
//      },function(){
//         $(this).removeClass("Yhover");
//     })
	
// 	GetLogin();
	
// }); 
//顶部获取未付款订单，优惠劵，积分，待确认收货，已完成订单的数值
    function GetHNavList(){
        $.ajax({
            url:window.urlConfig.multiDomain.pc() + "/Order/GetUserCenterLinks",
            type: "Get", cache: true, dataType: "jsonp", jsonp: "callback",
            success: function (data) {
                var YIntegral = "<a href=\"" + userDomain + "/integral\" rel=\"nofollow\">积分<b>" + data.Point + "</b></a>";//积分
                var YCoupon = "<a href=\"" + userDomain + "/coupon\" rel=\"nofollow\">优惠劵<b>" + data.Coupon + "</b></a>";//优惠劵
                var YNopay = "<a href=\"" + userDomain + "//Order/?type=1\" rel=\"nofollow\">未付款订单<b>" + data.OrderTip + "</b></a>";//未付款订单
                var YNosure = "<a href=\"" + userDomain + "/Order/?type=2\" rel=\"nofollow\">待确认收货<b>" + data.OrderNoReply + "</b></a>";//待收货
                var Yyespay = "<a href=\"" + userDomain + "/Order/?type=4\" rel=\"nofollow\">已完成订单<b>" + data.OrderCancel + "</b></a>";//已完成订单
                
                $(".YIntegral").html(YIntegral);
                $(".YCoupon").html(YCoupon);
                $(".YNopay").html(YNopay);
                $(".YNosure").html(YNosure);
                $(".Yyespay").html(Yyespay);
                $(".Ymyorder .Ytopnavdiv").css({"width":"96px"});
            }
        });
    }
//顶部购物车显示400-8811-020购物车件
function GetTopCartList() {
    var numbers = 0;  
    jQuery.ajax({
        url: window.urlConfig.multiDomain.pc() + "/cart/GetCartList2",
        type: "Get", cache: true, dataType: "jsonp", jsonp: "callback",
        success: function (data) {           
 
                var str ="<a href=\""+cartDomain+"/cart/index\">购物车<span name='kadCartNum' style='color:red;padding:0;font-weight:bold;'>"+ (data.TotalItemCount||0) +"</span>件</a>"    
                $(".hNavList .tcart").html(str)
		}
                       
    });
}

//提示框
//type 0：空 1：叹号 2：勾 3：叉
//Opera 0：空 1：确定 2：确定+取消 
//<span class="ico_exc"></span><span class="ico_dui"></span><span class="ico_cuo"></span>
function showPrompt(message,Opera,type){
   
   //判断操作按钮
   if(Opera == 1){
        switch(type){
            case 0:
              $('#showPrompt .go_sure').attr('onclick','diyfunction0()')
                 break;
            case 1:
              $('#showPrompt .go_sure').attr('onclick','diyfunction1()')
                 break;
            case 2:
              $('#showPrompt .go_sure').attr('onclick','diyfunction2()')
                 break;
            case 3:
              $('#showPrompt .go_sure').attr('onclick','diyfunction3()')
                 break;
        }
   }
   else {
       $('#showPrompt .go_Cancel').hide()
   }
             
   //显示界面
   //$('#showPrompt').show();
   
   $('#showPrompt .Bcon').html( message);
   poplayer('showPrompt','',true);
}
//提示框 end

//遮罩层2013-08-23
function poplayer(idname, closename, showtype, zIndex) {
    var zIndex = zIndex || "9998";
    var isIE = (document.all) ? true : false;
    var isIE6 = isIE && (navigator.userAgent.indexOf('MSIE 6.0') != -1);
    var newbox = document.getElementById(idname);
    var closebtn = document.getElementById(closename);
    newbox.style.zIndex = zIndex;
    newbox.style.display = "block"
    newbox.style.position = !isIE6 ? "fixed" : "absolute";
    newbox.style.top = newbox.style.left = "50%";
    newbox.style.marginTop = -newbox.offsetHeight / 2 + "px";
    newbox.style.marginLeft = -newbox.offsetWidth / 2 + "px";
    if (showtype) {
        if (document.getElementById("layer") == undefined) {
            var layer = document.createElement("div");
            layer.id = "layer";
            layer.style.width = layer.style.height = "100%";
            layer.style.position = !isIE6 ? "fixed" : "absolute";
            layer.style.top = layer.style.left = 0;
            layer.style.backgroundColor = "#000";
            layer.style.zIndex = (parseInt(zIndex) - 1).toString();
            layer.style.opacity = "0.4";
            document.body.appendChild(layer);
        }
        else { layer = document.getElementById("layer"); document.getElementById("layer").style.display = "block"; }
    }

    function layer_iestyle() {
        document.getElementById("layer").style.width = Math.max(document.documentElement.scrollWidth, document.documentElement.clientWidth)
+ "px";
        layer.style.height = $(document).height();
    }
    function newbox_iestyle() {
        newbox.style.marginTop = document.documentElement.scrollTop - newbox.offsetHeight / 2 + "px";
        newbox.style.marginLeft = document.documentElement.scrollLeft - newbox.offsetWidth / 2 + "px";
    }
    if (isIE) { document.getElementById("layer").style.filter = "alpha(opacity=60)"; }
    if (isIE6) {
        if (showtype) { layer_iestyle(); }
        newbox_iestyle();
        window.attachEvent("onscroll", function () {
            newbox_iestyle();
        })
        window.attachEvent("onresize", layer_iestyle)
    }
    if(closebtn!=null){
    closebtn.onclick = function () {
        newbox.style.display = "none"; if (showtype) { document.getElementById("layer").style.display = "none" };
    }
	}
}