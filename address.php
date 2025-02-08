<?php

/**
 * @author Chris S - AKA Someguy123
 * @version 0.01 (ALPHA!)
 * @license PUBLIC DOMAIN http://unlicense.org
 * @package +Coin - Bitcoin & forks Web Interface
 */

include ("header.php");
?>

<!-- Java Script -->
<script type='text/javascript'>

$(document).on("click", ".open-EditAddrDialog", function () {
     var myAddrId = $(this).data('id');
     $("#myAddress").html(myAddrId);
     $(".modal-body #myAddress").val( myAddrId );

     var myAddrName = $(this).data('name');
     $(".modal-body #AddrName").val( myAddrName );

    $('#EditAddrDialog').modal('show');
});

</script>

<?php
if (isset($_POST['addaddr']))
{
  //$nmc->getnewaddress();
  print("Call getnewaddress('".$_POST['label'."')"]);
}

$myaddresses = file("myaddresses.csv");
$myaddress_arr = array();

if (is_bool($myaddresses) != true)
{
	foreach ($myaddresses as $line)
	{
		$values = explode(";", $line);
		$address = $values[0];
		$name = str_replace("\n", "", $values[1]);
		$myaddress_arr[$address] = $name;
	}
}


if (isset($_POST['AddrName']) && isset($_POST['myAddress']))
{
	$myaddress_arr[$_POST['myAddress']] = $_POST['AddrName'];

	$f = fopen("myaddresses.csv", "w");

	foreach ($myaddress_arr as $address => $name)
	{
			$line = $address.";".$name."\n";
			fputs($f, $line);
	}
	fclose($f);
}


$labels = $nmc->listlabels();
$addresses_balances = array();
// $addrkeys = array_keys($addr);
echo "<h2>Addresses</h2>";
echo "<div class='content'>";
echo "<form action='address.php' method='POST'>
<div class=\"row\">
<div class=\"col-sm-8\">";

		foreach ($labels as $label)
		{
			$addressesOnLabel = $nmc->getaddressesbylabel($label);
			foreach ($addressesOnLabel as $address => $purpose)
			{
				$addresses_balances[$address] = array('amount' => 0, 'label' => $label, 'group' => -1);
			}
		}

		$groups = $nmc->listaddressgroupings();

		$groupId = 0;
		foreach ($groups as $group)
		{
			foreach ($group as $info)
			{	
				$address = $info[0];

				if (in_array($address, array_keys($addresses_balances)))
					$addresses_balances[$address]['group'] = $groupId;
				else
					$addresses_balances[$address] = array('amount' => 0, 'label' => null, 'group' => $groupId);

			}
			$groupId++;
		}

		$unspent = $nmc->listunspent();

		foreach ($unspent as $address_unspent)
		{
			$addresses_balances[$address_unspent['address']]['amount'] = $address_unspent['amount'];
		}

		for ($i = -1 ; $i < $groupId; ++$i)
		{
			if ($i < 0)
				echo 	"<h3>Ungrouped</h3>";
			else
				echo 	"<h3>Group ".$i."</h3>";

			echo 	"<table class='table-striped table-bordered table-condensed table'>
					<thead><tr><th>Address</th><th>Amount</th><th>Label</th></tr></thead>";

			foreach ($addresses_balances as $address=>$info)
			{
				if ($info['group'] == $i)
				{
					$labelCell = $info['label'] === null ? "<td><em>unset<em></td>" : "<td>".$info['label']."</td>";
					echo "<tr><td>". $address ."</td><td>". number_format($info['amount'], 8) ."</td>".$labelCell."</tr>";
				}
			}
			echo "</table>";
		}

echo "</div>
<div class=\"col-sm-2\">
	<input class='btn btn-default form-control' name='addaddr' type='submit' value='Add address' />
</div>
</div>
</form><br>";
?>
<form action='address.php' method='POST'>
<!-- Modal --->
<div id="EditAddrDialog" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="myModalLabel">Change Address Name</h3>
	</div>
	<div class="modal-body">
	<table><tr>
		<td><div id="myAddress">Address to change</div></td>
		<td><input class="form-control" type="text" name="AddrName" id="AddrName" value="Name"/></td>
		</tr></table>
		<input type="hidden" name="myAddress" id="myAddress" value="Nothing"/>
		<input type="hidden" name="label" id="label" value="<?php echo $label ?>"/>
		</div>
		<div class="modal-footer">
			<button class="btn btn-default" data-dismiss="modal">Close</button>
			<button class="btn btn-primary">Save Changes</button>
		</div>
</div>
</form>
<?php
echo"</div>";

echo "</div>";
include ("footer.php");
?>
