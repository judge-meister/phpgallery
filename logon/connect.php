<?php

if(!defined('INCLUDE_CHECK'))
{ 
	header("Location: http://".$_SERVER['SERVER_NAME']."/index.php");
	die();
}


/* Database config */

$db_host		= 'localhost';
$db_user		= 'judge';
$db_pass		= 'r0adster';
$db_database		= 'sessions'; 

/* End config */



//$link = mysql_connect($db_host,$db_user,$db_pass) or die('Unable to establish a DB connection');

//mysql_select_db($db_database,$link);
//mysql_query("SET names UTF8");

?>