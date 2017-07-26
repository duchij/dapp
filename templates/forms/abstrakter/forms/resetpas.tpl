
				<h1> Reset hesla do aplikácie ABSTRAKTER</h1>
				<hr />
				<div class="dissmisible info message">
				Zadaním Vašej emailovej adresy, ktorú používate na prihlásenie do systému sa Vám vytvorí unikátny link. Tento
				link Vám bude zaslaný na uvedenú emailovú adresu a kliknutím naň sa Vám umožní zmeniť heslo do systému.
				<br>Pozor tento link je platný len 24hodín...
				</div>
				<form method='post' action="index.php">
					<input type="hidden" name="mm" value="lostPasswd1">
					<table>
						<tr><td class="large">Email:</td><td> <input type="text" name="email"></td></tr>
						<!--<tr><td>Heslo: </td><td> <input type="password" name="password"></td></tr>
						<tr><td>Re-Heslo:</td><td>  <input type="password" name="password2"></td></tr>-->
						<tr><td colspan="2"><input type="submit" value="Resetuj" class="large"></td></tr>
						
					</table>
				</form> 
			