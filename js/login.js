function loginUser(name,passwd,kp)
{
	    
	var encryptedName = CryptoJS.AES.encrypt(name.trim(),kp);
	var encryptedPasswd = CryptoJS.AES.encrypt(passwd.trim(),kp);
	    

    var nm = encryptedName.toString();
    var np = encryptedPasswd.toString();
    var salt = encryptedName.salt.toString();
    
    $("#name_hf").val(nm);
    $("#passwd_hf").val(np);
    $("#salt_hf").val(salt);
	
    
	
}

function encryptData(data,kp)
{	
	return CryptoJS.AES.encrypt(data.trim(),kp);
}

function getSessionID()
{
	
	if (_SID === undefined){
		
		var t = new js_comunication();
		t.addRawRequest("index.php","login/js_getSessionID",thisMain,[{},"setSessionID"]);
		t.sendData();
		
	}
	
}

function setSessionID(status,result)
{
	_SID = result;
}


$(document).ready(function(){
	
	getSessionID();
	
	$("#loginForm").submit(function(e){
		
		var name = $("#loginN").val();
		var passwd = $("#loginP").val();
		
		var kp = _SID.substring(0,16);
		
		loginUser(name,passwd,kp);
		//e.preventDefault();
		
	});
	
	$("#changePasswd").submit(function(e) {
		
		var passwd1 = $("#acc_passwd1").val().trim();
		var passwd2 = $("#acc_passwd2").val().trim();
		
		var kp = _SID.substring(0,16);
		
		console.log(kp);
		
		var p1 = encryptData(passwd1,kp);
		var p2 = encryptData(passwd2,kp);
		
		
		$("#passwd1_hf").val(p1.toString());
		$("#passwd2_hf").val(p2.toString());
		
		
	});
	
	
	
});