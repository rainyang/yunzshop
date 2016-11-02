$(function(){
    bListSortOpen();
    bListSortAddressNew();

});

function bListSortOpen(){
    var _bListSort= $("#bListSort");
    var _bListSortMain= $(".bListSortMain");
    if(_bListSort.length>0){
        _bListSort.find("ul li").on("click",function(){
            if(!$(this).hasClass("in")){
                $(window).scrollTop(_bListSort.offset().top+1);
                bListSortMainFn($(this).index(),_bListSortMain);
                _bListSort.find("ul li").removeClass("in");
                $(this).addClass("in");
                console.log("has");
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
                    bListSortClose();
                });
            }
            else{
                bListSortClose();
            }

        });
    }
}
function bListSortClose(){
    var _bListSort= $("#bListSort");
    var _bListSortMain= $(".bListSortMain");
    $.fromLayerClose({
        "id":"bListSort",
        "callback":function(){
            console.log("close");
            _bListSort.find("ul li").removeClass("in");
            _bListSort.find(".b").removeClass("in");
        }
    });
    _bListSortMain.hide();


}

function bListSortMainFn(i,_bListSortMain){
    _bListSortMain.hide();
    _bListSortMain.eq(i).show();
}


function bListSortAddressNew(){
    var _li1 = $(".bListSortMain2 ul .li1");
    if(_li1.length>0){
        _li1.find(".a1").on("click",function(){
            bListSortAddress($(this).index(),0);
        });
    }
}

function bListSortAddress(i1,i2){
    console.log(i1);
    console.log(i2);
    var _li1 = $(".bListSortMain2 ul .li1");
    var _li2 = $(".bListSortMain2 ul .li2");
    console.log(_li1.html());
    _li1.find(".a1").removeClass("in");
    _li2.find(".sub").removeClass("in");
    _li2.find(".sub .a1").removeClass("in");
    //
    _li1.find(".a1").eq(i1).addClass("in");
    _li2.find(".sub").eq(i1).addClass("in");
    _li2.find(".sub").eq(i1).find(".a1").eq(i2).addClass("in");
    var _html = _li2.find(".sub").eq(i1).find(".a1").eq(i2).html();
    $("#bListSort ul li").eq(1).find("span").html(_html);
}








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
//省、市、区、街道数据
var _xml;
var _xml1;
var _xml2;
var _xml3;
//省、市、区、街道缓存
var _data1;
var _data2;
var _data3;
var _data4;
$(function(){
    //获取xml数据
    $.ajax({
        url:"../addons/sz_yi/static/js/dist/area/Area.xml",dataType:"xml",
        error: function(xml){
            console.log('error');
        },
        success:function(xml){
            //截取省
            _xml =$(xml).find("address province");
            setHtml($("#sessionProvince").val(),$("#sessionCity").val());
        }
    })

    //选择省
    //$(".lwww1").on("click","div",function(){
    //    _data1 = $(this).html();
    //    setHtml(_data1);
    //});
    //选择市
    //$(".lwww2").on("click","div",function(){
    //    _data2 = $(this).html();
    //    setHtml(_data1,_data2);
    //});
    //选择区
    $(".bListSortMain2 .li1").on("click","span",function(){
        _data3 = $(this).html();
        setHtml(_data1,_data2,_data3);
    });
    //选择街道
    $(".bListSortMain2 .li2").on("click","span",function(){
        _data4 = $(this).html();
        console.log(_data1+"--"+_data2+"--"+_data3+"--"+_data4);
    });
});

//根据参数，解析数据
function setHtml(_v1,_v2,_v3){
    //默认显示省
    addHtml1(_xml,$(".lwww1"));
    if(_v1){
        _xml.each(function(){
            if(_v1==$(this).attr("name")){
                _xml1 = $(this).find("city");
                addHtml1(_xml1,$(".lwww2"));
            }
        });
        _data1 = _v1;
        _data2 = "";
        _data3 = "";
        _data4 = "";
    }
    if(_v2){
        _xml1.each(function(){
            if(_v2==$(this).attr("name")){
                _xml2 = $(this).find("county");
                addHtml1(_xml2,$(".bListSortMain2 .li1"));
            }
        });
        _data2 = _v2;
        _data3 = "";
        _data4 = "";
    }
    if(_v3){
        _xml2.each(function(){
            if(_v3==$(this).attr("name")){
                _xml3 = $(this).find("street");
                addHtml(_xml3,$(".bListSortMain2 .li2"));
            }
        });
        _data3 = _v3;
        _data4 = "";
    }
}
function addHtml(_this,_dom){
    var _html ="";
    _this.each(function(){
        _html +='<span class="a1" data-type="street">'+$(this).attr("name")+'</span>';
    });
    _dom.html(_html);
}function addHtml1(_this,_dom){
    var _html ="";
    _this.each(function(){
        _html +='<span class="a1">'+$(this).attr("name")+'</span>';
    });
    _dom.html(_html);
}

function getAddess(){
    if($(".b-address").length>0){
        $.ajax({
            url:"../addons/sz_yi/static/js/dist/area/Area.xml",dataType:"xml",
            error: function(xml){
                console.log('error');
            },
            success:function(xml){
                var _html="";
                $(xml).find("address province").each(function(){
                    _html +='<li>';
                    _html +='<div class="b-br">'+$(this).attr("name")+'</div>';

                    _xml =$(xml).find("address province");
                    $(this).find("city").each(function(){
                        _html +='<a class="selectcity" style="display:none">'+$(this).attr("name")+'</a>';
                    });
                    _html +='</li>';
                });
                $(".b-address .main ul").html(_html);
            }
        })
    }
}


