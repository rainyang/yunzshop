$(function(){
    bListSortOpen();
});
function bListSortOpen(){
    var _bListSort= $("#bListSort");
    var _bListSortMain= $("#bListSortMain");
    if(_bListSort.length>0){
        _bListSort.find("ul li").on("click",function(){
            $(window).scrollTop(_bListSort.offset().top);
            bListSortMainFn($(this).index(),_bListSortMain);
            console.log(_bListSort.offset().top);
            $.fromLayer({
                "color":"#000",
                "close":false,
                "zIndex":99,
                "id":"bListSort",
                "callback":function(){
                    console.log("ok");
                }
            });
            _bListSort.find(".b").addClass("in");
            $(".fromLayerbListSort").on("click",function(){
                console.log("bListSortClose");
                bListSortClose(_bListSort,_bListSortMain);
            });
        });
    }
}
function bListSortClose(_bListSort,_bListSortMain){
    console.log("bListSortClose");
    _bListSortMain.find("ul li").hide();
    _bListSortMain.hide();
    $.fromLayerClose({
        "id":"bListSort",
        "callback":function(){
            console.log("close");
        }
    });
    _bListSort.find(".b").removeClass("in");

}
function bListSortMainFn(i,_bListSortMain){
    _bListSortMain.find("ul li").hide();
    _bListSortMain.find("ul li").eq(i).show();
    _bListSortMain.show();
}

/*!
 * jQuery Cookie Plugin v1.4.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2006, 2014 Klaus Hartl
 * Released under the MIT license
 */
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD (Register as an anonymous module)
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node/CommonJS
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {

    var pluses = /\+/g;

    function encode(s) {
        return config.raw ? s : encodeURIComponent(s);
    }

    function decode(s) {
        return config.raw ? s : decodeURIComponent(s);
    }

    function stringifyCookieValue(value) {
        return encode(config.json ? JSON.stringify(value) : String(value));
    }

    function parseCookieValue(s) {
        if (s.indexOf('"') === 0) {
            // This is a quoted cookie as according to RFC2068, unescape...
            s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
        }

        try {
            // Replace server-side written pluses with spaces.
            // If we can't decode the cookie, ignore it, it's unusable.
            // If we can't parse the cookie, ignore it, it's unusable.
            s = decodeURIComponent(s.replace(pluses, ' '));
            return config.json ? JSON.parse(s) : s;
        } catch(e) {}
    }

    function read(s, converter) {
        var value = config.raw ? s : parseCookieValue(s);
        return $.isFunction(converter) ? converter(value) : value;
    }

    var config = $.cookie = function (key, value, options) {

        // Write

        if (arguments.length > 1 && !$.isFunction(value)) {
            options = $.extend({}, config.defaults, options);

            if (typeof options.expires === 'number') {
                var days = options.expires, t = options.expires = new Date();
                t.setMilliseconds(t.getMilliseconds() + days * 864e+5);
            }

            return (document.cookie = [
                encode(key), '=', stringifyCookieValue(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path    ? '; path=' + options.path : '',
                options.domain  ? '; domain=' + options.domain : '',
                options.secure  ? '; secure' : ''
            ].join(''));
        }

        // Read

        var result = key ? undefined : {},
        // To prevent the for loop in the first place assign an empty array
        // in case there are no cookies at all. Also prevents odd result when
        // calling $.cookie().
            cookies = document.cookie ? document.cookie.split('; ') : [],
            i = 0,
            l = cookies.length;

        for (; i < l; i++) {
            var parts = cookies[i].split('='),
                name = decode(parts.shift()),
                cookie = parts.join('=');

            if (key === name) {
                // If second argument (value) is a function it's a converter...
                result = read(cookie, value);
                break;
            }

            // Prevent storing a cookie that we couldn't decode.
            if (!key && (cookie = read(cookie)) !== undefined) {
                result[name] = cookie;
            }
        }

        return result;
    };

    config.defaults = {};

    $.removeCookie = function (key, options) {
        // Must not alter options, thus extending a fresh object...
        $.cookie(key, '', $.extend({}, options, { expires: -1 }));
        return !$.cookie(key);
    };

}));
var lwfn = $.extend({}, lwfn);
lwfn.bp = function() {
    var curWwwPath = window.document.location.href;
    var pathName = window.document.location.pathname;
    var pos = curWwwPath.indexOf(pathName);
    var localhostPaht = curWwwPath.substring(0, pos);
    var projectName = pathName.substring(0, pathName.substr(1).indexOf('/') + 1);
    //return "http://125.211.217.199";//(localhostPaht + projectName);
    return (localhostPaht + projectName);
//	return "http://www.dingpaivip.com"//(localhostPaht + projectName);
};
//根据系统时间毫秒生成随机数
lwfn.dateRandom = function(){
    var myDate = new Date();
    return myDate.getTime();
};

/* 分页 */
jQuery.extend({
    pageLwFn:function (name,pageNow,pageRow) {
        var style = "";
        var m = "";
        var listpageNow = parseInt(pageNow);
        var listpageRow = parseInt(pageRow);

        m += "<div class='pageLw-style pageLw-style-"+style+"'>";
        if((pageNow-1)>0){
            m += "<a class='a2' href=\"javascript:"+name+"("+(pageNow-1)+")\"></a>";
        }
        if(listpageNow>3){
            //m +='<div class="l">'
            m += "<a class='a1' href=\"javascript:"+name+"(1)\">1</a>";
            if(listpageNow>4){
                m += "<a class='a1' href=\"javascript:"+name+"(2)\">2</a>";
                if(listpageNow>5){
                    m += '<span class="s1">……</span>';
                }
            }
        }
        if((listpageNow-2)>0){m += "<a class='a1' href=\"javascript:"+name+"("+(listpageNow-2)+")\">"+(listpageNow-2)+"</a>";}
        if((listpageNow-1)>0){m += "<a class='a1' href=\"javascript:"+name+"("+(listpageNow-1)+")\">"+(listpageNow-1)+"</a>";}

        m += '<span class="s2">'+(listpageNow)+'</span>';


        if((listpageNow+1)<=listpageRow){m += "<a class='a1' href=\"javascript:"+name+"("+(listpageNow+1)+")\">"+(listpageNow+1)+"</a>";}
        if((listpageNow+2)<=listpageRow){m += "<a class='a1' href=\"javascript:"+name+"("+(listpageNow+2)+")\">"+(listpageNow+2)+"</a>";}

        if(listpageRow>(listpageNow+4)){
            m += '<span class="s1">……</span>';
        }
        if(listpageRow>(listpageNow+3)){
            m += "<a class='a1' href=\"javascript:"+name+"("+(listpageRow-1)+")\">"+(listpageRow-1)+"</a>";
        }
        if(listpageRow>(listpageNow+2)){
            m += "<a class='a1' href=\"javascript:"+name+"("+listpageRow+")\">"+listpageRow+"</a>";
        }
        //m += '  <div class="pageLw-go">';
        //m += '      <input type="text" value=""/>';
        //m += '      <button class="btn-red" onclick="pageLw_go($(this),\''+name+'\')">GO</button>';
        //m += '  </div>';
        //m += '  </div>';
        //m += '  <div class="r">'
        //m += '  </div>';
        //m += '  <div class="clearBoth">'
        if(pageNow<pageRow){
            m += "<a class='a3' href=\"javascript:"+name+"("+(pageNow+1)+")\"></a>";
        }

        $("."+name).html(m);

    }
});

function pageLw_go(_this,name){
    var temp = _this.siblings("input").val();
    var a = /[^0-9]+/;
    if(temp){
        if(!a.test(temp)){
            eval(name+"("+temp+")");
        }
        else{
            $.notify("top","center","只能是数字","btn-red",3000);
        }
    }
    else{
        $.notify("top","center","不能为空","btn-red",3000);
    }


}



//通告组件生成函数
//y 参数top bottom ， x 参数 left center right ，val 参数  显示的字，type 默认default 其他的如 ， time 参数 0 其他 如果等于0 表示不自动关闭 单位毫秒
jQuery.extend({
    notify:function (y,x,val,type,time){
        if($(".notify-top-center").length<=0){
            var mmm = "<div class='notify-top-center'></div>";
            mmm += "<div class='notify-top-left'></div>";
            mmm += "<div class='notify-top-right'></div>";
            mmm += "<div class='notify-bottom-center'></div>";
            mmm += "<div class='notify-bottom-left'></div>";
            mmm += "<div class='notify-bottom-right'></div>";
            $("body").append(mmm);
        }
        var randomR = lwfn.dateRandom();
        if(y=="top"){
            var m = "";
            m += "<div class='notify-box notify-box-"+randomR+"'>";
            m += "	<div class='notify-kong'></div>";
            m += "	<div class='notify-sub "+type+"'>"+val+"</div>";
            m += "</div>";
            m += "";
            m += "";
            $(".notify-top-"+x).prepend(m);
            var h = $(".notify-box-"+randomR).find(".notify-sub").height()+$(".notify-kong").height();
            $(".notify-box-"+randomR).css({"margin-top":-h});
            $(".notify-box-"+randomR).stop().animate({"margin-top":0},500);
            $(".notify-box-"+randomR).find(".notify-sub").stop().fadeTo(500,0.98);
            $(document).on("click",".notify-box-"+randomR,function(){
                $.notify_close(randomR,h,"top");
            });
            if(time>0){
                setTimeout(function(){$.notify_close(randomR,h,"top");},time);
            }
        }
        else if(y=="bottom"){
            var m = "";
            m += "<div class='notify-box notify-box-"+randomR+"'>";
            m += "	<div class='notify-kong'></div>";
            m += "	<div class='notify-sub "+type+"'>"+val+"</div>";
            m += "</div>";
            m += "";
            m += "";
            $(".notify-bottom-"+x).append(m);
            var h = $(".notify-box-"+randomR).find(".notify-sub").height()+$(".notify-kong").height();
            $(".notify-box-"+randomR).css({"margin-bottom":-h});
            $(".notify-box-"+randomR).stop().animate({"margin-bottom":0},500);
            $(".notify-box-"+randomR).find(".notify-sub").stop().fadeTo(500,0.98);
            $(document).on("click",".notify-box-"+randomR,function(){
                notify_close(randomR,h,"bottom");
            });
            if(time>0){
                setTimeout(function(){notify_close(randomR,h,"top");},time);
            }
        }
    }
});

//通告组件关闭销毁函数
jQuery.extend({
    notify_close:function(randomR,h,type){
        $(".notify-box-"+randomR).find(".notify-sub").stop().fadeOut(500);
        if(type=="top"){
            $(".notify-box-"+randomR).stop().animate({"margin-top":-h},500,function(){$(".notify-box-"+randomR).remove();});
        }
        else if(type=="bottom"){
            $(".notify-box-"+randomR).stop().animate({"margin-bottom":-h},500,function(){$(".notify-box-"+randomR).remove();});
        }
        $(document).off("click",".notify-box-"+randomR);
    }
});





//生成透明背景遮罩层
//color 背景然后 #000, fade 背景透明度 0.8, zIndex 优先级  100, id 唯一标识 lwfn.dateRandom();
//$.fromLayer({
//    "color":"#000",
//    "close":false,
//    "callback":function(){
//        console.log("ok");
//    }
//});
$.extend({
    fromLayer:function(options){
        var default_options = {
            "color":"#000",
            "fade":"0.5",
            "zIndex":1000,
            "id":"fromLayer",
            "close":true,
            "callback":function(){
            }
        }
        this.options = $.extend({},default_options,options);
        if($(".fromLayer"+this.options.id).length>=1){return;}
        var m = "<div class='fromLayer fromLayer"+this.options.id+"' style='width:100%; height:100%; background:"+this.options.color+";z-index:"+this.options.zIndex+"; display:none; position: fixed; top:0px; left:0px;'></div>";
        $("body").prepend(m);
        var _options = this.options;
        $(".fromLayer"+this.options.id).stop().fadeTo(500,this.options.fade,function(){
            if(_options.close){
                $(".fromLayer"+_options.id).click(function(){
                    //console.log(_options.id);
                    $.fromLayerClose({
                        "id":_options.id
                    });
                });
            }
            _options.callback();
        });
        return this.options.id;
    }
});

//销毁透明背景
$.extend({
    fromLayerClose:function(options){
        //console.log(22);
        var default_options = {
            "id":"",
            "callback":function(){
            }
        }

        this.options = $.extend({},default_options,options);
        var _options = this.options;
        $(".fromLayer"+this.options.id).stop().fadeOut(500,function(){
            $(".fromLayer"+_options.id).remove();
            _options.callback();
        });
    }
});


