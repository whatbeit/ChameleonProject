<?php
require 'includes/config.php';
$host = $mysql['host'];
$user = $mysql['username'];
$pass = $mysql['password'];
$database = $mysql['database'];

function acctID(){
		global $host,$user,$pass,$database;
		$mysql_connect = mysqli_connect($host,$user,$pass) or die("Unable to connect to the database.");
		mysqli_select_db($mysql_connect,$database) or die("Unable to connect to the database.");
			$username = mysqli_real_escape_string($mysql_connect, trim(strtoupper($_SESSION['username'])));
			$query = mysqli_query($mysql_connect,"SELECT `id` FROM riftweb WHERE (username = '".$account."')");
			$row = mysqli_fetch_array($query);
			return $row['id'];
}

function displayName(){
		global $host,$user,$pass,$database;
		$mysql_connect = mysqli_connect($host,$user,$pass) or die("Unable to connect to the database.");
		mysqli_select_db($mysql_connect,$database) or die("Unable to connect to the database.");
			$account = mysqli_real_escape_string($mysql_connect, trim(strtoupper($_SESSION['username'])));
			$displayname = mysqli_query($mysql_connect,"SELECT `displayname` FROM riftweb WHERE (username = '".$account."')");
			$row = mysqli_fetch_array($displayname);
			return $row['displayname'];
}

function getStatus($host, $port, $name){
		$err = array('no' => NULL, 'str' => NULL);
		$status = @fsockopen($host, $port, $err['no'], $err['str'], (float)1.0);
		if(!$status){
			echo $name," is <font color='red'><strong>OFFLINE</strong></font>";
		}else
		{
			echo $name," is <font color='green'><strong>ONLINE</strong></font>";
			fclose($status);
		}
		echo '<br />';
}

function setAcct_ID(){
		mt_srand((double)microtime()*10000);
		$charid = strtoupper(md5(uniqid(rand(), true)));
		$hyphen = chr(45);
		$uuid = substr($charid, 0, 8).$hyphen
			.substr($charid, 8, 4).$hyphen
			.substr($charid,12, 4).$hyphen
			.substr($charid,16, 4).$hyphen
			.substr($charid,20,12);
		return $uuid;
}		

function getAcct_ID(){
		global $host,$user,$pass,$database;
		$mysql_connect = mysqli_connect($host,$user,$pass) or die("Unable to connect to the database.");
		mysqli_select_db($mysql_connect,$database) or die("Unable to connect to the database.");
			$query = mysqli_query($mysql_connect,"SELECT MAX(`id`) FROM accounts");
			$row = mysqli_fetch_array($query);
			return $row['0'];
}

function deleteAcct(){
		global $host,$user,$pass,$database;
		$mysql_connect = mysqli_connect($host,$user,$pass) or die("Unable to connect to the database.");
		mysqli_select_db($mysql_connect,$database) or die("Unable to connect to the database.");
			$account = mysqli_real_escape_string($mysql_connect, trim(strtoupper($_SESSION['username'])));
			$query = mysqli_query($mysql_connect,"SELECT `id` FROM riftweb WHERE (username = '".$account."')");
			$row = mysqli_fetch_array($query);
			$query = mysqli_query($mysql_connect,"DELETE FROM riftweb WHERE (id = '".$row['id']."'),");
			$query = mysqli_query($mysql_connect,"DELETE FROM accounts WHERE (Id = '".$row['id']."'),");
			unset($_SESSION['username']);
			session_destroy();
			header('Location: index.php');
			exit;
}
?>