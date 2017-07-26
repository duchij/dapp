<html>
	<head>
		<meta charset="utf=8">
		<title>Voting result for {$data.voteTitle} {$data.from}-{$data.to}</title>
		
		<link rel="stylesheet" type="text/css" href="css/mainprint.css">
	</head>
<body>
<div id="printBanner" class="no-print"><button onclick="window.print();">Print</button></div>

<page size="A4">
	<h1>Definitive results : {$data.pollTitle}</h1>
	<hr>
	{$data.from}-{$data.to}
	<hr>
	
	<div class="description">
		{$data.voteDescription}
		
		Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.
		
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
				<td class="large">Choice</td><td class="large">Total counts</td><td class="large">Percentage</td>
				</tr>
			</thead>
			{foreach from=$resData.multichoice item=choice key=label}
			<tr>
				<td class="large center">{$label}</td> 
				<td class="large center">{$choice}</td> 
				<td class="large center">[ {math equation="(x/y)*100" x=$choice y=$resData.totalCount format="%.2f"}%] </td>
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
	
	<hr>
	
	
	</page>


</body>
</html>