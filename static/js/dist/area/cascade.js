var _provinceNetworkData =null;
var _cityNetworkData =null;
var _districtNetworkData =null;

var dropElement1 = document.getElementById("sel-provance");
var dropElement2 = document.getElementById("sel-city");
var dropElement3 = document.getElementById("sel-area");
function cascdeInit(v1,v2,v3){
 
   getProvinceData(v1,v2,v3);
}
// 获取省数据
function getProvinceData(v1,v2,v3){
    $.ajax({
        url:'../addons/sz_yi/static/js/dist/area/province.min.json',
        type:'GET', //GET
        async:true,    //或false,是否异步
        timeout:5000,    //超时时间
        dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
        beforeSend:function(xhr){
            console.log('发送前')
        },
        success:function(data,textStatus,jqXHR){
            _provinceNetworkData = data;
            v1 = conversionV1(v1);
            getCityData(v1,v2,v3);
            console.log(data)
        },
        error:function(xhr,textStatus){
            console.log('错误')
            console.log(xhr)
            console.log(textStatus)
        },
        complete:function(){
            console.log('结束')
        }
    }) 
}
// 获取城市数据
function getCityData(v1,v2,v3){
    $.ajax({
        url:'../addons/sz_yi/static/js/dist/area/city.min.json',
        type:'GET', //GET
        async:true,    //或false,是否异步
        timeout:5000,    //超时时间
        dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
        beforeSend:function(xhr){
            console.log('发送前')
        },
        success:function(data,textStatus,jqXHR){
            _cityNetworkData = data;
            console.log(data)
            v2 = conversionV2(v2);
            getDistrictData(v1,v2,v3);
        },
        error:function(xhr,textStatus){
            console.log('错误')
            console.log(xhr)
            console.log(textStatus)
        },
        complete:function(){
            console.log('结束')
        }
    }) 
}
// 获取区数据
function getDistrictData(v1,v2,v3){
    $.ajax({
        url:'../addons/sz_yi/static/js/dist/area/district.min.json',
        type:'GET', //GET
        async:true,    //或false,是否异步
        timeout:5000,    //超时时间
        dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
        beforeSend:function(xhr){
            console.log('发送前')
        },
        success:function(data,textStatus,jqXHR){
            console.log(data)
            _districtNetworkData = data;
            v3 = conversionV3(v3);
            setProvinceData(v1,v2,v3);
        },
        error:function(xhr,textStatus){
            console.log('错误')
            console.log(xhr)
            console.log(textStatus)
        },
        complete:function(){
            console.log('结束')
        }
    }) 
}


// 设置省
function setProvinceData(v1,v2,v3){

    var _html = "";
    _html += '<option value="0">请选择省份</option>';
    for (var i = 0; i < _provinceNetworkData.length; i++) {
        var _selected = '';
        if (v1 == _provinceNetworkData[i].id) {
            _selected = 'selected="selected"';
        }
        _html += '<option '+_selected+' value="'+_provinceNetworkData[i].name+'">'+_provinceNetworkData[i].name+'</option>';
        
    }
    $("#sel-provance").html(_html);
    setCityData(v1,v2,v3);
}
//设置市
function setCityData(v1,v2,v3){
    var _html = '<option value="0">请选择城市</option>';

    for (var i = 0; i < _cityNetworkData.length; i++) {

        var _selected = '';
        if (v2 == _cityNetworkData[i].city_id) {
            _selected = 'selected="selected"';
        }
        if(v1 == _cityNetworkData[i].pid){
            _html += '<option '+_selected+' value="'+_cityNetworkData[i].name+'">'+_cityNetworkData[i].name+'</option>';
        }   
    }

    $("#sel-city").html(_html);
    setDistrictData(v1,v2,v3);
}
//设置区
function setDistrictData(v1,v2,v3){
    var _html = "";
    
    _html += '<option value="0">请选择区域</option>';
    for (var i = 0; i < _districtNetworkData.length; i++) {
        var _selected = '';
        if (v3 == _districtNetworkData[i].county_id) {
            _selected = 'selected="selected"';
        }
        if(v2 == _districtNetworkData[i].pid){
            _html += '<option '+_selected+' value="'+_districtNetworkData[i].name+'">'+_districtNetworkData[i].name+'</option>';
        }
    }
    $("#sel-area").html(_html);
}

/*
//依据省设置城市，县
*/
function selectCity() {
    var _provanceid = $("#sel-provance").val();
    v1 = conversionV1(_provanceid);
    setProvinceData(v1);
}

function selectcounty() {
    var _provanceid = $("#sel-provance").val();
    var _cityid = $("#sel-city").val();
    v1 = conversionV1(_provanceid);
    v2 = conversionV2(_cityid);
    setProvinceData(v1,v2);
}

//转换v1,v2,v3数据类型
function conversionV1(v1){
    for (var i = 0; i < _provinceNetworkData.length; i++) {
        if (v1 == _provinceNetworkData[i].name) {
            v1 = _provinceNetworkData[i].id;
            break;
        }
    } 
    return v1;
}
function conversionV2(v2){
    for (var i = 0; i < _cityNetworkData.length; i++) {
        if (v2 == _cityNetworkData[i].name) {
            v2 =  _cityNetworkData[i].city_id;
            break;
        }
    } 
    return v2;
}
function conversionV3(v3){
    for (var i = 0; i < _districtNetworkData.length; i++) {
        if (v3 == _districtNetworkData[i].name) {
            return _districtNetworkData[i].county_id;
            break;
        }
    }
    return v3;
}


// /**
//  * @name		jQuery Cascdejs plugin
//  * @author		zdy
//  * @version 	1.0 
//  */

// //首先需要初始化
// var xmlDoc;
// var TopnodeList;
// var citys;
// var countyNodes;
// var nodeindex = 0;
// var childnodeindex = 0;
// //获取xml文件
// function cascdeInit(v1,v2,v3) {
//     //打开xlmdocm文档
//     xmlDoc = loadXmlFile('../addons/sz_yi/static/js/dist/area/Area.xml?v=3');
//     var dropElement1 = document.getElementById("sel-provance");
//     var dropElement2 = document.getElementById("sel-city");
//     var dropElement3 = document.getElementById("sel-area");
//     RemoveDropDownList(dropElement1);
//     RemoveDropDownList(dropElement2);
//     RemoveDropDownList(dropElement3);
//     if (window.ActiveXObject) {
//         TopnodeList = xmlDoc.selectSingleNode("address").childNodes;
//     }
//     else {
//         TopnodeList = xmlDoc.childNodes[0].getElementsByTagName("province");      
//     }
//     if (TopnodeList.length > 0) {
//         //省份列表
//         var county;
//         var province;
//         var city;
//         for (var i = 0; i < TopnodeList.length; i++) {
//             //添加列表项目
//             county = TopnodeList[i];          
//             var option = document.createElement("option");
//             option.value = county.getAttribute("name");
//             option.text = county.getAttribute("name");
//             if (v1 == option.value) {
//                 option.selected = true;
//                 nodeindex = i;
//             }
//             dropElement1.add(option);
//         }
//         if (TopnodeList.length > 0) {
//             //城市列表
//             citys = TopnodeList[nodeindex].getElementsByTagName("city")
//             for (var i = 0; i < citys.length; i++) {
//                 var id = dropElement1.options[nodeindex].value;
//                 //默认为第一个省份的城市
//                 province = TopnodeList[nodeindex].getElementsByTagName("city");
//                 var option = document.createElement("option");
//                 option.value = province[i] .getAttribute("name");
//                 option.text = province[i].getAttribute("name");
//                 if (v2 == option.value) {
//                     option.selected = true;
//                     childnodeindex = i;
//                 }
//                 dropElement2.add(option);
//             }
// 			selectcounty(v3);
//         }
//     }
// }

// /*
// //依据省设置城市，县
// */
// function selectCity() {
    
//     var dropElement1 = document.getElementById("sel-provance");
//     var name = dropElement1.options[dropElement1.selectedIndex].value;     
//     countyNodes = TopnodeList[dropElement1.selectedIndex];      
//     var province = document.getElementById("sel-city");
//     var city = document.getElementById("sel-area");
//     RemoveDropDownList(province);
//     RemoveDropDownList(city);
//     var citynodes;
//     var countycodes;
//     if (window.ActiveXObject) {
//         citynodes = xmlDoc.selectSingleNode('//address/province [@name="' + name + '"]').childNodes;
//     } else {
//         citynodes = countyNodes.getElementsByTagName("city")
//     }
//     if (window.ActiveXObject) {
//         countycodes = citynodes[0].childNodes;
//     } else {
//         countycodes = citynodes[0].getElementsByTagName("county")
//     }
  
//     if (citynodes.length > 0) {
//         //城市
//         for (var i = 0; i < citynodes.length; i++) {
//             var provinceNode = citynodes[i];
//             var option = document.createElement("option");
//             option.value = provinceNode.getAttribute("name");
//             option.text = provinceNode.getAttribute("name");
//             province.add(option);
//         }
//         if (countycodes.length > 0) {
//             //填充选择省份的第一个城市的县列表
//             for (var i = 0; i < countycodes.length; i++) {
//                 var dropElement2 = document.getElementById("sel-city");
//                 var dropElement3 = document.getElementById("sel-area");
//                 //取当天省份下第一个城市列表
                
//                 //alert(cityNode.childNodes.length); 
//                 var option = document.createElement("option");
//                 option.value = countycodes[i].getAttribute("name");
//                 option.text = countycodes[i].getAttribute("name");
//                 dropElement3.add(option);
//             }
//         }
//     }
// }
// /*
// //设置县,区
// */
// function selectcounty(v3) {
//     var dropElement1 = document.getElementById("sel-provance");
//     var dropElement2 = document.getElementById("sel-city");
//     var name = dropElement2.options[dropElement2.selectedIndex].value;
//     var city = document.getElementById("sel-area");  
//     var countys = TopnodeList[dropElement1.selectedIndex].getElementsByTagName("city")[dropElement2.selectedIndex].getElementsByTagName("county")
//     RemoveDropDownList(city);
//     for (var i = 0; i < countys.length; i++) {
//         var countyNode = countys[i];
//         var option = document.createElement("option");
//         option.value = countyNode.getAttribute("name");
//         option.text = countyNode.getAttribute("name");
//         if(v3==option.value){
//         	option.selected=true;
//         }
//         city.add(option);
//     }
// }
// function RemoveDropDownList(obj) {
//     if (obj) {
//         var len = obj.options.length;
//         if (len > 0) {  
//             for (var i = len; i >= 0; i--) {
//                 obj.remove(i);
//             }
//         }
//     }
// }
// /*
// //读取xml文件
// */
// function loadXmlFile(xmlFile) {
//     var xmlDom = null;
//     if (window.ActiveXObject) {
//         xmlDom = new ActiveXObject("Microsoft.XMLDOM");
//         xmlDom.async = false;
//         xmlDom.load(xmlFile) || xmlDom.loadXML(xmlFile);//如果用的是XML字符串//如果用的是xml文件  
//     } else if (document.implementation && document.implementation.createDocument) {
//         var xmlhttp = new window.XMLHttpRequest();
//         xmlhttp.open("GET", xmlFile, false);
//         xmlhttp.send(null);
//         xmlDom = xmlhttp.responseXML;
//     } else {
//         xmlDom = null;
//     }
//     return xmlDom;
// }
