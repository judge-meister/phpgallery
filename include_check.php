<?php
require_once( gethostname().'/config.php' );

if(!defined('INCLUDE_CHECK'))
{ 
	header("Location: http://".$_SERVER['SERVER_NAME']."/?activepage=".PROGRAM);
	die();
}
?>