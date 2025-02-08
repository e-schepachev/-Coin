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


$list = $nmc->listaddressgroupings();
// $addrkeys = array_keys($addr);
echo "<div class='content'>
<h2>Addresses</h2>";
echo "<form action='address.php' method='POST'>
<div class=\"row\">
<div class=\"col-sm-8\">
<table class='table-striped table-bordered table-condensed table'>
	<thead><tr><th >Address </th><th>Amount</th><th>Label</th></tr></thead>";
		foreach ($list as $group)
			foreach ($group as $balance)
			{
				$address = $balance[0];
				$amount = $balance[1];

				$label = "";
				if (count($balance) > 2 && $balance[2] !== "")
					$label = $balance[2];

				echo "<tr><td>". $address ."</td><td>". number_format($amount, 8) ."</td><td>".$label."</td></tr>";
			}

echo "</table>
</div>
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
