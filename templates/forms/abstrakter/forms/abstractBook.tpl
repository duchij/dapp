<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Zbornik abstraktov</title>
	<link rel="stylesheet" type="text/css" href="../css/abstrakter/screen.css" media="screen" />
	{literal}
	<style>
        .indexLink {
        	text-decoration: none;
        	font-size: 11px;
        }

    </style>
	{/literal}

</head>
<body>

{*$defAbs|@var_dump*}

<div id="header">
<h1>{$data.congress_title} - Zborník Abstraktov</h1>
</div>
<div class="colmask threecol">
	<div class="colmid">
		<div class="colleft">
		<div class="col1">
		{foreach from=$defAbs item=abs key=k}
				<div class="itemAbs">
					<p>
					
						<h2><a name="{$abs.abstract_title}">{$abs.abstract_title}</a></h2><br>
						<strong>{$abs.abstract_autor}, {$abs.abstract_coauthor}</strong><br>
						<i>{$abs.abstract_adress}</i><br>
						<hr>
						<div class="description">
							{$abs.abstract_text}
						</div>
					</p>
				</div>
			{/foreach}
		
		</div>
		<div class="col2">
		<a name="index"><center>Index</center></a>
		<ul>
			{foreach from=$defAbs item=abs key=k}
				<li><a href="#{$abs.abstract_title}"} class="indexLink">{$abs.abstract_title}</a></li>
			{/foreach}
			</ul>
		
		</div>
		<div class="col3">
		<a href="files/zbornik.pdf" target="_blank">Zborník abstraktov(pdf)</a><br>
		ISBN: 978–80–89306–43-8<br>
		<img src="../images/barcode.png" altg="barcode">
		</div>
		
	</div>

</div>

<div id="footer">
	<center>2017 63.Kongres slovenských a detských chirurgov</center>
</div>

</body>
</html>