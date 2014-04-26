<?php

//--------------------------------------------------------
// TEST - full page with specific 'path' value - testpage
define('INCLUDE_CHECK',true);
require_once( gethostname().'/config.php' );

class TestPageTest extends PHPUnit_Framework_TestCase
{
	// ...
	public static function setUpBeforeClass()
	{
		if( !defined( "PROGRAM" ))    { define( "PROGRAM",   '/phpgallery/' ); }
		if( !defined( "CSS_ROOT" ))   { define('CSS_ROOT',   '/css/'); }
		if( !defined( "IMAGE_ROOT" )) { define('IMAGE_ROOT', '/images/'); }
		if( !defined( "TOP" ))        { define('TOP',        '/secret'); }
		// logon
		if( !defined( "LOGIN_ENABLED" )){ define('LOGIN_ENABLED', False); }
		//define('LOGIN_PATH',    '/php/');
		
		// additional pages
		if( !defined( "ALS" ))        { define('ALS', True); }
		//define('WIREDPUSSY', True);
		
		// database password
		//define('PASSWD_DB', True);
		
		// search tool
		//define('SEARCH_ENABLED', True);
		
		$_POST['PHPUNIT'] = True;
		$_SERVER['SERVER_NAME']=gethostname(); //'skynet';
		if (gethostname() == 'skynet')
		{ 
			$_SERVER['DOCUMENT_ROOT']='/home/www/html';
			//$this->base = 'testing/';
		}
		else
		{
			$_SERVER['DOCUMENT_ROOT']='/Users/judge/Sites';
			//$this->base = '';
		}
		include('index.php');
	}
	public function setUp()
	{
		$this->stdIgnores=array('.','..');
		$this->Config = array('wplus'=>6,'full_ht'=>145,'pagesize'=>100,'phpThumbs'=>False,'logon'=>False);
		if (gethostname() == 'skynet')
		{ 
			//$_SERVER['DOCUMENT_ROOT']='/home/www/html';
			$this->base = 'testing/';
		}
		else
		{
			//$_SERVER['DOCUMENT_ROOT']='/Users/judge/Sites';
			$this->base = '';
		}
	}
	public function testPageOne()
	{
		$_POST['path']=$this->base.'testpage';
		$_POST['opt']='1_100';
		
		$G = new Gallery($this->stdIgnores, $this->Config, $_POST['path'], $_POST['opt']);
		$G->buildThumbs();
		$G->pagebreakcomment();
		$G->pageNavigation(); 
		include('head.php');
		$html = $G->getHtml();
	}
	public function testOpenHtml()
	{
		$_POST['path']=$this->base.'testpage/dir1';
		$_POST['opt']='1_100';
		
		$G = new Gallery($this->stdIgnores, $this->Config, $_POST['path'], $_POST['opt']);
		$G->wholePages();
	}
	public function testPageTwo()
	{
		$_POST['path']=$this->base.'testpage/dir2';
		$_POST['opt']='2_100';
		
		$G = new Gallery($this->stdIgnores, $this->Config, $_POST['path'], $_POST['opt']);
		$G->buildThumbs();
		$G->pagebreakcomment();
		$G->pageNavigation(); 
	}
	public function testAlsAngels()
	{
		$_POST['path']=$this->base.'testpage/dir3/www.alsangels.com/members';
		$_POST['opt']='1_100';
		
		$G = new Gallery($this->stdIgnores, $this->Config, $_POST['path'], $_POST['opt']);
		$G->wholePages();
		//$G->buildThumbs();
		//$G->pagebreakcomment();
		//$G->pageNavigation(); 
	}
	public function testAlsScan()
	{
		$_POST['path']=$this->base.'testpage/dir3/www2.alsscan.com/members/models';
		$_POST['opt']='1_100';
		
		$G = new Gallery($this->stdIgnores, $this->Config, $_POST['path'], $_POST['opt']);
		$G->wholePages();
		//$G->buildThumbs();
		//$G->pagebreakcomment();
		//$G->pageNavigation(); 
	}
	public function testWiredPussy()
	{
		$_POST['path']=$this->base.'testpage/dir3/www.wiredpussy.com';
		$_POST['opt']='1_100';
		
		$G = new Gallery($this->stdIgnores, $this->Config, $_POST['path'], $_POST['opt']);
		$G->wholePages();
		//$G->buildThumbs();
		//$G->pagebreakcomment();
		//$G->pageNavigation(); 
		echo gethostname();
	}
}

//--------------------------------------------------------
// TEST - is in ignore array
//$ignore_dir = array(".","..",".pics");

//if(in_array("..", $ignore_dir))
//{
	//echo ".. in array";
	//}
//else
//{
	//echo ".. not found";
	//}

//--------------------------------------------------------
// TEST - getimagesize - and increasing width by 5
//list($width, $height, $type, $attr) = getimagesize("../secret/sdc1/dildo_bike/dildo_bike_4.thm");
//print $width."|".$height."|".$type."|".$attr;

//print (int)$width+5;

//--------------------------------------------------------
// TEST - getFilesFromLogo (reading .logo file)
//include('functions.php');

//$_SERVER['DOCUMENT_ROOT']='/home/www/html/';
//$files=getFilesFromLogo('secret/sdc1/wickedweasel/bikini_competition/2013');
	
//var_dump($files);

//--------------------------------------------------------
// TEST - shorten function
//echo shorten("www.test.test.test.test.com.jpg", 90);

//--------------------------------------------------------
// TEST - full page with specific 'path' value
//$_SERVER['SERVER_NAME']='skynet';
//$_SERVER['DOCUMENT_ROOT']='/home/www/html';
//$_POST['path']='secret/sdc1/www.alsvideo.com';
//$_POST['opt']='1_100_f';
//include('index.php');
//--------------------------------------------------------
// TEST - full page with specific 'path' value
//define('INCLUDE_CHECK',true);
/*
$_SERVER['SERVER_NAME']='skynet';
$_SERVER['DOCUMENT_ROOT']='/home/www/html';
$_POST['path']='secret/sdc1/inthecrack.com/videos';
$_POST['opt']='1_100';
include('index.php');
*/
//--------------------------------------------------------
// TEST - display modification time of movie file
//$stat = stat('/data/sdd1/videos/www.wiredpussy.com/devicebondage/32100_1.wmv');
//var_dump($stat['mtime']);

//--------------------------------------------------------
// TEST - explode
//$str = explode('_', "2345_1.wmv");
//echo $str[0];

//--------------------------------------------------------
// TEST - first character is dot
//$file='secret/.htaccess';
//$f = basename($file);
//echo $f[0];
//if($f[0]=='.'){echo 'dot';} else {echo 'not dot';}
//--------------------------------------------------------
// TEST - displayname function
/*
include('functions.php');
$res = displayName('evaangelina');
echo $res.' '.strlen($res)."\n";
$res = displayName('biancabeauchamp');
echo $res.' '.strlen($res)."\n";
$res = displayName('lilianetiger');
echo $res.' '.strlen($res)."\n";
$res = displayName('veronicazemanova');
echo $res.' '.strlen($res)."\n";
*/
//--------------------------------------------------------
// TEST - div layout for file_blank.png with overlayed text
//
//<div style="width:120px;height:120px;background-image:url('/images/file_blank.png');"><div style="padding-top:90px;padding-left:20px;color:white;font-family:arial;font-weight:bold;font-size:120%;">.sql</div></div>

//--------------------------------------------------------
// TEST - makeCalendar function
//
//define('PROGRAM', '/phpgallery/');
//include('calendar.php');
/*$res = makeCalendar('/home/www/html/secret/sdc1/www.hegre-art.com','secret/sdc1/www.hegre-art.com','2011');
echo $res;*/
//allYears('/home/www/html/secret/sdc1/www.hegre-art.com');

//--------------------------------------------------------
// TEST - using classes
/*class testclass
{
	public function run($s)
	{
		return $s."\n";
	}
}
$tc = new testclass();
echo $tc->run("hello");
//$x = (new testclass())->run("hello");
//echo $x; */

//--------------------------------------------------------
// TEST - nonMedia Types and Thumbs
/*
include('functions.php');
echo "nonMediaTypes = ";
var_dump($nonMediaTypes);
echo "nonMediaThumbs = ";
var_dump($nonMediaThumbs);

if( isNonMedia('test.php'))
{
	echo "yes\n";
	var_dump($nonMediaThumbs[$e]);
}
else{ echo "no\n";}
*/
//--------------------------------------------------------
// TEST - locate system call
//$last_line = system("locate -d /data/sdc1/db_file -b --regex 'digitaldesire' ", $retval);
//echo $retval."\n";
//echo $last_line."\n";

//--------------------------------------------------------
// TEST - GD Library
//$res=gd_info();
//var_dump($res);

//--------------------------------------------------------
// TEST - span classes
//define('INCLUDE_CHECK',true);

//$_SERVER['SERVER_NAME']='skynet';
//$_SERVER['DOCUMENT_ROOT']='/home/www/html';
//include gethostname().'/config.php';
//include 'functions.php';

//echo "TEST - span classes";

//$s=new SpanLogo('/secret/sdc1/inthecrack.com', 'videos', 90, 120, '1_100', 'picture1', 'thumbnail.jpg');
//echo $s->html();
//echo "\n";

//$s=new SpanLogoRollover('/secret/sdc1/inthecrack.com', 'videos', 90, 120, '1_100', 'picture1', 'thumbnail.jpg');
//echo $s->html();
//echo "\n";

//--------------------------------------------------------
// TEST - HTML.class.php (plus new class CssStyle)
//
/*
require_once('HtmlTag.class.php');


echo "\n\n";
$style = CssStyle::createStyle()->set('width','96px')->set('height','145px');
echo $style;
echo "\n\n";

$html = HtmlTag::createElement('span')->set('style',CssStyle::createStyle()->set('width','96px')->set('height','145px'));
$html->addElement('a')  ->set('href',"/phpgallery/?opt=1_100&path=/secret/sdc1/inthecrack.com/videos")
			->set('style',CssStyle::createStyle()->set('height','120px')->set('overflow','hidden'))->setText('<br>picture1')->setText('just a div');
$div = HtmlTag::createElement('div')->id("rollover");
$img = HtmlTag::createELement('img')->addClass("rollover")
			->set('src',"/secret/sdc1/inthecrack.com/thumbnail.jpg")
			->set('style',CssStyle::createStyle()->set('width','90px')->set('height','120px'));
$div->addElement($img);
$html->addElement($div);
echo($html);

echo "\n\n";
*/
//$celldata = array('path'=>'/secret/sdc1/','dir'=>null,'width'=>0,'height'=>0,'opt'=>null,'caption'=>null,'thumb'=>null,'image'=>null);
//echo $celldata['path']."\n";
//echo $celldata['width']."\n";
//echo $celldata['caption']."\n";

?>
