// var kad_search_url = urlConfig.search;

function Suggest() {
    this.objParent = null;
    this.delaySec = 100;
    this.cursor = -1;
    this.objAjax = null;
    this.layerWidth = 300;
    this.result = 0;
    this.taskID = null;
    this.keyword = null;
    this.startIndex = 0;
}
var sug = new Suggest();
var keyword;
var mydivId;
var re = /<(.[^>]*)>/g;
var regValue = /^[^@@\/\'\\\"#$%&\^\*\、\{\}\[\]\【\】\。\，\｛\｝\：\；\+\/\·\！\（\）\￥\……\——\<\>\《\》\？\～\`\=\,\.\?\;\:\‘\“\'\"\;\”\_\~\!\(\)\|\｜]+$/;
function SearchText(obj, divId) {
    mydivId = divId;
    var txtKey = obj.value;
    sug.objParent = obj;
    sug.CreateElement(obj);
    var inputText = txtKey.replace(/^s+|\s+$/g, "");
    if (inputText != "" && keyword != inputText && regValue.test(inputText)) {
        keyword = inputText;
        DataSource(inputText);
    }
}
function DataSource(txtKey) {
    $.ajax({
        type: "GET",
        url: kad_search_url + "/Home/SearchPanGuWordResult?KeyWord=" + encodeURIComponent(txtKey),
        type: "Get", cache: true, dataType: "jsonp", json: "callback",
        success: function (data) {
            if (data != null) {
                sug.SearchResult(txtKey, data);
            }
        }
    });
}


Suggest.prototype.SearchResult = function (txt, result) {
    if (document.getElementById("findContent")) {
        if (result.length != 0) {
            this.FillResult(result);

        } else {
            this.Hidden();
        }
    } else {
        alert("未找到容器!");
    }
}
Suggest.prototype.FillResult = function (aryResult) {
    this.result = 0;
    var findList = document.getElementById("findList");
    var findListArray = findList.getElementsByTagName("li");
    if (findListArray.length > 0) {
        for (var i = findListArray.length - 1; i >= 0; i--) {
            findList.removeChild(findListArray[i]);
        }
    }

    $(document).ready(function () {
        $("#txtSearchbox1 #findContent").hover(function () { $(this).show(); }, function () { $(this).hide() });
    });
    this.result = aryResult.length;
    if (this.result > 0) { document.getElementById("findContent").style.display = "block"; }
    else { document.getElementById("findContent").style.display = "none"; }
    for (var i = 0; i < this.result; i++) {
        var This = this;
        var findLI = document.createElement("li");
        //var findspan = document.createElement("span");
        findLI.id = "li" + i;
        findLI.cursor = i;
        findLI.className = "sealist";
        findLI.style.color = "black";
        findLI.style.cursor = "pointer";
        findLI.style.fontSize = "12px";
        findLI.style.lineHeight = "22px";
        findLI.style.padding = "0px 4px 0px 4px";
        findLI.style.width = "450px";
        //findspan.style.cssFloat = "right";
        //findspan.style.color = "#9b9b9b";
        //findspan.style.margin = "0px 0px 0px 4px";
        if (screen.width > 1200) { findLI.style.width = "450px"; }
        findLI.onclick = function () {
            var txtSearch = this.innerHTML.replace(re, "");
            This.cursor = this.cursor; This.AddToInput(txtSearch);
            var Seaurl = kad_search_url + '/?type=1&pageText=' + encodeURIComponent(txtSearch); window.location.href = Seaurl;
        };  //改善搜索跳转
        findLI.onmouseover = function () { this.style.background = "#f9f5e2"; This.cursor = this.cursor; };
        findLI.onmouseout = function () { this.style.background = "#fff"; this.style.color = "black"; };
        findList.appendChild(findLI);
        findLI.innerHTML = aryResult[i].KeyWord;
        //findLI.appendChild(findspan);
        //findspan.innerHTML = aryResult[i].ResultCount + "件商品 ";

    }
    findList.style.height = (this.result > 10 ? 220 : 22 * this.result) + "px";

}
Suggest.prototype.CreateElement = function (objParent) {
    if (!objParent) { alert("object not found!"); return (false); }
    var This = this;
    if (this.objParent && this.objParent != objParent) { this.Hidden(); }
    this.objParent = objParent;
    this.objParent.onkeyup = function (event) { This.ReSearch(event); };
    this.objParent.onkeydown = function (event) { This.MoveCursor(event); };
    this.layerWidth = this.objParent.offsetWidth;
    if (!document.getElementById("findContent")) {
        var findContent = document.createElement("div");
        findContent.id = "findContent";
        //findContent.style.width = (this.layerWidth - 2) + "px";
        findContent.style.width = "456px";
        if (screen.width > 1200) { findContent.style.width = "456px"; }
        findContent.style.zIndex = "100";
        findContent.style.position = "absolute";
        findContent.style.background = "#fff";
        findContent.style.border = "1px solid #2d8ef3";
        findContent.style.display = "none";
        var findList = document.createElement("ul");
        findList.id = "findList";
        findList.style.width = "456px";
        if (screen.width > 1200) { findList.style.width = "456px"; }
        findList.style.listStyle = "none";
        findList.style.margin = "0px 0px 0px 0px";
        findList.style.padding = "0px 0px 0px 0px";
        findList.style.textAlign = "left";
        findList.style.height = "220px";
        findList.style.overflowX = "hidden";
        findList.style.overflowY = "auto";
        findContent.appendChild(findList);
        document.getElementById(mydivId).appendChild(findContent);
        this.Position(findContent);
    }
    else {
        this.Position(document.getElementById("findContent"));
    }
    clearTimeout(this.taskID);
}

Suggest.prototype.Position = function (findContent) {
    var selectedPosX = 2;
    var selectedPosY = -1;
    theElement = this.objParent;

    while (theElement != null) {
        selectedPosX += theElement.offsetLeft;
        selectedPosY += theElement.offsetTop;
        theElement = theElement.offsetParent;
    }
    findContent.style.left = "1px";
    findContent.style.top = "38px";
}

Suggest.prototype.AddToInput = function (el) {
    this.objParent.value = el;
    this.Hidden();
}

Suggest.prototype.MoveCursor = function (event) {
    var e = event || window.event;
    switch (e.keyCode) {
        case 38:
            if (this.result > 0) { this.MoveToCurrent(-1); }
            break;
        case 40:
            if (this.result > 0) { this.MoveToCurrent(1); }
            break;
        case 13:
            if (this.result > 0) { if (this.cursor < 0) { this.CheckFormat(); this.Hidden(); } else { this.CheckFormat(); } return (false); }
            break;
        case 27:
            this.Hidden();
            break;
        default:
            return (false);
            break;
    }
}

Suggest.prototype.ReSearch = function (event) {
    var e = event || window.event;
    if (e.keyCode == 38 || e.keyCode == 40) { return (false); }
    this.objParent = e.target || e.srcElement;
    this.cursor = -1;
    if (this.objParent.value.replace(/(^\s*)|(\s*$)/g, "") != "") { SearchText(this.objParent); } else { this.Hidden(); }
}


Suggest.prototype.MoveToCurrent = function (step) {
    var el1 = document.getElementById("li" + this.cursor);
    if (el1) { el1.style.background = "#fff"; el1.style.color = "black"; }
    this.cursor += step;
    if (this.cursor < 0) { this.cursor = 0; }
    if (this.cursor >= this.result) { this.cursor = this.result - 1; }
    var el2 = document.getElementById("li" + this.cursor);
    if (el2) { el2.style.background = "lightgreen"; el2.style.color = "black"; }
    this.startIndex += step;
    if (this.startIndex > 9) { this.startIndex = 9; }
    if (this.startIndex < 0) { this.startIndex = 0; }
    if (this.cursor > 9) {
        if (this.startIndex == 0) { document.getElementById("findList").scrollTop = (this.cursor) * 22; }
        if (this.startIndex == 9) { document.getElementById("findList").scrollTop = (this.cursor - 9) * 22; }
    } else {
        document.getElementById("findList").scrollTop = 0;
    }
}
Suggest.prototype.CheckFormat = function () {
    if (!/[\W]/g.test(keyword)) {
        if (this.SelectedValue() == undefined) {
            this.AddToInput((this.SelectedFristValue().innerHTML).replace(re, ""));
        } else {
            this.AddToInput((this.SelectedValue().innerHTML).replace(re, ""));
        }
    }
    else {
        this.AddToInput((this.SelectedValue().innerHTML).replace(re, ""));
    }
}

Suggest.prototype.SelectedValue = function () {
    return document.getElementById("findList").getElementsByTagName("li")[this.cursor];
}

Suggest.prototype.Hidden = function () {
    this.cursor = -1;
    if (document.getElementById("findContent")) {
        document.getElementById("findContent").style.display = "none";
    }
}
Suggest.prototype.SelectedFristValue = function () {
    return document.getElementById("findList").getElementsByTagName("li")[0];
}

//关键字刷新自动变化
var SearchTxt = new Array('六味地黄丸', '班赛', '五子衍宗丸', '洁悠神', '克林霉素磷酸酯凝胶', '过氧苯甲酰凝胶', '米诺地尔', '锁阳固精丸', '洁悠神', '爱乐维'), SearchTxtId = Math.floor(Math.random() * SearchTxt.length), SearchValue = SearchTxt[SearchTxtId];
$(document).ready(function () {
    jQuery("#pageText").val(SearchValue).bind("focus", function () { if (this.value == SearchValue) { this.value = ""; this.style.color = "#333" } }).bind("blur", function () { if (!this.value) { this.value = SearchValue; this.style.color = "#999" } });
});

//jQuery.cookie
jQuery.cookie = function (name, value, options) { if (typeof value != 'undefined') { options = options || {}; if (value === null) { value = ''; options.expires = -1; } var expires = ''; if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) { var date; if (typeof options.expires == 'number') { date = new Date(); date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000)); } else { date = options.expires; } expires = '; expires=' + date.toUTCString(); } var path = options.path ? '; path=' + (options.path) : ''; var domain = options.domain ? '; domain=' + (options.domain) : ''; var secure = options.secure ? '; secure' : ''; document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join(''); } else { var cookieValue = null; if (document.cookie && document.cookie != '') { var cookies = document.cookie.split(';'); for (var i = 0; i < cookies.length; i++) { var cookie = jQuery.trim(cookies[i]); if (cookie.substring(0, name.length + 1) == (name + '=')) { cookieValue = decodeURIComponent(cookie.substring(name.length + 1)); break; } } } return cookieValue; } };
//end jQuery.cookie
//cookie操作 2012-8-14
$(document).ready(function () {
    if (jQuery(".topcollect").length == 1 && jQuery.cookie("topcollect") != 0) { jQuery(".topcollect").slideDown(300); jQuery("#nocollectkad").click(function () { jQuery(".topcollect").slideUp(250); jQuery.cookie("topcollect", "0", { expires: 7, domain: '360kad.com', path: '/' }); }); }
    $("#closecollect").click(function () { $("#topcollect").hide(); }, function () { $("#topcollect").show();; });
    $(".topcollect").hover(function () { $(this).addClass("topcollecthover"); }, function () { $(this).removeClass("topcollecthover"); });
});