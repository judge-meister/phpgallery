<?php

require_once('include_check.php');

/*$Config = array(
*		'wplus'=>6,
*		'full_ht'=>145,
*		'cell_ht'=>120,
*		'cell_wt'=>120,
*		'pagesize'=>100,
*		'maxPageWt'=>1200,
*		'phpThumbs'=>False,
*		'debug'=>False
*	);
*/
		/*
*class Config
*{
*	const full_ht=145;
*	const cell_ht=120;
*	const cell_wt=120;
*	const maxPageWt = 1200;
*	const wplus = 6;
*	//public pagesize = 100;
*	const phpThumbs = False;
*	//debug = False;
*	//screenWidth = 0;
*}*/

class Config 
{
	private static $inst = null;

	//Array to hold global settings
	private static $config = array(
		'wplus'     => 6,
		'full_ht'   => 145,
		'cell_ht'   => 120,
		'cell_wt'   => 120,
		'pagesize'  => 1000,
		'maxPageWt' => 1200,
		'phpThumbs' => False,
		'debug'     => False
	);

	public static function getInstance() 
	{
		if (static::$inst === null) 
		{
			static::$inst = new Config();
		}
		return static::$inst;
	}

	public function getConfig($path=NULL) 
	{
		$setting =& static::$config;
		if($path) {
			//parse path to return config
			$path = explode('/', $path);

			foreach($path as $element) {
				if(isset($setting[$element])) {
					$setting =& $setting[$element]; 
				} else {
					//echo "If specified path not exist\n";
					$setting = false;
				}
			}
		}
		return $setting;
	}
	
	public function set($path=NULL,$value=NULL) 
	{
		if($path) {
			//parse path to return config
			$path = explode('/', $path);
			//Modify global settings
			$setting =& static::$config;

			$element = $path;
			foreach($path as $element) {
				$setting =& $setting[$element];
			}
			$setting = $value;
		}
	}

	public function get($elem)
	{
		return static::$config[$elem];
	}

	//Override to prevent duplicate instance
	private function __construct() {}
	private function __clone() {}
	private function __wakeup() {}
}

function param($key)
{
	if(isset($_POST[$key]))     { return $_POST[$key]; }
	else if(isset($_GET[$key])) { return $_GET[$key]; }
	return NULL;
}
function joinUrl($paths)
{
	return preg_replace('#/+#','/', join("/",$paths));
}
function mkRawUrl($paths)
{
	return str_replace('%2F','/', rawurlencode(joinUrl($paths)));
}
function mkUrl($paths)
{
	return str_replace('%2F','/', urlencode(joinUrl($paths)));
}
class DebugLogger
{
	public function __construct(){}
	public function display($line)
	{
		$cfg = Config::getInstance();
		if($cfg->get('debug') == True)
		//if(Config::debug == True)
		{
			echo $line;
		}
	}
}
class Path
{
	public function __construct($path)
	{
		$this->m_path = $path;
		if($this->m_path == "")
		{
			$this->m_path = TOP;
		}
		else if(substr($this->m_path, 1) != "/")
		{
			$this->m_path = "/".$this->m_path;
		}
	}
	public function str()				{ return  $this->m_path; }
	public function hasDebug()			{ return  dotFileExists($this->m_path, '.debug'); } // used to display debugging statements
	public function hasModelDB()		{ return  dotFileExists($this->m_path, '.modeldb'); } // used to call modeldetails for specific directories
	public function hasThumbs()			{ return  dotFileExists($this->m_path, '.thumbs'); } // used in www2.alsscan.com for model pages
	public function hasGalleryIgnore()	{ return  dotFileExists($this->m_path, '.gallery_ignore'); } // not sure this is actually required
	public function hasTitle()			{ return  dotFileExists($this->m_path, '.title'); } // optional page title
	public function hasIgnore()			{ return  dotFileExists($this->m_path, '.ignore'); } // list of files/dirs to ignore
	public function hasImgsize()		{ return  dotFileExists($this->m_path, '.imgsize'); } // list of image dimensions
	public function hasPics()			{ return  dotFileExists($this->m_path, '.pics'); } // hidden dir full of thumbnails
	public function hasLogo()			{ return  dotFileExists($this->m_path, '.logo'); } // list of dir to thumbs associations
	public function hasAlpha()			{ return  dotFileExists($this->m_path, '.alpha'); }
	public function hasAlphabet()		{ return  dotFileExists($this->m_path, '.alphabet'); }
	public function hasCalendar()		{ return  dotFileExists($this->m_path, '.calendar'); } // use calendar links
	public function hasIndex()			{ return (dotFileExists($this->m_path, 'igallery.html') || dotFileExists($this->m_path, 'igallery.php')); }
	public function hasComments()		{ return  dotFileExists($this->m_path, 'comments.php'); }
	public function hasFavourites()		{ return  dotFileExists($this->m_path, '.favourites'); }
	public function hasBookmarks()		{ return  dotFileExists($this->m_path, '.bookmarks'); }
	public function hasRollovers()		{ return  dotFileExists($this->m_path, '.rollovers'); }
	public function hasReverse()		{ return  dotFileExists($this->m_path, '.reverse'); }
	public function hasDu()				{ return  dotFileExists($this->m_path, '.du'); }
	public function hasPages()			{ return  dotFileExists($this->m_path, '.pages'); }
	public function hasLatest()			{ return  dotFileExists($this->m_path, '.latest'); }
	
	public function getImgSize($image)	{ return getImgSize($_SERVER['DOCUMENT_ROOT'].$this->m_path.'/'.$image); }
	public function fileExists($file)	{ return file_exists($_SERVER['DOCUMENT_ROOT'].$this->m_path.'/'.$file) || file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$file); }
	private function openFile($path)	{ return file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); }
	public function openLogo()			{ return $this->openFile($_SERVER['DOCUMENT_ROOT'].$this->m_path.'/.logo'      ); }
	public function openFavourites()	{ return $this->openFile($_SERVER['DOCUMENT_ROOT'].$this->m_path.'/.favourites'); }
	public function openDu()			{ return $this->openFile($_SERVER['DOCUMENT_ROOT'].$this->m_path.'/.du'        ); }
	public function openPages()			{ return $this->openFile($_SERVER['DOCUMENT_ROOT'].$this->m_path.'/.pages'     ); }
}

define('THUMBSIZE', 120);
define('MAXITEMS', 100);
define('STARTURL', PROGRAM.'?'.$_SERVER['QUERY_STRING']);


require_once('HtmlTag.class.php');
require_once('iPhpThumb.php');

define('FILE_FOLDER', IMAGE_ROOT.'file_folder.png');
define('FILE_BLANK',  IMAGE_ROOT.'file_blank.png');
define('PLAY_BUTTON', IMAGE_ROOT.'play_button_overlay_50x50.png');
define('FAV_FOLDER',  IMAGE_ROOT.'file_folder_red.png');
define('BORDER_ONLY', IMAGE_ROOT.'border.png');

global $stdIgnores;
$stdIgnores = array(".","..",'reiserfs_priv','.pics','.picasaoriginals','.AppleDB','.AppleDesktop','.AppleDouble','Network Trash Folder','.TemporaryItems','Temporary Items','Thumbs.db',':2e*','comments.php','comments');

global $mediaTypes;
$mediaTypes = array(
		"movie" => array('ext'=>array('.avi','.divx','.mpg','.wmv','.mov','.mpeg','.rm','.rmvb','.rmm','.asf','.mkv','.swf','.mp4','.m4v','.mpe','.mpa','.qt','.3pg','.flv'),
						'thm'=>array(IMAGE_ROOT.'MovieClip.png',102,120)),
		"image" => array('ext'=>array('.jpg','.JPG','.jpeg','.jpe','.gif','.png','.bmp','.tbn'), /*,'.pcx','.tif','.tiff','.pbm','.pgm','.ppm','.tga','.xbm','.xpm','.xcf'*/ 
						'thm'=>array('',0,0)),

		"css"   => array('ext'=>array('.css'), 'thm'=>array(IMAGE_ROOT.'file_css.png',120,120)),
		"dmg"   => array('ext'=>array('.dmg'), 'thm'=>array(IMAGE_ROOT.'file_dmg.png',120,120)),
		"doc"   => array('ext'=>array('.doc'), 'thm'=>array(IMAGE_ROOT.'file_doc.png',120,120)),
		"exe"   => array('ext'=>array('.exe'), 'thm'=>array(IMAGE_ROOT.'file_exe.png',120,120)),
		"htm"   => array('ext'=>array('.html','.htm','.asp','.shtml'), 'thm'=>array(IMAGE_ROOT.'file_html.png',120,120)),
		"ini"   => array('ext'=>array('.ini'), 'thm'=>array(IMAGE_ROOT.'file_ini.png',120,120)),
		"pdf"   => array('ext'=>array('.pdf'), 'thm'=>array(IMAGE_ROOT.'file_pdf.png',120,120)),
		"php"   => array('ext'=>array('.php'), 'thm'=>array(IMAGE_ROOT.'file_php.png',120,120)),
		/*"thm"   => array('ext'=>array('.thm'), 'thm'=>array('',0,0)),*/
		"txt"   => array('ext'=>array('.txt'), 'thm'=>array(IMAGE_ROOT.'file_txt.png',120,120)),
		"xml"   => array('ext'=>array('.xml'), 'thm'=>array(IMAGE_ROOT.'file_xml.png',120,120)),
		"zip"   => array('ext'=>array('.zip','.tar','.rar','.gz'), 'thm'=>array(IMAGE_ROOT.'file_zip.png',120,120)),

		"misc"  => array('ext'=>array('.*'),   'thm'=>array(IMAGE_ROOT.'file_blank.png',120,120))
	);
global $nonMediaTypes;
$nonMediaTypes = array();
global $nonMediaThumbs;
$nonMediaThumbs = array();
$mediaCategories = array("movie", "image", "misc");
foreach($mediaTypes as $key => $val)
{
	if(in_array($key, $mediaCategories)) /* != "movie" && $key != "image" && $key != "misc")*/
	{
		//echo $key."\n";
		$nonMediaTypes = array_merge($nonMediaTypes, $mediaTypes[$key]['ext']);
		foreach($mediaTypes[$key]['ext'] as $e)
		{
			//echo $e."\n";
			//var_dump($mediaTypes[$key]['thm']);
			$nonMediaThumbs[$e] = $mediaTypes[$key]['thm'];
		}
	}
}
//var_dump($mediaTypes);
//var_dump($nonMediaTypes);
//var_dump($nonMediaThumbs);

global $stdIncludes;
$stdIncludes = array();
$includeCategories = array("movie", "image", "css", "htm", "pdf", "txt", "xml");
foreach($mediaTypes as $key => $val)
{
	if(in_array($key, $includeCategories)) /* == "movie" || $key == "image")*/
	{
		$stdIncludes = array_merge($stdIncludes, $mediaTypes[$key]['ext']);
	}
}
//var_dump($stdIncludes);

function mediatype($path, $type)
{
	global $mediaTypes;
	$e = substr($path, strrpos($path, '.'));
	//printDebug("[".$e."] ");
	return (in_array($e, $mediaTypes[$type]['ext']) && !is_dir($path));
}
function isNonMedia($path)
{ 
	global $nonMediaTypes;
	$e = substr($path, strrpos($path, '.'));
	return (in_array($e, $nonMediaTypes) && !is_dir($path));
}
function isimage($path) 
{ 
	return mediatype(strtolower($path), 'image'); 
}
function ismovie($path) 
{ 
	//printDebug("ismovie() "); 
	return mediatype(strtolower($path), 'movie'); 
}
function ismedia($path) 
{ 
	return isimage($path) || ismovie($path); 
}

function cleanStr($s)
{
	$s = str_replace('_',' ',$s);
	$s = str_replace('-',' ',$s);
	$s = str_replace('.',' ',$s);
	return $s;
}
function displayName($s)
{
	// how many of each letter fit across the cell box
	$letters=array( 'A'=>9, 'B'=>9, 'C'=>8, 'D'=>8, 'E'=>9, 'F'=>10,'G'=>8, 'H'=>8, 'I'=>22,
					'J'=>13,'K'=>9, 'L'=>11,'M'=>6, 'N'=>8, 'O'=>8, 'P'=>9, 'Q'=>8, 'R'=>8,
					'S'=>9, 'T'=>10,'U'=>8, 'V'=>9, 'W'=>5, 'X'=>9, 'Y'=>9, 'Z'=>10,
					
					'a'=>11,'b'=>11,'c'=>12,'d'=>11,'e'=>11,'f'=>22,'g'=>11,'h'=>11,'i'=>26,
					'j'=>27,'k'=>12,'l'=>27,'m'=>6, 'n'=>11,'o'=>11,'p'=>11,'q'=>11,'r'=>17,
					's'=>12,'t'=>21,'u'=>11,'v'=>12,'w'=>8, 'x'=>11,'y'=>12,'z'=>12,
					
					' '=>15,'+'=>11,'-'=>13,'.'=>25,'_'=>9, '('=>9, ')'=>9, '['=>9, ']'=>9,
					'0'=>11,'1'=>13,'2'=>11,'3'=>11,'4'=>11,'5'=>11,'6'=>11,'7'=>11,'8'=>11,'9'=>11);
	//$s = cleanStr($s);
	$size = 0.0;
	$base = 110;
	//$width = 88; //for font 150%
	//$width = 78; //for font 180%
	$width = 109;
	$j = 0;
	$linecount = 1;
	$breaks = array();
	for($i = 0; $i < strlen($s) && (int)$size < $width && $linecount < 5; $i++)
	{
		//echo $s[$i].'=>'.$letters[$s[$i]].' '.$size."\n";
		if(array_key_exists($s[$i], $letters)) {
			$newsize = $size + ((float)$base / (float)$letters[$s[$i]]);
		} else { 
			printf( "<!-- displayName: [".$s[$i]."] is missing -->"); 
			$newsize = $size + ((float)$base / 10.0);
		}
		//printf("%d %f %s\n", $i, $newsize, $s[$i]);
		if ($newsize < $width) {
			$size = $newsize;
		} else {
			$j = $i;
			$linecount = $linecount + 1;
			array_push($breaks, $i-1);
			//var_dump($breaks);
			$size = 0.0;
		}
		$j = $i;
	}
	$rbreaks = array_reverse($breaks);
	foreach($rbreaks as $p)
	{
		//printf("\nbreak %d\n",$p);
		$s = substr($s,0,$p)."\n".substr($s,$p);
		//printf("\n%s\n",$s);
	}
	//$s = wordwrap($s, $j+1, "<br />\n", true);
	//printf("REPLACE\n");
	$s = str_replace("\n", "<br />\n", $s);
	//printf("IMPLODE\n");
	$s = implode("<br />\n", array_slice(explode("<br />\n", $s), 0, 4));
	//printf("\nCONVERTED TO HTML\n");

	return $s;
}
function removeExt($s)
{
	//return substr($s, 0, strrpos($s,"."));
	if(strrpos($s, '.') !== False){ return substr($s, 0, strrpos($s, '.')); }
	else{ return $s; }
}
function getExt($file)
{
	if(strrpos($file, '.') !== False){ return substr($file, strrpos($file, '.')); }
	//else{ return "<font style=\"color:#ddd;font-weight:normal;font-size:90%;\"><i>unk</i></font>"; }
	else{ return NULL; }
}
function shorten($s, $w)
{
	if($w > 0) { $ln = (int)($w / 5.4); } else { $ln = 18; }
	$s = str_replace('_', ' ', $s);
	if(strlen($s) > $ln) { $s = substr($s, 0, $ln); }
	return $s;
}
function captionName($s,$w)
{
	//$s = removeExt($s);
	$s = cleanStr($s);
	$s = shorten($s, $w);
	return $s;
}
function dotFileExists($d,$f)
{
	if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$d.'/'.$f)) 
	{ 
		//echo $_SERVER['DOCUMENT_ROOT'].'/'.$d.'/'.$f." TRUE\n"; 
		return true; 
	} 
	else 
	{ 
		//echo $_SERVER['DOCUMENT_ROOT'].'/'.$d.'/'.$f." FALSE\n"; 
		return false; 
	}
}
function hasModelDB($d)			{ return  dotFileExists($d, '.modeldb'); } // used to call modeldetails for specific directories
function hasTitle($d)			{ return  dotFileExists($d, '.title'); } // optional page title
function hasIgnore($d)			{ return  dotFileExists($d, '.ignore'); } // list of files/dirs to ignore
function hasLogo($d)			{ return  dotFileExists($d, '.logo'); } // list of dir to thumbs associations
function hasIndex($d)			{ return (dotFileExists($d, 'igallery.html') || dotFileExists($d, 'igallery.php')); }

#function hasDebug($d)			{ return  dotFileExists($d, '.debug'); } // used to display debugging statements
#function hasThumbs($d)			{ return  dotFileExists($d, '.thumbs'); } // used in www2.alsscan.com for model pages
#function hasGalleryIgnore($d)	{ return  dotFileExists($d, '.gallery_ignore'); } // not sure this is actually required
#function hasImgsize($d)		{ return  dotFileExists($d, '.imgsize'); } // list of image dimensions
#function hasPics($d)			{ return  dotFileExists($d, '.pics'); } // hidden dir full of thumbnails
#function hasAlpha($d)			{ return  dotFileExists($d, '.alpha'); }
#function hasAlphabet($d)		{ return  dotFileExists($d, '.alphabet'); }
#function hasCalendar($d)		{ return  dotFileExists($d, '.calendar'); } // use calendar links
#function hasComments($d)		{ return  dotFileExists($d, 'comments.php'); }
#function hasFavourites($d)		{ return  dotFileExists($d, '.favourites'); }
#function hasBookmarks($d)		{ return  dotFileExists($d, '.bookmarks'); }
#function hasRollovers($d)		{ return  dotFileExists($d, '.rollovers'); }
#function hasReverse($d)		{ return  dotFileExists($d, '.reverse'); }

function mkOverlay($s,$o=-90) { return '<div style="margin-top:'.$o.'px;">'.$s.'</div>'; }


function getIgnores($path)
{
	$ignores = file($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/.ignore', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if ($ignores == False) { return array(); } else { return $ignores; }
}

function inExcludes($file, $ignores)
{
	var_dump(getExt($file));
		if(!in_array(getExt($file), $stdIncludes))
	{
		return true;
	}
	else if(in_array(basename($file), $ignores))
	{
		return true;
	}
	else
	{
		 if(substr(basename($file),0,1)=="."){ return true; }
		 if(substr(basename($file),0,3)==":2e"){ return true; }
		 if(getExt($file) == ".$$$"){ return true; }
	}
	return false;
}

function getFilesFromLogo($path)
{
	$files = array();
	$lines = file($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/.logo', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	for($i = 0; $i < count($lines); $i++)
	{
		if(substr( $lines[$i], 0, 1 ) != "#")
		{
			$pieces = explode(",", $lines[$i]);
			if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/'.$pieces[0]))
			{
				$files[] = $pieces[0];
			}
		}
	}
	return $files;
}

function getImgSize($path)
{
	//getimagesize
	//list($this->m_width, $this->m_height, $type, $attr) = getimagesize($_SERVER['DOCUMENT_ROOT'].'/'.$this->m_path.'/'.$this->m_thumb);
	if(file_exists($path) && is_file($path))
	{
		return getimagesize($path);
	}
	else
	{
		return array(120, 120, "", "");
	}
}

function favicon($d)
{
	if(strpos($d, 'www.hegre-art.com') !== false)   { $icon = 'hegreart.png'; }
	else if(strpos($d, 'www.femjoy.com') !== false) { $icon = 'femjoy.ico'; }
	else if(strpos($d, 'wickedweasel') !== false)   { $icon = 'wickedweasel.ico'; }
	else if(strpos($d, 'malibustrings') !== false)  { $icon = 'malibustrings.ico'; }
	else if(strpos($d, 'wiredpussy') !== false)     { $icon = 'kink.ico'; }
	else                                            { $icon = 'gallery.ico'; }
	return $icon;
}

function title($d)
{
	if(hasTitle($d))
	{
		$t = file($_SERVER['DOCUMENT_ROOT'].'/'.$d.'/.title', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		return $t[0];
	}
	else
	{
		$str = basename(dirname($d))."/".basename($d);
		if (strlen($str) > 93)
		{
			$str = substr($str, 0 , 45)."...".substr($str, -45, -1);
		}
		//return basename(dirname($d))."/".basename($d);
		return $str;
	}
}

function title2($d)
{
	if(hasTitle($d))
	{
		$t = file($_SERVER['DOCUMENT_ROOT'].$d.'/.title', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		return $t[0];
	}
	else
	{
		$str = basename($d);
		if (strlen($str) > 92)
		{
			$str = substr($str, 0 , 45)."...".substr($str, -45, -1);
		}
		return $str;
	}
}

function pageTitle($d)
{
	if(favicon($d) == "gallery.ico")
	{
		//echo '<title>'.basename($G->getPath()).' - page '.repr(self.page).'</title>';
		return '<title>'.title($d).' - page 1</title>';
	}
	else
	{
		//echo '<title>G: '.basename($G->getPath()).' - page '.repr(self.page).'</title>';
		return '<title>G: '.title($d).' - page 1</title>';
	}
}

function comment($s)
{
	return "<!-- ".$s." -->";
}

/* 
* this function was ripped off the php.net manual pages;
* https://www.php.net/manual/en/function.scandir.php
* It reads a directory list and adds a custom sort to it 
*/
function myscandir($dir, $exp, $how='name', $desc=0)
{
	//print $dir." : ".$how."\n";
	$r = array();
	if(!file_exists($dir)) { print "File Does Not Exist: ".$dir."\n"; }
	$dh = @opendir($dir);
	if ($dh) {
		while (($fname = readdir($dh)) !== false) {
			if (preg_match($exp, $fname)) {
				if(file_exists(realpath("$dir/$fname"))) {
					$stat = stat("$dir/$fname");
					$r[$fname] = ($how == 'name')? $fname: $stat[$how];
				}
			}
		}
		closedir($dh);
		if ($desc) {
			arsort($r);
		}
		else {
			asort($r);
		}
	}
	return(array_keys($r));
}

function myurlencode($path)
{
	return preg_replace('#/+#','/',str_replace('%2F','/',urlencode($path)));
}

function isMobile()
{
	$useragent=$_SERVER['HTTP_USER_AGENT'];
	//return True;
	return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4));

	//header('Location: http://detectmobilebrowser.com/mobile');
}
?>
