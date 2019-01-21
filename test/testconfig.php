<?php
$_SERVER['SERVER_PORT'] = 80;
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['QUERY_STRING'] = '';

if(!defined('INCLUDE_CHECK')) { define('INCLUDE_CHECK',true); }

if(!isset($_POST['PHPUNIT']))      { include( gethostname().'/config.php' ); } 
else   if($_POST['PHPUNIT']!=True) { include( gethostname().'/config.php' ); }
	
include('functions.php');

//var_dump(explode('/','pagesize'));
$aaa = Config::getInstance();
//var_dump($aaa->get());
echo "pagesize = ".$aaa->get('pagesize')."\n";

$aaa->set('pagesize',99);
//echo "GETALL\n";
//var_dump($aaa->get());

echo "pagesize = ".$aaa->get('pagesize')."\n";
//echo "GETALL\n";
//var_dump($aaa->get());
//echo "GETALL AGAIN\n";
//var_dump($aaa->get());
if($aaa->get('unknown') != false) { 
	echo "unknown = ".$aaa->get('unknown')."\n";
} else {
	echo "unknown = false\n";
}
?>