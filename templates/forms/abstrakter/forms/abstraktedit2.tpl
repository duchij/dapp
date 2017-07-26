<div class="row">
			<h1 class="logo">Prihlásenie na kongres/akciu</h1>
				<hr />
				<div class="info box">
				<h2>{$data.congress_titel} </h2>
				{$congress_subtitel}<br>
				<a href="{$data.congress_url}" target="_blank">Web stranka...</a><br>
				{$data.congress_venue}<br>
				{$data.congress_from|date_format:"%d.%m.%Y"} - {$data.congress_until|date_format:"%d.%m.%Y"}<br>
				
			</div>
				<input type="hidden" id="congress_id" value="{$data.congress_id}">
				<input type="hidden" id="user_id" value="{$data.reg_user_id}">
				<input type="hidden" id="registr_id" value="{$data.registr_id}">
				<table>
				<tr>
					<td class="large"> 
						<label for="aktiv_rb">Aktívna účasť (autor)</td><td class="large"> <input id="aktiv_rb" type="radio" name="particip" value="aktiv" {$data.aktiv_rb}></label>
					</td>
				</tr>
				<tr>
					<td class="large">
						<label for="pasiv_rb">Pasívna účasť (spoluautor)  </td><td class="large">  <input id="pasiv_rb" type="radio" name="particip" value ="pasiv"   {$data.pasiv_rb}></label>
					</td>
				</tr>
				<tr>
					<td class="large">	
						<label for="visit_rb">Pasívna účasť (návštevník)</td><td class="large">  	<input id="visit_rb" type="radio" name="particip"  value ="visit"   {$data.visit_rb}></label>
					</td>
				</tr>
				{*<tr><td class="large">	Spoločenská akcia</td><td> <select name="etc"><option value="none" {$data.selected_none}>Žiadna</option> <option value="rafting" {$data.selected_raf}>Plavba loďou do Čunova na rafting (http://www.divokavoda.sk/, 10 € na osobu)</option>
				<option value="golf" {$data.selected_golf}> Golf (Báč, Bernolákovo)</option>*}
				</select></td></tr>
				</table>
				
				
				<table>
				<tr>
				<td class="large"> <label for="doctor_rb"><input id="doctor_rb" type="radio" id="section" name="section" value="doctor" {$data.doctor_rb}> - Lekárska sekcia</label></td> 
				<td class="large"><label for="nurse_rb"><input id="nurse_rb" type="radio" id="section" name="section" value="nurse" {$data.nurse_rb} > - Sesterská sekcia</label></td>
				</tr> 
				 
				</table>
				
				<table>
					<tr><td class="large">Názov prednášky:</td><td>  <input type="text" id="abstract_titul" value="{$data.reg_abstract_titul}" {$data.state}></td></tr>
					<tr><td class="large">Názov pracoviska:</td><td>  <input type="text" id="abstract_adresy" value="{$data.reg_abstract_adresy}"   {$data.state}></td></tr>
					<tr><td class="large">Prvý autor:</td><td>  <input type="text" id="abstract_main_autor" value="{$data.reg_main_autor}" {$data.state}></td></tr>
					<tr><td class="large">Ostatní autori:</td><td>  <input type="text" id="abstract_autori" value="{$data.reg_abstract_autori}"   {$data.state}></td></tr>
					
					<tr><td class="large">Abstrakt:</td><td> <textarea class="teditor" id="abstract_text" rows="80"   style='width:600px;height:400px;' {$data.state}>{$data.reg_abstract_text}</textarea> </td></tr>
					
				
				
			</table>
			{if $data.state != 'readonly'}
					<button class="asphalt large" id="saveUserAbstract">Uložiť</button> 
				{else}
				<div class="warning box">Registrácia je ukončená, už nemôžete editovať Váš abstrakt...</div>
				{/if}
			
	
	
</div>

