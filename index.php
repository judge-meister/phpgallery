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


if(!defined('INCLUDE_CHECK')) { define('INCLUDE_CHECK',true); }

if(!isset($_POST['PHPUNIT']))      { include( 'config.php' ); } 
else   if($_POST['PHPUNIT']!=True) { include( 'config.php' ); }

require_once( 'functions.php' );
require_once( 'Span.class.php' );
require_once( 'pluginLoader.php' );
require_once( 'gallery.php' );

$cfg = Config::getInstance();
$cfg->set('logon',False);

function getBrowserWidth()
{
    if(isset($_COOKIE['currBrowserWidth']))
    {
        return (int)$_COOKIE['currBrowserWidth'];
    }
    return 1280;
}

if(param('PHPUNIT') == True) 
{
    die();
}

// ----------------------------------- //
// ------- S T A R T   H E R E ------- //
// ----------------------------------- //

// handle media - like videos
if(param('media') != NULL)
{
    //$SITE_PORT = $_SERVER['SERVER_NAME'];
    // check if we are being accessed via a port other that 80 (eg. ssh tunnel and localhost:8080/)
    //if($_SERVER['SERVER_PORT'] != "80")
    //{
    //    $SITE_PORT = $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'];
    //}
    header("Location: http://".$_SERVER['HTTP_HOST'].param('media'));
    //print($SITE_PORT."\n");
    //var_dump($_SERVER);
    die();
}
    
$G = new Gallery($stdIgnores, getBrowserWidth(), param('path'), param('opt'));
$PG = new Gallery($stdIgnores, getBrowserWidth(), dirname(param('path')), param('opt'));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/DTD/strict.dtd">
<html>
<?php require('head.php'); ?>
<body>
<?php if($cfg->get('logon') == True) { login_panel(); } ?>
<?php
?>
  <div id="title">
    <?php echo title($G->getPath()); ?>

<?php
echo "<!-- SERVER_NAME:SERVER_PORT=".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']."   SITE_PORT=".$SITE_PORT." -->";
echo "<!-- PHP_SELF     = ".  $_SERVER['PHP_SELF']." -->";
echo "<!-- GATEWAY_INTERFACE = ".  $_SERVER['GATEWAY_INTERFACE']."  -->";
echo "<!-- SERVER_ADDR  = ".  $_SERVER['SERVER_ADDR']."  -->";
echo "<!-- SERVER_NME   = ".  $_SERVER['SERVER_NAME']."  -->";
echo "<!-- SERVER_SOFTWARE = ".  $_SERVER['SERVER_SOFTWARE']."  -->";
echo "<!-- SERVER_PROTOCOL = ".  $_SERVER['SERVER_PROTOCOL']."  -->";
echo "<!-- REQUEST_METHOD = ".  $_SERVER['REQUEST_METHOD']."  -->";
echo "<!-- REQUEST_TIME = ".  $_SERVER['REQUEST_TIME']."  -->";
echo "<!-- QUERY_STRING = ".  $_SERVER['QUERY_STRING']."  -->";
echo "<!-- HTTP_ACCEPT  = ".  $_SERVER['HTTP_ACCEPT']."  -->";
echo "<!-- HTTP_HOST    = ".  $_SERVER['HTTP_HOST']."  -->";
echo "<!-- HTTP_REFERER = ".  $_SERVER['HTTP_REFERER']."  -->";
echo "<!-- REMOTE_ADDR  = ".  $_SERVER['REMOTE_ADDR']."  -->";
echo "<!-- REMOTE_PORT  = ".  $_SERVER['REMOTE_PORT']."  -->";
echo "<!-- SCRIPT_FILENAME = ".  $_SERVER['SCRIPT_FILENAME']."  -->";
echo "<!-- SERVER_PORT  = ".  $_SERVER['SERVER_PORT']."  -->";
echo "<!-- SCRIPT_NAME  = ".  $_SERVER['SCRIPT_NAME']."  -->";
?>
  </div>
<!-- <?php echo "path=".param('path'); ?> -->

<?php 
    try {
        //$G->readHiddenFiles();
        $wholePage = $G->wholePages();
        $using_kindgirls = $G->kindgirls();
        echo "<!-- kd=".$using_kindgirls." -->";
        if(!$wholePage && !$using_kindgirls)
        {
            $G->readHiddenFiles();
            $G->buildThumbs();
        }
        $PG->readHiddenFiles();
        $PG->buildThumbs();
        $G->pagebreakcomment();
        $PG->pageNavigation(basename(param('path')), $G->getNumItems()); 
        echo "<!-- call PG->getPageNavHtml() -->";
        echo $PG->getPageNavHtml(true, param('path'));
    }
    catch (Exception $e) {
        echo '<pre>';
        echo 'Caught exception: ', $e->getMessage(), "\n";
        echo $e->getTraceAsString();
        echo '<\pre>';
        die();
    }
?>

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

<?php
    if($using_kindgirls)
    {
        $PG->pageNavigation(basename(param('path')), $G->getNumItems());
        echo "<!-- call PG->getPageNavHtml() -->";
        echo $PG->getPageNavHtml(false, param('path'));
    }
?>

<script type="text/javascript" src="/js/lazy.js"></script>
<div id="appModeNote" style="display:none;">
    <em><a href="">Refresh!</a></em>
</div>

</body>
</html>

