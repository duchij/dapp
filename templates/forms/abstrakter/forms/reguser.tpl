
				{if $email}
				<h1>Restovanie zabudnuteho hesla</h1>
				{else}
				<h1> Registrácia nového užívateľa do aplikácie ABSTRAKTER</h1>
				{/if}
				<hr />
				<form method='post' action="index.php">
				
					{if $reset == "passwd"}
					<input type="hidden" name="mm" value="resetHeslo1">
					{else}
					<input type="hidden" name="mm" value="setNewUser">
					{/if}
					<table>
						<tr><td class="large">Email:</td><td> <input type="text" name="email" value="{$email}" {$state}></td></tr>
						<tr><td class="large">Heslo: </td><td> <input type="password" name="password"></td></tr>
						<tr><td class="large">Re-Heslo:</td><td>  <input type="password" name="password2"></td></tr>
						<tr><td colspan="2">
						{if $email}
						<input type="submit" value="Zmeň">
						{else}
						<input type="submit" value="Zaregistruj...">
						{/if}
						
						</td></tr>
						
					</table>
				</form> 
			