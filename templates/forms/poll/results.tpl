{*$resData|@var_dump*}
<div class="row padded">
	
	{if $print==1} 
	<div style="float:right;">
		<button onclick="window.print();" class="green no-print">Print</button>
	</div>
	{/if}

	{if $noData}
	<h1>{$noData}</h1>
	{else}


	<h1>{$resStatus} Results: <span class="blue">{$resData.title}</span></h1>
	
	<hr>
	<h3>Description:</h3>
	<div class="box">
	<p>{$resData.description}</p>
	</div>
	
	
	{if $resData.multichoice}
			<h3>Multichoice results:</h3>
			<table>
			<tr>
				<td class="large">Number of members voting:</td><td class="large"><span class="black"><strong>{$resData.allVotes}</strong></span></td>
			</tr>
			
			<tr>
				<td class="large">Total votes given: </td><td class="large"><span class="black"><strong>{$resData.totalCount}</strong></span></td>
				
			</tr>
			
			</table>
			
			 <br>
			
			<h3>Each choice count:</h3>
			<table>
			<thead>
				<tr>
				<td class="large">Poll</td><td class="large">Total counts</td><td class="large">Percentage</td>
				</tr>
			</thead>
			{foreach from=$resData.multichoice item=choice key=label}
			<tr>
				<td class="large">{$label}</td> <td class="large">{$choice}</td> <td class="large">[ {math equation="(x/y)*100" x=$choice y=$resData.totalCount format="%.2f"}%] </td>
			</tr>
			{/foreach}
			</table>
			</p>
	{else}
		<h3>Single Choice results:</h2>
		<table>
		<thead>
			<tr>
				<td class="large">Answer</td>
				<td class="large">Count</td>
				<td class="large">Total all votes</td>
				<td class="large">Percentage</td>
			</tr>
		</thead>
			
		
		{foreach from=$resData.data item=data key=k}
			
			<tr>
				<td class="large"><strong>{$data.answer}</strong></td>
				<td class="large">{$data.count}</td> 
				<td class="large">{$resData.totalCount}</span> </td>
				<td class="large"> {math equation="(x/y)*100" x=$data.count y=$resData.totalCount format="%.2f"}%</td>
			</tr>
	
		{/foreach} 
		</table>
		{*<hr>
		<p>Total amount of votes: <span class="asphalt large"><strong>{$resData.totalCount}</strong></span>
		*}
	{/if}
{/if}
<button class="asphalt" onclick="window.history.back();"><<< Back to Polls</button>
</div>