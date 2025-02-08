<?php
/**
 * @author Chris S - AKA Someguy123
 * @version 0.01 (ALPHA!)
 * @license PUBLIC DOMAIN http://unlicense.org
 * @package +Coin - Bitcoin & forks Web Interface
 */
	include ("header.php");
	$trans = $nmc->listtransactions('*', 7);
	$x = array_reverse($trans);
	$bal = $nmc->getbalance(null, 6);
	$bal3 = $nmc->getbalance(null, 0);
	$bal2 = $bal - $bal3;
?>

<div class='content'>
<div class='row'>
<div class='col-md-6'>
	<h3>Current Balance: <font color='green'><?php echo $bal; ?></font></h1>
	<h4>Unconfirmed Balance: <font color='red'><?php echo $bal2; ?></font></h2>
	<hr />
	<h3>Send coins:</h3>
	<form action='send.php' method='POST'>
	<table class="table">
		<tr>
			<td>To address:</td>
			<td>
				<?php 
					// addressbook
					$addressbook = file("addressbook.csv");
					echo "<select class=\"form-control\" name='addressbook'>";
					echo "<option value='---'>Use custom to address:</option>";
					foreach ($addressbook as $line)
					{
						$values = explode(";", $line);
						$address = $values[0];
						$name = str_replace("\n", "", $values[1]);
						echo "<option value='{$address}'>{$name} ({$address})</option>";
					}
					echo "</select><br />";
				
					echo "<input class=\"form-control\" type='text' placeholder='To address' name='address'>";
				?>
			</td>
		</tr>
		<tr>
			<td>Amount:</td>
			<td><input class="form-control" type='text' placeholder='Amount' name='amount'></td>
		</tr>
		<tr>
			<td>Passphrase:</td>
			<td>
				<?php
					if (isset($_POST['PassPhrase']) && isset($_POST['PassPhrase2']))
					{
						//check both passwords are the same
						if ($_POST['PassPhrase'] === $_POST['PassPhrase2'])
						{
							if (isset($_POST['CurrPassPhrase']))
							{
								// Change password
								try {
									$nmc->walletpassphrasechange($_POST['CurrPassPhrase'], $_POST['PassPhrase']);

	  							echo "<p class='bg-success'>
									<button type='button' class='close' data-dismiss='alert'>&times;</button>
									Wallet passphrase successfully changed.
									</p>";												
								} catch(Exception $e) {
									echo "<p class='bg-danger'><strong>Passphrase error!</strong> Wrong current passphrase entered.</p>";
								}

							}else{

								// Set password
								$nmc->encryptwallet($_POST['PassPhrase']);

								echo "<p class='abg-success'>
								<button type='button' class='close' data-dismiss='alert'>&times;</button>
								Wallet is now encypted.<br>Keep that passphrase safe!
								</p>";
							}
						}
						else
						{
							echo "<p class='bg-danger'>
							<button type='button' class='close' data-dismiss='alert'>&times;</button>
							<strong>Warning!</strong> Passphrases do not match!<br>Wallet encryption not set.
							</p>";
						}
					}
				
					if ($wallet_encrypted)
						echo "<div class='input-group'>
						<input class=\"form-control\" type='password' placeholder='Wallet Passphrase' name='walletpassphrase'>
							<span class=\"input-group-btn\">
								<button class='open-ChangePassPhrase btn btn-default' type=\"button\">Change</button>
							</span>
						</div>";
					else
						echo "<p class=\"help-block\">Wallet un-encrypted &nbsp; &nbsp; <a href='#SetPassPhrase' class='open-SetPassPhrase btn btn-default btn-xs'>Set</a></p>";
       ?>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<br><input class='btn btn-primary' type='submit' value='Send coins'>	
			</td>
		</tr>
	</table>
    </form>
    
    <hr />
    <h3>Daemon Info</h3>
    <table class="table-striped table-bordered table">
    	<thead>
    		<tr>
    			<th>Key</th>
    			<th>Value</th>
    		</tr>
    	</thead>
    	<tbody>
    		 <?php $info = $nmc->getwalletinfo(); ?>
				<?php
					foreach ($info as $key => $val){
						if ($val != "")
							echo "<tr><td>".$key."</td><td>".$val."</td></tr>";
						}
					?>
    	</tbody>
    </table>
    </div>
    <div class='col-md-6'>
    <table class='table-striped table-bordered table'>
    <thead><tr><th>Method</th><th>Account and Address</th><th>Amount</th><th>Confirms</th></tr></thead>

<?php
	// Load address book
	$addresses_arr = array();
	$addressbook = file("addressbook.csv");
	foreach ($addressbook as $line)
	{
		$values = explode(";", $line);
		$address = $values[0];
		$name = str_replace("\n", "", $values[1]);
		$addresses_arr[$address] = $name;
	}
	// Load my addresses
	$myaddresses = file("myaddresses.csv");
	foreach ($myaddresses as $line)
	{
		$values = explode(";", $line);
		$address = $values[0];
		$name = str_replace("\n", "", $values[1]);
		$addresses_arr[$address] = $name;
	}

	foreach ($x as $x)
	{
    if($x['amount'] > 0) { $coloramount = "green"; } else { $coloramount = "red"; }
    if($x['confirmations'] >= 6) { $colorconfirms = "green"; } else { $colorconfirms = "red"; }

	//$date = date(DATE_RFC822, $x['time']);
	echo "<tr>";
    echo "<td>" . ucfirst($x['category']) . "</td>";
	if (isset($x['address']))
	{
		if ($addresses_arr[$x['address']])
			$name = $addresses_arr[$x['address']];
		else 
			$name = $x['address'];
		echo "<td>{$name} - {$x['account']}</td>";
	}
	else
	   echo "<td style='text-align: center'>Generated</td>";
	echo "<td><font color='{$coloramount}'>{$x['amount']}</font></td><td><font color='{$colorconfirms}'>{$x['confirmations']}</font></td></tr>";
}
echo "</table>
<a href='btc.php'>More...</a>
    </div>
</div>";

?>
<form action='index.php' method='POST'>
<!-- Modal --->
<div id="SetPassPhrase" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
<h3 id="myModalLabel">Set Wallet Pass Phrase</h3>
</div>
<div class="modal-body">
  Choose a long, secure passphrase... Your wallet depends on it:
  <br><input type="password" class="input-xxlarge" name="PassPhrase" id="PassPhrase" value="" />
  <br>Re-type to confirm:
  <br><input type="password" class="input-xxlarge" name="PassPhrase2" id="PassPhrase2" value="" />
  <br>Passphrase can be changed later.
</div>
<div class="modal-footer">
<button class="btn" data-dismiss="modal">Close</button>
<button class="btn btn-primary">Save Changes</button>
</div>
</div>

<!-- Modal --->
<div id="ChangePassPhrase" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
<h3 id="myModalLabel">Change Wallet Pass Phrase</h3>
</div>
<div class="modal-body">
  Enter your current passphrase:
  <br><input type="password" class="input-xxlarge" name="CurrPassPhrase" id="CurrPassPhrase" value="" />
  <br>Choose a new long, secure passphrase:
  <br><input type="password" class="input-xxlarge" name="PassPhrase" id="PassPhrase" value="" />
  <br>Re-type to confirm:
  <br><input type="password" class="input-xxlarge" name="PassPhrase2" id="PassPhrase2" value="" />
</div>
<div class="modal-footer">
<button class="btn" data-dismiss="modal">Close</button>
<button class="btn btn-primary">Save Changes</button>
</div>
</div>
</form>

<?php 
include("footer.php");
?>
