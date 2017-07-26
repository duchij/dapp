<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>KDCH Abstrakter</title>	
	
	{include file="forms/$module/css.tpl"}
	
</head>

<body>
{assign var="userData" value=$smarty.session.abstrakter.user_data}

<div id="dialogWindow"></div>


<div id="content">
<div class="row">
	<div id="commBall"><img src="images/{$module}/loader.gif"></div>
</div>
	<div class="row">
	
		<div class="one tenth"></div>
		
		<div class="eight tenth">
		
				<div class="row">
		
					<div class="one fifth half-padded">
						<img src="images/abstrakter/logo_kdch.png" alt="logo KDCH">
						<p class="asphalt"><a href="index.php" rel="home">Abstrakter v1.0</a></p>
						{*<img src="images/memreg/logo.png" alt="logo2">*}
					</div>
			
					<div class="three fifth half-padded ">
						<span style="font-size:28px;color:#362b5b;">
						<strong>Klinika detskej chirurgie LFUK a DFNsP</strong></span><br>
						<span style="font-size:16px;color:#362b5b;">Registrácie na akcie detskej chirurgie </span>
						<div class="error box">
						
						Ak už máte vytvorené konto, skontrolujte si svoje osobné údaje. Prenesením do novšej verzie
						Vám nemusí sedieť diakritika. Ďakujeme za pochopenie.
						
						</div>
						
		
					</div>
					<div class="one fifth half-padded">
					
							<span class="asphalt pull-right">{$smarty.now|date_format:"%m.%d.%Y, %A"}</span>
							<br>
					
					<div id="login_form"> 
						{if $userData.account}
						 	<strong>{$smarty.session.account}</strong>
							<a href="index.php?c=main&m=logout" class="button red pull-right" target="_self">LogOut</a>
						{else}
							<form action="index.php?c=main&m=login" method="POST" id="loginForm">
							<input type="hidden" id="name_hf" name="name" value="">
							<input type="hidden" id="passwd_hf" name="password" value="">
							<input type="hidden" id="salt_hf" name="sa" value="">
							<div style="display:block;width:100%;">
							<div class="inline">Email: </div><div class="inline"><input id="loginN" type="text" style="width:150px;display:inline;"></div><br>
							<div class="inline">Heslo: </div><div class="inline"><input type="password" id="loginP" style="width:150px;display:inline;"></div>
							</div>
							
								 
							<input type="submit" value="Prihlás" class="blue button pull-right" >
							
							</p>
							</form>
							<br>
							<a href="index.php?mm=lostPassword" class="red button pull-right" target="_self"><span class="white">Zabudnuté heslo</span></a>
							<a href="index.php?mm=newUser" class="yellow button pull-right" target="_self"><span class="white">Nový užívateľ</span></a>
						{/if}
			
					</div> 
					
					<div style="float:left;">
						
							{*<span class="black">User:</span><br>*}
							{if $userData.priezvisko}
							<span class="grey">{$userData.priezvisko} {$userData.meno}</span><br>
							{/if}
							
						</div>
					
					</div>
		
			 	</div>
        </div>         
		
		
			
			
		<div class="one tenth"></div>
	</div>
	
	<div class="row">
	<div class="one tenth"></div>
	<div class="eight tenth">
	
	{if $smarty.session.abstrakter.user_data}
			<nav class="nav nocollappse blue">
				<ul>
					{*<li><a href="index.php" target="_self">Home</a></li>*}
					{*<li><a href="http://abstra" target="_blank">Back to espes.eu</a>*}
					
					{*if $smarty.session.account*}
				
				
					<li><a href="index.php?c={$module}&amp;m=avabKongres" target="_self">Aktuálne kongresy</a></li>
					{*<li><a href="index.php?c=poll&amp;m=pollList&amp;a=0" target="_self">Past Polls</a></li>*}
					{*if $sessionData.account_type=='admin'*}
					<li><button>Admin.....</Button>
					<ul> 
						<li><a href="index.php?c={$module}&amp;m=createCongress" target="_self">Create Kongress</a></li>
					</ul>
					<li><a href="index.php?c={$module}&amp;m=myData">Moje údaje</a></li>
					<li><a href="index.php?c={$module}&amp;m=myAbstracts">Moje aktuálne abstrakty</a></li>
					{*/if*}
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
			{else}
			<hr>
			{/if}
			
	</div>
	
	
	</div>
	
	<div class="row">
		<div class="one tenth"></div>
		<div class="eight tenth">
					{$errorMsg}
			
			
			
			{if $body}
				{include file="forms/$module/$body"}
				{else}
				{include file="forms/$module/body.tpl"}
			{/if}
			
			
		</div>	
		
		<div class="one tenth"></div>
		
	</div>
	<div class="row">
		<div class="one tenth"></div>
		<div class="eight tenth">
			<div class="box blue">
				<p style="text-align:center;">Design & scripting by Boris Duchaj, &copy;2017</p>
			
		</div>
		<div class="one tenth"></div>
	</div>
	</div>
{include file="forms/$module/scripts.tpl"}
</body>
</html>
