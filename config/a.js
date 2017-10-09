data={a:{b:1}};
data.getAttrbute = function (){
	var that = this;
	var key = ['a','b'];
	key.forEach(function(e){  
   		console.log(e);
   		console.log(that[e]);
	})
}
data.getAttrbute();