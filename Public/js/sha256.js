var digitArray = new Array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f');

function toHex( n ) {

        var result = ''
        var start = true;

        for ( var i=32; i>0; ) {
                i -= 4;
                var digit = ( n >> i ) & 0xf;

                if (!start || digit != 0) {
                        start = false;
                        result += digitArray[digit];
                }
        }

        return ( result == '' ? '0' : result );
}
function login_onclick() 
{

	try
	{
            var DevicePath,mylen,ret,keyid,username,mykey,outstring,address,mydata,i,InString,versionex,version;
		    if(navigator.userAgent.indexOf("MSIE")>0 && !navigator.userAgent.indexOf("opera") > -1)
		    {
			    s_simnew1=new ActiveXObject("Syunew3A.s_simnew3");
		    }
		    else
		    {
				s_simnew1= document.getElementById('s_simnew31');
		    }            
			DevicePath = s_simnew1.FindPort(0);
			if( s_simnew1.LastError!= 0 )
			{
			 frmlogin.KeyID.value='';
			}else{
			 frmlogin.KeyID.value=toHex(s_simnew1.GetID_1(DevicePath))+toHex(s_simnew1.GetID_2(DevicePath));	
			}
            return true;
		}
	catch (e) 
	{
		alert("可能是没有安装相应的控件或插件");
		return false;
	}
}