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
$_SERVER['DOCUMENT_ROOT'] = "/Users/judge/Sites";

require_once('celldata.class.php');
require_once('functions.php');
require_once('newhead.php');

$root="/Users/judge/Sites/";
$path="testpage/";
$base=$root.$path;


$cells = FolderItemFactory::create()->read($base);
$content = $cells->html();

$page=Page::createPage();
echo $page->html($content);


?>