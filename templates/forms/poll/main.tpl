<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>ESPES Elections</title>	
	
	{include file="css.tpl"}
	
</head>

<body>

{assign var="sessionData" value=$smarty.session._sf2_attributes.election2017data}

<div id="dialogWindow"></div>

<div id="content">
	<div class="row">
	
		<div class="one sixth"></div>
		
		<div class="four sixth">
		
				<div class="row">
		
					<div class="two fifth">
						<img src="images/espes_footer_logo.png" width="100" alt="logo1"> 
						<img src="images/memreg/logo.png" alt="logo2">
					</div>
		
					<div class="three fifth">
						<span style="font-size:28px;color:#362b5b;">
						<strong>Elections 2017</strong></span><br>
						<span style="font-size:16px;color:#362b5b;">Electronic and anonymous</span>
		
						<div style="float:right;">
							<span class="black">Member:</span><br>
							<span class="grey">{$sessionData.surname}, {$sessionData.name}, {$sessionData.espesId}</span><br>
							<span class="asphalt">{$smarty.now|date_format:"%m.%d.%Y, %A"}</span>
		
						</div>
		
					</div>
		
			 	</div>
        </div>         
		
		
			{* <div id="login_form">
			{if $smarty.session.account_type}
				Logged in: <strong>{$smarty.session.account}</strong>
				<a href="index.php?c=main&m=logout" class="button red" target="_self">LogOut</a>
				{else}
				<form action="index.php?c=main&m=login" method="POST" id="loginForm">
				<input type="hidden" id="name_hf" name="name" value="">
				<input type="hidden" id="passwd_hf" name="password" value="">
				<input type="hidden" id="salt_hf" name="sa" value="">
				<p class="small">Name: <input id="loginN" type="text" style="width:100px;display:inline;">
				Password: <input type="password" id="loginP" style="width:100px;display:inline;">
				<input type="submit" value="login" class="blue button">
				</p>
				</form>
			{/if}
			
			</div> *}
			
		<div class="one sixth"></div>
	</div>
	
	<div class="row">
	<div class="one sixth two-up-mobile"></div>
	<div class="four sixth two-up-mobile">

			<nav class="nav mobile nocollapse blue">
				<ul>
					{*<li><a href="index.php" target="_self">Home</a></li>*}
					<li><a href="http://espes.eu" target="_blank">Back to espes.eu</a>
					
					{*if $smarty.session.account*}
				
				
					<li><a href="index.php?c=poll&amp;m=pollList&amp;a=1" target="_self">Voting</a></li>
					{*<li><a href="index.php?c=poll&amp;m=pollList&amp;a=0" target="_self">Past Polls</a></li>*}
					{if $sessionData.account_type=='superadmin'}
					<li><a href="index.php?c=poll&amp;m=create" target="_self">Create Poll</a></li>
					{/if}
					{*  <li><a href="index.php?c=discus&m=create" target="_self">Create discussion</a></li> *}
				{*/if*}
					
					
					{*
				<nav class="nav vertical nocollappse">
				<h2 class="green"> Members</h2>
				<ul>
					<li class="green"><a href="index.php?c=memreg&m=showForm" target="_self" >Become Member</a></li>
					{if $smarty.session.account_type=="admin"}
						<li><a href="index.php?c=memreg&m=editMembers" target="_self">Show requests</a></li>
					{/if}
				</ul>
					</nav> *}
					
					
					
				</ul>
			</nav>
	</div>
	
	
	</div>
	
	<div class="row">
		<div class="one sixth two-up-mobile"></div>
		<div class="four sixth two-up-mobile">
					{$errorMsg}
			
			
			
			{if $body}
				{include file="forms/$body"}
				{else}
				{include file="body.tpl"}
			{/if}
			
			
		</div>	
		
		<div class="one sixth two-up-mobile"></div>
		
	</div>
	<div class="row">
		<div class="one sixth two-up-mobile"></div>
		<div class="four sixth two-up-mobile">
			<div class="box blue">
				<p style="text-align:center;">Design & scripting by Boris Duchaj, &copy;2017</p>
			
		</div>
		<div class="one sixth two-up-mobile"></div>
	</div>
	</div>
{include file="scripts.tpl"}
</body>
</html>
