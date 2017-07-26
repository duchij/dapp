		<h1>Moje kontaktne údaje...</h1>
		<input type="hidden" id="user_id" value="{$data.user_id}">
				<table>
					
					<tr> 
					<td class="large">Titul pred menom: </td>
					
					<td ><input type="text" id="titul_pred" value="{$data.titul_pred}"  style="width:400px;"></td>
					
					</tr>
					
					<tr><td class="large">Meno:</td> <td><input type="text" id="meno" value="{$data.meno}" style="width:400px;"></td></tr>
					<tr><td class="large">Priezvisko:</td> <td><input type="text" id="priezvisko" value="{$data.priezvisko}"  style="width:400px;"></td></tr>
					<tr><td class="large">Titul za menom:</td> <td> <input type="text" id="titul_za" value="{$data.titul_za}"  style="width:400px;"></td></tr>
					<tr><td class="large">Kontaktný email:</td> <td> <input type="text" id="contact_email" value="{$data.contact_email}"  style="width:400px;">
					<p class="red"> Tento mail je veľmi dôležitý, pretože naň budete dostávať info maily</p>
										</td></tr>
					<tr><td class="large">Kontaktný telefon:</td> <td> <input type="text" id="contact_phone" value="{$data.contact_phone}"  style="width:400px;"></td></tr>
					
					<tr><td class="large">Adresa pracoviska:</td> <td> <textarea rows="10" id="adresa" style="width:400px;" >{$data.adresa}</textarea></td></tr>
					<tr><td colspan="2"><button id="saveUserData" class="green large">Ulozit</button></td></tr>
								
			
		</table>
		