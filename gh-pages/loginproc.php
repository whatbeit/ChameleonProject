<?php
session_start();
require 'includes/config.php';
require 'includes/functions/main.php';
$mysql_connect = mysqli_connect($mysql["host"], $mysql["username"], $mysql["password"]) or die("Unable to connect to the database.");
mysqli_select_db($mysql_connect, 'acct_db') or die("Unable to connect to the database.");
$post_accountname = mysqli_real_escape_string($mysql_connect, trim(strtoupper($_POST["accountname"])));
$post_password = mysqli_real_escape_string($mysql_connect, trim(strtoupper($_POST["password"])));
$post_password_final = mysqli_real_escape_string($mysql_connect, SHA1("".$post_accountname.":".$post_password.""));
		$login = mysqli_query($mysql_connect,"SELECT * FROM accounts WHERE (Username = '" . $post_accountname . "') and (Sha_Password = '" . $post_password_final . "')");
			if (mysqli_num_rows($login) == 1) {
				$ip = mysqli_real_escape_string($mysql_connect, trim($_SERVER['SERVER_ADDR']));
				$set_lastlogin = mysqli_query($mysql_connect,"UPDATE riftweb SET ip='".$ip."' WHERE (username='".$post_accountname."')");
				$_SESSION['username'] = $_POST['accountname'];
					header('Location: account.php');
	}
	else{
		echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml"><head>
		<title>Login failed</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="author" content="Zze | github.com" />
		<meta http-equiv="refresh" content="6;url=login.php">
		<meta name="description" content="Rift private server" />
		<link href="css/global.css" rel="stylesheet" type="text/css" />
		<link href="css/home.css" rel="stylesheet" type="text/css" />
		<link href="css/typography.css" rel="stylesheet" type="text/css" />
		</head>
	<body>
		<div id="container">
		<div id="header">
		<div id="logo"><h1></h1></div>
		</div>
			<div id="masthead">
			</div>
				<div id="content">
					<div id="primary_content">
					<div id="blank">
					<div class="primary_panel_btm">
					<h2></h2>
					<ul><center>
						Invaild Password or Username.
						<br />
						<a href="login.php"><font color="blue">Back</font></a>
						</center>
					  <div class="clear">&nbsp;</div>
				</div>
				</div>
		   </div>
			<div id="sidebar">
			<div id="sidebar_btm">
				<h2></h2>
				<div id="sidebar_dark_container">
		',
		GetStatus($WorldServer['IP'],$WorldServer['Port'],"World Server");
		GetStatus($CharacterServer['IP'],$CharacterServer['Port'],"Character Server");
		'
				</div>
			<div id="statistics"><a href="statistics">statistics</a></div>
		</div>
	</div>
</div>
	<div id="footer">
		<center>
			<small>
			';
			include 'includes/text/footer.txt';
			echo'
			</small>					
		</center>
	</div>
</div>
</body>
</html>' ;
}
?>