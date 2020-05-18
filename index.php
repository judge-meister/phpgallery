<?php
// --------------------------------------------------------------------
// Re-write of gallery.py cgi script into php to allow addition of 
// login system.
// 
// need to analyse original gallery.py code for more features
// - can't find original python code, so further analysis is not an option
// --------------------------------------------------------------------
//
// I M P R O V E M E N T S
//
// - create a class object for a cell with derived classes for type of cell
// - create a factory class to supply and register them
// - cell classes would provide a render method which would use the SpanLogo classes
// hopefully this would allow a search page to be created more easily,  as well
// as possibilities like sorting, filtering etc...
//
// http://www.phpro.org/tutorials/Design-Patterns.html
// http://www.phpro.org/tutorials/Model-View-Controller-MVC.html
// http://www.phptherightway.com/pages/Design-Patterns.html
// http://www.php5dp.com/
// http://www.fluffycat.com/PHP-Design-Patterns/
//
// --------------------------------------------------------------------

//try {
/*function myErrorHandler($errno, $errstr, $errfile, $errline) { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/DTD/strict.dtd">
<html><body>
<?php
    echo "<b>Custom error:</b> [$errno] $errstr<br>";
    echo " Error on line $errline in $errfile<br>";
    echo "</body></html>";
    echo "\n";
    die();
}
*/
// Set user-defined error handler function
//set_error_handler("myErrorHandler");

if(!defined('INCLUDE_CHECK')) { define('INCLUDE_CHECK',true); }

if(!isset($_POST['PHPUNIT']))      { include( gethostname().'/config.php' ); } 
else   if($_POST['PHPUNIT']!=True) { include( gethostname().'/config.php' ); }

require_once( 'functions.php' );
require_once( 'Span.class.php' );
require_once( 'pluginLoader.php' );
require_once( 'gallery.php' );

$cfg = Config::getInstance();
$cfg->set('logon',False);

/*}
catch (Exception $e) { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/DTD/strict.dtd">
<html><body><pre>
<?php
        echo 'Caught exception: ', $e->getMessage(), "\n";
        echo $e->getTraceAsString();
        die();
}*/

//if(LOGIN_ENABLED == True && param('PHPUNIT') != True)
//{
//	require $_SERVER['DOCUMENT_ROOT'].LOGIN_PATH.'/user_check.php';
//	require $_SERVER['DOCUMENT_ROOT'].LOGIN_PATH.'/logoff.php';
//	//$Config['logon']=True;
//  $cfg->set('logon',True);
//}

function getBrowserWidth()
{
	if(isset($_COOKIE['currBrowserWidth']))
	{
		return (int)$_COOKIE['currBrowserWidth'];
	}
	return 1280;
}


// ----------------------------------- //
// ------- S T A R T   H E R E ------- //
// ----------------------------------- //

//echo "PHPUNIT = ".param('PHPUNIT')."\n";
if(param('PHPUNIT') != True) 
{
	if(param('media') != NULL)
	{
		$SITE_PORT = $_SERVER['SERVER_NAME'];
		// check if we are being accessed via ssh tunnel and localhost:8080/
		if($_SERVER['SERVER_NAME'] == 'localhost' && $_SERVER['SERVER_PORT'] != 80)
		{
			$SITE_PORT = $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'];
		}
		header("Location: http://".$SITE_PORT."/".param('media'));
		//print($SITE_PORT."\n");
		//var_dump($_SERVER);
		die();
	}
    try {
	    //$Config['screenWidth'] = getBrowserWidth();
	    //Config::screenWidth = getBrowserWidth();
	    $G = new Gallery($stdIgnores, getBrowserWidth(), param('path'), param('opt'));
		$PG = new Gallery($stdIgnores, getBrowserWidth(), dirname(param('path')), param('opt'));
	} catch (Exception $e) { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/DTD/strict.dtd">
<html><body><pre>
<?php
        echo 'Caught exception: ', $e->getMessage(), "\n";
        echo $e->getTraceAsString();
        die();
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/DTD/strict.dtd">
<html>
<?php require('head.php'); ?>
<body>
<?php if($cfg->get('logon') == True) { login_panel(); } ?>
<?php
//	<form name="gallery" action="< ? php echo PROGRAM; ? >" method="get">
//	</form>
?>	
	<div id="title">

	    <?php echo title($G->getPath()); ?>

	</div>


<?php 
	try {
		$wholePage = $G->wholePages();
		$using_kindgirls = $G->kindgirls();
		if(!$wholePage && !$using_kindgirls)
		{
			$G->buildThumbs();
			$PG->buildThumbs();
		}
		$G->pagebreakcomment();
		$PG->pageNavigation(basename(param('path'))); 
    }
    catch (Exception $e) {
        echo '<pre>';
        echo 'Caught exception: ', $e->getMessage(), "\n";
        echo $e->getTraceAsString();
        echo '<\pre>';
        die();
    }
?>

<!-- div class="thumbnails" id="thumbnails" -->
 <!-- div style="< ? php echo $G->getThumbWidth($wholePage); ? >" id="thumbnails" class="centredthumbs" -->
 <div style="width: 98%" id="thumbnails" class="centredthumbs">
  <?php
  try {
      echo $G->getHtml(); 
  }
  catch (Exception $e) {
      echo "Caught exception: ", $e->getMessage(), "\n";
      echo $e->getTraceAsString();
      die();
  }
  ?>
 </div>
 <!-- /div -->

<?php
if($using_kindgirls)
{
	$PG->pageNavigation(basename(param('path')));
}
?>

<script type="text/javascript" src="/js/lazy.js"></script>
<div id="appModeNote" style="display:none;">
	<em><a href="">Refresh!</a></em>
</div>

</body>
</html>

<?php
}	
?>
