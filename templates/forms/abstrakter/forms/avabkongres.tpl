<div class="row">
<h1>Aktuálne akcie</h1>
<hr>
{*$avab_kongres|@var_dump*}
{assign var="userData" value=$smarty.session.abstrakter.user_data}
 	
 	
 		{foreach from=$avab_kongres key=i item=row}
 		<div class="success box">
 			<h1 class="source-sans-pro">{$row.congress_titel}</h1> 
			<p>{$row.congress_venue}</p>
 			<p><em>{$row.congress_from|date_format:"%d.%m.%Y"} - {$row.congress_until|date_format:"%d.%m.%Y"}</em></p>
 			
 			<p><strong>Web:</strong> <a href="{$row.congress_url}" rel="link" target="_blank">{$row.congress_url}</a></p>
 			
 			<div class="box">{$row.congres_description}</div>
 			
 			
 			{if $userData.account}
 			<button id="regKongres_{$row.item_id}" value="{$row.item_id}">Prihlásiť sa... </button>
 			{else}
 			<div class="warning box">Prihlásiť sa môžete až po príhlásení sa do systému....</div>
			{/if}
 			
 			{if $userData.account == "admin"}
 			<button class="blue" id="editKongresBtn_{$row.item_id}">Edituj kongress</button>
 			<button class="asphalt" id="toExcel_{$row.item_id}">Export to excel...</button>
 			<button class="yellow" id="showRegistrations_{$row.item_id}">Registrácie</button>
 			{/if}
 			
 		</div>	
 		<p></p>	
 			
		{/foreach}
		
		
</div>