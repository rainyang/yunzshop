// JavaScript Document
// JavaScript Document 1.焦点图切换  2.实体药店/登录tab切换 3.排行信息放大显示效果  4.友情链接切换jquery tab  5.public.js


/*得到金额（获取两位小数点）*/
function ToMoney(x) {
    var f_x = parseFloat(x);
    if (isNaN(f_x)) {
        //  alert('function:changeTwoDecimal->parameter error');
        return false;
    }
    var f_x = Math.round(x * 100) / 100;
    var s_x = f_x.toString();
    var pos_decimal = s_x.indexOf('.');
    if (pos_decimal < 0) {
        pos_decimal = s_x.length;
        s_x += '.';
    }
    while (s_x.length <= pos_decimal + 2) {
        s_x += '0';
    }
    return s_x;
}

//是否登录
function GetLogin() {
    jQuery.ajax({
        type: "Get",
        url: "/Login/GetUserInfo",
        cache: false,
        dataType: "json",
        success: function (data) {
            if (data["UserName"] != undefined) {
                if (data["QQShowMsg"] != null) {
                    jQuery(".header_caibei").show();
                    jQuery(".header_caibei p").html("<span>" + data["QQShowMsg"] + data["QQHeadShow"] + " </span>");
                }
                var str = "<span class=\"good\"><a href=\"http://user.360kad.com/user\"  rel=\"nofollow\">" + data["UserName"] + "</a>！欢迎来到康爱多网上药店！</span>" +
                    "<a href=\"http://user.360kad.com/User/Logout?ReturnUrl=http://www.360kad.com\" class=\"markerBlue markerBlue01\" rel=\"nofollow\">退出</a> ";
                jQuery("#yes_log,#yes_log1").show();
                jQuery("#yes_log span,#yes_log1 span").html("欢迎您 <samp>" + data["UserName"] + "</samp>");
                jQuery("#no_log,#no_log1").hide();
                jQuery(".loginreg").html(str);
                jQuery("#loginUserId").val(data["UserId"]);
                jQuery("#loginUserName").val(data["UserName"]);
                jQuery("#loginName").html(data["UserName"]);
                jQuery("#txt_mobilPhone").val(data["Mobilephone"]);
                jQuery("#txtUserName").val(data["UserName"]);

            }

        }
    });

}

//是否登录
function IsLogin() {
    var result = false;
    jQuery.ajax({
        type: "Get",
        url: "/remote.aspx",
        data: "Remote=Login/GetUserInfo?",
        cache: false,
        async: false,
        success: function (data) {
            if (data["UserName"] != undefined) {
                result = true;
            }
        }
    });
    return result;
}

function login() {
    var ReturnUrl = window.location.href;
    var url = "http://user.360kad.com/Login?ReturnUrl=" + ReturnUrl;
    window.location.href = url;
    window.event.returnValue = false;
}
function reg() {
    var ReturnUrl = window.location.href;
    var url = "http://user.360kad.com/Register?ReturnUrl=" + ReturnUrl;
    window.location.href = url;
    window.event.returnValue = false;
}

//购物车数量 2013-4-27
function GetCartNumber() {
    jQuery(".cartNum").text(0);
    jQuery.ajax({
        type: "Get",
        url: "/Cart/GetCart",
        cache: false,
        dataType: "JSON",
        success: function (data) {
            jQuery(".cartNum").html(data.cartCount);
        }
    });
}

//添加到购物车type : now(立即购买),rx（处方药）,'' 普通购买
function CreateCart(productId, quantity, type) {

    jQuery.support.cors = true;
    jQuery.ajax({
        type: "Get",
        url: "http://user.360kad.com/Cart/Creat?productId=" + productId + "&quantity=" + quantity,
        cache: false,
        dataType: "JSONP",
        jsonp: "callback",
        success: function (data) {
            if (data == "true") {
                if (type == "now") {
                    urlTo();
                }
                else if (type == "rx") {
                    GetCart();
                    jQuery("#lookshopcart").fadeIn("fast");
                }
                else {
                    GetCart();
                    jQuery("#payshopcart").fadeIn("fast");
                }
            }
            else {
                alert(data);
            }
        }
    });
}

//购物车数量和金额
function GetCart() {
    jQuery.ajax({
        type: "Get",
        url: "/Cart/GetCart",
        cache: false,
        dataType: "JSON",
        success: function (data) {
            jQuery(".cartNum").html(data.cartCount);
            jQuery(".cartCount").html(data.cartCount);
            jQuery(".cartTotal").html(data.cartTotal);
        }
    });
}


//加到购物车
function AddCart(q, type) {
    if (type == "now") {
        CreateCart(jQuery('#h_productId').val(), jQuery('#product_amount').val(), type);
    }
    else {
        jQuery("#h_Quantity").val(q);
        var rc = jQuery("#h_rxotc").val();
        if (rc == "rx") {
            jQuery("#popupcontent").fadeIn("fast");
        }
        else {
            CreateCart(jQuery('#h_productId').val(), q, type);
        }
    }
}

//加到购物车

function AddCart_new(q, type, productid, titleinfo) {
    if (type == "now") {
        CreateCart(productid, jQuery('#product_amount').val(), type);
    }
    else {
        jQuery("#h_Quantity").val(q);
        //  var rc = jQuery("#h_rxotc").val();
        if (type == "rx") {
            jQuery("#popupcontent").fadeIn("fast");
            jQuery("#h_productid").val(productid);
            jQuery("#on_title").html(titleinfo);
        }
        else {
            CreateCart(productid, q, type);
        }
    }
    return false;
}
//处方药点击
function rxClick() {
    jQuery("#popupcontent").fadeOut("fast");
    CreateCart(jQuery("#h_productid").val(), jQuery('#product_amount').val(), "rx");
}

//疗程总额

function TotalCoursePrice() {
    jQuery("#TotalCoursePrice").html(ToMoney(parseFloat(jQuery("#product_CourseQuantity").val()) * parseFloat(jQuery("#CoursePrice").html())));
    jQuery("#TotalCoursePrice2").html(ToMoney(parseFloat(jQuery("#product_CourseQuantity").val()) * parseFloat(jQuery("#CoursePrice").html())));
    jQuery(".ShopcartCourseQuantity").html(jQuery("#product_CourseQuantity").val());
    jQuery("#totalPrice").html(ToMoney(parseFloat(jQuery("#product_CourseQuantity").val()) * parseFloat(jQuery("#h_Price").val())));
    jQuery("#CourseDate").html(parseFloat(jQuery("#product_CourseQuantity").val()) * parseFloat(jQuery("#h_CourseDate").val()));
    jQuery("#TotalCoursePrice3").html(ToMoney(parseFloat(jQuery("#product_CourseQuantity").val()) * parseFloat(jQuery("#h_PriceMarket").val())));

}

//立省
function SavePrice() {
    var p = parseFloat(jQuery("#product_CourseQuantity").val()) * parseFloat(jQuery("#h_Price").val());
    var c = parseFloat(jQuery("#product_CourseQuantity").val()) * parseFloat(jQuery("#CoursePrice").html());
    jQuery("#SavePrice").html(ToMoney(parseFloat(p) - parseFloat(c)));
}



//购买数量（数量加减）
function BuyQuantity(quantity) {

    var q = jQuery("#product_amount").val();
    if (isNaN(q)) {
        jQuery("#product_amount").val(1);
        return false;
    }
    var total = parseInt(q) + parseInt(quantity);
    if (parseInt(total) > 0) {
        jQuery("#product_amount").val(total);
    }
}

//疗程购买数量（数量加减）
function BuyCourseQuantity(quantity) {
    var q = jQuery("#product_CourseQuantity").val();
    var l = jQuery("#h_LowestCourseQuantity").val();
    if (isNaN(q)) {
        jQuery("#product_CourseQuantity").val(l);
        return false;
    }
    if (parseInt(quantity) < 0) {
        if (parseInt(q) <= parseInt(l)) {
            return;
        }
    }

    var total = parseInt(q) + parseInt(quantity);
    if (parseInt(total) > 0) {
        jQuery("#product_CourseQuantity").val(total);

    }
    if (parseInt(q) < parseInt(l)) {
        jQuery("#product_CourseQuantity").val(l);

    }
    TotalCoursePrice();
    SavePrice();
}


jQuery(document).ready(function () {

    //获取登录
    GetLogin();
    //购物车数量
    GetCartNumber();
    //处方药购买
    jQuery("#rx_ok").click(function () {
        jQuery("#popupcontent").fadeOut("fast");
        CreateCart(jQuery('#h_productId').val(), jQuery("#h_Quantity").val(), "rx");
    });


    //关闭弹出处方药对话框
    jQuery("#closethis").click(function () {
        jQuery("#popupcontent").fadeOut("fast");
    });
    //关闭弹出处方药对话框
    jQuery("#closethis2").click(function () {
        jQuery("#lookshopcart").fadeOut("fast");
    });

    //关闭弹出结算购物车对话框
    jQuery("#closethis3").click(function () {
        jQuery("#payshopcart").fadeOut("fast");
    });

    //处方药处理
    var rc = jQuery("#h_rxotc").val();
    if (rc != "rx") {
        jQuery("#btn_BuyImmediate").show();
    }
    //是否疗程购买
    var _lowestCourseQuantity = jQuery("#h_LowestCourseQuantity").val();
    if (parseInt(_lowestCourseQuantity) > 0) {

        jQuery(".shopcart").removeClass("shopcartHidden");
        jQuery(".shopcart").addClass("shopcartShow");
    }
    else {
        jQuery(".shopcart").removeClass("shopcartShow");
        jQuery(".shopcart").addClass("shopcartHidden");
    }

    //立省
    SavePrice();
    //疗程总额
    TotalCoursePrice();

});
//  jQuery(document).ready   END
//专家咨询 已废弃
function CreateConsulting() {
    var Content = $("#txt_Content").val();
    var Phone = $("#txt_mobilPhone").val();
    var ProductId = $("#h_productId").val();
    var ProductTitle = $("#h_ProductTitle").val();

    if (Content.length < 5) {
        alert("请输入咨询内容 !");
        $("#txt_Content").focus();
        return;
    }
    else if (Content.length > 200) {
        alert("您输入咨询内容过长，请把字数限制在200个以内 !");
        $("#txt_Content").focus();
        return;
    }
    jQuery.ajax({
        url: "/Remote.aspx",
        type: "GET",
        data: "Remote=/Consulting/Create?Phone=" + Phone + "&Method=POST&Content=" + Content + "&ProductId=" + ProductId + "&ProductTitle=" + ProductTitle,
        async: false,
        cache: false,
        success: function (data) {
            if (data) {
                $("#txt_Content").val("");
                alert("您的咨询提交成功 !")
            }
            else {
                alert("信息提交失败，请重试 !");
            }
        }
    });

}
//咨询列表  不能改动
function getAjaxPage(page, webUrl, htmlId) {
    jQuery.ajax({
        url: "/Remote.aspx",
        type: "GET",
        data: "Remote=" + webUrl + "&page=" + page,
        cache: false,
        dataType: "text",
        success: function (data) {
            $("#" + htmlId).html(data);
        }
    });

}
//登陆
function UserLogin() {

    var UserEmail = $("#UserEmail").val();
    var UserPassword = $("#UserPassword").val();

    if (UserEmail == "") {
        var txt = "请输入用户帐号/邮箱地址/手机号码 !";
        $("#LoginError").html(txt);
        return;
    }
    if (UserPassword == "") {
        $("#LoginError").html("请输入密码！");
        return;
    }
    jQuery.ajax({
        url: "/Remote.aspx",
        type: "GET",
        data: "Remote=Login/webLogin?UserName=" + UserEmail + "&Method=POST&UserPassword=" + UserPassword,
        async: false,
        cache: false,
        dataType: "text",
        success: function (data) {
            if (data == "True") {
                easyDialog.close();
                if ($("#h_type").val() == "1") {//抽奖登陆
                    easyDialog.open({
                        container: "couponPop"
                    })
                }

                var productid = jQuery("#product_id_str").val();
                if (productid > 0) {
                    AddCart_new(1, '', productid, '');
                }

                GetLogin();
            }
            else {
                $("#LoginError").html(data);
            }
        }
    });

}

//注册(不需要用)
function UserReg() {
    var Email = $("#Email").val();
    var Password = $("#Password").val();
    var ConfirmPassword = $("#ConfirmPassword").val();
    var ValidateCode = $("#ValidateCode").val();

    if (!isEmail(Email)) {
        var txt = "请输入正确的邮箱 !";
        $("#EmailError").html(txt);
        return false;
    }
    else if (Password == "") {
        $("#PasswordError").html("请输入密码！");
        return false;
    }
    else if (Password != ConfirmPassword) {
        $("#ConfirmPasswordError").html("前后密码不一致！");
        return false;

    }
    else if (ValidateCode == "") {
        $("#ValidateCodeError").html("请输入验证码！");
        return false;
    }
    else if (!$("#ChkIsAgreed").attr("checked")) {
        alert("请阅读并同意《康爱多网上药店服务条款》!");
        return false;
    }
    //注册
    jQuery.ajax({
        url: "/Remote.aspx",
        type: "Get",
        data: "Remote=Register/AjaxRegister?UserName=" + Email + "&Password=" + Password + "&ConfirmPassword=" + ConfirmPassword + "&Email=" + Email + "&Method=POST&ValidateCode=" + ValidateCode,
        async: false,
        cache: false,
        dataType: "text",
        success: function (data) {
            if (data == "True") {
                easyDialog.close();
                if ($("#h_type").val() == "1") {//抽奖登陆
                    easyDialog.open({
                        container: "couponPop"
                    })
                }
                GetLogin();
            }
            else {
                $("#EmailError").html(data);
                return false;
            }
        }
    });

}


//登录注册的切换
function showLogin() {

    $(".loginLi a").addClass("hoverNavA");
    $(".regLi a").removeClass("hoverNavA");
    $(".regForm").hide();
    $(".loginForm").show();
}
function showReg() {
    $(".loginLi a").removeClass("hoverNavA");
    $(".regLi a").addClass("hoverNavA");
    $(".regForm").show();
    $(".loginForm").hide();
}

function ToLogin() {
    $("#h_type").val(2);
    easyDialog.open({
        container: 'newLoginForm'
    })
    $(".loginLi a").addClass("hoverNavA");
    $(".regLi a").removeClass("hoverNavA");
    $(".regForm").hide();
    $(".loginForm").show();
}
function ToReg() {
    $("#h_type").val(2);
    easyDialog.open({
        container: 'newLoginForm'
    })
    $(".loginLi a").removeClass("hoverNavA");
    $(".regLi a").addClass("hoverNavA");
    $(".regForm").show();
    $(".loginForm").hide();
}

function showDiv(str) {
    var e, i = 0;
    while (e = document.getElementById(str).getElementsByTagName('DIV')[i++]) {
        if (e.className == 'hit_on' || e.className == 'hit_off') {
            e.onclick = function () {
                var getEls = document.getElementsByTagName('DIV');
                for (var z = 0; z < getEls.length; z++) {
                    getEls[z].className = getEls[z].className.replace('show', 'hide');
                    getEls[z].className = getEls[z].className.replace('hit_on', 'hit_off');
                }
                this.className = 'hit_on';
                var max = this.getAttribute('title');
                document.getElementById(max).className = "show";
            }
        }
    }
}

///是否邮件
function isEmail(strEmail) {
    if (strEmail.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1) {
        return true;
    }
    else {
        return false;
    }
}

//回车
//function isEnter() {
//   if (window.event.keyCode == 13) {        UserLogin();    }
//}
//回车
function isRegEnter() {

    if (window.event.keyCode == 13) {

        UserReg();
    }
}
//抽奖
var CouponObj = new Object();
CouponObj = {
    couponSlideUp: function () {
        var t = setTimeout(function () {
            $(".showCoupon").slideDown(500);
        }, 4000)
    },
    closeSlideDown: function () {
        $("#closeSlideDown").click(function () {
            $(".showCoupon").slideUp(500);
        })
    },
    //点击马上体验
    show1: function () {
        $("#gotoExp").click(function () {
            //用户登录的判断执行

            //对返回数据进行判断
            var d = IsLogin();
            if (d) {
                //已登录弹出优惠劵窗口  
                easyDialog.open({
                    container: "couponPop"
                })
            } else {
                //未登录弹出登录注册框
                $("#h_type").val(1);
                easyDialog.open({
                    container: "newLoginForm"
                })
            }

            //------------


        })
    },
    closePop1: function () {
        $("#closePop1").click(function () {
            easyDialog.close();
        })
    },
    closePop2: function () {
        $(".closePop2").click(function () {
            $(this).parent().parent().hide();
        })
    }
}


//领取优惠劵  已不存在
function GetCoupon(type) {
    jQuery.ajax({
        type: "Get",
        url: "/remote.aspx",
        data: "Remote=/Coupon/GetCoupon?",
        async: false,
        cache: false,
        success: function (data) {
            if (data) {
                if (type == 1) {
                    $(".gotoBuy").hide();
                } else {
                    $(".gotoBuy").show();
                }
                var url = $(this).attr("href");
                $(".couponPop2").show();
            } else {
                alert("您已经领取了优惠劵！");
                if (type == 0) {
                    CreateCart($('#h_Pid').val(), 1, 'now');
                }
            }
        }
    });
}


//路径
function urlTo() {
    location.href = "http://tstuser.360kad.com/cart/index";

}
//加到购物车
function AddCart_new(q, type, productid) {
    if (type == "now") {
        CreateCart(productid, jQuery('#product_amount').val(), type);
    }
    else {
        jQuery("#h_Quantity").val(q);
        var rc = jQuery("#h_rxotc").val();
        if (rc == "rx") {
            jQuery("#popupcontent").fadeIn("fast");
            jQuery("#h_productid").val(productid);
        }
        else {
            CreateCart(productid, q, type);
        }
    }
    return false;
}
//处方药点击
function rxClick() {
    jQuery("#popupcontent").fadeOut("fast");
    CreateCart(jQuery("#h_productid").val(), jQuery('#product_amount').val(), "rx");
}

//活动产品提示
function ActivityProductMessage(productId, price, quantity, htmlId) {
    jQuery.ajax({
        url: "/Remote.aspx",
        type: "GET",
        data: "Remote=/product/GetActivityProduct?productId=" + productId + "&price=" + price + "&quantity=" + quantity,
        cache: false,
        success: function (data) {
            if (data != "") {
                $("#" + htmlId).html(data["Concession"]);
            }
        }
    });
}

//添组合商品到购物车
function AddCombination(ids, rxStr) {
    var array = ids.split(',');
    for (var n = 0; n < array.length; n++) {
        var id = array[n];
        if (id > 0) {
            CreateCart(id, 1, rxStr);
        }
    }
    var type = jQuery("#h_rxotc").val();
    if (type == "rx") {
        GetCart();
        jQuery("#lookshopcart").fadeIn("fast");
    }
    else {
        GetCart();
        jQuery("#payshopcart").fadeIn("fast");
    }
}

//获取html内容
function GetHtml(webUrl, htmlId) {
    jQuery.ajax({
        url: "/Remote.aspx",
        type: "GET",
        data: "Remote=" + webUrl,
        cache: false,
        dataType: "text",
        success: function (data) {
            $("#" + htmlId).html(data);
        }
    });
}

//活动产品提示
function ActivityProduct(productId, price, quantity, htmlId) {
    jQuery.ajax({
        url: "/Remote.aspx",
        type: "GET",
        data: "Remote=/product/GetActivityProduct?productId=" + productId + "&price=" + price + "&quantity=" + quantity,
        cache: false,
        success: function (data) {
            if (data["Concession"] > 0) {
                var t = parseFloat(price) - parseFloat(data["Concession"]);
                var t1 = t.toFixed(2);
                var s = "活动价:<span class='span_price span_price1'>￥" + t1 + "</span>  <label id='l_message'></label>";
                $("#" + htmlId).html(s);
                $("#l_message").html("（" + data["Message"] + "）");
                $("#scroe2").html(t);
            }
        }
    });
}

//专家咨询手机号码合法性
function checkPhone(phone) {
    if (!isMobilephone(phone)) {
        $('#tips').html('手机号码格式不对！');
    }
    else {
        $('#tips').html('手机号码格式正确！');
    }
}

//组合购买
function CreatePackageToCart(packageId) {
    jQuery.ajax({
        type: "GET",
        url: "http://user.360kad.com/Cart/CreatePackageToCart?packageId=" + packageId,
        cache: false,
        dataType: "JSONP",
        jsonp: "callback",
        success: function (data) {
            if (data == "true") {
                GetCart();
                jQuery("#payshopcart").fadeIn("fast");
            }
            else {
                alert(data);
            }
        }
    });
}

//获取活动时间(秒杀) 
function GetActivityTime(id, type) {
    var actTime = "";
    var _urlData = "Remote=/Product/GetPackage?packageId=";    // 活动套餐

    if (type == 1) {
        _urlData = "Remote=/Product/GetActivityByProductId?ProductId="; //活动产品
    }
    jQuery.ajax({
        url: "/Remote.aspx",
        type: "GET",
        data: _urlData + id,
        //   cache: false,
        async: false,
        success: function (data) {
            if (data != null) {
                var etime = data["EndTime"];
                etime = etime.substring(1, etime.length - 1);
                etime = eval(' new ' + etime);
                actTime = etime.getFullYear() + "-" + (parseInt(etime.getMonth()) + 1) + "-" + etime.getDate() + " " +
               etime.getHours() + ":" + etime.getMinutes() + ":" + etime.getSeconds();
            }
            else {
                actTime = "0000-0-00 00:00:00";
            }
        }
    });
    return actTime;
}
//校验是否发过祝福语 暂不支持jsonp
function CheckBlessing() {
    var flag = false;
    jQuery.ajax({
        //url: "/Coupon/GetFreeCoupon",
        //type: "get",
        url: "/Remote.aspx",
        data: "Remote=Blessing/Check?Method=POST",
        cache: false,
        async: false,
        type: "get",
        dataType: "json",
        success: function (data) {
            if (data == "True") {
                flag = true;
            }
        }
    });
    return flag;
}

//发祝福语
function CreateBlessing() {
    if (!IsLogin()) {
        ToLogin();
        $("#getpass_error").attr("style", "visibility:visible");
        $("#content").focus();
    }
    else {
        var content = Trim($("#content").val());
        if (content == "" || content == "新年快乐，心想事成......") {
            alert("祝福语不能为空！");
            $("#content").focus();
            return false;
        }
        if (content.length <= 6) {
            alert("您的祝福太少了，心意还不够哦！");
            return false;
        }
        jQuery.ajax({
            url: "http://user.360kad.com/Blessing/Create?Method=POST&Content=" + escape(content),
            type: "get",
            dataType: "jsonp",
            jsonp: "callback",
            cache: false,
            success: function (data) {
                if (data) {
                    alert(data);
                    $("#content").val("");
                    GetHtml("/Blessing/list?id=1", "demo1");
                }
            }
        });
    }
}

//领取优惠劵 (已没有用了)
function Get35Coupon() {
    if (!IsLogin()) {
        ToLogin();
        $("#getpass_error").attr("style", "visibility:visible");
        $("#content").focus();
    }
    else {
        if (!CheckBlessing()) {
            $("#receive_error").attr("style", "visibility:visible;color:red");
            $("#content").focus();

        }
        else {
            jQuery.ajax({
                //url: "/Coupon/GetFreeCoupon",
                //type: "get",
                url: "/Remote.aspx",
                data: "Remote=Coupon/GetFreeCoupon?Method=POST",
                cache: false,
                success: function (data) {
                    alert(data);
                }
            });
        }

    }
}

// 去左右空格
function Trim(str) {
    return str.replace(/\s+$|^\s+/g, "");
}
//----------------------------------------------------------------end 5.public.js
//购物车结算20120525

//获得购物车药品信息 2013-4-27
function GetCartList() {
    var numbers = 0;
    jQuery.ajax({
        url: "http://user.360kad.com/cart/list",
        type: "Get", cache: true, dataType: "jsonp", jsonp: "callback",
        success: function (data) {
            if (data != null && data.length >= 1) {
                numbers = data.length;
                AppendToDiv(data);
            } else {
                $(".cartNum").text(0);
            }

        }
    });

}

//2013-4-27
function AppendToDiv(data) {
    var obj = $(".goCartbox");
    $(obj).text("");
    //$(obj).show();
    var str = "";
    var sum = 0.00;
    var num_sum = 0;
    if (data.length > 6) { str = "<ul style='overflow-y:scroll;height:295px;padding-right:0px;clear:both;'>"; }
    else { str = "<ul>"; }
    for (var i = 0; i < data.length; i += 1) {
        str += "<li>";
        if (data[i].ProductType == 2) {
            str += "<p class=\"goCartbox_l\"><img src=" + data[i].productPic + " alt=" + data[i].ProductName + " /></p>";
            str += "<p class=\"goCartbox_m\">" + data[i].ProductName + "</p>";
        }
        else {
            str += "<p class=\"goCartbox_l\"><a href=" + data[i].ProductUrl + " title=\"\" target=\"_blank\"><img src=" + data[i].productPic + " alt=" + data[i].ProductName + " /></a></p>";
            str += "<p class=\"goCartbox_m\"><a href=" + data[i].ProductUrl + " title=\"\" target=\"_blank\">" + data[i].ProductName + "</a></p>";
        }

        str += "<p class=\"goCartbox_r\">";
        str += "<span class=\"num_p\"><label class=\"price\">￥" + Math.round((parseFloat(data[i].Price) - parseFloat(data[i].Concession)) * 100) / 100 + "</label> X " + data[i].Quantity + "</span>";
        str += "<span class=\"btn_del\"><a onclick=\"DeleteCart(" + data[i].CartId + ")\" title=\"删除\" style=\"cursor:pointer;\">删除</a></span></p>";
        str += "</li>";
        sum += TotalMoney(data[i].Price, data[i].Concession, data[i].Quantity);
        num_sum += data[i].Quantity;
    }
    var strstart = "<p class=\"title\"><span>您的购物车有 <label>" + num_sum + "</label> 件商品</span></p>";
    str = strstart + str + "</ul>";
    $(".cartNum").text(num_sum);
    var sumStr = ChangeTODecimal(Math.round((parseFloat(sum)) * 100) / 100);
    str += "<hr style=\"border:1px solid #ffe788\"/>";
    str += "<p class=\"btn_goCart\">共计￥" + sumStr + " <a href=\"http://user.360kad.com/cart/index\" title=\"去购物车结算\" style=\"cursor:pointer;\" ><img src=\"http://skin.360kad.com/skin/img/new_blank.gif\" alt=\"\"/></a></p>";
    $(obj).append(str);
}
function TotalMoney(price, concession, quantity) {
    return Math.round(((parseFloat(price) - parseFloat(concession)) * parseInt(quantity)) * 100) / 100;
}
function ChangeTODecimal(sum) {
    var newsum = parseFloat(sum.toString());
    var newStr = newsum.toString();
    var index = newStr.indexOf('.');
    if (index <= 0) {
        newStr += ".00";
    } else {
        var subStr = newStr.substring((index + 1), newStr.length);
        if (subStr.length == 1) {
            newStr += "0";
        }
    }
    return newStr;
}

//删除购物车药品信息2013-4-27
function DeleteCart(carId) {
    if (confirm("确定不购买该商品吗?")) {
        jQuery.ajax({
            url: "http://user.360kad.com/Cart/Delete?CartId=" + carId,
            type: "Get", cache: true, dataType: "jsonp", jsonp: "callback",
            success: function (data) {
                if (data == true) {
                    GetCartList();
                } else {
                    alert("删除商品失败");
                }
            }
        });
    }
}

/* 购物车结算弹窗 */
$(document).ready(function () {
    $(".header_r_btn_2").hover(function () {
        var numbers = $(".cartNum").text();
        $(this).attr("class", "header_r_btn_2 btn_show1");
        if (parseInt(numbers) == 0) {
            $(".no_goCartbox").show(); $(".goCartbox").hide();
        }
        else { GetCartList(); $(".goCartbox").show(); }
    }, function () {
        $(this).attr("class", "header_r_btn_2"); $(".no_goCartbox").hide(); $(".goCartbox").hide();
    });
});

$(document).ready(function () {

    /* 我的康爱多弹窗 */
    $(".header_r_btn_1").hover(function () {
        $(this).attr("class", "header_r_btn_1 btn_show");
    }, function () { $(this).attr("class", "header_r_btn_1"); }
    );
    /* 全部商品分类弹窗 */
    // $(".categorys").hover(function () { $(this).attr("class", "categorys catehover"); }, function () { $(this).attr("class", "categorys"); });
    $(".item_m").hover(function () {
        $(this).attr("class", "item_m hover"); var numid = this.id.substr(1); var numid = parseInt(numid);
        $("#f" + (numid + 1)).css("margin-top", "49px"); $(this).children(".item_m_c").show(); $(this).children("h3").css("top", (numid - 1) * 49 + "px");
    }, function () { var numid = this.id.substr(1); var numid = parseInt(numid); $(this).attr("class", "item_m"); $("#f" + (numid + 1)).css("margin-top", "0px"); $(this).children(".item_m_c").hide(); });

    $(".f1item_m_c .item_m_c_l dl").hover(function () {
        $(this).css("background", "#feedc1");
    }, function () { $(this).css("background", "#FEFEF2"); });

    /* 友情链接全部展示 */
    $(".flinkcon").hover(function () { $(this).attr("class", "flinkcon hover"); }, function () { $(this).attr("class", "flinkcon"); });
});
//手机订阅
///是否手机
function isMobilephone(strMobilephone) {
    if ((strMobilephone.search(/^0?(13[0-9]|15[012356789]|18[0236789]|14[57])[0-9]{8}$/) != -1)) { return true; }
    else { return false; }
}


///是否邮件
function isEmail(strEmail) {
    if (strEmail.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1) { return true; }
    else { return false; }
}

// 去左右空格
function Trim(str) { return str.replace(/\s+$|^\s+/g, ""); }

function AddNotice(id, content, type) {
    var flag = 0;
    if (type == 0) {
        if (!isEmail(Trim(content))) {
            alert("邮件格式不合法!")
            return false;
        }
    }
    if (type == 1) {
        if (!isMobilephone(Trim(content))) {
            alert("手机格式不合法!")
            return false;
        }
    }
    var url = "/Remote.aspx?Remote=Notice/CreateNotice&ProductId=" + id + "&Email=" + content + "&Type=" + type;
    jQuery.ajax({
        url: url,
        type: "GET",
        cache: false,
        async: false,
        success: function (data) {
            if (data == "2") {
                alert("订阅成功"); $(".special_n_show").hide(); $("#txtEmail").val("");
            }
            else if (data == "0") {
                alert("抱歉，您已经订阅了！"); $(".special_n_show").hide(); $("#txtEmail").val("");
            }
        }
    });

}



$(function () {
    var imgField = $('#J_imgList');
    var imgList = $('#J_imgList>li');
    var navField = $('#J_navList');
    var navList = $('#J_navList>li');
    var turnPage = navList.length; //每屏显示数
    var T = 5000; //切换间隔时间
    var turnT = 300; //animate时间
    var N = 0; //图片初始索引
    var P = 1; //屏初始索引
    var goFun = null;
    var hoverFun = null;
    var triggerFun = null;
    var delayFun = null
    var navListW = navList.outerWidth(true);
    var turnPages = Math.ceil(navList.size() / turnPage);
    //初始图片区域高度与标题区域宽度
    imgField.height(imgList.size() * imgList.height());

    //初始自动切换
    GO();
    //自动切换
    function GO() {
        if (N == turnPage) { N = 0; }
        imgField.stop().animate({
            marginTop: -N * (imgList.height())
        }, turnT);
        navList.eq(N).addClass('numcur').siblings().removeClass('numcur');

        N++;
        //console.log(N)
        N = N >= imgList.size() ? 0 : N;
        P = Math.ceil(N / turnPage);
        goFun = setTimeout(GO, T);
    }

    //停止切换
    function STOP() { clearTimeout(goFun); }

    //标题划过移出
    navList.hover(function () {
        clearTimeout(delayFun); STOP();
        N = navList.index(this);
        imgField.stop().animate({
            marginTop: -N * (imgList.height())
        }, turnT);
        $(this).addClass('numcur').siblings().removeClass('numcur');
    }, function () {
        N++;
        delayFun = setTimeout(GO, T)
    });
    //图片划过移出
    imgList.hover(function () {
        N = imgList.index(this);
        navList.eq(N).trigger('mouseover');
    }, function () {
        navList.eq(N).trigger('mouseleave');
    });

});

$(document).ready(function () {
    $(".sidebar_right .prolist .pic").hover(function () {
        $(this).children(".yin_ico").show();
    }, function () { $(this).children(".yin_ico").hide(); });
});


$(document).ready(function () {
    /* 付款方式mousemove */
    $(".payment_c dl").hover(function () { $(this).addClass("payhover"); }, function () { $(this).removeClass("payhover"); });

});
/* 特价/热卖/热评商品之选项卡区域图片延迟加载 */
$(function () { $("img[original]").lazyload({ placeholder: "http://skin.360kad.com/skin/kadad/index/blank.gif" }); });
function lazyloadTab(container) {
    container.find('img').each(function () {
        var original = $(this).attr("data-original"); if (original) { $(this).attr('src', original).removeAttr('data-original'); }
    });
}
function ConTab(name, curr, n) {
    for (i = 1; i <= n; i++) {
        var menu = document.getElementById(name + i), cont = document.getElementById("con" + name + i); menu.className = i == curr ? "selH3" : "";
        if (i == curr) {
            cont.style.display = "block"; lazyloadTab($(cont));  //回调
        } else { cont.style.display = "none"; }
    }
}

/*end 特价/热卖/热评商品之选项卡区域图片延迟加载 */


$(document).ready(function () {
    tabList("newsall", "infoContent"); tabList("proall", "procontent");
    CarouselIndex("wrap_theme");
});
//标签切换JS
function indexTab_li(m, n) {
    var tli = document.getElementById("menu" + m).getElementsByTagName("li");
    var mli = document.getElementById("main" + m).getElementsByTagName("ol");
    for (i = 0; i < tli.length; i++) {
        tli[i].className = i == n ? "tab_on" : ""; mli[i].style.display = i == n ? "block" : "none";
    }
}
//切换
function tabList(obj, showObj) {
    $("#" + obj + " ul.tabnav li").each(function (index) {
        var liNone = $(this);
        $(this).hover(function () {
            $("." + showObj + " ul").removeClass("show");
            $("#" + obj + " ul.tabnav li.tabnavlist").removeClass("tabnavlist");
            $("." + showObj + " .news" + (index + 1)).addClass("show");
            liNone.addClass("tabnavlist");
        });
    });
}

//案例轮播
function CarouselIndex(obj) {
    //***变量
    var $cur = 1; //初始化显示的版面
    var $i = 1; //每版显示数
    var $len = $("#" + obj + " .showbox>ul>li").length; //计算列表总长度(个数)
    var $pages = Math.ceil($len / $i); //计算展示版面数量
    var $w = $("#" + obj + " .showbox").width(); //取得展示区外围宽度
    if (screen.width > 1200) {
        if (obj == "wrap_t") { $w = 668; } else { $w = 1100; }
    }
    var $showbox = $("#" + obj + " .showbox1");
    var $pre = $("#" + obj + " span.left");
    var $next = $("#" + obj + " span.right");
    var $autoFun;
    //***调用自动滚动
    autoSlide();
    //***向前滚动
    $pre.click(function () {
        if (!$showbox.is(':animated')) {  //判断展示区是否动画
            if ($cur == 1) {   //在第一个版面时,再向前滚动到最后一个版面
                $showbox.animate({
                    left: '-=' + $w * ($pages - 1)
                }, 500); //改变left值,切换显示版面,500(ms)为滚动时间,下同
                $cur = $pages; //初始化版面为最后一个版面
            }
            else {
                $showbox.animate({
                    left: '+=' + $w
                }, 500); //改变left值,切换显示版面
                $cur--; //版面累减
            }
        }
    });
    //***向后滚动
    $next.click(function () {
        if (!$showbox.is(':animated')) { //判断展示区是否动画
            if ($cur == $pages) {  //在最后一个版面时,再向后滚动到第一个版面
                $showbox.animate({
                    left: 0
                }, 500); //改变left值,切换显示版面,500(ms)为滚动时间,下同
                $cur = 1; //初始化版面为第一个版面
            }
            else {
                $showbox.animate({
                    left: '-=' + $w
                }, 500); //改变left值,切换显示版面
                $cur++; //版面数累加
            }
        }
    });

    clearFun($showbox);
    clearFun($pre);
    clearFun($next);
    //***事件划入时停止自动滚动
    function clearFun(elem) {
        elem.hover(function () {
            clearAuto();
        }, function () {
            autoSlide();
        });
    }
    //***自动滚动
    function autoSlide() {
        $next.trigger('click');
        $autoFun = setTimeout(autoSlide, 6000); //此处不可使用setInterval,setInterval是重复执行传入函数,这会引起第二次划入时停止失效
    }
    //***清除自动滚动
    function clearAuto() {
        clearTimeout($autoFun);
    }
}

//更多频道
$(function () {
    $('.more_chan').hover(function () {
        $('.more_chan_sub').show();
        $('.more_chan_hd').addClass('more_chan_hd_1');
    },
function () {
    $('.more_chan_sub').hide();
    $('.more_chan_hd').removeClass('more_chan_hd_1');
});

    var url = window.location.href;
    switch (url) {
        case url = "http://www.360kad.com/dymhh/men.shtml":
            $('#nav1').css('background', '#a9005f');
            break;
        case url = "http://www.360kad.com/Drugs/List_759.shtml":
            $('#nav2').css('background', '#a9005f');
            break;
        case url = "http://www.360kad.com/Drugs/List_1133.shtml":
            $('#nav3').css('background', '#a9005f');
            break;
        case url = "http://www.360kad.com/Drugs/List_1131.shtml":
            $('#nav4').css('background', '#a9005f');
            break;
        case url = "http://www.360kad.com/Drugs/List_795.shtml":
            $('#nav5').css('background', '#a9005f');
            break;
        case url = "http://www.360kad.com/Adult/nyqj/":
            $('#nav6').css('background', '#a9005f');
            break;
        default:
            $('#nav7').css('background', '#a9005f');
            break;

    }

})

//2013-7-15-新登录
$(function () {
    var getInCenterBtn = $('.getInCenterBtn');
    var myKadL = $('.myKadL');
    var myKadList = $('.myKadList');
    var vipShare = $('.vipShare');
    var vipShareList = $('.vipShareList');
    var newYesLogin = $('.newYesLogin');
    var activeLia = $('.myKadList li.activeLi');
    var activeLib = $('.vipShareList li');

    getInCenterBtn.hover(function () {
        $(this).addClass('getInCenterBtn_hover');
    }, function () {
        $(this).removeClass('getInCenterBtn_hover');
    });
    //我的康爱多列表展示
    myKadL.mouseleave(function () {
        myKadList.hide();
    });
    $('.myKadList li.activeLi,.vipShareList li').mouseenter(function () {
        $(this).addClass('li_bgclor');
    });
    $('.myKadList li.activeLi,.vipShareList li').mouseleave(function () {
        $(this).removeClass('li_bgclor');
    });
    //会员专享展示
    vipShare.mouseenter(function () {
        vipShareList.show();
        newYesLogin.addClass('newYesLogin_hover');

    });
    vipShare.mouseleave(function () {
        vipShareList.hide();
        newYesLogin.removeClass('newYesLogin_hover');
    });
    //登录与未登录状态切换
    if (IsLogin()) {
        $('.newYesLogin').show();
        $('.newNoLogin').hide();
    } else {
        $('.newYesLogin').hide();
        $('.newNoLogin').show();
    }
});
//获取未付款的数值
function GetWithoutPay() {
    $.ajax({
        url: "/Order/GetUserCenterLinks",
        type: "GET",
        cache: false,
        dataType: "json",
        success: function (data) {
            var my_orderPay1 = $('.my_orderPay1');
            var my_orderPay2 = $('.my_orderPay2');
            if (data == '0') {

                //没有登录 或者数据为空！
            }
            else {
                var alertDiv = $('.alertDiv');
                my_orderPay1.hide();
                my_orderPay2.show().attr('href', 'http://user.360kad.com' + ((data[0] != null && data[0].link != null) ? data[0].link : ""));
                //$('.wZf_num').html(data[0].Num);

                if ($('.wZf_num').text() == 0) {
                    alertDiv.hide();
                } else {
                    if (jQuery.cookie('theCookie') == 'userName') {
                        alertDiv.hide();
                    } else {
                        alertDiv.show().stop().animate({
                            width: 242 + 'px',
                            height: 167 + 'px'
                        }, 600);
                    }
                }
                //如果我的康爱多数值为0状态的切换
                var myKadL = $('.myKadL');
                var myKadList = $('.myKadList');
                var oSpanNum = $('.myKadList li span');
                myKadL.live('mouseenter', function () {
                    myKadList.show();
                    oSpanNum.each(function (i) {
                        if (oSpanNum.eq(i).text() == '0') {
                            oSpanNum.eq(i).css('color', '#333').parent('.liA1,.liA2,.liA3,.liA4,.liA5,.liA6').removeAttr('href').removeAttr('target');
                            oSpanNum.eq(i).parent().parent().addClass('myKadList_hover');
                        }
                    })
                });
                $('.targetLink>a,.leftLink').attr('href', 'http://user.360kad.com' + data[0].link);
                $('.myKadList .liA1').attr('href', 'http://user.360kad.com' + data[0].link);
                $('.myKadList .liNum1 ').html(data[0].Num);
                $('.myKadList .liA2').attr('href', 'http://user.360kad.com' + data[1].link);
                $('.myKadList .liNum2 ').html(data[1].Num);
                $('.myKadList .liA3').attr('href', 'http://user.360kad.com' + data[2].link);
                $('.myKadList .liNum3 ').html(data[2].Num);
                $('.myKadList .liA4').attr('href', 'http://user.360kad.com' + data[3].link);
                $('.myKadList .liNum4 ').html(data[3].Num);
                $('.myKadList .liA5').attr('href', 'http://user.360kad.com' + data[4].link);
                $('.myKadList .liNum5 ').html(data[4].Num);
                $('.myKadList .liA6').attr('href', 'http://user.360kad.com' + data[5].link);
                $('.myKadList .liNum6 ').html(data[5].Num);


            }
        }
    });
}
//cookie操作 2012-8-14
$(function () {
    GetWithoutPay();
    var alertDiv = $('.alertDiv');
    var targetLinka = $('.targetLink a');

    //快速支付切换
    targetLinka.hover(function () {
        $(this).addClass('changeA');
    }, function () {
        $(this).removeClass('changeA');
    });

    //自动关闭
    function AutoHide() {
        alertDiv.hide();
    }
    setTimeout(AutoHide, 60000);
    //END自动关闭

    //不再提示
    $('.rightLink ').click(function () {
        alertDiv.hide();
        //新建个cookie值
        jQuery.cookie('theCookie', 'userName', { expires: 1, path: '/', domain: '360kad.com' });
    });
    //关闭
    $('.wClose,.targetLink,.leftLink').click(function () {
        alertDiv.hide();
    });
});
//我的订单
function adobe_wddd() {
    var s = s_gi(s_account);         //var tid=regid;s.linkTrackVars='events';s.linkTrackEvents='event80'; //s.products=tid;
    s.events = 'event80'; s.tl(this, 'o', '我的订单');
}
//收藏康爱多
function adobe_sckad() {
    var s = s_gi(s_account);         //var tid=regid;s.linkTrackVars='events';s.linkTrackEvents='event81'; //s.products=tid;
    s.events = 'event81'; s.tl(this, 'o', '收藏康爱多');
}
//点击搜药
function adobe_djsy() {
    var s = s_gi(s_account);         //var tid=regid;s.linkTrackVars='events';s.linkTrackEvents='event82'; //s.products=tid;
    s.events = 'event82'; s.tl(this, 'o', '点击搜药');
}
//点击免费注册
function adobe_djmfzc() {
    var s = s_gi(s_account);         //var tid=regid;s.linkTrackVars='events';s.linkTrackEvents='event83'; //s.products=tid;
    s.events = 'event83'; s.tl(this, 'o', '点击免费注册');
}
//点击登陆我的康爱多
function adobe_djwdkad() {
    var s = s_gi(s_account);         //var tid=regid;s.linkTrackVars='events';s.linkTrackEvents='event84'; //s.products=tid;
    s.events = 'event84'; s.tl(this, 'o', '点击登陆我的康爱多');
}
//点击网站logo
function adobe_djlogo() {
    var s = s_gi(s_account);         //var tid=regid;s.linkTrackVars='events';s.linkTrackEvents='event85'; //s.products=tid;
    s.events = 'event85'; s.tl(this, 'o', '点击网站logo');
}

//首页右下角购物车
function GetCartList2() {
    var numbers = 0;
    jQuery.ajax({
        url: "http://user.360kad.com/cart/list",
        type: "Get", cache: true, dataType: "jsonp", jsonp: "callback",
        success: function (data) {
            if (data != null && data.length >= 1) {
                numbers = data.length;
                AppendToDiv2(data);
            } else {
                $(".cartNum").text(0);
                $("#widthoutPr").show();
                $("#inProducts").hide();
            }

        }
    });
}

function AppendToDiv2(data) {
    var obj = $("#inProducts");
    $(obj).text("");

    var str = "";
    var sum = 0.00;
    var num_sum = 0;

    str = "<div class=\"newList_BoxT\"><ul class='proListShow'>";
    for (var i = 0; i < data.length; i += 1) {
        str += "<li>";
        if (data[i].ProductType == 2) {

            str += "<div class=\"leftDiv\"><img src=" + data[i].productPic + " alt=" + data[i].ProductName + " /></div>";
            str += "<div class=\"centerDiv\">";
            str += "<p>" + data[i].ProductName + "</p>";
            for (var j = 0; j < data[i].CartViewChildList.length; j++) {
                num_sum += data[i].CartViewChildList[j].Quantity * data[i].Quantity;
            }
            str += "<p class=\"num_p\"><span class=\"single_prices\">￥" + Math.round((parseFloat(data[i].Price) - parseFloat(data[i].Concession)) * 100) / 100 + "</span> X " + data[i].Quantity + "</p>";
            str += "</div>";
        }
        else {
            str += "<div class=\"leftDiv\"><a href='" + data[i].ProductUrl + "' title=\"\" target=\"_blank\"><img src=" + data[i].productPic + " alt=" + data[i].ProductName + " /></a></div>";
            str += "<div class=\"centerDiv\">";
            str += "<p><a href='" + data[i].ProductUrl + "' title=\"\" target=\"_blank\">" + data[i].ProductName + "</a></p>";
            for (var j = 0; j < data[i].CartViewChildList.length; j++) {
                str += "<p class=\"zengPin\"><a href='" + data[i].CartViewChildList[j].ProductUrl + "'>" + data[i].CartViewChildList[j].ProductName + "</a></p>";

            }
            str += "<p class=\"num_p\"><span class=\"single_prices\">￥" + Math.round((parseFloat(data[i].Price) - parseFloat(data[i].Concession)) * 100) / 100 + "</span> X " + data[i].Quantity + "</p>";
            str += "</div>";
            num_sum += data[i].Quantity;
        }
        str += "<div class=\"rightDiv\">";
        str += "<span class=\"btn_del\"><a class=\"deleteTxt\" onclick=\"DeleteCart(" + data[i].CartId + ")\" title=\"删除\" style=\"cursor:pointer;\">删除</a></span></div>";
        str += "</li>";
        sum += TotalMoney(data[i].Price, data[i].Concession, data[i].Quantity);
    }
    str = str + "</ul></div>";
    $(".cartNum").text(num_sum);
    var sumStr = ChangeTODecimal(Math.round((parseFloat(sum)) * 100) / 100);
    str += "<div class=\"newList_BoxB\"><div class=\"priceMain\"><p class=\"leftSide\">共<span class=\"newCounts\">" + num_sum + "</span>件商品</p><p class=\"rightSide\">共计<span class=\"totalPrices\">" + "￥" + sumStr + "</span></p></div><p class=\"fl_pl\"><a class=\"newGoBuy_Btn\" href=\"http://user.360kad.com/cart/index\"></a></p></div>";
    $(obj).append(str);
    //高度超出300px显示滚动条
    if ($(".newList_BoxT").height() > 230) {
        $(".newList_BoxT").css({
            height: 230,
            overflowX: "hidden",
            overflowY: "scroll"
        });
    }
}
//删除购物车药品信息2013-4-27
function DeleteCart(carId) {
    if (confirm("确定不购买该商品吗?")) {
        jQuery.ajax({
            url: "http://user.360kad.com/Cart/Delete?CartId=" + carId,
            type: "Get", cache: true, dataType: "jsonp", jsonp: "callback",
            success: function (data) {
                if (data == true) {
                    GetCartList2();
                } else {
                    alert("删除商品失败");
                }
            }
        });
    }
}

//右下角购物车商品展示20130829
$(function () {
    $("#Rp_zoe130726").parent("div").css("zIndex", "998");
    var cartBody = $(".cartBody");
    var newList_Wrap = $("#newList_Wrap");
    var objTimer = null;
    //修改z-index
    $("#tq_float_container").css("zIndex", "998");
    cartBody.mouseenter(function () {
        clearTimeout(objTimer);
        newList_Wrap.show();
        if (parseInt($("#p_num").html()) == 0) {
            $("#widthoutPr").show();
            $("#inProducts").hide();
        } else {
            GetCartList2();//获取商品信息 
            $("#widthoutPr").hide();
            $("#inProducts").show();
        }
    });
    cartBody.mouseleave(function () {
        objTimer = setTimeout(function () { newList_Wrap.hide(); }, 1000);
    });
    newList_Wrap.mouseenter(function (event) {
        clearTimeout(objTimer);
        event.stopPropagation();//取消事件冒泡
    });
})
//END右下角购物车商品展示20130829