<?php

if(!defined('INCLUDE_CHECK')) 
{ 
	// die('You are not allowed to execute this file directly');
	header("Location: http://".$_SERVER['SERVER_NAME']."/index.php");
	die();
}

function checkEmail($str)
{
	return preg_match("/^[\.A-z0-9_\-\+]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $str);
}


function send_mail($from,$to,$subject,$body)
{
	$headers = '';
	$headers .= "From: $from\n";
	$headers .= "Reply-to: $from\n";
	$headers .= "Return-Path: $from\n";
	$headers .= "Message-ID: <" . md5(uniqid(time())) . "@" . $_SERVER['SERVER_NAME'] . ">\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Date: " . date('r', time()) . "\n";

	mail($to,$subject,$body,$headers);
}

function passwordIsValid($post)
{
	if(False == PASSWD_DB) { return array("id" => 1, "usr" => "judge"); }
	//select SHA2(CONCAT("Zaq1@mko0",SHA2("judge@skynet.wrac3eus.org/10.0.1.69/2013-12-05 14:46:03",256)),512); 
	$escUser = mysql_real_escape_string($post['username']);
	$escPass = mysql_real_escape_string($post['password']);
	$row = mysql_fetch_assoc(mysql_query("SELECT id,usr,SHA2(CONCAT(\"".$escPass."\", SHA2(CONCAT(email,'/',dt),256)),512) = pass AS ok FROM tz_members WHERE usr=\"".$escUser."\";"));
	if($row['ok'] == '1')
	{
		return array("id"=>(int)$row['id'], "usr"=>$row['usr']);
	}
	return array("id" => -1, "usr" => "");
}


//$row1 = mysql_fetch_assoc(mysql_query("SELECT id,usr,email,regIP,dt FROM tz_members WHERE usr='{$post['username']}'"));
//$salt = $row1['email']."/".$row1['regIP']."/".$row1['dt'];
//$query = "SELECT id,usr FROM tz_members WHERE usr=\"".$escapedUsername."\" AND 
//		      pass=SHA2(CONCAT(\"".$escapedPassword."\",SHA2(\"".$salt."\",256)),512)";
//$r = mysql_fetch_assoc(mysql_query($query));
//if($r['id'])
//{
//	return $r;
//}

?>