<div class="row">
<h1> Vaše aktuálne zahlasené abstrakty</h1>
<hr>
 
	{foreach from=$regbyuser item=reg_row}
	
	
	{if $reg_row.abstract_titul|count_characters == 0}
		{if $reg_row.reg_participation == 'aktiv'}
			<h3>Aktívna účasť</h3>
		{/if}
		
		{if $reg_row.reg_participation == 'pasiv'}
			<h3>Pasívna úcasť</h3>
		{/if}
		
		{if $reg_row.reg_participation == 'visit'}
			<h3>Navštevník</h3>
		{/if}
		
	{else}
	<h3>Aktívna účasť</h3> 
	<h2>Titul abstraktu: <strong>{$reg_row.abstract_titul}</strong></h2>
	{/if}
	Kongres: <strong>{$reg_row.congress_titel}</strong><br>
	Venue: {$reg_row.congress_venue}<br />
	
	<div class="info box small">
		{$reg_row.reg_abstract_text}
	
	</div>
	<p>
	<button id="editAbstr_{$reg_row.registr_id}" class="green">Edituj</button>
	<button id="delAbstr_{$reg_row.registr_id}" class="red" >Zmaž</button>
	
	</p>
		<hr />
	
	{/foreach}
</div>