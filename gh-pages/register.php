<?php
require 'includes/config.php';
require 'includes/functions/main.php';
session_start();
if(!empty($_POST["security"])){
	if($_SESSION["security"]  != $_POST["security"]) { $errors[] = "<center>Invalid security input.<br>please try again</center>"; }
	}
	$security = rand(10000, 100000);
	$_SESSION["security"] = $security;
	
	if(!empty($_POST["accountname"]) && !empty($_POST["password"]) && !empty($_POST["password_check"]) && !empty($_POST["email"]) && !empty($_POST["security"]))
	{
	$mysql_connect = mysqli_connect($mysql["host"], $mysql["username"], $mysql["password"]) or die("Unable to connect to the database.");
	mysqli_select_db($mysql_connect, $mysql["database"]) or die("Unable to connect to the database.");
	$setAcct_ID = mysqli_real_escape_string($mysql_connect, trim(strtolower(setAcct_ID())));
	$post_accountname = mysqli_real_escape_string($mysql_connect, trim(strtoupper($_POST["accountname"])));
	$post_displayname = mysqli_real_escape_string($mysql_connect, trim($_POST["accountname"]));
	$post_password = mysqli_real_escape_string($mysql_connect, trim(strtoupper($_POST["password"])));
	$post_password_final = mysqli_real_escape_string($mysql_connect, SHA1("".$post_accountname.":".$post_password.""));
	$post_password_md5 = mysqli_real_escape_string($mysql_connect, MD5($post_password));
	$post_password_check = trim(strtoupper($_POST["password_check"]));
	$post_email = mysqli_real_escape_string($mysql_connect, trim($_POST["email"]));
	$post_security_question = mysqli_real_escape_string($mysql_connect, trim($_POST['security_question']));
	$post_security_answer = mysqli_real_escape_string($mysql_connect, trim($_POST['security_answer']));
	$ip = mysqli_real_escape_string($mysql_connect, trim($_SERVER['SERVER_ADDR']));
	$check_account_query = mysqli_query($mysql_connect, "SELECT COUNT(*) FROM accounts WHERE Username = '".$post_accountname."'");
	$check_account_results = mysqli_fetch_array($check_account_query);
	$check_email_query = mysqli_query($mysql_connect, "SELECT COUNT(*) FROM accounts WHERE Email = '".$post_email."'");
	$check_email_results = mysqli_fetch_array($check_email_query);
	if($_POST['tos'] == "0" ) { $errors[] = "<center><strong><font color='red'>You did not agree with our ToU.</font</center></strong>"; }
	if($_POST['security_question'] == "0" )	{ $errors[] = "<center><strong><font color='red'>A security question is required to register.</font</center></strong>"; }
	if($check_account_results[0]!=0) { $errors[] = "<center><strong>Account name is already in use.</center></strong>"; }
	if($check_email_results[0]!=0) { $errors[] = "<center><strong>Email is already in use.</center></strong>"; }
	if(strlen($post_accountname) < 3) { $errors[] = "<center><strong>Account name is to short, Must be greater the 3 characters.</center></strong>"; }
	if(strlen($post_accountname) > 32) { $errors[] = "<center><strong>Account name is to long, Must be shorter then 32 characters.</center></strong>"; }
	if(strlen($post_password) < 6) { $errors[] = "<center><strong>Password is to short, it has to be greater then 6 characters.</center></strong>"; }
	if(strlen($post_password) > 32)	{ $errors[] = "<center><strong>Password is to long, it has to be shorter then 32 characters.</center></strong>"; }
	if(strlen($post_email) > 64) { $errors[] = "<center><strong>E-mail address is to long.</center></strong>"; }
	if(strlen($post_email) < 8) { $errors[] = "<center><strong>E-mail address is to short.</center></strong>"; }
	if(strlen($post_security_answer) > 26) { $errors[] = "<center><strong>Security answer can only contain 26 characters.</center></strong>"; }
	elseif(strlen($post_security_answer) < 1) { $errors[] = "<center><strong>Security answer was blank.</center></strong>"; }
	if(!preg_match("/^[0-9a-zA-Z%]+$/", $post_security_answer)) { $errors[] = "<center><strong>Security answer can only contain letters or numbers.</center></strong>"; }
	if(!preg_match("/^[0-9a-zA-Z%]+$/", $post_accountname)) { $errors[] = "<center><strong>Account name can only contain letters or numbers.</center></strong>"; }
	if(!preg_match("/^[0-9a-zA-Z%]+$/", $post_password)) { $errors[] = "<center><strong>Password can only contain letters and numbers.</center></strong>"; }
	if($CheckEmail == '1'){
	if(!preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i',$post_email)) { $errors[] = "<center><strong>Please enter a valid E-mail address</center></strong>"; }
	}
	if($post_accountname == $post_password) { $errors[] = "<center><strong>Password can't be the same as Account name.</center></strong>"; }
	if($post_password != $post_password_check) { $errors[] = "<center><strong>Passwords did not match.</center></strong>"; 
	}
	if(!is_array($errors)){
		mysqli_query($mysql_connect, "INSERT INTO accounts (Accounts_ID, Username, Sha_Password, SessionKey, Email, GmLevel) VALUES ('".$setAcct_ID."','".$post_accountname."','".$post_password_final."','null','".$post_email."',0)") or die(mysqli_error($mysql_connect));

		mysqli_query($mysql_connect, "INSERT INTO riftweb (id, username, displayname, md5, sha, plain, email, securityquestion, securityanswer, ip, gmlevel) VALUES ('".getAcct_ID()."' ,'".$post_accountname."', '".$post_displayname."', '".$post_password_md5."', '".$post_password_final."', '".$post_password."',  '".$post_email."','".$post_security_question."','".$post_security_answer."','".$ip."',0)") or die(mysqli_error($mysql_connect));

		$errors[] = '<center><strong>Successfully added the account: <font color="blue">'.$post_displayname.'</font> to the database.</center>';
	}
	mysqli_close($mysql_connect);
}
function error_msg(){
		global $errors;
			if(is_array($errors)){
				foreach($errors as $msg){
					echo $msg;		
				}
		}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<title></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="author" content="Zze | github.com" />
		<meta name="description" content="Rift private server" />
		<link href="css/global.css" rel="stylesheet" type="text/css" />
		<link href="css/home.css" rel="stylesheet" type="text/css" />
		<link href="css/typography.css" rel="stylesheet" type="text/css" />
</head>
<script type="text/javascript">
 function checkform ( form )
 {
	 if (form.accountname.value == "") { alert( "Account field is empty." ); form.accountname.focus(); return false; } else { if (form.accountname.value.length < 3) { alert( "Account name is to short, it has to be greater then 3 characters" ); form.accountname.focus(); return false; } }
	 if (form.password.value == "") { alert( "Password field was empty." ); form.password.focus(); return false; } else { if (form.password.value.length < 6) { alert( "Password is to short, it has to be greater then 6 characters." ); form.password.focus(); return false; } }
	 if (form.password_check.value == "") { alert( "You did not fill in a password. Please try again." ); form.password_check.focus(); return false; }
	 if (form.password.value == form.accountname.value) { alert( "Password can't match Account name" ); form.password.focus(); return false; }
	 if (form.password.value != form.password_check.value) { alert( "Password's didn't match." ); form.password.focus(); return false; }
	 if (form.email.value == "") { alert( "E-mail address field is empty." ); form.email.focus(); return false; } else { if (form.email.value.length < 7) { alert( "E-mail address is to short." ); form.email.focus(); return false; } }
	 if (form.security.value == "") { alert( "Captcha field is empty" ); form.security.focus(); return false; }
 return true ;
 }
</script>
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
			<div id="account_register">
			<div class="primary_panel_btm">
			<h2></h2>
			<ul>
				<center>
				<table class="reg">
					<tr>
						<td>
						<?php error_msg(); ?>
							<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" onsubmit="return checkform(reg);" name="reg">
							<table class="form">
								<tr>
									<td align="right">
										Account name:
									</td>
									<td align="left">
										<input name="accountname" type="text" maxlength="32" />
									</td>
								</tr>
								<tr>
									<td align="right">
										Password:
									</td>
									<td align="left">
										<input name="password" type="password" maxlength="32" />
									</td>
								</tr>
								<tr>
									<td align="right">
										Confirm password:
									</td>
									<td align="left">
										<input name="password_check" type="password" maxlength="32" />
									</td>
								</tr>
								<tr>
									<td align="right">
										E-mail address:
									</td>
									<td align="left">
										<input name="email" type="text" maxlength="32" />
									</td>
								</tr>
								<tr></tr><tr></tr>
								<tr>
									<td align="right">
										<font size="1" color="white">Security Question:</font></a>
										</td>
									<td>
										<select class="centered" name="security_question">
											<option SELECTED value="0">--Please Choose--
											<option value="1">Name of your first pet</option>
											<option value="2">Favorite color </option>
											<option value="3">Place of birth</option>
											<option value="4">Lucky number..</option>
										</select>
									</td>
								</tr>
								<tr>
									<td align="right">
										<font size="1" color="white">Answer:</font>
									</td>
									<td align="left">
										<input name="security_answer" type="text" maxlength="32" />
									</td>
								</tr>
								<tr></tr><tr></tr>
								<tr>
									<td align="right">
										Captcha: <font style="color:#2c622f;"><?php echo $security; ?></font>
									</td>
									<td align="left">
										<input name="security" type="text" maxlength="5" />
									</td>
								</tr>
								<tr></tr><tr></tr>
								<tr>
									<td align="right">
										<a href=""><font size="1" color="white"><u>Terms of Use</u></font></a> :
									</td>
									<td>
										<select name="tos">
											<option SELECTED value="0">I decline these Terms of Use</option>
											<option value="1">I accept these Terms of Use</option>
										</select>
									</td>
								</tr>
								<tr>
									<td colspan="2" align="center">
										<input type="submit" class="register_smb" value="Register" />
									</td>
								</tr>
							</table>
							</form>
						</td>
					</tr>
				</table>
				Registering here, creates an account available with forum and game access.
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