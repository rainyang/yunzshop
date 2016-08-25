//新版首页交互如下
function autoScroll(obj) {
    $(obj).find("ul").animate({
        marginTop: "-36px"
    }, 500, function () {
        $(this).css({ marginTop: "0px" }).find("li:first").appendTo(this);
    })
};

$(function () {

    $(".contextM .pinter dd:last-child").css("margin-right", "0px");
    $("#scroll_num span:last-child").css("margin-right", "0px");

    //全部分类
    var $nodesLi = $(".kinds_lists").find("li");
    $nodesLi.hover(function () {
        $(this).addClass('act');        
        $(this).find('.sideBox').show();
        $(this).find(".Line").show();
        $(this).find(".potiner_h").hide();
        $id = $(this).attr("id");
        $id = ($id.substring($id.length-1,$id.length));
        if($id==0){
           $margin = 1;  
        } else if($id>0 && $id<5){
           $margin =$id*40+1; 
        }else if($id>4 && $id<10){
           $margin = $id*40; 
        }
        $(this).find('.potiner_h').prev("a").css('color', '#036dde');
        $(this).find('.sideBox').css('margin-top',"-"+$margin+"px");

        
    }, function () {
        $(this).removeClass('act');
        $(this).find('.sideBox').hide();
        $(this).find(".Line").hide();
        $(this).find(".potiner_h").show();       
        $(this).find('.potiner_h').prev("a").css('color', '#ffffff');
    });

    //END全部分类
    var $linkBox = $(".footer_list980 .linkBox");
    $linkBox.hover(function () {
        $(this).parent().css('height', '66px');
    }, function () {
        $(this).parent().css('height', '22px');
    });

    //用药问答
    $(".kad-mc-ask").hover(function () {

        $(this).find(".kad-mc-askBox").css("display", "block");
        $(this).children("i").css("borderColor", "#0164ca transparent transparent transparent");
        $(this).css('color', '#0063c8');
    }, function () {

        $(this).find(".kad-mc-askBox").css("display", "none");
        $(this).css('color', '#141414');
        $(this).children("i").css("borderColor", "#000000 transparent transparent transparent");
    });

    //换一批产品
    change();
    function change() {

        var numIndex = 1;
        indexChange(1);
        $(".changeP .changeBtn").click(function () {

            indexChange(numIndex);

        });
        function indexChange() {
            jQuery.ajax({
                url: "/DataPlatform/GetIndexGuessLikeProducts?",
                type: "get",
                dataType: "jsonp",
                jsonp: "callback",
                data: "pagesize=5&pageindex=" + numIndex,
                cache: false,
                success: function (data) {
                    var page = parseInt(data.TotalPage);
                    if (numIndex >= page) {
                        numIndex = 1;
                    } else {
                        numIndex++;
                    };

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
                    if (data.PageIndex > 0) {
                        var changPro = "";

                        for (var i = 0; i < data.Data.length; i++) {
                            var newS = parseFloat(data.Data[i].SalePrice);
                            var fix = toDecimal2(newS);

                            var pic180 = (data.Data[i].Pic180 == "无" || data.Data[i].Pic180 == null) ? "http://res.360kad.com/theme/default/img/nopic.gif" : data.Data[i].Pic180;
                            changPro += "<li><a href='/product/" + data.Data[i].WareSkuCode + ".shtml?kzone=find_maybe_pcindex' target='_blank' class='imgpro' ><img style='width:180px;height:180px;' src='" + pic180 + "' title='" + data.Data[i].WareName + "'/></a><div class='hotPadding'><p class='nameC'><a href='/product/" + data.Data[i].WareSkuCode + ".shtml?kzone=find_maybe_pcindex' target='_blank' title='" + data.Data[i].WareName + "'>" + data.Data[i].WareName + "</a></p><p class='priceC'>￥" + fix + "</p></div></li>";

                        }

                        $(".changeP ul").html(changPro);
                        $("#changeP ul li:last-child").css("margin-right", "0px");

                    } else {
                        $(".changeP ul").html("<p>没有热销产品</p>")
                    }
                }
            })
        }
    }
    //秒杀产品
    var GetSecKill_EndTime = "";
    jQuery.ajax({
        url: "/product/GetSecKillList?channelCode=1032020&sort=4&type=1&externalCall=true",
        type: "get",
        dataType: "json",
        jsonp: "callback",
        cache: false,
        success: function (data) {
            /*如果产品小于5 则隐藏*/
            if (data.Data.length < 5) {
                $(".hotPro").hide();
                return false;
            }
            /*/如果产品小于5 则隐藏*/
            if (data.Data.length > 0) {
                /*取得秒杀结束时间*/
                GetSecKill_EndTime = data.Data[0].EndTime;
                /*/取得秒杀结束时间*/
                var splus = parseInt(data.Data.length / 5);
                if ((data.Data.length / 5) > splus) { splus = splus + 1; }
                $(".hotPage").html("<b class=\"reduction\">1</b><b>/</b><b class=\"plus\">" + splus + "</b>");
                var SeckillInfo = "";
                for (var i = 0; i < data.Data.length; i++) {
                    var Limit = data.Data[i].Store;
                    if (Limit == 0) {
                        SeckillInfo += "<li><div class=\"zhe\" style='background:url(http://res.360kad.com/theme/default/img/product/2016new/ze2.png) no-repeat;'><p class='f'>已抢完</p></div>";
                    } else {
                        SeckillInfo += "<li><div class=\"zhe\" style='background:url(http://res.360kad.com/theme/default/img/product/2016new/zebg.png) no-repeat;'><span class=\"zheS\">" + (data.Data[i].PrmPrice / data.Data[i].Price * 10).toFixed(1) + "</span>折</div>";
                    }
                    var pic180 = (data.Data[i].Pic180 == "无" || data.Data[i].Pic180 == null) ? "http://res.360kad.com/theme/default/img/nopic.gif" : data.Data[i].Pic180;
                    SeckillInfo += "<p style=\"text-align:center\"><a href='/product/" + data.Data[i].WareCode + ".shtml?kzone+jrtm' target='_blank'><img style=\"width:150px;height:150px;\" src=" + pic180 + " title='" + data.Data[i].WareName + "'/></a></p>"
                    SeckillInfo += "<div class=\"clum\"><dl><dd class=\"secentLi\"><a href='/product/" + data.Data[i].WareCode + ".shtml?kzone+jrtm' target='_blank' title='" + data.Data[i].WareName + "'>" + data.Data[i].WareName + "</a></dd>";
                    SeckillInfo += "<dd class=\"thirdLi\"><span class=\"newPrice\">￥" + data.Data[i].PrmPrice + "</span><span class=\"oldPrice\">￥" + data.Data[i].Price + "</span></dd></dl></div></li>";
                }
                $("#hotProC ul").html(SeckillInfo);
                $("#hotProC ul li:nth-child(5n)").css("border-right", "0px");
                /*秒杀倒计时*/
                var getTime_time = parseInt(new Date().getTime());
                var source = GetSecKill_EndTime;
                var rt = /(.+)?(?:\(|（)(.+)(?=\)|）)/.exec(source);
                TimeCountDonw(getTime_time, rt[2], 'TimeCountDonw_', null);
                /*/秒杀倒计时*/
                function hot() {
                    var timer = null;
                    var num2 = 0;
                    var page = 5;
                    var oUl2 = $("#hotProC ul");
                    var oLit = $("#hotProC ul li")
                    var oLi = $("#hotProC ul li").eq(0);
                    var oLiW2 = parseFloat(239.6);
                    var oUlW2 = (oLiW2 * oLit.length) + "px";
                    var pageN = parseInt(oLit.length / 5);
                    var oLiW5 = oLiW2 * page;
                    oUl2.css("width", oUlW2);
                    $(".reduction").text(num2 + 1);
                    $(".plus").text(pageN);
                    /*如果页数小于1 隐藏分页*/
                    //console.log($("#hotProC ul li").size());
                    if (pageN == 1 || $("#hotProC ul li").size() < 6) {
                        $(".hotProHeader .hotProR").hide();
                        $(".hotProHeader .hotProM").css("width", "1100px");
                    }
                    /*/如果页数小于1 隐藏分页*/
                    $(".direction .r").click(function () {
                        if (num2 >= pageN - 1) {
                            num2 = 0;
                        } else {
                            num2++;
                        }
                        $(".reduction").text(num2 + 1);
                        oUl2.stop().animate({
                            left: -num2 * oLiW5 + "px"
                        }, 500)

                    });

                    $(".direction .s").click(function () {
                        if (num2 <= 0) {
                            num2 = pageN - 1;
                        } else {
                            num2--;
                        }
                        $(".reduction").text(num2 + 1);
                        oUl2.stop().animate({
                            left: -num2 * oLiW5 + "px"
                        }, 500);

                    });
                    function run() {
                        if (num2 >= pageN - 1) {
                            num2 = 0;
                        } else {
                            num2++;
                        };
                        $(".reduction").text(num2 + 1);
                        oUl2.stop().animate({
                            left: -num2 * oLiW5 + "px"
                        }, 500)
                    };
                }
                hot();
            } else {

                $(".hotPro").hide();

            }
        }
    });


    //产品滑动
    scroll();
    function scroll() {
        var oULW = $(".scollImg ul");
        var oLi2 = $(".scollImg ul li")
        var oLi = $(".scollImg ul li").eq(0);
        var oLiW = oLi.outerWidth(true);
        var allW = (oLiW * oLi2.length) + "px";
        var num = 0;
        var speed = 400;
        var len = oLi2.length;
        oULW.css("width", allW);
        if (len <= 4) {
            $(".prv,.next").hide();
        } else {

            $(".prv").click(function () {

                oULW.find('li:last').prependTo(oULW);
                oULW.css({ 'margin-left': -oLiW });
                oULW.animate({ 'margin-left': 0 });

            })

            $(".next").click(function () {
                oULW.animate({ 'margin-left': -oLiW }, function () {
                    oULW.find('li').eq(0).appendTo(oULW);
                    oULW.css({ 'margin-left': 0 });
                });
            })


        }
    }

    //切换图片楼层
    function DY_scroll(wraper, left, right, pinter, img, speed, or) {
        var wraper = $(wraper);
        var oLi = $(img).find("li");
        var img = $(img).find('ul');
        var w = img.find('li:first').outerWidth(true);
        var s = speed;
        var tt = (w * oLi.length) + "px";
        var dIndex = 0;
        var oDd = $(pinter).find("dd");
        var ad = null;
        var oNext = $(left);
        var oPrev = $(right);

        img.css("wdith", tt);
        oPrev.css("display", "none");
        oNext.css("display", "none");
        if (or == true) {
            ad = setInterval(autoPlay, s * 1500);
            wraper.hover(
           function () {
               clearInterval(ad);
               oPrev.fadeIn();
               oNext.fadeIn();
           }, function () {
               ad = setInterval(autoPlay, s * 1500);
               oPrev.fadeOut();
               oNext.fadeOut();
           });

        }

        function autoPlay() {
            if (dIndex == oDd.length - 1) {
                dIndex = 0
            } else {
                dIndex++;
            }
            oDd.removeClass("actv");
            oDd.eq(dIndex).addClass("actv");
            img.animate({ 'left': -w * dIndex + "px" }, 300);
        }


        oDd.click(function (event) {
            event.stopPropagation();
            var pIndex = $(this).index();
            oDd.removeClass("actv");
            $(this).addClass("actv");
            img.animate({ 'left': -w * pIndex + "px" }, 300);

        });


        function go() {

            if (dIndex == oDd.length - 1) {
                dIndex = 0;
            } else {
                dIndex++;
            }
            oDd.removeClass("actv");
            oDd.eq(dIndex).addClass("actv");
            img.animate({ left: -w * dIndex + "px" }, 300);
            return false;
        }


        function go2() {
            if (dIndex <= 0) {
                dIndex = oDd.length - 1;
            } else {
                dIndex--;
            }
            oDd.removeClass("actv");
            oDd.eq(dIndex).addClass("actv");
            img.animate({ left: -w * dIndex + "px" }, 300);
            return false;
        }

        oPrev.click(function () { go(); });
        oNext.click(function () { go2(); });


    };

    DY_scroll('#contextM1', '#pinterPrv1', '#pinterNext1', '#pinter1', '#switchB1', 2, true);
    DY_scroll('#contextM2', '#pinterPrv2', '#pinterNext2', '#pinter2', '#switchB2', 3, true);
    DY_scroll('#contextM3', '#pinterPrv3', '#pinterNext3', '#pinter3', '#switchB3', 2, true);
    DY_scroll('#contextM4', '#pinterPrv4', '#pinterNext4', '#pinter4', '#switchB4', 3, true);
    DY_scroll('#contextM5', '#pinterPrv5', '#pinterNext5', '#pinter5', '#switchB5', 2, true);
    DY_scroll('#contextM6', '#pinterPrv6', '#pinterNext6', '#pinter6', '#switchB6', 3, true);
    DY_scroll('#contextM7', '#pinterPrv7', '#pinterNext7', '#pinter7', '#switchB7', 2, true);
    DY_scroll('#contextM8', '#pinterPrv8', '#pinterNext8', '#pinter8', '#switchB8', 3, true);
    DY_scroll('#contextM9', '#pinterPrv9', '#pinterNext9', '#pinter9', '#switchB9', 2, true);
    DY_scroll('#contextM10', '#pinterPrv10', '#pinterNext10', '#pinter10', '#switchB10', 3, true);



    //快报文字滚动
    var timerauto = null;
    timerauto = setInterval('autoScroll(".rightscroll")', 3000);
    $(".rightscroll").mouseover(function () {
        clearInterval(timerauto);
    }).mouseout(function () {
        timerauto = setInterval('autoScroll(".rightscroll")', 3000);
    })



    //轮播
    var intitNum = 0;
    var imgTimer = null;
    var $scroll_container = $("#scroll_container");
    var $scrollUl = $scroll_container.find("ul");
    var $scrollLi = $scrollUl.find("li");
    var $liSizes = $scrollLi.size();
    var $scrollNum = $("#scroll_num span");
    var $liWidth = $scrollLi.width();
    var $prevBtn = $scroll_container.find(".prevBtn");
    var $nextBtn = $scroll_container.find(".nextBtn");

    $scrollNum.mouseenter(function (event) {
        var _this = $(this).index();
        intitNum = _this;
        changeImg(_this);
    });

    //轮播切换
    function changeImg(obj) {
        $scrollLi.css('opacity', '0');
        $scrollNum.eq(obj).addClass('act').siblings().removeClass('act');
        $scrollLi.eq(obj).show().stop().animate({ opacity: 1 }, 600).siblings('li').hide();
    }
    //上一个
    $prevBtn.click(function () {
        intitNum <= 0 ? intitNum = $liSizes - 1 : intitNum--;
        changeImg(intitNum);
    });
    //下一个
    $nextBtn.click(function () {
        intitNum >= $liSizes - 1 ? intitNum = 0 : intitNum++;
        changeImg(intitNum);
    });
    //鼠标移入显示上下按钮
    $scroll_container.hover(function (event) {
        clearTimeout(imgTimer);
        $(".clickBtn").show();
    }, function () {
        imgAutoMove();
        $(".clickBtn").hide();
    });
    //下一个鼠标经过切换
    $(".prevBtn").hover(function () {
        $(this).addClass('preH');
    }, function () {
        $(this).removeClass('preH');
    });
    //上一个鼠标经过切换
    $(".nextBtn").hover(function () {
        $(this).addClass('nextH');
    }, function () {
        $(this).removeClass('nextH');
    });
    imgAutoMove();
    //自动轮播
    function imgAutoMove() {
        clearTimeout(imgTimer);
        imgTimer = setTimeout(function () {
            intitNum >= $liSizes - 1 ? intitNum = 0 : intitNum++;
            changeImg(intitNum);
            imgAutoMove();
        }, 4000);
    }

    //左侧导航切换状态
    var $fixed_lSide = $("#fixed_lSides");
    var $fixed_lSidesLi = $fixed_lSide.find('li');
    var $searchForm = $("#searchForm");
    var $fixed_searchBox = $("#fixed_searchBox");

    //点击导航滚屏
    $fixed_lSidesLi.click(function (event) {
        var _this = $(this).index();
        $("html,body").stop().animate({ scrollTop: $(".storey").eq(_this).offset().top - 60 }, 300);
        $(this).addClass('act_click').siblings('li').removeClass('act_click');
    });
    //搜索框冻结

    $("#fixed_B .categorys").mousemove(function () {

        $("#fixed_B .newSide").fadeIn("fast");

    });

    $("#fixed_B .categorys").mouseleave(function () {

        if ($("#fixed_B .newSide").has)
            $("#fixed_B .newSide").fadeOut("fast");

    })

    // $(window).scroll(function () {
    //     var $docuScrolV = $(document).scrollTop();
    //     if ($docuScrolV >= 600) {
    //         $("#fixed_B .leftSide").addClass("newSide");

    //         $fixed_searchBox.show();
    //         $searchForm.css({ "position": "fixed", "top": 5, "z-index": 8 });
    //         $(".header_l").css({ "width": "295px", "float": "left" });
    //     } else {
    //         $("#fixed_B .leftSide").hide();
    //         $("#fixed_B .leftSide").removeClass("newSide");
    //         $fixed_searchBox.hide();
    //         $searchForm.css({ "position": "", "top": "", "z-index": "" });
    //         $(".header_l").css({ "width": "342px", "float": "left" });

    //     }
    // });



});


function TimeCountDonw(time, endTime, timesWarpID, callback) {
    this.startTime = time;
    this.endTime = endTime;
    this.time = this.endTime - this.startTime;
    if (this.time <= 1000) {
        callback && typeof callback == 'function' && callback.call(null);
        return
    }
    var t, d, h, m, s, bd = document.getElementById(timesWarpID),
      foramt = function (a) {
          return a < 10 ? '0' + a : a
      };
    (function () {
        this.time -= 1000;
        d = Math.floor(this.time / 1000 / 60 / 60 / 24);
        h = Math.floor(this.time / 1000 / 60 / 60 % 24);
        m = Math.floor(this.time / 1000 / 60 % 60);
        s = Math.floor(this.time / 1000 % 60);
        if (d == 0) {
            t = '<i></i>剩余<span>' + foramt(h) + '</span>时<span>' + foramt(m) + '</span>分<span>' + foramt(s) + '</span>秒'
        } else {
            t = '<i></i>剩余<span>' + foramt(d) + '</span>天<span>' + foramt(h) + '</span>时<span>' + foramt(m) + '</span>分<span>' + foramt(s) + '</span>秒'
        }
        bd.innerHTML = t;
        if (this.time >= 1000) {
            setTimeout(arguments.callee, 1000);
            return
        };
        callback && typeof callback == 'function' && callback.call(null)
    })()
}

