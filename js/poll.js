var pollStates = [];

var thisPoll = this;


function addPoll()
{
	
	var stats = $("#poll_answers");
	
	var stLn = stats.find('input').length+1;
	
	var row = $("<div/>").addClass("row");
	
	var html ="<div id='p_answer_"+stLn+"'>Chioce "+stLn+" <input type='text' id='answer_"+stLn+"' style='width:150px;' class='inline'><button id='answer_"+stLn+"' " +
			"onclick='deleteAnswer(" +stLn+");'"+
			"class='red button inline'>Remove</button></div>";
	
	row.append(html);
	stats.append(row);
	
	
}

function deleteAnswer(id)
{
	$("#p_answer_"+id).remove();
}

function getAnswers()
{
	var stats = $("#poll_answers");
	
	var answers = [];
	
	stats.find('input').each(function(row,r){
			
		
		if (r.value.trim().length > 0){
			answers.push(r.value);
		}
		
		
		
	});
	
	return answers;
}

function editVote(item)
{

	var id = item.context.id;
	
	var tmp = id.split("_");
	
	window.location="index.php?c=poll&m=editPoll&id="+tmp[1];
	
}


function savePoll()
{
	
	var poll_start = $("#poll_start_date").val().trim();
	var poll_end = $("#poll_end_date").val().trim();
	var poll_title = $("#poll_title").val().trim();
	
	var poll_hash = $("#poll_hash").val().trim();
	
	var answer_count = Number($("#poll_choices_count").val().trim());
	
	if (answer_count == 0 || isNaN(answer_count)==true){
		
		pushWindow({caption:"Error",content:"Please enter number of choices..."});
		return;
		
	}
	
	
	if (poll_title.length == 0){
		
		pushWindow({caption:"Error",content:"No poll title given..."});
		return;
	}
	
	
	if (poll_start.length == 0){
		
		pushWindow({caption:"Error",content:"No poll start date given..."});
		return;
		
	}
	
	if (poll_end.length == 0){
		
		pushWindow({caption:"Error",content:"No poll end date given..."});
		return;
	}
	
	
	var data = {
		poll_title:poll_title,
		poll_description:tinymce.get("poll_description").getContent(),
		poll_start_date:poll_start+" 00:00",
		poll_end_date:poll_end+" 23:59",
		poll_answers:getAnswers(),
		poll_hash:poll_hash,
		answer_count:answer_count
	}
	
	
	if (data.poll_answers.length == 0){
		
		pushWindow({caption:"Error",content:"No answers given..."});
		
		return ;
	}
	
	
	
	var t = new js_comunication();
	t.addRawRequest("index.php","poll/js_saveData",thisPoll,[data,"afterSaveData"]);
	t.sendData();
}

function afterSaveData(status,result){
	if (status){
		alert("Data saved OK");
	}
}


function showResults(item)
{
	var id = item.context.id;
	var tmp  = id.split("_");
	
	window.location ="index.php?c=poll&m=getResults&id="+tmp[1];
	
}


function showDefResults(item)
{
	var id = item.context.id;
	var tmp  = id.split("_");
	
	window.location ="index.php?c=poll&m=getResults&d=1&id="+tmp[1];
	
}

function disableVoting(item)
{
	var id = item.context.id;
	var tmp = id.split("_");
	
	//console.log(tmp);
	
	var t = new js_comunication();
	var data = {voteId:tmp[1]};
	
	t.addRawRequest("index.php","poll/js_disableVoteById",thisPoll,[data,"afterDisableVote"]);
	t.sendData();
	
}

function afterDisableVote(status,result)
{
	if (status){
	
		window.location ="index.php?c=poll&m=pollList";
	
	}else{
	
		pushWindow({caption:"Error",content:result});
	}
	
}


function initPollCreate()
{
	//$.datetimepicker.setLocale("en");
	
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
	
	$("[id^=editVote_]").on("click",function(e){
		editVote($(this));
	});
	
	$("[id^=showResults_]").on("click",function(e){
		showResults($(this));
	});
	
	$("[id^=showDefResults_]").on("click",function(e){
		showDefResults($(this));
	});
	
	$("[id^=disableVote_]").on("click",function(e){
		disableVoting($(this));
	});
	
	$("#printResult").on("click",function(e){
		
		
		console.log("print");
		e.print();
		
	});
	
	
}


function selectPoll(answer,pollId){
	
	
	var answers = $("#votingHd_"+pollId).val().trim();
	
	

	
	var data = {
			
			poll_answer:answer,
			poll_id:pollId,
			answer_count:$("#paHd_"+pollId).val(),
			answers:answers
	};
	
	
	//console.log(data);
	//return;
	var t = new js_comunication();
	
	t.addRawRequest("index.php","poll/js_userVotePoll",
			thisPoll,[data,"afterUserVoting"]);
	
	t.sendData();
}

function afterUserVoting(status,result)
{
	
	//console.log(result);
	//return;
	
	if (status){
		
		
		
		window.location ="index.php?c=poll&m=pollList";
		
		
	}else{
		
		pushWindow({caption:"Error",content:result});
	}
}

$(document).ready(function(){
	
	var form = $("#tplform").val();
	
	
	switch (form){
		case "polls":
			initPollCreate();
			break;
	}
	
});