<div class="row">
		
				<h1>Akcia / Seminár / Konferencia / Kongress...</h1>
				<input type="hidden" value="{$data.item_hash}" id="item_hash">
				<table>
					<tr>
						<td class="large">Názov kongresu:</td>
						<td> <input type="text" id="congress_titel" value="{$data.congress_titel}" style='width:400px'></td></tr>
					<tr>
						<td class="large">Podnázov:</td>
						<td> <input type="text" id="congress_subtitel" value="{$data.congress_subtitel}"  style='width:400px;'></td></tr>
					<tr>
						<td class="large">URL adresa:</td>
						<td> <input type="text" id="congress_url" value="{$data.congress_url}" style='width:400px;'></td></tr>
					<tr>
						<td class="large">Venue: </td>
						<td><input type="text" id ="congress_venue" value="{$data.congress_venue}" style='width:400px;'></td></tr>
					<tr>
						<td colspan="2"><hr></td>
					</tr>
					<tr>
						<td class="large">Kratky popis</td>
						<td><textarea class="teditor" rows="10" cols="10" id="congres_description">{$data.congres_description}</textarea>
					
					
					<tr>
						<td class="large">Kongress od:</td>
						<td> <input type="text" class="flatpickr" id="congress_from" value="{$data.congress_from}" style="width:150px;"></td></tr>
					<tr>
						<td class="large">Kongress do:</td>
						<td> <input type="text" class="flatpickr" id="congress_until" value="{$data.congress_until}" style="width:150px;"></td></tr>
					<tr>
						<td colspan="2"><hr></td>
					</tr>
					<tr>
						<td class="large">Registrácia od:</td>
						<td> <input type="text" class="flatpickr" id="congress_regfrom" value="{$data.congress_regfrom}" style="width:150px;"></td></tr>
					<tr>
						<td class="large">Registrácia do:</td>
						<td> <input type="text" class="flatpickr" id="congress_reguntil" value="{$data.congress_reguntil}" style="width:150px;"></td></tr>
					<tr>
						<td class="large">Verejne viditeľný:</td>
						<td> <input type="checkbox" id="public" value="{$data.public}" {$data.public}></td></tr>
					
					
					<tr><td colspan="2"><button id="saveCongress" class="green large">Uloz</button></td></tr>
				</table>
		
</div>