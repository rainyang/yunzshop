(function($){
	var bigColorPicker = new function(){
		var currentPicker = null; //µ±Ç°°ó¶¨Ê°È¡Æ÷µÄ¶ÔÏó
		var allColorArray = [];//Õû¸öÑÕÉ«ÇøÓò·ÖÎªÇ°Á½ÁÐ£¬ºóÃæËùÓÐÁÐ·ÖÎª6¸ö¿é£¬¼ÆËã³ö¸÷¸öÇøÓòµÄÊý×éºó·ÅÈëÒ»¸ö×ÜµÄ¶þÎ¬Êý×éÀï
		var sideLength = 6;//Áù¸öÇøÓòµÄÃ¿¸ö±ß³¤
		this.init = function(){
			$("body").append('<div id="bigpicker" class="bigpicker"><ul class="bigpicker-bgview-text" ><li><div id="bigBgshowDiv"></div></li><li><input id="bigHexColorText" size="7" maxlength="7" value="#000000" /></li></ul><div id="bigSections" class="bigpicker-sections-color"></div><div id="bigLayout" class="biglayout" ></div></div>');
			
			$("#bigLayout").hover(function(){
				$(this).show();
			},function(){
				$(this).hide();
			}).click(function(){
				$("#bigpicker").hide();
			});
			
			//ÑÕÉ«Ê°È¡Æ÷textÊäÈë¿ò×¢²áÊÂ¼þ
			$("#bigHexColorText").keypress(function(){
				var text = $.trim($(this).val());
				$(this).val(text.replace(/[^A-Fa-f0-9#]/g,''));
				if(text.length <= 0)return;
				text = text.charAt(0)=='#'?text:"#" + text;
				var countChar = 7 - text.length;
				if(countChar < 0){
					text = text.substring(0,7);
				}else if(countChar > 0){
					for(var i=0;i<countChar;i++){
						text += "0";
					};
				}
				$("#bigBgshowDiv").css("backgroundColor",text);
			}).keyup(function(){
				var text = $.trim($(this).val());
				$(this).val(text.replace(/[^A-Fa-f0-9#]/g,''));	
			}).focus(function(){
				this.select();
			});	
			
			//µ¥»÷Ò³ÃæÑ¡ÔñÆ÷ÒÔÍâµÄµØ·½Ê±Ñ¡ÔñÆ÷Òþ²Ø
			$(document).bind('mousedown',function(event){
				if(!($(event.target).parents().andSelf().is('#bigpicker'))){
					$("#bigpicker").hide();
				}
			});			
		};
		
		/*
		 * callback:¿ÉÒÔÊÇundefined£¨²»´«²ÎÊýÊ±£©¡¢×Ö·û´®¡¢º¯Êý
		 * 		undefined£º°ÑÑ¡ÔñµÄÑÕÉ«ÉèÖÃµ½°ó¶¨bigColorpickerµÄÔªËØµÄvalueÉÏ¡£
		 * 		×Ö·û´®£º°ÑÑ¡ÔñµÄÑÕÉ«ÉèÖÃµ½idÎª"×Ö·û´®"µÄÔªËØµÄvalueÉÏ¡£
		 * 		º¯Êý£ºÖ´ÐÐ´«ÈëµÄº¯ÊýÒÔÊµÏÖ×Ô¶¨ÒåµÄ»ñÈ¡ÑÕÉ«ºóµÄ¶¯×÷¡£
		 * engine:¿ÉÒÔÊÇundefined¡¢p(»òP)¡¢l(»òL)£¬
		 * 		ÔÚbigColorpickerÕ¹ÏÖÑÕÉ«Ñ¡ÔñÇøÓòÐ¡¸ñÊ±ÓÐÁ½ÖÖÊµÏÖ·½Ê½£º
		 *     Ò»¡¢ÊÇÒ»ÕÅ±³¾°Í¼Æ¬£¬²ÉÓÃ¹â±ê¶¨Î»µÄ·½Ê½»ñÈ¡ÑÕÉ«¡£p(»òP)
		 *     ¶þ¡¢Ã¿¸öÐ¡¸ñ¾ÍÊÇÒ»¸öli£¬ÉèÖÃliµÄ±³¾°ÑÕÉ«¡£l(»òL)
		 *     ÊµÏÖ·½Ê½µÄ²»Í¬£¬Ð§ÂÊÓÐËù²îÒì£¬¿ÉÒÔ×Ô¼ºÔÚÊ¹ÓÃÊ±Ñ¡Ôñ£¬Ä¬ÈÏÊÇp(»òP)¡£
		 *     undefined£¨²»´«²ÎÊýÊ±£©£ºÊ¹ÓÃÄ¬ÈÏp(»òP)
		 * sideLength:ÉèÖÃÑÕÉ«ÇøÓòµÄ´óÐ¡È¡Öµ·¶Î§Îª2¡«10£¬Ä¬ÈÏÎª6,Ö»ÓÐengineÎªLÊ±²ÅÉúÐ§
		 */
		this.showPicker = function(callback,engine,sideLength_){
			
			if($("body").length > 0 && $("#bigpicker").length <= 0){
				bigColorPicker.init();	
			}
			
			var sl_ = parseInt(sideLength_,10);
			if(engine == "L" && !isNaN(sl_) && sl_ >=2 && sl_ <= 10){
				sideLength = sl_;
			}else{
				sideLength = 6;
			}
			
			this.getAllColorArray = function(){
				allColorArray = new Array(sideLength*3 + 2);
				//¼ÆËãµÚÒ»ÁÐµÄÊý×é
				var mixColorArray = [];
				var blackWhiteGradientArray = gradientColor(new RGB(0,0,0),new RGB(255,255,255));
				for(var i=0;i<blackWhiteGradientArray.length;i++){
					mixColorArray[i] = blackWhiteGradientArray[i];
				}
				var baseArray = [new RGB(255,0,0),new RGB(0,255,0),new RGB(0,0,255),new RGB(255,255,0),new RGB(0,255,255),new RGB(255,0,255),new RGB(204,255,0),new RGB(153,0,255),new RGB(102,255,255),new RGB(51,0,0)];
				mixColorArray = mixColorArray.concat(baseArray.slice(0, sideLength));
				allColorArray[0] = mixColorArray;
				
				//¼ÆËãµÚ¶þÁÐµÄÊý×é
				var blackArray = new Array(sideLength*2);
				for(var i=0;i<blackArray.length;i++){
					blackArray[i] = new RGB(0,0,0);
				}
				allColorArray[1] = blackArray;
				
				//¼ÆËãÁù¸öÇøÓòµÄÊý×é
				var cornerColorArray = new Array();//Áù¸öÔªËØ£¬Ã¿¸öÔªËØ·Å6¸öÇøÓòµÄËÄ½ÇÑÕÉ«¶þÎ¬Êý×é
				cornerColorArray.push(generateBlockcornerColor(0),generateBlockcornerColor(51),generateBlockcornerColor(102),generateBlockcornerColor(153),generateBlockcornerColor(204),generateBlockcornerColor(255));
				var count = 0;
				var halfOfAllArray1 = [];
				var halfOfAllArray2 = [];
				for(var i=0;i<cornerColorArray.length;i++){
					var startArray = gradientColor(cornerColorArray[i][0][0],cornerColorArray[i][0][1]);
					var endArray = gradientColor(cornerColorArray[i][1][0],cornerColorArray[i][1][1]);
					for(var j=0;j<sideLength;j++){
						if(i < 3){
							halfOfAllArray1[count] = gradientColor(startArray[j],endArray[j]);
						}else{
							halfOfAllArray2[count - sideLength*3] = gradientColor(startArray[j],endArray[j]);
							
						}
						count++;
					}
				}
				for(var i=0;i<halfOfAllArray1.length;i++){
					allColorArray[i + 2] = halfOfAllArray1[i].concat(halfOfAllArray2[i]);
				}
				
				//½«Êý×éÀïËùÓÐµÄRGBÑÕÉ«×ª»»³ÉHexÐÎÊ½
				for(var i=0;i<allColorArray.length;i++){
					for(var j=0;j<allColorArray[i].length;j++){
						allColorArray[i][j] = RGBToHex(allColorArray[i][j]);
					}
				}
			};
			
			this.getAllColorArray();
			
			$(this).data("bigpickerCallback",callback);
			
			if(engine){
				try{
					engine = engine.toUpperCase();
				}catch(e){
					engine = "P";
				}
			}
			
			if(engine == "L" ){
				$("#bigSections").unbind("mousemove").unbind("mouseout").removeClass("bigpicker-bgimage");
				var ulArray = new Array();
				for(var i=0;i<sideLength*3 + 2;i++){
					ulArray.push("<ul>");
					for(var j=0;j<sideLength*2;j++){
						ulArray.push("<li data-color='" + allColorArray[i][j] + "' style='background-color: " + allColorArray[i][j] + ";' ></li>");
					}
					ulArray.push("</ul>");
				}
				$("#bigSections").html(ulArray.join(""));
				var minBigpickerHeight = 90;
				var minBigpickerWidth = 129;
				var minSectionsHeight = minBigpickerHeight - 29;
				var minSectionsWidth = minBigpickerWidth - 1;
				
				var defaultSectionsWidth = (sideLength*3 + 2)*11 + 1;
				if(defaultSectionsWidth < minSectionsWidth){
					$("#bigSections li,#bigLayout").width(minSectionsWidth/(sideLength*3 + 2) - 2)
						    			.height(minSectionsHeight/(sideLength*2) - 1);
					$("#bigpicker").height(minBigpickerHeight).width(minBigpickerWidth);
					$("#bigSections").height(minSectionsHeight).width(minSectionsWidth);
				}else{
					$("#bigSections").width(defaultSectionsWidth)
					                 .height(sideLength*2*11);
					$("#bigpicker").width(defaultSectionsWidth + 5)
					               .height(sideLength*2*11 + 29);
				}
				
				$("#bigSections ul").find("li:last").css("border-bottom","1px solid #000000");
				$("#bigSections ul:last li").css("border-right","1px solid #000000");
				
				$("#bigSections li").hover(function(){
					var $this = $(this);
					$("#bigLayout").css({"left":$this.position().left,"top":$this.position().top}).show();
					var cor = $this.attr("data-color");
					$("#bigBgshowDiv").css("backgroundColor",cor);
					$("#bigHexColorText").val(cor);
					invokeCallBack(cor);				
				},function(){
					$("#bigLayout").hide();
				});
			}else{
				//Ô¤¼ÓÔØÍ¼Æ¬
			     var bgmage = new Image();
			     bgmage.src = "big_bgcolor.jpg";
			     //³õÊ¼»¯ÎªÄ¬ÈÏÑùÊ½
				$("#bigSections").height(134).width(222).addClass("bigpicker-bgimage").empty();
				$("#bigpicker").width(227).height(163);
				//PÄ£Ê½Ê±Êó±êÔÚÑÕÉ«Ð¡¸ñÉÏÒÆ¶¯Ê±»ñÈ¡ÑÕÉ«
				$("#bigSections").unbind("mousemove").unbind("mouseout").mousemove(function(event){
					var xSections = getSections(sideLength*3 + 2);
					var ySections = getSections(sideLength*3);
					var $this = $(this);
					var cursorXPos = event.pageX - $this.offset().left;
					var cursorYPos = event.pageY - $this.offset().top;
					var xi = 0;
					var yi = 0;
					for(var i=0;i<(sideLength*3+2);i++){
						if(cursorXPos >= xSections[i][0] && cursorXPos <= xSections[i][1]){
							xi = i;
							break;
						}
					}
					for(var i=0;i<(sideLength*3);i++){
						if(cursorYPos >= ySections[i][0] && cursorYPos <= ySections[i][1]){
							yi = i;
							break;
						}
					}
					$("#bigLayout").css({"left":$this.position().left + xi*11,"top":$this.position().top + yi*11}).show();
					var hex = allColorArray[xi][yi];
					$("#bigBgshowDiv").css("backgroundColor",hex);
					$("#bigHexColorText").val(hex);
					
					invokeCallBack(hex);
					
				}).mouseout(function(){
					$("#bigLayout").hide();
				});			
			}
			
			//¸ø´«ÈëµÄÊ°È¡ÑÕÉ«µÄÔªËØ°ó¶¨clickÊÂ¼þ£¬ÏÔÊ¾ÑÕÉ«Ê°È¡Æ÷
			$(this).bind("click",function(event){
				currentPicker = event.currentTarget;
				$("#bigBgshowDiv").css("backgroundColor","#000000");
				$("#bigHexColorText").val("#000000");
				
				var pos = calculatePosition ($(currentPicker));
				$("#bigpicker").css({"left":pos.left + "px","top":pos.top + "px"}).fadeIn(300);
				
				var bigSectionsP = $("#bigSections").position();
				$("#bigLayout").css({"left":bigSectionsP.left,"top":bigSectionsP.top}).show();
				
			});			
		};
		
		this.hidePicker = function(){
			$("#bigpicker").hide();
		};
		
		this.movePicker = function(){
			var pos = calculatePosition ($(currentPicker));
			$("#bigpicker").css({"left":pos.left + "px","top":pos.top + "px"});
			$("#bigLayout").hide();
		};
		
		
		//¼ÆËã³öÊ°È¡Æ÷²ãµÄleft,top×ø±ê
		function calculatePosition ($el) {
			var offset = $el.offset();
			var compatMode = document.compatMode == 'CSS1Compat';
			var w = compatMode ? document.documentElement.clientWidth : document.body.clientWidth;
			var h = compatMode ? document.documentElement.clientHeight : document.body.clientHeight;
			var pos = {left:offset.left,top:offset.top + $el.height() + 7};
			var $bigpicker = $("#bigpicker");
			if(offset.left + $bigpicker.width() > w){
				pos.left = offset.left - $bigpicker.width() - 7;
				if(pos.left < 0){
					pos.left = 0;
				}						
			}
			if(offset.top + $el.height() + 7 + $bigpicker.height() > h){
				pos.top = offset.top -  $bigpicker.height() - 7;
				if(pos.top < 0){
					pos.top = 0;
				}
			}
			return pos;
		}		
		
		//´´½¨Ð¡ÇøÓòµÄËÄ¸ö½ÇµÄÑÕÉ«¶þÎ¬Êý×é
		function generateBlockcornerColor(r){
			var a = new Array(2);
			a[0] = [new RGB(r,0,0),new RGB(r,255,0)];
			a[1] = [new RGB(r,0,255),new RGB(r,255,255)];			
			return a;
		}

		//Á½¸öÑÕÉ«µÄ½¥±äÑÕÉ«Êý×é
		function gradientColor(startColor,endColor){
			var gradientArray = [];
			gradientArray[0] = startColor;
			gradientArray[sideLength-1] = endColor;
			var averageR = Math.round((endColor.r - startColor.r)/sideLength);
			var averageG = Math.round((endColor.g - startColor.g)/sideLength);
			var averageB = Math.round((endColor.b - startColor.b)/sideLength);
			for(var i=1;i<sideLength-1;i++){
				gradientArray[i] =  new RGB(startColor.r + i*averageR,startColor.g + i*averageG,startColor.b + i*averageB);
			}			
			return gradientArray;
		}
		/*»ñÈ¡Ò»¸ö¶þÎ¬Çø¼äÊý×é±ÈÈç[0,11],[12,23],[24,37]..
		 * sl:Çø¼äµÄ³¤¶È
		*/
		function getSections(sl){
			var sections = new Array(sl);
			var initData = 0; 
			for(var i=0;i<sl;i++){
				var temp = initData + 1;
				initData += 11;
				sections[i] = [temp,initData];
			}			
			return sections;
		}
		
		function RGBToHex(rgb){
			var hex = "#";
			for(c in rgb){
				var h = rgb[c].toString(16).toUpperCase();
				if(h.length == 1){
					hex += "0" + h;
				}else{
					hex += h;
				}
			}
			return hex;
		}
		
		//RGB¶ÔÏóº¯Êý£¬ÓÃÓÚ´´½¨Ò»¸öRGBÑÕÉ«¶ÔÏó
		function RGB(r,g,b){
			this.r = Math.max(Math.min(r,255),0);
			this.g = Math.max(Math.min(g,255),0);
			this.b = Math.max(Math.min(b,255),0);
		}
		
		function invokeCallBack(hex){
			var callback_ = $(currentPicker).data("bigpickerCallback");
			if($.isFunction(callback_)){
				callback_(currentPicker,hex);
			}else if(callback_ == undefined || callback_ == ""){
				$(currentPicker).val(hex);
			}else{
				if(callback_.charAt(0) != "#"){
					callback_ = "#" + callback_;
				}
				$(callback_).val(hex);
			}			
		}
				
	};
	$.fn.bigColorpicker = bigColorPicker.showPicker;
	$.fn.bigColorpickerMove = bigColorPicker.movePicker; //Ê¹ÓÃÔÚÍÏ×§µÄ¸¡²ãÉÏ£¬ÔÚÍÏ×§Ê±Ê°È¡Æ÷Ëæ¸¡²ãÒÆ¶¯Î»ÖÃ
	$.fn.bigColorpickerHide = bigColorPicker.hidePicker; //¶ÔÓ¦Ò»Ð©ÌØ¶¨µÄÓ¦ÓÃÐèÒªÊÖ¶¯Òþ²ØÊ°È¡Æ÷Ê±Ê¹ÓÃ¡£±ÈÈçÍÏ×§¸¡²ãÊ±Òþ²ØÊ°È¡Æ÷
})(jQuery);