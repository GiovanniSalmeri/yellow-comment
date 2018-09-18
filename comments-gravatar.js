document.addEventListener("DOMContentLoaded", function() {
	document.getElementById('from').addEventListener("change", fillOutFrom);
});
var md5 = function(){for(var m=[],l=0;64>l;)m[l]=0|4294967296*Math.abs(Math.sin(++l));return function(c){var e,g,f,a,h=[];c=unescape(encodeURI(c));for(var b=c.length,k=[e=1732584193,g=-271733879,~e,~g],d=0;d<=b;)h[d>>2]|=(c.charCodeAt(d)||128)<<8*(d++%4);h[c=16*(b+8>>6)+14]=8*b;for(d=0;d<c;d+=16){b=k;for(a=0;64>a;)b=[f=b[3],(e=b[1]|0)+((f=b[0]+[e&(g=b[2])|~e&f,f&e|~f&g,e^g^f,g^(e|~f)][b=a>>4]+(m[a]+(h[[a,5*a+1,3*a+5,7*a][b]%16+d]|0)))<<(b=[7,12,17,22,5,9,14,20,4,11,16,23,6,10,15,21][4*b+a++%4])|f>>>32-b),e,g];for(a=4;a;)k[--a]=k[a]+b[a]}for(c="";32>a;)c+=(k[a>>3]>>4*(1^a++&7)&15).toString(16);return c}}();
function fillOutFrom() {
	var PATT = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]+$/
	var BASE = "https://en.gravatar.com/";
	var email = this.value.trim().toLowerCase();
	var hash = md5(PATT.test(email) ? email : "");
	var size = document.getElementById("gravatar").width;
	var def = document.getElementById("gravatar").getAttribute("data-default");
	document.getElementById("gravatar").src = BASE+"avatar/"+hash+"?s="+size+"&d="+def;
	var jsonFile = new XMLHttpRequest();
	jsonFile.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById("name").value = JSON.parse(this.responseText).entry[0].displayName;
			document.getElementById("comment").focus();
		} else {
			document.getElementById("name").value ="";
		}
	}
	jsonFile.open("get", BASE+hash+".json", true);
	jsonFile.send();
}
