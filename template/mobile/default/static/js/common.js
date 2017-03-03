//判断图片是否存在
function isExitsImg_nopic(thisObj) {

}

//判断头像入径是否存在目标服务器
function isExitsImg_logo(thisObj) {

}

//保留2位小数点
function toDecimal2(x) {
    var f = parseFloat(x);
    if (isNaN(f)) {
        return false;
    }
    var f = Math.round(x * 100) / 100;
    var s = f.toString();
    var rs = s.indexOf('.');
    if (rs < 0) {
        rs = s.length;
        s += '.';
    }
    while (s.length <= rs + 2) {
        s += '0';
    }
    return s;
}

$(document).ready(function () {

    /*喜欢按钮*/
    $(".shouc").click(function () {
        var ico = $(this);
        $.ajax({
            type: "POST",
            url: "../tools/tc.ashx?action=like",
            dataType: "json",
            data: {
                "good_id": ico.attr("data-goodid"),
                "wxuser_id": ico.attr("data-wxuserid")
            },
            timeout: 20000,
            success: function (data, textStatus) {
                if (data.status == 1) {
                    ico.addClass("curr");
                } else {
                    ico.removeClass("curr");
                }
                dialog(data.msg);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
            }
        });
    })

    /*搜索按钮*/
    $("#btnsearch").click(function () {
        var key = $("#txt_search").val();
            location.href = "/tc/goods.aspx?key=" + encodeURI(encodeURI(key));
    })

    /*新用户引导关注弹出div*/
    $("#close").click(function () {
        $("#close_bar").css("display", "none");
    });
    $('#enter_gz').click(function (e) {
        e.stopPropagation();
        if ($('#bar_show').css("display") == "none") {
            $('#bar_show').fadeIn(500);
        } else {
            $('#bar_show').fadeOut(500);
        }
    });
    //open popup
    $('.cd-popup-trigger').on('click', function (event) {
        event.preventDefault();
        $('.cd-popup').addClass('is-visible');
    });
    //close popup
    $('.cd-popup').on('click', function (event) {
        if ($(event.target).is('.cd-popup-close') || $(event.target).is('.cd-popup')) {
            event.preventDefault();
            $(this).removeClass('is-visible');
        }
    });
    //close popup when clicking the esc keyboard button
    $(document).keyup(function (event) {
        if (event.which == '27') {
            $('.cd-popup').removeClass('is-visible');
        }
    });

    /* 页面跳转选中 样式 */
    $('.footer li a').each(function () {
        if ($($(this))[0].href == String('http://' + window.location.host + window.location.pathname)) {
            $(this).addClass('fta');
        }
    });
    
});

function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]); return null;
}

/*消息提示*/
function dialog(txt) {
    $(document.body).append("<div id='ts_txt' class='ts_txt'><span>" + txt + "</span></div>");
    $('#ts_txt').fadeIn(500).delay(1000).fadeOut(500,  function () { $('.ts_txt').remove() });
}


/* 根据屏幕大小设置html的font-size */
var cssE1 = document.createElement('style');
document.documentElement.firstElementChild.appendChild(cssE1);
function setPxPerRem() {
    var dpr = 1;
    var pxPerPem = document.documentElement.clientWidth * dpr / 10;
    if (document.documentElement.clientWidth <= 640) {
        cssE1.innerHTML = 'html{font-size:' + pxPerPem + 'px !important;}';
    } else {
        cssE1.innerHTML = 'html{font-size:64px !important;}';
    }
}
setPxPerRem();

//没有上一页时返回主页
function GoBack() {
    if ((navigator.userAgent.indexOf('MSIE') >= 0) && (navigator.userAgent.indexOf('Opera') < 0)) { // IE  
        if (history.length > 0) {
            window.history.go(-1);
        }
    } else { //非IE浏览器  
        if (navigator.userAgent.indexOf('Firefox') >= 0 ||
            navigator.userAgent.indexOf('Opera') >= 0 ||
            navigator.userAgent.indexOf('Safari') >= 0 ||
            navigator.userAgent.indexOf('Chrome') >= 0 ||
            navigator.userAgent.indexOf('WebKit') >= 0) {
            if (window.history.length >= 2) {
                window.history.go(-1);
            }
        } else {//未知的浏览器  
            if (history.length > 1) {
                window.history.go(-1);
            }
        }
    }
}

