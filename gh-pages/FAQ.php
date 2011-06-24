<?php
session_start();
include 'includes/functions/main.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<title></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="author" content="Zze | Edenia-Gaming.com" />
		<meta name="description" content="Rift private server" />
		<link href="css/global.css" rel="stylesheet" type="text/css" />
		<link href="css/home.css" rel="stylesheet" type="text/css" />
		<link href="css/typography.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="container">
    <div id="header">
	<div id="logo"><h1></h1></div>
        <div id="global_nav">
        	<ul>
         		<li id="nav_forum"><a href="forum/">Froum</a></li>
				<li id="nav_faq"><a href="FAQ.php">FAQ</a></li>
				<?php
				if (isset($_SESSION['username'])) {
					echo'<li id="nav_account_management"><a href="account.php">Management</a>';
					echo '<li id="nav_logout"><a href="logout.php">logout</a></li>';
				}
				else {
				echo '<li id="nav_registertrion"><a href="register.php">Registertrion</a></li>';
				echo '<li id="nav_login"><a href="login.php">login.php</a></li>';
				}
				?>
            </ul>
        </div>
    </div>
    <div id="masthead">
</div>
	<div id="content">
		<div id="primary_content">
        <div id="faq">
        <div class="primary_panel_btm">
        <h2></h2>
			<ul>
			<?php include 'includes/text/faq.txt';?>
			</ul>
			<div class="clear">&nbsp;</div>
		</div>
	</div>
</div>
	<div id="sidebar">
		<div id="sidebar_btm">
			<h2></h2>
				<div id="sidebar_dark_container">
					<?php
						GetStatus($WorldServer['IP'],$WorldServer['Port'],"World Server");
						GetStatus($CharacterServer['IP'],$CharacterServer['Port'],"Character Server");
					?>
				</div>
			<div id="statistics"><a href="statistics">statistics</a></div>
		</div>
	</div>
</div>
	<div id="footer">
		<center>
		<?php include 'includes/text/footer.txt'; ?>
		</center>
	</div>
</div>
</body>
</html>