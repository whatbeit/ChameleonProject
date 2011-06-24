<?php
	/*
	#############################################################
	# Edit the below settings to match your own servers details #
	#############################################################
	*/

	// MySQL Host info
	$mysql = array(
	"host" => "localhost",
	"username" => "root",
	"password" => "",
	"database" => "acct_db",
	);

	// character server ip and port
	$CharacterServer = array(
	"IP" => '127.0.0.1',
	"Port" => '',
	);

	// world server ip and port
	$WorldServer = array(
	"IP" => '127.0.0.1',
	"Port" => '',
	);
	/*
	#############################################################
	# 				Registertation_script settings 				#
	#############################################################
	*/

	// enable or disable vaild email checking
		// 0 = off | 1 = on
	$CheckEmail	= '1';

	// minimum - maximum characters allowed in account
	$MinMax_account = array(
		"mimimum" => '3',
		"maximum" => '26',
	);

	// minimum - maximum characters allowed in password
	$MinMax_password = array(
		"mimimum" => '3',
		"maximum" => '26',
	);

	// minimum - maximum characters allowed in email
	$MinMax_email = array(
		"mimimum" => '3',
		"maximum" => '26',
	);
?>