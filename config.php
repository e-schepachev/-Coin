<?php
/**
 * @author Chris S - AKA Someguy123
 * @version 0.01 (ALPHA!)
 * @license PUBLIC DOMAIN http://unlicense.org
 * @package +Coin - Bitcoin & forks Web Interface
 * This is the config file for +Coin
 * You MUST add your daemon host information to this
 * file, or it won't work.
 * 
 */

	$wallets = array();

	$wallets['testwallet'] = array(
		"user" => "testuser",  
		"pass" => "testpass",      
		"host" => "172.20.0.3",     
		"port" => 18443,
		"protocol" => "http"
	);
?>
