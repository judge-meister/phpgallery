<?php
require_once( 'config.php' );

if($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_NAME'] == 'localhost')
{
	$SITE_PORT = $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'];
}
else
{
	$SITE_PORT = $_SERVER['SERVER_NAME'];
}

if(!defined('INCLUDE_CHECK'))
{ 
	header("Location: http://".$SITE_PORT."/?activepage=".PROGRAM);
	die();
}
?>
