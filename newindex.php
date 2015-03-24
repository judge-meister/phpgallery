<?php
/*
D E S I G N

inputs: path to current folder, 
		page number, 
		thumb count
outputs: content of folder as html, 
		paginated

responsibility: get content of folder, 
				categorize that content, 
				render each item depending on category
*/

if(!defined('INCLUDE_CHECK')) { define('INCLUDE_CHECK',true); }
//if(!defined($_SERVER['DOCUMENT_ROOT'])) { $_SERVER['DOCUMENT_ROOT'] = "/Users/judge/Sites"; }

require_once('celldata.class.php');
//require_once('functions.php');
require_once('newhead.php');

if(gethostname() == "skynet") 
{
	$_SERVER['DOCUMENT_ROOT'] = "/home/www/html";
	$root = "/home/www/html";
	$path = "/testing/testpage/"; 
	$path = "/secret/sdc1/inthecrack.com/videos/";
}
else 
{
	$_SERVER['DOCUMENT_ROOT'] = "/Users/judge/Sites";
	$root = "/Users/judge/Sites";
	$path = "/testpage/"; 
}

$base = $root.$path;


$cells = FolderItemFactory::create()->read($path);
$content = $cells->html();

$page = Page::createPage();
echo $page->html($content);


?>
