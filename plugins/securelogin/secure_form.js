SHA1=function(l){function p(b,a){return b<<a|b>>>32-a}l+="";for(var n=Math,c=[1518500249,1859775393,2400959708,3395469782,1732584193,4023233417,2562383102,271733878,3285377520,4294967295],s=n.ceil(l.length/4)+2,q=n.ceil(s/16),g=[],a=0,h=[],j,d,e,f,m,i,b,k;a<q;a++){g[a]=[];for(k=0;k<16;k++){function o(b,c){return l.charCodeAt(a*64+k*4+b)<<c}g[a][k]=o(0,24)|o(1,16)|o(2,8)|o(3,0)}}i=l.length*8-8;a=q-1;g[a][14]=i/(c[9]+1);g[a][14]=n.floor(g[a][14]);g[a][15]=i&c[9];for(a=0;a<q;a++){for(b=0;b<16;b++)h[b]=g[a][b];for(b=16;b<80;b++)h[b]=p(h[b-3]^h[b-8]^h[b-14]^h[b-16],1);j=c[4];d=c[5];e=c[6];f=c[7];m=c[8];for(b=0;b<80;b++){var r=n.floor(b/20),t=p(j,5)+(r<1?d&e^~d&f:r==2?d&e^d&f^e&f:d^e^f)+m+c[r]+h[b]&c[9];m=f;f=e;e=p(d,30);d=j;j=t}c[4]+=j;c[5]+=d;c[6]+=e;c[7]+=f;c[8]+=m}i="";for(z=4;z<9;z++)for(a=7;a>=0;a--)i+=((c[z]&c[9])>>>a*4&15).toString(16);return i};

function secure_form(element_id)
{
	var element = s.id(element_id);
	var input = null;
	if(typeof element['secured_password'] != 'undefined')
		input = element['secured_password'];
	else
	{
		input = document.createElement('input');
		input.type = 'hidden';
		input.name = 'secured_password';
	}

	// Make sure that cookies are enabled, or else there will be some major issues!!!
	var rand_value = Math.floor(Math.random() * 999) + 1;
	s.setcookie('login_cookie_check', rand_value, 7);

	if(!s.cookie('login_cookie_check'))
	{
		// Cookies don't appear to be enabled, no login salt, sorry!
		login_salt = null;
	}
	else
		// Delete the cookie, we don't need it anymore :)
		s.setcookie('login_cookie_check', null, -7);

	input.value = typeof login_salt == 'undefined' || login_salt == null ? SHA1(element['member_name'].value.toLowerCase() + element['member_pass'].value) : SHA1(SHA1(element['member_name'].value.toLowerCase() + element['member_pass'].value) + login_salt);
	element['member_pass'].value = '';

	if(typeof element['secured_password'] == 'undefined')
		element.appendChild(input);
}

s.onload(function()
{
	document.getElementById('login_form').onsubmit = function() { secure_form('login_form'); };
});