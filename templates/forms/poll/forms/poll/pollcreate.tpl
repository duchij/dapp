<input type="hidden" value="polls" id="tplform">
{*$pollData|@var_dump*}
<div class="row padded">
{if $pollData.poll_id}
	<h1> Edit poll</h1>
{else}
	<h1> Create poll</h1>
{/if}
{* <form method="POST" action="index.php?c=poll&m=p_save">*}

<input type="hidden" id="poll_hash" value="{$pollData.poll_hash}" >

	<table>
	<tr>
		<td><span class="large"><strong>Poll title</strong></span></td>
		<td>
			<input type="text" id="poll_title" value="{$pollData.poll_title}" required>
			Enter the full title for the poll
		</td>
	</tr>
	<tr>
		<td><span class="large"><strong>Poll Description:</strong></span></td>
		<td>
			<textarea id="poll_description" >{$pollData.poll_description}</textarea>
			Enter description or content for the poll, e.g. what goals or target it has. You cam format your desscription with simple formatting aids.
		</td>
	</tr>
	
	<tr>
		<td><span class="large"><strong>Poll start:</strong></span></td>
		<td> 
			<input type="text" class="flatpickr" id="poll_start_date" name="poll_date_start" value="{$pollData.poll_start_date}" readonly required>
			<Enter the start date, voting <strong>YYYY-mm-dd 00:00</strong> hours
		</td>
	</tr>
	
	<tr>
		<td><span class="large"><strong>Poll end:</strong></span></td>
		<td> 
			<input type="text" class="flatpickr" id="poll_end_date" value="{$pollData.poll_end_date}" readonly required>
			Enter the end date, voting ends <strong>YYYY-mm-dd 23:59</strong> hours
		</td>
	</tr>
	<tr>
		<td><span class="large"> <strong>Number of possible choices:</strong></span></td>
		<td><input type="number"  id="poll_choices_count" required patern="/[0-9]+/" value="{$pollData.answer_count}">
		Enter number 1 or more...
		</td>
	</tr>
	
	<tr>
		<td><span class="large"><strong>Chioces:</strong></span> <a href="javascript:addPoll();" >&nbsp;&nbsp;<i class="icon-plus icon-2x"></i></a></td>
		<td>
			<div id="poll_answers">
				{if $pollData.poll_stats}
					{foreach from=$pollData.poll_stats item=answer key=k}
					
						<div id="p_answer_{$k}">
						Answer 
						<input type="text" id="answer_{$k}" style="width:150px;" class="inline" value="{$answer}">
						<button id="answer_{$k}" onclick="deleteAnswer({$k});" class="red button inline">Remove</button></div>
					{/foreach}
				{/if}
			</div>
			Enter possible choices for the poll. The Rul One Vote One Choice
		</td>
	</tr>
	
	<tr>
		<td class="large"><strong>Order:</strong></td><td><input type="number" id="poll_order" required patern="/[1-9]+/" value="{$pollData.poll_order}" >
		<p>Determines the position of the individual votes on the page</p>
	</tr>
	</table>
	<div style="float:right;"><a href="javascript:savePoll();" class="button large green">Save</a></div>
{* </form> *}

</div>