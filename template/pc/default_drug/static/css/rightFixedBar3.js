//var sign_login = 0;
//var json = eval("(" + $.cookie("KadProductHistory") + ")");
// var kad_user_url = urlConfig.user;
// var kad_cart_url = urlConfig.cart;
// var kad_chat_url = urlConfig.chat;
// var kadSellerCode = 'f0445f5d-a570-4407-827d-f40dc4a0603e';
//删除对应的浏览记录
function deleteView(wareSkuCode) {
    json = eval("(" + $.cookie("KadProductHistory") + ")");
    for (var i = 0; i < json.length; i++) {
        if (json[i].WareSkuCode == wareSkuCode) {
            json.splice(i, 1);
        }
    }
    json.length <= 0 ? $("#view_soon_lists_show .cart_top").hide() : $("#view_soon_lists_show .cart_top").show();
    $("#view_num").text(json.length + '件商品');
    var b = JSON.stringify(json);
    $.cookie("KadProductHistory", b, { expires: 1, domain: '360kad.com', path: '/' });
    json = eval("(" + $.cookie("KadProductHistory") + ")");
    curViewHestory();
}

//商品最近浏览历史
function curViewHestory() {
    $.ajax({
        url: urlConfig.pc + "/Product/GetProductHistoryInfo",
        type: "Get",
        dataType: "jsonp",
        jsonp: "callback",
        success: function (data) {
            var $windowH = $(window).height();
            var arr01 = [];
            var list = "";
            var ids = "";
            if (data != "" && data != null) {
                //json = data;
                $("#view_soon_lists_show .cart_top").show();
                $("#view_soon_lists_show .without_favor").hide();
                $("#scrollBar3").show();
                $("#view_num").text(data.length + '件商品');
                for (var i = 0; i < data.length; i++) {
                    list += '<li><p class="pro_t clearfix"><a target="_blank" href="/product/' + data[i].WareSkuCode + '.shtml">' + data[i].WareName + '</a><span class="pro_price"><i class="favor_icon" id="f_' + data[i].WareSkuCode + '" onclick="rCreateFavorite(\'' + data[i].WareSkuCode + '\')"></i><i class="favor_delet_btns" onclick="deleteView(\'' + data[i].WareSkuCode + '\')"></i></span></p><div class="pro_box clearfix"><div class="pic_show"><a target="_blank" href="/product/' + data[i].WareSkuCode + '.shtml"><img src="' + data[i].Pic + '"></a></div><div class="changeNum"><p class="price1">¥ ' + data[i].Price + '</p><p class="put_inCart">';
                    if (data[i].IsRx != "undefined" && data[i].IsRx) {
                        list += "<a href=\"/product/" + data[i].WareSkuCode + ".shtml\"  target=\"_blank\">查看详情</a></p></div></div></li>";
                    } else {
                        list += '<a href="javascript:;" onclick="rightAddCart_new(1,' + data[i].WareSkuCode + ',0,\'' + data[i].SellerCode + '\');">加入购物车</a></p></div></div></li>';
                    }
                    ids += data[i].WareSkuCode + ',';

                }
                isFavored(ids);

                //判断小屏幕出现滚动条
                if (data.length > 4 && $windowH < 700) {
                    $("#bar_box3").show();
                    scrollPlun(getId("bar3"), getId("scrollBar3"), getId("bar_box3"), getId("pc_proLists3"));
                } else if (data.length > 5) {
                    $("#bar_box3").show();
                    scrollPlun(getId("bar3"), getId("scrollBar3"), getId("bar_box3"), getId("pc_proLists3"));
                } else {
                    $("#pc_proLists3").css('top', 0);
                    $("#bar_box3").hide();
                }
            } else {
                $("#view_soon_lists_show .cart_top").hide();
                $("#view_soon_lists_show .without_favor").show();
                $("#scrollBar3").hide();
            }
            $("#pc_proLists3 ul").html(list);
        }
    });

}




//获取浏览商品的ID
function getProId(obj) {
    var id = obj.substring(30);
    id = id.substr(0, id.length - 6);
    return parseInt(id);
}

//右侧导航添加收藏
function rCreateFavorite(id) {
    if (confirm("确定要收藏吗?")) {
        var d = IsLogin();
        if (d) {
            jQuery.ajax({
                url: "/Favorite/AddFavorite?wareCode=" + id,
                type: "Get",
                cache: false,
                success: function (data) {
                    $("#view_soon_lists_show").show();
                    if (data == 3) {
                        $("#f_" + id).addClass('favor_icon_act');
                        $("#scrollBar3 .favor_success").text("商品收藏成功").show();
                    }
                    else if (data == 1) {
                        $("#scrollBar3 .favor_success").show().text("该商品已收藏！");
                    }
                    else if (data == 0) {
                        $(".favor_success").show().text("收藏失败，请重试！");
                    }
                    setTimeout(function () { $("#scrollBar3 .favor_success").hide(); }, 2000);
                }
            });
        }
        else {
            $("#login_boxs").show();
            $("#view_soon_lists_show").hide();
        }
    }
}
//收藏过商品函数调用
function isFavored(ids) {
    ids = ids.substring(0, ids.length - 1);
    ids = new Array(ids);
    if (!IsLogin())
        return;
    jQuery.ajax({
        type: "Get",
        url: urlConfig.pc + "/Favorite/IsFavorite?productIds=" + ids,
        cache: false,
        dataType: "jsonp",
        jsonp: "callback",
        success: function (data) {
            for (var i = 0; i < data.length; i++) {
                $("#f_" + data[i]).addClass('favor_icon_act');
            }
        }
    });
}
/*得到金额（获取两位小数点）*/
function ToMoney(x) {
    var f_x = parseFloat(x);
    if (isNaN(f_x)) {
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
//登陆后转到的链接地址(回到当前页面)
function login() {
    var ReturnUrl = window.location.href;
    var url = kad_user_url + '/Login?ReturnUrl=' + ReturnUrl;
    window.location.href = url;
    window.event.returnValue = false;
}
//注册后转到的链接地址(回到当前页面)
function reg() {
    var ReturnUrl = window.location.href;
    var url = kad_user_url + '/Register?ReturnUrl=' + ReturnUrl;
    window.location.href = url;
    window.event.returnValue = false;
}

jQuery(document).ready(function () {
    //记住登录名
    // if ($(".checked_val1").hasClass('checked_val2') && $.cookie("kadusername") != null) {
    //     $("#userNam_val").val($.cookie("kadusername"));
    // }

    isbd_mobile();
});
//检查用户名是否存在
function checkUserName() {
    var Email = $("#userNam_val").val();
    if (Email == "") {
        $("#userNam_val").addClass("errorInput");
        $("#userNam_val").siblings("span").show().text("用户名不能为空");
        $("#error_btn1").show();
        return false;
    }
    //$("#userNam_val").removeClass("errorInput");
    //$("#userNam_val").siblings("span").hide();
    //$("#error_btn1").hide();
    $.ajax({
        type: 'get',
        url: kad_user_url + '/Login/ExitsUserName?userName=' + Email,
        dataType: "jsonp",
        jsonp: "callback",
        success: function (data) {
            if (!data.Result) {
                $("#userNam_val").addClass("errorInput");
                $("#userNam_val").siblings("span").text("用户名不存在");
                $("#userNam_val").siblings("span").show();
                return;
            }
            $("#userNam_val").removeClass("errorInput");
            $("#userNam_val").css("border-color", "#ccc");
            $("#userNam_val").siblings("span").hide();
            $("#error_btn1").hide();
        }
    });
}

//检查密码是否为空及是否正确
function checkUserPwd() {
    var Password = $("#userPsd_val").val();
    if (Password == "") {
        $("#userPsd_val").css('border-color', '#ff0000');
        $("#userPsd_val").next(".errorTxt").show().text("密码不能为空");
        $("#error_btn2").show();
        return;
    } else {
        $("#userPsd_val").css('border-color', '#ccc');
        $("#error_btn2").hide();
        $("#userPsd_val").next(".errorTxt").hide();
    }

}

//登陆
function UserLogin2() {
    var Email = $("#userNam_val").val();
    var Password = $("#userPsd_val").val();
    if (Password == "") {
        if (Email == "" || Email == "用户名/邮箱/手机号码") {
            $("#userPsd_val").css('border-color', '#ff0000');
            $("#userPsd_val").next(".errorTxt").show().text("密码不能为空");
            $("#error_btn2").show();
            $("#userNam_val").addClass("errorInput");
            $("#userNam_val").siblings("span").show().text("用户名不能为空");
            $("#error_btn1").show();
            return;
        }
        else {
            $("#userPsd_val").css('border-color', '#ff0000');
            $("#userPsd_val").next(".errorTxt").show().text("密码不能为空");
            $("#error_btn2").show();
        }
    }
    else {
        $("#userPsd_val").css('border-color', '#ccc');
        $("#error_btn2").hide();
        $("#userPsd_val").next(".errorTxt").hide();
    }
    $.ajax({
        url: kad_user_url + '/Login/AjaxLoginV2',
        type: "Post",
        data: "userName=" + Email + "&pass=" + Password,
        dataType: "jsonp",
        jsonp: "callback",
        success: function (data) {
            if (data.Message == "没有绑定主帐号") {
                location.href = urlConfig.user + "/Register/RegisterKadMain?guId=" + data.Code + "&returnUrl=" + document.URL;
                return;
            }
            if (data.Message == "没有验证手机") {
                location.href = urlConfig.user + "/register/verification?guId=" + data.Code + "&returnUrl=" + document.URL;
                return;
            }
            if (data.Result) {
                $("#userPsd_val,#userNam_val").next(".errorTxt").hide();
                $("#login_boxs").hide();
                if ($("#view_soon_lists_show").css("display") == "block") { $("#view_soon_lists_show").show(); }
                $("#Favorites_lists_show .go_order_box").stop().animate({ right: 0 }, 400);
                if (GetLogin != null && (typeof GetLogin) === "function") //此处是为了更新头部的登陆后状态
                {
                    GetLogin();
                }
                if (sign_login == 1) {
                    login_cs();
                }
                NewGetCartList();
                getMyFavLists();
                getMyBuyLists();

                $("#view_soon_lists_show,.YnewYesLogin").show();
                $(".YnewNoLogin").hide();
                $("#view_soon_lists_show").hide();
                $("#Favorites_lists_show .go_order_box").stop().animate({ right: -264 }, 400);
                $("#mycode").show();//我的二维码
            }
            else if (!data.Result) {
                if (data.Code == "UserName") {
                    if (data.Message == "您输入的用户名不能为空！") {
                        $("#userNam_val").next(".errorTxt").show().text(data.Message);
                    }
                    else {
                        $("#userNam_val").next(".errorTxt").show().text("账户名不存在或密码不匹配，请重新输入！");
                    }
                    $("#userNam_val").css('border-color', '#ff0000');
                }
                else if (data.Code == "UserPassword") {
                    if (data.Message == "您输入的用户名不能为空！") {
                        $("#userNam_val").next(".errorTxt").show().text(data.Message);
                    }
                    else {
                        $("#userNam_val").next(".errorTxt").show().text("账户名不存在或密码不匹配，请重新输入！");
                    }
                    $("#userPsd_val").css('border-color', '#ff0000');
                }
                $("#error_btn2").show();
            }
        }
    });
}
///是否邮件
function isEmail(strEmail) {
    if (!strEmail.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1) {
        return false;
    }
    return true;
}
//回车
function isRegEnter() {
    if (window.event.keyCode == 13) {
        UserReg();
    }
}
//路径 (回到购物车首页)
function urlTo() {
    location.href = window.urlConfig.cart + "/cart/";
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
// 去左右空格
function Trim(str) {
    return str.replace(/\s+$|^\s+/g, "");
}
//----------------------------------------------------------------end 5.public.js
//购物车结算20120525

//转成money类型
function TotalMoney(price, concession, quantity) {
    return Math.round(((parseFloat(price) - parseFloat(concession)) * parseInt(quantity)) * 100) / 100;
}
//转成Decimal类型
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

$(document).ready(function () {
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
function Trim(str) {
    return str.replace(/\s+$|^\s+/g, "");
}

//右边购物车
// function NewGetCartList() {
//     var numbers = 0;
//     var $windowH = $(window).height();
//     $.ajax({
//         url: window.urlConfig.multiDomain.pc() + "/Cart/GetCartList",
//         type: "get",
//         cache: false,
//         dataType: "jsonp",
//         json: "callback",
//         success: function (data) {
//             if (data == null) {
//                 $("#myCarts").hide();
//                 return;
//             }
//             $(".hNavList .tcart").html("<a href=\"" + cartDomain + "/cart/index\">购物车<span style='color:red;padding:0;font-weight:bold;'>" + (data.TotalItemCount || 0) + "</span>件</a>")
//             $(".kad-cartNums").text(data.TotalItemCount);//更改购物车数量显示
//             var jn = data;
//             if (!jn) { $("#myCarts").hide(); }
//             //if (jn.ApiException == '同一商品不能参与多个同一类型的活动') {
//             //    alert('同一商品不能参与多个同类型的活动');
//             //    return;
//             //}
//             if (jn.ApiException != '' && jn.ApiException != null) {
//                 alert(jn.ApiException);
//                 return;
//             }

//             if (jn.SellerCart) {
//                 numbers = data.TotalItemCount;
//                 NewAppendToDiv(jn);

//                 $("#myCarts").show();
//                 if (numbers > 1 && numbers <= 4) {
//                     $("#cart_lists_show").find('.bar_box').hide();
//                     $("#pc_proLists1").css("top", 0);
//                 }


//                 //判断小屏幕出现滚动条
//                 if (numbers >= 3 && $windowH < 700 && $("#pc_proLists1").height() > $windowH - 100) {
//                     $("#bar_box1").show();
//                     //alert($("#pc_proLists1").height()+'---'+($windowH));
//                     scrollPlun(getId("bar1"), getId("scrollBar1"), getId("bar_box1"), getId("pc_proLists1"));
//                     return false;
//                 }
//                 if (numbers >= 4 && $("#pc_proLists1").height() > $windowH - 100) {
//                     //alert($("#pc_proLists1").height()+'---'+($windowH));              
//                     $("#bar_box1").show();
//                     scrollPlun(getId("bar1"), getId("scrollBar1"), getId("bar_box1"), getId("pc_proLists1"));
//                 }
//             } else {
//                 $(".cartNums").text(0);
//                 $("#cart_lists_show").children('.without_favor').show();
//                 $("#cart_lists_show").children(".cart_top,.scrollBar,.go_order_box").hide();
//                 $("#cart_lists_show").find('.bar_box').hide();
//             }
//         }
//     });
// }

function cutString(str, len) {
    if (str == null || str == '' || str == undefined)
        return str;
    var str_length = 0;
    var str_len = 0;
    str_cut = new String();
    str_len = str.length;
    for (var i = 0; i < str_len; i++) {
        a = str.charAt(i);
        str_length++;
        if (escape(a).length > 4) {
            //中文字符的长度经编码之后大于4  
            str_length++;
        }
        str_cut = str_cut.concat(a);
        if (str_length >= len) {
            str_cut = str_cut.concat("...");
            return str_cut;
        }
    }
    //如果给定字符串小于指定长度，则返回源字符串；  
    if (str_length < len) {
        return str;
    }
}



//获取购物车商品信息列表
function NewAppendToDiv(jn) {


    var obj = $("#pc_proLists1");
    var $go_order_box = $("#cart_lists_show .go_order_box");
    $(obj).html("");
    $go_order_box.html("");
    var str2 = "";
    var str3 = "";
    var sum = 0.00;
    var htmlStr = "";
    if (jn.SellerCart) {
        $.each(jn.SellerCart, function (i, o) {
            var rxTotal = 0;
            var notRxTotal = 0;
            var rxStr = "", otcStr = "";
            var cartList = o.CartList;
            var giftList = o.CartGiftList;
            if (!cartList)
                return true;

            $.each(cartList, function (i, ent) {
                var promotionPrice = 0;
                var sku = ent.WareSkuList;
                var str = "<li>";

                var entprice = (ent.Price - (ent.DisPrice / ent.Quantity)).toFixed(2);//原先价格减去平均优惠价格并且取两位小数
                if (ent.BuyType == 1 && sku) {
                    str += "<i class='delet_btns' onclick='DeleteCart(\"" + ent.CartId + "\")'></i>";
                    str += "<p class='pro_t clearfix'><span class='tcTitle' title='" + ent.PackageName + "'>" + ent.PackageName + "</span></p>";
                    //if (ent.Message != "") { str += "<p class='yhMsg'>" + ent.Message + "</p>"; }
                    var mainPic = '';
                    var mainName = '';
                    $.each(sku, function (i, sub) {
                        if (sub.DetailType == 4) {
                            str += "<p class=\"zengPin clearfix\"><a target='_blank' title='[子产品]" + sub.WareName + "' href='/product/" + sub.WareSkuCode + ".shtml'>" + "[子产品]" + cutString(sub.WareName, 26) + "</a></p>";
                            if (sub.IsKeyWare) {
                                mainPic = sub.MainPic;
                                mainName = sub.WareName;
                            }
                        }
                    });
                    str += "<div class=\"pro_box clearfix\"><div class='pic_show'><img src=" + mainPic + " alt=" + mainName + " /></div>" + '<div class="changeNum"><p>单价：¥' + entprice + '</p><p class="pro_price pb4">小计：¥' + ToMoney(ent.NetSumAmt) + '</p><span onclick="changeCart(event,\'' + ent.CartId + '\',-1)" class="clickBtns red_btn"></span><input type="text" class="changeNum_txt" id="tb_quantity_' + ent.CartId + '" value="' + ent.Quantity + '" maxlength="3" onblur="changeCart(event,\'' + ent.CartId + '\',0)"><span onclick="changeCart(event,\'' + ent.CartId + '\',1)" class="clickBtns add_btn"></span></div>';
                }

                if (ent.BuyType == 0 && sku) {
                    var zengPinStr = "";//赠品HTML

                    $.each(sku, function (i, sub) {
                        if (sub.DetailType != 4) {  //DetailType 商品类型 1-普通订购 2-赠品 3-换购商品 4-搭配商品 5-限时限量 6-秒杀商品   

                            if (sub.DetailType != 2) {
                                str += "<i class='delet_btns' onclick='DeleteCart(\"" + ent.CartId + "\")'></i>";
                                str += "<p class='pro_t clearfix'><a href='/product/" + sub.WareSkuCode + ".shtml' target='_blank' title='" + sub.WareName + "'>" + cutString(sub.WareName, 32) + "</a></p>";
                                str += "{0}";
                                str += "<div class=\"pro_box clearfix\"><div class='pic_show'><a href='/product/" + sub.WareSkuCode + ".shtml' title=\"\" target=\"_blank\"><img src=" + sub.MainPic + " alt=" + sub.WareName + " /></a></div>" + '<div class="changeNum"><p class="pro_price">单价：¥ ' + entprice + '</p><p class="pro_price pb4">小计：¥ ' + ToMoney(entprice * sub.Qty) + '</p><span onclick="changeCart(event,\'' + ent.CartId + '\',-1)" class="clickBtns red_btn"></span><input type="text" id="tb_quantity_' + ent.CartId + '" class="changeNum_txt" name="" value="' + sub.Qty + '" maxlength="3" onblur="changeCart(event,\'' + ent.CartId + '\',0)"><span onclick="changeCart(event,\'' + ent.CartId + '\',1)" class="clickBtns add_btn"></span></div>';
                            } else {
                                zengPinStr += "<p class=\"zengPin clearfix\"><a target='_blank' title='[赠品]" + sub.WareName + "' href='/product/" + sub.WareSkuCode + ".shtml'>" + "[赠品]" + cutString(sub.WareName, 26) + " [X" + sub.Qty + "]</a></p>";
                            }
                        }
                    });

                    str = str.replace(/\{0\}/g, zengPinStr);
                }
                str += "</div></li>";
                //换购
                if (ent.RedemptionList && ent.RedemptionList.length > 0) {
                    var strRedemption = '';
                    $.each(ent.RedemptionList, function (i, sub) {
                        promotionPrice += ent.PromotionPrice;
                        console.log(sub);
                        strRedemption += "<li>";
                        //str += "<i class='delet_btns' onclick='DeleteCart(\"" + ent.CartId + "\")'></i>";
                        strRedemption += "<p class='pro_t clearfix'><span class='tcTitle' title='" + sub.WareSkuCode + "'>[换购]" + sub.WareName + "</span></p>";

                        strRedemption += "<div class=\"pro_box clearfix\"><div class='pic_show'><a href='/product/" + sub.WareSkuCode + ".shtml' title=\"\" target=\"_blank\"><img src=" + sub.MainPic + " alt=" + sub.WareName + " /></a></div>" + '<div class="changeNum"><p class="pro_price">单价：¥ ' + ent.PromotionPrice.toFixed(2) + '</p><p class="pro_price pb4">小计：¥ ' + ToMoney(ent.PromotionPrice * sub.Qty) + '</p><span  class="clickBtns red_btn"></span><input type="text" id="tb_quantity_' + sub.WareSkuCode + '" class="changeNum_txt" disabled="disabled" name="" value="' + sub.Qty + '" maxlength="3" onblur="changeCart(event,\'' + sub.WareSkuCode + '\',0)"><span class="clickBtns add_btn"></span></div>';
                        strRedemption += "</li>"
                    });
                    str += strRedemption;
                }
                var totalReslt = ent.NetSumAmt + promotionPrice;
                if (o.SellerCode == kadSellerCode) {
                    if (ent.IsRx) {
                        rxStr += str;
                        rxTotal += totalReslt;
                    } else {
                        otcStr += str;
                        notRxTotal += totalReslt;
                    }
                }
                else {
                    rxStr += str;
                    rxTotal += totalReslt;
                }
            });
            var gifStr = '';
            //赠品
            $.each(giftList, function (i, sub) {
                gifStr += "<li>";
                //str += "<i class='delet_btns' onclick='DeleteCart(\"" + ent.CartId + "\")'></i>";
                gifStr += "<p class='pro_t clearfix'><span class='tcTitle' title='" + sub.WareSkuCode + "'>[赠品]" + sub.WareName + "</span></p>";

                //gifStr += "<p class=\"zengPin clearfix\"><a target='_blank' title='" + sub.WareName + "' href='/product/" + sub.WareSkuCode + ".shtml'>" + "[赠品]" + cutString(sub.WareName, 26) + "</a></p>";

                gifStr += "<div class=\"pro_box clearfix\"><div class='pic_show'><a href='/product/" + sub.WareSkuCode + ".shtml' title=\"\" target=\"_blank\"><img src=" + sub.MainPic + " alt=" + sub.WareName + " /></a></div>" + '<div class="changeNum"><p class="pro_price">单价：¥ 0.00</p><p class="pro_price pb4">小计：¥ 0.00</p><span  class="clickBtns red_btn"></span><input type="text" id="tb_quantity_' + sub.WareSkuCode + '" class="changeNum_txt" disabled="disabled" name="" value="' + sub.Qty + '" maxlength="3" onblur="changeCart(event,\'' + sub.WareSkuCode + '\',0)"><span class="clickBtns add_btn"></span></div>';
                gifStr += "</li>"
            });

            if (o.SellerCode == kadSellerCode) {
                var couponList = '';
                var couponPrice = 0;
                if (o.CouponList && o.CouponList.length > 0) {
                    couponList = getCoupon(o)
                    couponPrice = o.CouponPrice;
                }

                if (otcStr != "") {
                    htmlStr += "<ul id='ul1'>" + otcStr + gifStr + "</ul>";
                    htmlStr += couponList + '<p class="total_prices">康爱多  <span class="price_num"> 合计：' + ToMoney(notRxTotal - couponPrice) + '</span></p>';
                }
                if (o.CouponList && o.CouponList.length > 0) {
                    couponList = getCoupon(o)
                    if (otcStr.length > 0) {
                        couponList = '';
                        couponPrice = 0;
                    }
                }
                if (rxStr != "") {
                    htmlStr += "<ul id='ul2'>" + rxStr + gifStr + "</ul>";
                    htmlStr += couponList + '<p class="total_prices">新特药房云景店  <span class="price_num"> 合计：' + ToMoney(rxTotal - couponPrice) + '</span></p>';
                }
            }
            else {
                htmlStr += "<ul id='ul1'>" + rxStr + gifStr + "</ul>";
                htmlStr += getCoupon(o) + '<p class="total_prices">' + o.SellerName + '  <span class="price_num"> 合计：' + ToMoney(rxTotal - o.CouponPrice) + '</span></p>';
            }
        });
    }
    //判断是网上药店与先烈店


    $(".pro_num,.cartNums").text(jn.TotalItemCount);
    $(".tcart a").html("购物车<span style='color:red;padding:0;font-weight:bold;'>" + jn.TotalItemCount + "</span>件");
    str2 += '<p class="clearfix cart_top"><span class="total_pros"><span class="pro_num">' + jn.TotalItemCount + '</span>件商品</span> <span class="cart_totalPrices">¥ ' + jn.TotalProductPrice.toFixed(2) + '</span></p>';
    str2 += "<p><a class='go_nowOr' href=\"" + kad_cart_url + "/Cart/\" target=\"_blank\">去购物车结算</a></p>";
    $(obj).append(htmlStr);
    $go_order_box.append(str2);
    if (jn && jn.SellerCart) {
        $("#cart_lists_show").children('.without_favor').hide();
        $("#cart_lists_show").children(".scrollBar,.go_order_box").show();
        $("#cart_lists_show .go_order_box").children(".cart_top").show();
        $("#myCarts").show();
    } else {
        $("#myCarts").hide();
    }
}

function getCoupon(obj) {
    if (obj.CouponList.length == 0)
        return '';
    var couponName = '';
    $.each(obj.CouponList, function (i, ent) {
        if (obj.CouponCode != '' && obj.CouponCode == ent.CouponCode) {
            couponName = '<p style="padding-left:10px;color:#ff0000;">已选优惠：' + ent.CouponName + "<p>";
            return false;
        }
    });
    return couponName;
}
//删除购物车药品信息  2014-09-02
function DeleteCart(carId) {
    $("#showPrompt-fixed .Bcon").text("");
    $("#showPrompt-fixed .Bcon").html("<i class='ico-tipsExc'></i>确定不购买该商品吗?");
    popShow("showPrompt-fixed", "go_Cancel_fixed", 1, $("#go_sure_cart"), function () {
        jQuery.ajax({
            url: "" + kad_cart_url + "/Cart/Delete?CartId=" + carId,
            type: "Get", cache: false, dataType: "jsonp", jsonp: "callback",
            success: function (data) {
                $("#go_Cancel_fixed").trigger("click");
                if (!data) {
                    alert("删除商品失败");
                    return;
                }
                NewGetCartList();
                $("#bar1,#pc_proLists1").css("top", 0);
            }
        });
    });

}


//数量点击
function changeCart(e, cartId, type) {
    var quantity = $("#tb_quantity_" + cartId).val();
    quantity = parseFloat(quantity) + parseFloat(type);
    if (isNaN(quantity)) {
        quantity = 1;
        $("#tb_quantity_" + cartId).val("1");
    }
    if (quantity <= 0) {
        quantity = 1;
        $("#tb_quantity_" + cartId).val("1");
        NewGetCartList();
        return;
    } else if (quantity > 999) {
        quantity = 999;
        $("#tb_quantity_" + cartId).val("999");
    }
    else {
        $.ajax({
            url: kad_cart_url + "/Cart/ChangeQuantity?cartId=" + cartId + "&quantity=" + quantity,
            type: "GET",
            cache: false,
            dataType: "jsonp",
            jsonp: "callback",
            success: function (data) {
                if (data.Result) {
                    jQuery("#tb_quantity_" + cartId).val(quantity);
                }
                else {
                    //alert(data.Message);
                    showPrompt(data.Message, 0, 1);
                }
                NewGetCartList();
            }
        });
    }
}
//获取我的收藏商品列表信息
function getMyFavLists() {
    sign_login = 0;
    var login = IsLogin();
    if (!login) {
        $("#login_boxs").show();
        return;
    }
    var $windowH = $(window).height();
    var $pc_proLists2 = $("#pc_proLists2");
    var str = "";
    str += "<ul class='clearfix proLists'>";
    $.ajax({
        url: urlConfig.pc + '/Favorite/GetList',
        type: "Post",
        dataType: "jsonp",
        jsonp: "callback",
        cache: false,
        success: function (data) {
            if (data) {
                $("#Favorites_lists_show .without_favor").hide();

                $("#Favorites_lists_show .cart_top").show();
                $(".without_conetion").hide();
                $("#scrollBar2").show();
                if ($("#Favorites_lists_show").css("display") == "block") {
                    $("#Favorites_lists_show .go_order_box").stop().animate({ right: -0 }, 400);
                } else if ($("#Favorites_lists_show").css("display") == "none") {
                    $("#Favorites_lists_show .go_order_box").stop().animate({ right: -264 }, 400);
                }
                for (var i = 0; i < data.length; i++) {

                    str += "<li>";
                    str += '<p class="pro_t clearfix"><a target="_blank" href="/product/' + data[i].WareSkuCode + '.shtml">' + data[i].WareName + '</a><span class="pro_price"><i class="favor_delet_btns" onclick="DeleteFavor(\'' + data[i].Id + '\')"></i></span></p>';
                    if (data[i].IsRx == true) {
                        str += '<div class="pro_box clearfix"><div class="pic_show"><a target="_blank" href="/product/' + data[i].WareSkuCode + '.shtml"><img alt="" src="' + data[i].Pic180 + '"></a></div><div class="changeNum"><p class="price1">¥ ' + data[i].SalePrice + '</p><p class="put_inCart"><a target="_blank" href="/product/' + data[i].WareSkuCode + '.shtml">查看详情</a></p></div></div>';
                    } else {
                        str += '<div class="pro_box clearfix"><div class="pic_show"><a target="_blank" href="/product/' + data[i].WareSkuCode + '.shtml"><img alt="" src="' + data[i].Pic180 + '"></a></div><div class="changeNum"><p class="price1">¥ ' + data[i].SalePrice + '</p><p class="put_inCart"><a href="javascript:;" onclick="rightAddCart_new(1,\'' + data[i].WareSkuCode + '\',0,\'' + data[i].SellerCode + '\')' + ';">加入购物车</a></p></div></div>';
                    }

                    str += "</li>";

                }
            } else {
                if (!login) {
                    $("#login_boxs").show();
                    $("#Favorites_lists_show .go_order_box").stop().animate({ right: -264 }, 400);
                    return false;
                }
                $("#Favorites_lists_show .go_order_box").stop().animate({ right: -264 }, 400);
                $("#Favorites_lists_show .without_favor").show();
                $("#Favorites_lists_show .cart_top").hide();
                $("#scrollBar2").hide();
            }
            str += "</ul>";
            $("#favor_num").html(data.length + "件商品");
            if (data.length == 0) {
                $('.without_conetion').show();
            }
            //$("#favor_num").html(data.length);
            $("#favor_num").next("a").attr("href", kad_user_url + "/Favorite/");
            $("#pc_proLists2").html(str);

            function getId(obj) {
                return document.getElementById(obj);
            }

            //小屏幕显示滚动条
            if (data.length >= 4 && $windowH < 700 && $("#pc_proLists2").height() > $windowH - 100) {
                $("#bar_box2").show();
                scrollPlun(getId("bar2"), getId("scrollBar2"), getId("bar_box2"), getId("pc_proLists2"));
                return false;
            }
            //大屏幕显示滚动条
            if (data.length >= 6) {
                $("#bar_box2").show();
                scrollPlun(getId("bar2"), getId("scrollBar2"), getId("bar_box2"), getId("pc_proLists2"));
            } else {
                $("#bar_box2").hide();
                $("#pc_proLists2").css("top", 0);
            }
        }
    });
}
//删除收藏商品
function DeleteFavor(id) {
    $("#showPrompt-fixed .Bcon").text("");
    $("#showPrompt-fixed .Bcon").html("<i class='ico-tipsExc'></i>确定取消收藏?");
    popShow("showPrompt-fixed", "go_Cancel_fixed", 1, $("#go_sure_cart"), function () {
        jQuery.ajax({
            url: urlConfig.pc + '/Favorite/DeleteFavorite?wareCode=' + id,
            type: "get",
            dataType: "jsonp",
            jsonp: "callback",
            cache: false,
            success: function (data) {
                $("#go_Cancel_fixed").trigger("click");
                if (data) {
                    getMyFavLists();
                    $("#bar2,#pc_proLists2").css("top", 0);
                    $("#scrollBar2 .favor_success").text("删除成功").show();
                    setTimeout(function () { $("#scrollBar2 .favor_success").hide(); }, 2000);
                }
                else {
                    $("#scrollBar2 .favor_success").show();
                    setTimeout(function () { $("#scrollBar2 .favor_success").hide().text("删除失败,请重试！"); }, 2000);
                }
            }
        });
    });

}
//ref定义
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null)
        return unescape(r[2]);
    return 0;
}
//右侧导航添加到购物车函数
function rightAddCart_new(q, productId, buyType, sellerCode) {
    //ctrActionsend("add_to_card");
    $("#h_Quantity").val(q);
    addCart(productId, q, buyType, sellerCode);

}
//添加到购物车 CreateCart函数  
var oLocathref = window.location.href;//获取当页面的链接地址
var cartMsg;
//Id 为商品编码，quantity 为数量 buyType 为 0--商品  1--套餐 sellerCode:  卖家编码
function addCart(id, quantity, buyType, sellerCode) {
    if (sellerCode == undefined || sellerCode == 'undefined' || sellerCode == '' || sellerCode == null)
        sellerCode = kadSellerCode;
    var url = 'id=' + id + '&quantity=' + quantity + '&buyType=' + buyType + '&sellerCode=' + sellerCode + '&ref=' + GetQueryString("ref");
    if (quantity == 0) { quantity = 1; }
    $.support.cors = true;
    $.ajax({
        url: urlConfig.pc + "/Cart/AddCartJsonp",
        type: "Post",
        data: url,
        cache: false,
        dataType: "jsonp",
        jsonp: "callback",
        success: function (data) {
            NewGetCartList();
            $(".favor_success1").text(data).show();
            setTimeout(function () { $(".favor_success1").hide(); }, 2000);
            GetTopCartList();
        }
    });
}
//最近购买的商品信息列表
function getMyBuyLists() {
    sign_login = 0;
    var $windowH = $(window).height();
    var $pc_proLists4 = $("#pc_proLists4");
    var str = "";
    var timeStr = null;
    str += "<ul class='clearfix proLists'>";
    $.ajax({
        url: urlConfig.pc + '/Product/GetRecentOrders',
        type: "get",
        dataType: 'jsonp',
        jsonp: "callback",
        cache: false,
        success: function (data) {
            if (data.length >= 1) {
                $("#go_buyAgain .cart_top").show();
                $("#scrollBar4").show();
                $("#go_buyAgain .without_favor").hide();
                $("#go_buyAgain .go_order_box").stop().animate({ "right": 0 }, 400);
                if ($("#go_buyAgain").css("display") == "none") { $("#go_buyAgain .go_order_box").stop().animate({ "right": -264 }, 400); }

            } else {
                if (!IsLogin()) {
                    $("#login_boxs").show();
                    $("#go_buyAgain .without_favor").hide();
                } else {
                    $("#go_buyAgain .without_favor").show();
                }
                $("#go_buyAgain .go_order_box").stop();
            }

            for (var i = 0; i < data.length; i++) {
                timeStr = getTimeStr(data[i].CreateDate);
                str += "<li>";
                str += '<p class="pro_t clearfix"><span class="buy_date">' + timeStr + '</span><span class="pro_price">¥ ' + data[i].Price + '</span></p>';
                str += '<p class="yhMsg"><a target="_blank" href="/product/' + data[i].WareCode + '.shtml">' + data[i].WareName + '</a></p>';
                if (data[i].IsRx == true) {
                    str += '<div class="pro_box clearfix"><div class="pic_show"><a target="_blank" href="' + data[i].Pic + '.shtml"><img alt="" src="' + data[i].Pic + '"></a></div><div class="changeNum"><a class="buy_againBtn" target="_blank" href="/product/' + data[i].WareCode + '.shtml">查看详情</a></div></div>';
                } else {
                    str += '<div class="pro_box clearfix"><div class="pic_show"><a target="_blank" href="/product/' + data[i].WareCode + '.shtml"><img alt="" src="' + data[i].Pic + '"></a></div><div class="changeNum"><a class="buy_againBtn" href="javascript:;" onclick="rightAddCart_new(1,\'' + data[i].WareCode + '\',0,\'' + data[i].SellerCode + '\')">加入购物</a></div></div>';
                }
                str += "</li>";
            }
            str += "</ul>";




            //小屏幕显示滚动条
            if (data.length >= 4 && $windowH < 700 && $("#pc_proLists4").height() > $windowH - 100) {
                $("#bar_box4").show();
                scrollPlun(getId("bar4"), getId("scrollBar4"), getId("bar_box4"), getId("pc_proLists4"));
                return false;
            }
            //大屏幕显示滚动条
            if (data.length >= 5) {
                $("#bar_box4").show();
                scrollPlun(getId("bar4"), getId("scrollBar4"), getId("bar_box4"), getId("pc_proLists4"));
            } else {
                $("#bar_box4").hide();
                $("#pc_proLists4").css("top", 0);
            }
            $("#pc_proLists4").html(str);
        }
    });

}
//最近购买时间换算
function getTimeStr(obj) {
    var str = obj;
    str = str.substring(6);
    str = str.substring(-2, str.length - 2);
    var T = new Date(parseInt(str));
    var timeTxt = T.getFullYear() + '-' + bZoro(T.getUTCMonth() + 1) + '-' + bZoro(T.getDate());
    return timeTxt;

}

function bZoro(obj) {
    parseInt(obj) < 10 ? obj = "0" + obj : obj;
    return obj;
}
//END最近购买时间换算
//滚动条
function addEvent(obj, sEv, fn) {
    if (obj.addEventListener) {
        obj.addEventListener(sEv, fn, false);
    } else {
        obj.attachEvent('on' + sEv, fn);
    }
}

function addWheel(obj, fn) {
    function fnWheel(ev) {
        var oEvent = ev || event;
        var bDown = false;

        if (oEvent.wheelDelta) {
            if (oEvent.wheelDelta < 0) {
                bDown = true;
            } else {
                bDown = false;
            }
        } else {
            if (oEvent.detail < 0) {
                bDown = false;
            } else {
                bDown = true;
            }
        }

        fn && fn(bDown);

        oEvent.preventDefault && oEvent.preventDefault();
        return false;
    }

    if (window.navigator.userAgent.toLowerCase().indexOf('firefox') != -1) {
        obj.addEventListener('DOMMouseScroll', fnWheel, false);
    } else {
        addEvent(obj, 'mousewheel', fnWheel);
    }
}
//获取对应的ID
function getId(obj) {
    return document.getElementById(obj);
}
//滚动条函数调用
function scrollPlun(bar, objDiv, obj2, obj3) {
    bar.onmousedown = function (ev) {
        var oEvent = ev || event;

        var disY = oEvent.clientY - bar.offsetTop;
        document.onmousemove = function (ev) {
            var oEvent = ev || event;

            var t = oEvent.clientY - disY;

            (t < 0) && (t = 0);
            (t > obj2.offsetHeight - bar.offsetHeight) && (t = obj2.offsetHeight - bar.offsetHeight);

            bar.style.top = t + 'px';

            var scale = t / (obj2.offsetHeight - bar.offsetHeight);
            obj3.style.top = -scale * (obj3.offsetHeight - objDiv.offsetHeight - $(".total_prices").length * 40) + 'px';
        };

        document.onmouseup = function () {
            document.onmousemove = null;
            document.onmouseup = null;

            bar.releaseCapture && bar.releaseCapture();
        };

        bar.setCapture && bar.setCapture();
        return false;
    };

    addWheel(objDiv, function (bDown) {
        var t = bar.offsetTop;
        if (bDown) {
            t += 10;
        } else {
            t -= 10;
        }

        (t < 0) && (t = 0);
        (t > obj2.offsetHeight - bar.offsetHeight) && (t = obj2.offsetHeight - bar.offsetHeight);

        bar.style.top = t + 'px';

        var scale = t / (obj2.offsetHeight - bar.offsetHeight);

        obj3.style.top = -scale * (obj3.offsetHeight - objDiv.offsetHeight) + 'px';
    });


}
//end滚动条
(function () {
    $(function () {
        var $fixed_navigation = $("#fixed_navigation");
        var $windowH = $(window).height();
        var $commonWidth = $("#fixed_navigation .commonWidth");
        var $online_kefu = $(".online_kefu");
        var $online_kefu_container = $online_kefu.find(".online_kefu_container");
        var $navi_right = $fixed_navigation.find(".navi_right");
        var $cart_box = $fixed_navigation.find(".cart_box");
        var $nav_closeBtn = $("#nav_closeBtn");
        var $go_order_box = $fixed_navigation.find(".go_order_box");
        var $Favorites = $fixed_navigation.find(".Favorites");
        var $view_soon = $fixed_navigation.find(".view_soon");
        var $order_current = $fixed_navigation.find(".order_current");
        var $favor_boxs = $(".favor_boxs");
        var $cart_lists_show = $(".cart_lists_show");
        var $sm_two_order = $fixed_navigation.find(".sm_two_order");
        var $my_Info_isLogin = $fixed_navigation.find(".my_Info_isLogin");
        // NewGetCartList();

        if ($windowH > 700) { $(".navi_left").css("padding-top", 200); $(".sm_two_order").css("margin-top", 20); }
        if ($windowH < 800) { $(".navi_left").css("padding-top", 150); $(".sm_two_order").css("margin-top", 20); }
        $(window).resize(function (event) {
            //NewGetCartList();

            var $W = $(window).height();
            $(".scrollBar").css('height', $W - 78);
            if ($W < 800) { $(".navi_left").css("padding-top", 150); $(".sm_two_order").css("margin-top", 20); }
        });

        //我的账号//点击购物车/我的收藏/最近浏览/再次购买导航切换
        //我的账号-方君追加
        $(".my_Info").bind('click', function (event) {
            $("#login_boxs").hide();
            //NewGetCartList();
            var $windowH = $(window).height();
            $(".scrollBar").css('height', $windowH - 108);
            var _this = $(this).index() - 2;
            $("#scrollBar2 .favor_success").hide();
            if ($my_Info_isLogin.eq(_this).css('display') == "none") {
                $(this).css('background-color', '#0066d4').siblings().css('background-color', '');
                $fixed_navigation.stop().animate({ "right": 0 }, 400);
                $go_order_box.eq(_this).stop().animate({ "right": 0 }, 400);
                $my_Info_isLogin.eq(_this).show().siblings('.my_Info_isLogin').hide();
                vip_Islogin();
                $nav_closeBtn.show();
                $('.isbd_mobile').hide();
                $cart_lists_show.hide();
                return false;
            }
            if ($my_Info_isLogin.eq(_this).css('display') == "block") {
                $my_Info_isLogin.hide('slow');
                $(this).css('background-color', '');
                $fixed_navigation.stop().animate({ "right": "-264px" }, 400, function () {
                    $go_order_box.css('right', -264);
                });
                $go_order_box.eq(_this).stop().animate({ "right": -264 }, 400)
                $cart_lists_show.eq(_this).hide();
                $nav_closeBtn.hide();
                isbd_mobile();
            }
        });
        //判断是否登录，以便显示未登录或者登录状态-方君追加
        function vip_Islogin() {
            $.ajax({
                url: urlConfig.pc + "/user/GetUserDetail",
                type: "get",
                dataType: "jsonp",
                jsonp: "callback",
                cache: false,
                success: function (data) {
                    if (data.isLogin == false) {
                        $('.no_login').show();
                        $('.already_login_box').hide();
                        //isbd_mobile();
                    }
                    else {
                        $('.already_login_box').show();
                        $('.no_login').hide();
                        $('#vip_sname').text(data.userName);
                        $('#vip_sname').attr("href", urlConfig.user + "/user");
                        $('#vip_sign').text("[" + data.cusGradeName + "]");
                        //$('.plese_login_img img').attr("src",data.cusPic);
                        if (data.safeLevel == 0) {
                            $('.safe_level_type').css("width", "33%");
                            $('#safe_level_list').text("低");
                        }
                        else if (data.safeLevel == 1) {
                            $('.safe_level_type').css("width", "66%");
                            $('#safe_level_list').text("中");
                        }
                        else if (data.safeLevel == 2) {
                            $('.safe_level_type').css("width", "100%");
                            $('#safe_level_list').text("高");
                        }
                        if (data.isVerifyMobile == false) {
                            $('#mobile_isbd').text("未绑定");
                            $('#mobile_isbd').attr("href", urlConfig.user + "/mobile/verification");
                            $('.bangd_achieve').show();
                        }
                        else if (data.isVerifyMobile == true) {
                            $('#mobile_isbd').text("已绑定");
                            $('#mobile_isbd').attr("href", urlConfig.user + "/safe");
                            $('.bangd_achieve').hide();
                        }
                        if (data.isVerifyEmail == false) {
                            $('#yx_isbd').text("未绑定");
                            $('#yx_isbd').attr("href", urlConfig.user + "/mail/verification");
                        }
                        else if (data.isVerifyEmail == true) {
                            $('#yx_isbd').text("已绑定");
                            $('#yx_isbd').attr("href", urlConfig.user + "/safe");
                        }
                    }
                }
            });
        }

        //我的购物车
        $(".cart_box").bind('click', function (event) {
            $("#login_boxs").hide();
            NewGetCartList();
            var $windowH = $(window).height();
            $(".scrollBar").css('height', $windowH - 108);
            var _this = $(this).index() - 3;
            $("#scrollBar2 .favor_success").hide();
            if ($cart_lists_show.eq(_this).css('display') == "none") {
                $(this).css('background-color', '#0066d4').siblings().css('background-color', '');
                $fixed_navigation.stop().animate({ "right": 0 }, 400);
                $go_order_box.eq(_this).stop().animate({ "right": 0 }, 400);
                $('.my_Info_isLogin').hide();
                $cart_lists_show.eq(_this).show().siblings('.cart_lists_show').hide();
                $nav_closeBtn.show();
                $('.isbd_mobile').hide();
                return false;
            }
            if ($cart_lists_show.eq(_this).css('display') == "block") {
                $(this).css('background-color', '');
                $fixed_navigation.stop().animate({ "right": "-264px" }, 400, function () {
                    $go_order_box.css('right', -264);
                });
                $go_order_box.eq(_this).stop().animate({ "right": -264 }, 400)
                $cart_lists_show.eq(_this).hide();
                $nav_closeBtn.hide();
                isbd_mobile();
            }
        });
        //我的收藏
        $(".Favorites").bind("click", function (event) {
            var _this = $(this).index() - 3;
            getMyFavLists();
            $("#scrollBar2 .favor_success").hide();
            if ($cart_lists_show.eq(_this).css('display') == "none") {
                $(this).css('background-color', '#0066d4').siblings().css('background-color', '');
                var $windowH = $(window).height();
                $(".scrollBar").css('height', $windowH - 108);
                $fixed_navigation.stop().animate({ "right": 0 }, 400);
                $('.my_Info_isLogin').hide();
                $cart_lists_show.eq(_this).show().siblings('.cart_lists_show').hide();
                $nav_closeBtn.show();
                $('.isbd_mobile').hide();
                return false;
            }
            if ($cart_lists_show.eq(_this).css('display') == "block") {
                $(this).css('background-color', '');
                $fixed_navigation.stop().animate({ "right": "-264px" }, 400, function () {
                    $go_order_box.css('right', -264);
                });
                $cart_lists_show.eq(_this).hide();
                $nav_closeBtn.hide();
                isbd_mobile();
            }
        });
        //最近浏览
        $(".view_soon").bind('click', function (event) {
            $("#login_boxs").hide();
            curViewHestory();
            var $windowH = $(window).height();
            $(".scrollBar").css('height', $windowH - 60);
            var _this = $(this).index() - 3;
            if ($cart_lists_show.eq(_this).css('display') == "none") {
                $(this).css('background-color', '#0066d4').siblings().css('background-color', '');
                $fixed_navigation.stop().animate({ "right": 0 }, 400);
                $('.my_Info_isLogin').hide();
                $cart_lists_show.eq(_this).show().siblings('.cart_lists_show').hide();
                $nav_closeBtn.show();
                $('.isbd_mobile').hide();
                return false;
            }
            if ($cart_lists_show.eq(_this).css('display') == "block") {
                $(this).css('background-color', '');
                $fixed_navigation.stop().animate({ "right": "-264px" }, 400, function () {
                    $go_order_box.css('right', -264);
                });
                //$("#go_buyAgain .go_order_box").stop().animate({"right":-264}, 400);
                $cart_lists_show.eq(_this).hide();

                $nav_closeBtn.hide();
                isbd_mobile();
            }

        });
        //清空浏览记录
        $("#favor_clearBtn").click(function () {
            $("#showPrompt-fixed .Bcon").text("");
            $("#showPrompt-fixed .Bcon").html("<i class='ico-tipsExc'></i>确定清空浏览记录?");
            popShow("showPrompt-fixed", "go_Cancel_fixed", 1, $("#go_sure_cart"), function () {
                $("#go_Cancel_fixed").trigger("click");
                $("#view_soon_lists_show .cart_top").hide();
                $("#view_soon_lists_show .without_favor").show();
                $.cookie("KadProductHistory", null, { expires: 1, domain: '360kad.com', path: '/' });
                json = null; //清空cookie之后也需要把全局变量json清空
                $("#pc_proLists3 ul li").remove();
            });
        });
        //最近购买的商品
        $(".order_current").bind("click", function (event) {
            var _this = $(this).index() - 3;
            getMyBuyLists();
            if ($cart_lists_show.eq(_this).css('display') == "none") {
                $(this).css('background-color', '#0066d4').siblings().css('background-color', '');
                var $windowH = $(window).height();
                $(".scrollBar").css('height', $windowH - 78);
                $(window).resize(function (event) {
                    var $windowH = $(window).height();
                    $(".scrollBar").css('height', $windowH - 78);
                });
                $fixed_navigation.stop().animate({ "right": 0 }, 400);
                $('.my_Info_isLogin').hide();
                $cart_lists_show.eq(_this).show().siblings('.cart_lists_show').hide();
                $nav_closeBtn.show();
                $('.isbd_mobile').hide();
                return false;
            }
            if ($cart_lists_show.eq(_this).css('display') == "block") {
                $(this).css('background-color', '');
                $fixed_navigation.stop().animate({ "right": -264 }, 400, function () {
                    $go_order_box.css('right', -264);
                });
                //$("#go_buyAgain .go_order_box,#cart_lists_show .go_order_box").stop().animate({"right":-264}, 400);
                $cart_lists_show.eq(_this).hide();
                $nav_closeBtn.hide();
                isbd_mobile();
            }
        });


        //点击关闭右侧导航收起
        $nav_closeBtn.click(function (event) {
            $fixed_navigation.stop().animate({ right: "-264px" }, 400);
            $go_order_box.stop().animate({ right: "-264px" }, 400);
            $nav_closeBtn.hide();
            $commonWidth.css("background-color", "");
            $(".cart_lists_show").hide();
            $(".my_Info_isLogin").hide();
            isbd_mobile();
        });
        //鼠标经过切换状态
        $commonWidth.hover(function () {
            var _this = $(this).index();
            switch (_this) {
                case 2:
                    if ($('.isbd_mobile').css("display") == "none") {
                        $(this).children(".my_Info_container").stop().animate({ right: "36px", opacity: 1 }, 400).show();
                        $(this).children(".pointer").stop().animate({ right: "30px", opacity: 1 }, 400).show();
                    }
                    break;
                case 3:
                    $(this).addClass('cart_box_hover');
                    break;
                case 4:
                    $(this).addClass('Favorites_hover');
                    $(this).children(".Favorites_container").stop().animate({ right: "36px", opacity: 1 }, 400).show();
                    $(this).children(".pointer").stop().animate({ right: "30px", opacity: 1 }, 400).show();
                    break;
                case 5:
                    $(this).addClass('view_soon_hover');
                    $(this).children(".view_soon_container").stop().animate({ right: "36px", opacity: 1 }, 400).show();
                    $(this).children(".pointer").stop().animate({ right: "30px", opacity: 1 }, 400).show();
                    break;
                case 6:
                    $(this).addClass('order_current_hover');
                    $(this).children(".order_current_container").stop().animate({ right: "36px", opacity: 1 }, 400).show();
                    $(this).children(".pointer").stop().animate({ right: "30px", opacity: 1 }, 400).show();
                    break;
                case 7:
                    $(this).css('background-color', '#0066d4');
                    $(this).children(".order_current_container").stop().animate({ right: "36px", opacity: 1 }, 400).show();
                    $(this).children(".pointer").stop().animate({ right: "30px", opacity: 1 }, 400).show();
                    break;
                case 8:
                    $(this).css('background-color', '#0066d4');
                    $(this).children(".order_current_container").stop().animate({ right: "36px", opacity: 1 }, 400).show();
                    $(this).children(".pointer").stop().animate({ right: "30px", opacity: 1 }, 400).show();
                    break;
                case 9:
                    $(this).addClass('sm_two_order_hover');
                    break;
                case 10:
                    $(this).addClass('backUp_top_hover');
                    break;
            }


        }, function () {
            var _this = $(this).index();
            switch (_this) {
                case 2:
                    $(this).children(".my_Info_container").stop().animate({ right: "66px", opacity: 0 }, 400).hide();
                    $(this).children(".pointer").stop().animate({ right: "60px", opacity: 0 }, 400).hide();
                    break;
                case 3:
                    $(this).removeClass('cart_box_hover');
                    break;
                case 4:
                    $(this).removeClass('Favorites_hover');
                    $(this).children(".Favorites_container").stop().animate({ right: "66px", opacity: 0 }, 400).hide();
                    $(this).children(".pointer").stop().animate({ right: "60px", opacity: 0 }, 400).hide();
                    break;
                case 5:
                    $(this).removeClass('view_soon_hover');
                    $(this).children(".view_soon_container").stop().animate({ right: "66px", opacity: 0 }, 400).hide();
                    $(this).children(".pointer").stop().animate({ right: "60px", opacity: 0 }, 400).hide();
                    break;
                case 6:
                    $(this).removeClass('order_current_hover');
                    $(this).children(".order_current_container").stop().animate({ right: "66px", opacity: 0 }, 400).hide();
                    $(this).children(".pointer").stop().animate({ right: "60px", opacity: 0 }, 400).hide();
                    break;
                case 7:
                    $(this).css('background-color', '');
                    $(this).children(".order_current_container").stop().animate({ right: "66px", opacity: 0 }, 400).hide();
                    $(this).children(".pointer").stop().animate({ right: "60px", opacity: 0 }, 400).hide();
                    break;
                case 8:
                    $(this).css('background-color', '');
                    $(this).children(".order_current_container").stop().animate({ right: "66px", opacity: 0 }, 400).hide();
                    $(this).children(".pointer").stop().animate({ right: "60px", opacity: 0 }, 400).hide();
                    break;
                case 9:
                    $(this).removeClass('sm_two_order_hover');
                    break;
                case 10:
                    $(this).removeClass('backUp_top_hover');
                    break;
            }
        });
        //在线客服切换效果
        var keT = null;
        $online_kefu.hover(function () {
            clearTimeout(keT);
            $(this).children().show();
        }, function () {
            keT = setTimeout(function () {
                $online_kefu.children().hide();
            }, 600);
        });
        $sm_two_order.hover(function () {
            $(this).children().show();
        }, function () {
            $(this).children().hide();
        });
        //点击删除按钮清除用户名密码val
        $("#login_boxs .error_btn").click(function (event) {
            $(this).siblings('input').val("");
            event.stopPropagation();
        });
        //记住登录名
        $(".checked_val1").click(function () {
            $(this).hasClass('checked_val1 checked_val2') ? $(this).removeClass('checked_val2') : $(this).addClass('checked_val2');
        });

        //登录框垂直居中
        $navi_right.css({ height: $windowH + 'px' });

        //事件冒泡处理
        function checkFather(that, e) {
            var parent = e.relatedTarget;
            try {
                while (parent && parent !== that) {
                    parent = parent.parentNode;
                }
                return (parent !== that);
            } catch (e) { }
        }

        //回到顶部
        var $backUp_top = $(".backUp_top");
        $backUp_top.click(function () {
            $("html,body").animate({ scrollTop: 0 }, 100);
        });
        //删除按钮鼠标经过效果
        // $(".delet_btns").live("mouseover mouseout", function (event) {
        //     if (event.type == "mouseover") {
        //         $(this).addClass('delet_btnsH');
        //     } else {
        //         $(this).removeClass('delet_btnsH');
        //     }
        // });
        // 删除按钮鼠标经过效果
        // $(".favor_delet_btns").live("mouseover mouseout", function (event) {
        //     if (event.type == "mouseover") {
        //         $(this).addClass('favor_delet_btnsH');
        //     } else {
        //         $(this).removeClass('favor_delet_btnsH');
        //     }

        // });
    });
})();

//方君追加
$(function () {
    if ($('#fixed_navigation').css("right") == '0px') {
        $('.isbd_mobile').hide();
    }
    $('.close_bd_tip01').click(function () {
        $('.bangd_achieve').hide();
    })
    $('.close_bd_tip02').click(function () {
        $('.isbd_mobile').hide();
    })
    $('.plese_login').click(function () {
        $('.my_Info_isLogin').hide();
        $('#login_boxs').show();
        sign_login = 1;
    })
});
function login_cs() {
    $.ajax({
        url: "http://www.360kad.com/user/GetUserDetail",
        type: "get",
        dataType: "jsonp",
        jsonp: "callback",
        cache: false,
        success: function (data) {
            if (data.isLogin == true) {
                $('.my_Info_isLogin').show();
                $('.no_login').hide();
                $('.already_login_box').show();
                $('#vip_sname').text(data.userName);
                $('#vip_sname').attr("href", "http://user.360kad.com/user");
                $('#vip_sign').text("[" + data.cusGradeName + "]");
                //$('.plese_login_img img').attr("src",data.cusPic);
                if (data.safeLevel == 0) {
                    $('.safe_level_type').css("width", "33%");
                    $('#safe_level_list').text("低");
                }
                else if (data.safeLevel == 1) {
                    $('.safe_level_type').css("width", "66%");
                    $('#safe_level_list').text("中");
                }
                else if (data.safeLevel == 2) {
                    $('.safe_level_type').css("width", "100%");
                    $('#safe_level_list').text("高");
                }
                if (data.isVerifyMobile == false) {
                    $('#mobile_isbd').text("未绑定");
                    $('#mobile_isbd').attr("href", "http://user.360kad.com/mobile/verification");
                    $('.bangd_achieve').show();
                }
                else if (data.isVerifyMobile == true) {
                    $('#mobile_isbd').text("已绑定");
                    $('#mobile_isbd').attr("href", "http://user.360kad.com/safe");
                    $('.bangd_achieve').hide();
                }
                if (data.isVerifyEmail == false) {
                    $('#yx_isbd').text("未绑定");
                    $('#yx_isbd').attr("href", "http://user.360kad.com/mail/verification");
                }
                else if (data.isVerifyEmail == true) {
                    $('#yx_isbd').text("已绑定");
                    $('#yx_isbd').attr("href", "http://user.360kad.com/safe");
                }
            }
        }
    })
}

function isbd_mobile() {
    $.ajax({
        url: "http://www.360kad.com/user/GetUserDetail",
        type: "get",
        dataType: "jsonp",
        jsonp: "callback",
        cache: false,
        success: function (data) {
            if (data.isLogin == false) {
                $('.isbd_mobile').hide();
            }
            else if (data.isLogin == true && data.isVerifyMobile == false) {
                $('.isbd_mobile').show();
            }
        }
    })
}

