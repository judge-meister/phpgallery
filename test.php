<?php
if(!defined('INCLUDE_CHECK')) { define('INCLUDE_CHECK',true); }

if(gethostname() == "skynet") 
{
	$_SERVER['DOCUMENT_ROOT'] = "/home/www/html";
	$root = "/home/www/html";
	$path = "/testing/testpage/"; 
	$path = "/secret/sdc1/kindgirls.com/videos/960x540";
	$parent = "/secret/sdc1/kindgirls.com/videos";
}
else 
{
	$_SERVER['DOCUMENT_ROOT'] = "/Users/judge/Sites";
	$root = "/Users/judge/Sites";
	$path = "/testpage/"; 
	$path = "/Pictures/kindgirls/gals.kindgirls.com/NakedBy";
	$parent = "/Pictures/kindgirls/gals.kindgirls.com";
}
echo "Start\n";
$_GET['path'] = $path;

require_once( 'functions.php' );
//require_once( 'Span.class.php' );
//require_once( 'pluginLoader.php' );
require_once( 'gallery.php' );

$opt = "1_1000";


$browser_width = 1280;
echo $browser_width."\n";

$PG = new Gallery($stdIgnores, $browser_width, $parent, $opt);
$G = new Gallery($stdIgnores, $browser_width, $path, $opt);
echo title($G->getPath())."\n";

$PG->buildThumbs();
$G->buildThumbs();
$PG->pageNavigation(basename($path));

echo $G->getHtml();

//echo "\n";
//$PG->display_ordered_file_list();

?>