<?php
//
// for phpunit 5.1.3
//
// phpunit -c phpunit.xml --coverage-html report/  test/test.php
//

global $_SERVER;
global $_POST;
$_SERVER=array();
$_POST=array();
$_SERVER['SERVER_PORT'] = 80;
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['QUERY_STRING'] = '';

//--------------------------------------------------------
// TEST - full page with specific 'path' value - testpage
define('INCLUDE_CHECK',true);
include('index.php');

class TestPageTest extends PHPUnit_Framework_TestCase
{
	protected $G;
	//protected $_SERVER = array();
	
	public static function setUpBeforeClass()
	{
		if( !defined( "PROGRAM" ))    { define( "PROGRAM",   '/phpgallery/' ); }
		if( !defined( "CSS_ROOT" ))   { define('CSS_ROOT',   '/css/'); }
		if( !defined( "IMAGE_ROOT" )) { define('IMAGE_ROOT', '/images/'); }
		if( !defined( "TOP" ))        { define('TOP',        '/secret'); }
		// logon
		if( !defined( "LOGIN_ENABLED" )){ define('LOGIN_ENABLED', False); }
		
		// additional pages
		if( !defined( "ALS" ))        { define('ALS', True); }
		
		$_POST['PHPUNIT'] = True;
		$_SERVER['SERVER_NAME']=gethostname(); //'skynet';

		printf("\n1. DOC_ROOT = ".$_SERVER['DOCUMENT_ROOT']."\n");
		$G = null;
	}
	public function setUp()
	{
		$_SERVER['SERVER_PORT'] = 80;
		$_SERVER['SERVER_NAME'] = 'localhost';
		$_SERVER['QUERY_STRING'] = '';
		
		$this->stdIgnores=array('.','..');
		$this->Config = array('wplus'=>6,'full_ht'=>145,'pagesize'=>100,'phpThumbs'=>False,'logon'=>False);
		$this->screenWidth = 1440;
		if (gethostname() == 'skynet')
		{ 
			$_SERVER['DOCUMENT_ROOT']='/home/www/html';
			$this->base = 'testing/';
		}
		else
		{
			$_SERVER['DOCUMENT_ROOT']='/Users/judge/Sites';
			$this->base = '';
		}
		printf("\n2. DOC_ROOT = ".$_SERVER['DOCUMENT_ROOT']."\n");
	}
    public static function tearDownAfterClass()
    {
        unset($G);
        $G = null;
    }
	public function testPageOne()
	{
		printf("[ testPageOne ]\n");
		$_POST['path']=$this->base.'testpage';
		$_POST['opt']='1_100';
		echo $_POST['path']."\n";
		var_dump($_SERVER);
		var_dump($_POST);
		
		//$G = new Gallery($this->stdIgnores, $this->screenWidth, $_POST['path'], $_POST['opt'], $_SERVER);
		//$G->buildThumbs();
		//$G->pagebreakcomment();
		//$G->pageNavigation(); 
		//include('head.php');
		//$html = $G->getHtml();
	}
	/*public function testOpenHtml()
	{
		printf("[ testOpenHtml ]\n");
		$_POST['path']=$this->base.'testpage/dir1';
		$_POST['opt']='1_100';
		echo $_POST['path']."\n";
		var_dump($_SERVER);
		var_dump($_POST);
		
		$G = new Gallery($this->stdIgnores, $this->screenWidth, $_POST['path'], $_POST['opt'], $_SERVER);
		$G->wholePages();
		$G->pagebreakcomment();
		$G->pageNavigation(); 
	}
	public function testPageTwo()
	{
		printf("[ testPageTwo ]\n");
		$_POST['path']=$this->base.'testpage/dir2';
		$_POST['opt']='2_100';
		echo $_POST['path']."\n";
		var_dump($_SERVER);
		var_dump($_POST);
		
		$G = new Gallery($this->stdIgnores, $this->screenWidth, $_POST['path'], $_POST['opt'], $_SERVER);
		$G->buildThumbs();
		$G->pagebreakcomment();
		$G->pageNavigation(); 
	}
	public function testAlsAngels()
	{
		printf("[ testAlsAngels ]\n");
		$_POST['path']=$this->base.'testpage/dir3/www.alsangels.com/members';
		$_POST['opt']='1_100';
		echo $_POST['path']."\n";
		var_dump($_SERVER);
		var_dump($_POST);
		
		$G = new Gallery($this->stdIgnores, $this->screenWidth, $_POST['path'], $_POST['opt'], $_SERVER);
		$G->wholePages();
		$G->buildThumbs();
		$G->pagebreakcomment();
		$G->pageNavigation(); 
	}*/
	/*public function testAlsScan()
	{
		printf("[ testAlsScan ]\n");
		$_POST['path']=$this->base.'testpage/dir3/www2.alsscan.com/members/models';
		$_POST['opt']='1_100';
		echo $_POST['path']."\n";
		
		$G = new Gallery($this->stdIgnores, $this->screenWidth, $_POST['path'], $_POST['opt']);
		$G->wholePages();
		$G->buildThumbs();
		$G->pagebreakcomment();
		$G->pageNavigation(); 
	}*/
	/*public function testWiredPussy()
	{
		$_POST['path']=$this->base.'testpage/dir3/www.wiredpussy.com';
		$_POST['opt']='1_100';
		
		$G = new Gallery($this->stdIgnores, $this->screenWidth, $_POST['path'], $_POST['opt']);
		$G->wholePages();
		//$G->buildThumbs();
		$G->pagebreakcomment();
		$G->pageNavigation(); 
		echo gethostname();
	}*/
	/*public function testAllPages()
	{
		$PAGES = array(
			//'',
			//'testpage/',
			'testpage/dir3',
			'testpage/dir3/www.alsangels.com',
			'testpage/dir3/www.alsangels.com/members',
			'testpage/dir3/www2.alsscan.com',
			'testpage/dir3/www2.alsscan.com/members',
			'testpage/dir3/www2.alsscan.com/members/models',
			'testpage/dir4_100',
			'testpage/favorite',
			'testpage/dir4_101',
			'testpage/2014',
			'testpage/2014/03',
			'testpage/2014/MAY',
			'testpage/2014/01',
			'testpage/2014/sep',
			'testpage/dir4_99',
			//'testpage/dir2',
			'testpage/ignorethis',
			//'testpage/dir1'
		);
			
		printf("\n");	
		foreach($PAGES as $page) {
			printf("testAllPages | ".$page."\n");
			$_POST['path']=$this->base.$page;
			$_POST['opt']='1_100';
		
			self::$G = new Gallery($this->stdIgnores, $this->screenWidth, $_POST['path'], $_POST['opt']);
			self::$G->buildThumbs();
			self::$G->pagebreakcomment();
			self::$G->pageNavigation(); 
		}
	}*/
	/*public function testCutePage()
	{
		$page = 'zvideos/stuff.backup/Cute.Models/';
		printf("testCutePages | ".$page."\n");
		$_POST['path']=$page;
		$_POST['opt']='1_100';
	
		self::$G = new Gallery($this->stdIgnores, $this->screenWidth, $_POST['path'], $_POST['opt']);
		self::$G->buildThumbs();
		self::$G->pagebreakcomment();
		self::$G->pageNavigation(); 
	}*/
	/*public function testZvideosPages()
	{
		$PAGES = array(
			//'zvideos/stuff.backup/Cute.Models/',
			'zvideos/stuff.backup/IntheCrack.com',
			'zvideos/stuff.backup/3.Image.Sets/ALSScan.com/ALSScan.com_18.09.22.Zazie.Skymm.Deep.Tissue.XXX.IMAGESET-FuGLi[rarbg]/',
			'zvideos/stuff.backup/Hard.Core.BDSM',
			'zvideos/stuff.backup/ALSScan',
			'zdata/sdc1',
			);
			
		printf("\n");
		foreach($PAGES as $page) {
			printf("testZvideosPages | ".$page."\n");
			$_POST['path']=$page;
			$_POST['opt']='1_100';
		
			self::$G = new Gallery($this->stdIgnores, $this->screenWidth, $_POST['path'], $_POST['opt']);
			self::$G->buildThumbs();
			self::$G->pagebreakcomment();
			self::$G->pageNavigation(); 
		}
	}*/
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
