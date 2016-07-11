<?php

session_name('tzLogin');
session_set_cookie_params(2*7*24*60*60); // 14 days
session_start();

if((!isset($_SESSION['id'])) || (!$_SESSION['id']))
{
    if(isset($_SERVER['SERVER_NAME']))
    {
		$SITE_PORT = $_SERVER['SERVER_NAME'];
		// check if we are being accessed via ssh tunnel and localhost:8080
		if($_SERVER['SERVER_NAME'] == 'localhost' && $_SERVER['SERVER_PORT'] != 80)
		{
			$SITE_PORT = $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'];
		}
	    if(defined('STARTURL'))
	    {
	    	header("Location: http://".$SITE_PORT."/?activepage=".STARTURL);
	    }
	    else
	    {
    		header("Location: http://".$SITE_PORT);
	    }
	    die();
    }
}
?>

