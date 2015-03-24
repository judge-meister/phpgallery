<?php

session_name('tzLogin');
session_set_cookie_params(2*7*24*60*60); // 14 days
session_start();

if((!isset($_SESSION['id'])) || (!$_SESSION['id']))
{
    if(isset($_SERVER['SERVER_NAME']))
    {
	    if(defined('STARTURL'))
	    {
	    	header("Location: http://".$_SERVER['SERVER_NAME']."/?activepage=".STARTURL);
	    }
	    else
	    {
    		header("Location: http://".$_SERVER['SERVER_NAME']);
	    }
	    die();
    }
}
?>

