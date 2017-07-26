var _SID = undefined;
var thisMain = this;

loadJsFileForClass({class:"login.js"});

function mainInit(settings)
{
	//console.log(settings);
	
	var myClass = settings.class;
	
	if (myClass !="" || myClass!="")
	{
		
		loadJsFileForClass(settings);
	}
	
	
}

function loadJsFileForClass(settings)
{
	var jsfile = settings.class;
	
	if (jsfile === ".js"){
		return;
	}
	if (!checkClass(jsfile)){
		var fileRef = document.createElement("script");
		fileRef.setAttribute("type","text/javascript");
		fileRef.setAttribute("src","js/"+jsfile);
	
		document.getElementsByTagName("head")[0].appendChild(fileRef);
	}
}


function pushWindow(data)
{
	var msgBox = $("#dialogWindow");
	
	var message = {caption:data.caption,content:undefined};
	
	
	if (typeof data.content == "object"){
		
		if (data.content["msg"]!=undefined){
			message.content = data.content["msg"];
		}else{
			message.content = data.content["result"];
		}
		
	}
	
	if (typeof data.content == "string"){
		message.content = data.content;
	}
	
	
	msgBox.dialog({
		title:message.caption
	});
	
	msgBox.html(message.content);
	
	msgBox.show();
}


function checkClass(jsFile)
{
	
	var head = document.getElementsByTagName("head")[0].children;
	for (var ele in head)
	{
		if (typeof head[ele]==="object")
		{
			if (head[ele].outerHTML.indexOf("jsFile")!=-1) return true;
		}
	}
	return false;
}





