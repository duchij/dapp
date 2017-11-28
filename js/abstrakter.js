var thisAbstrakter = this;

function saveCongres()
{
	
	var congresTitel = $("#congress_titel").val().trim();
	
	
	if (congresTitel.length == 0){
		
		pushWindow({title:"Error",content:"Prazdny titul"});
		return;
	}
	
	var congresText = tinymce.get("congres_description").getContent();
	congresText = window.btoa(encodeURIComponent(congresText));
	
	var data  = {
			item_hash:$("#item_hash").val(),
			congress_titel:congresTitel,
			congress_subtitel:$("#congress_subtitel").val().trim(),
			congress_url:$("#congress_url").val().trim(),
			congress_venue:$("#congress_venue").val().trim(),
			congres_description:congresText,
			congress_from:$("#congress_from").val().trim(),
			congress_until:$("#congress_until").val().trim(),
			congress_regfrom:$("#congress_regfrom").val().trim(),
			congress_reguntil:$("#congress_reguntil").val().trim()
			//public:$("#public")
			
	};
	
	//console.log(data);
	//return;
	
	var t = new js_comunication();
	t.addRawRequest("index.php","abstrakter/js_saveCongress",thisAbstrakter,[data,"afterSaveCongress"]);
	t.sendData();
	
	
	
}

function afterSaveCongress(status,result)
{
	
	if (!status){
		pushWindow({caption:"Error",content:result});
		return;
	}
	
	alert("Data ulozene OK....");
}

function editKongres(item)
{
	
	var id = item.context.id;
	var tmp = id.split("_");
	
	window.location="index.php?c=abstrakter&m=editCongress&id="+tmp[1];

}

function saveUserData()
{
	var data = {
			user_id:$("#user_id").val().trim(),
			meno:$("#meno").val().trim(),
			priezvisko:$("#priezvisko").val().trim(),
			titul_pred:$("#titul_pred").val().trim(),
			titul_za:$("#titul_za").val().trim(),
			contact_email:$("#contact_email").val().trim(),
			contact_phone:$("#contact_phone").val().trim(),
			adresa:$("#adresa").val().trim()
			
	};
	
	//console.log(data);
	//return;
	
	var t = new js_comunication();
	t.addRawRequest("index.php","abstrakter/js_saveUserData",thisAbstrakter,[data,"afterSaveUserData"]);
	t.sendData();
	
}

function afterSaveUserData(status,result)
{
	if (!status){
		pushWindow({caption:"Error",content:result});
	}
	
	alert("Ulozene...");
}

function regToKongress(item)
{
	var id = item.context.id;
	var tmp = id.split("_");
	
	window.location = "index.php?c=abstrakter&m=regToCongres&cid="+tmp[1];

}

function saveUserAbstract()
{
	var data = {};
	
	var section ="doctor"
		
	if ($("#nurse_rb").prop("checked") == true)
	{
		section="nurse";
	}
	
	var particip = "aktiv";
	
	if ($("#pasiv_rb").prop("checked") == true){
		particip = "pasiv";
	}
	if ($("#visit_rb").prop("checked") == true){
		particip = "visit";
	}
	
	
	var abstrText = tinymce.get("abstract_text").getContent().trim();
	
	if (abstrText.length > 0){
		abstrText = window.btoa(encodeURIComponent(abstrText));
	}
	
	data = {
			section:section,
			participation:particip,
			item_id:$("#registr_id").val().trim(),
			user_id:$("#user_id").val().trim(),
			congress_id:$("#congress_id").val().trim(),
			abstract_titul:$("#abstract_titul").val().trim(),
			abstract_main_autor:$("#abstract_main_autor").val().trim(),
			abstract_autori:$("#abstract_autori").val().trim(),
			abstract_text:abstrText,
			abstract_adresy:$("#abstract_adresy").val().trim()
	}
	
	console.log(data);
	
	var t  = new js_comunication();
	t.addRawRequest("index.php","abstrakter/js_saveUserAbstrReg",thisAbstrakter,[data,"afterSaveUserAbstrReg"]);
	t.sendData();
	
}


function afterSaveUserAbstrReg(status,result)
{
	console.log([status,result]);
	
	if (!status){
		pushWindow({caption:"Error",content:result});
		return;
	}
	
	alert("Data ulozene. Cakajte na email...");
	
	//$("#registr_id").val(result.result);
	window.location = "index.php?c=abstrakter&m=myAbstracts";
	
}

function editUserAbstract(item)
{
	var id = item.context.id;
	var tmp = id.split("_");
	
	window.location = "index.php?c=abstrakter&m=editUserAsbstract&aid="+tmp[1];
	
	
}

function deleteUserAbstract(item){
	
	var id = item.context.id;
	
	var tmp = id.split("_");
	
	
	var res = confirm("Naozaj zmazat. Toto je nevratne....");
	
	if (res){
		
		var data = {
				reg_id:tmp[1]
		}
		
		var t = new js_comunication();
		t.addRawRequest("index.php","abstrakter/js_deleteUserAbstract",thisAbstrakter,[data,"afterDeleteUserAbstract"]);
		t.sendData();
		
	}
	
}

function afterDeleteUserAbstract(status,result)
{
	if (!status){
		pushWindow({caption:"Error",content:result});
	}
	window.location="index.php?c=abstrakter&m=myAbstracts";

}

function showInExcel(item)
{
	var id = item.context.id;
	var tmp = id.split("_");
	
	window.location="index.php?c=abstrakter&m=getXmlData&cid="+tmp[1];
	
	
}

function showRegistrations(item){
	
	var id = item.context.id;
	var tmp = id.split("_");
	
	window.location="index.php?c=abstrakter&m=showRegistrations&cid="+tmp[1];
	
}

function makeAbstracts(item){
	var id = item.context.id;
	var tmp = id.split("_");
	
	var t = new js_comunication();
	t.addRawRequest("index.php","abstrakter/js_makeAbstracts",thisAbstrakter,[{conId:tmp[1]},"afterProgramSend"]);
	t.sendData();
	
}

function sendProgramToAll(item){
	var id = item.context.id;
	var tmp = id.split("_");
	
	var t = new js_comunication();
	t.addRawRequest("index.php","abstrakter/js_sendProgramToAll",thisAbstrakter,[{conId:tmp[1]},"afterMakeAbstracts"]);
	t.sendData();
	
}

function afterMakeAbstracts(status,result){
	if (!status){
		pushWindow({caption:"Error",content:result});
		return;
	}
	
	pushWindow({caption:"Success",content:"Abstract html was done"});
}

function afterProgramSend(status,result)
{
	if (!status){
		pushWindow({caption:"Error",content:result});
		return;
	}
	
	pushWindow({caption:"Success",content:"Mail was send to all"});
}


function initAbstrakter(){
	
	$(".flatpickr").flatpickr({
		
		//enableTime:true,
		plugins: [new confirmDatePlugin({})],
		confirmText:"OK",
		showAlways:true,
		//dateFormat:"Y-m-d H:i",
		dateFormat:"Y-m-d",
		//time_24hr:true,
		
		onOpen:function(selectedDates, dateStr, instance){
			

			instance.setDate(new Date());
		},
		
		onClose:function(selectedDates,dateStr,instance) {
			
			//instance.setDate(dateStr+ "23:59");
			
		}
		
		//defaultDate:new Date()
		

	});	
	
	$("#saveCongress").on("click",function(e){
		saveCongres();
	});
	
	$("[id^=editKongresBtn_]").on("click",function(e){
		editKongres($(this));
	});
	
	$("#saveUserData").on("click",function(e){
		saveUserData();
	});
	
	$("[id^=regKongres_]").on("click",function(e){
		
		regToKongress($(this));
		
	});
	
	$("#saveUserAbstract").on("click",function(e){
		
		saveUserAbstract();
	});
	
	$("[id^=editAbstr_]").on("click",function(e){
		
		editUserAbstract($(this));
		
	});
	
	$("[id^=delAbstr_]").on("click",function(e){
		
		deleteUserAbstract($(this));
		
	});
	
	$("[id^=toExcel_]").on("click",function(e){
		showInExcel($(this));
	});
	
	$("[id^=showRegistrations_]").on("click",function(e){
		showRegistrations($(this));
	});
	
	$("[id^=sendProgram_]").on("click",function(e){
		sendProgramToAll($(this));
	});
	
	$("[id^=makeAbstracts_]").on("click",function(e){
		makeAbstracts($(this));
	});
	
}


$(document).ready(function(){
	
	initAbstrakter();
	
});