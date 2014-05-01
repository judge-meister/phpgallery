<?php

require_once('include_check.php');

$Config = array('wplus'=>6,
		'full_ht'=>145,
		'pagesize'=>100,
		'phpThumbs'=>False);


function param($key)
{
	if(isset($_POST[$key]))     { return $_POST[$key]; }
	else if(isset($_GET[$key])) { return $_GET[$key]; }
	return NULL;
}

define('THUMBSIZE', 120);
define('MAXITEMS', 100);
define('STARTURL', PROGRAM.'?path='.param('path'));


require_once('HtmlTag.class.php');
require_once('iPhpThumb.php');

define('FILE_FOLDER', IMAGE_ROOT.'file_folder.png');
define('FILE_BLANK',  IMAGE_ROOT.'file_blank.png');
define('PLAY_BUTTON', IMAGE_ROOT.'play_button_overlay_50x50.png');
define('FAV_FOLDER',  IMAGE_ROOT.'file_folder_red.png');

global $stdIgnores;
$stdIgnores = array(".","..",'reiserfs_priv','.pics','.picasaoriginals','.AppleDB','.AppleDesktop','.AppleDouble','Network Trash Folder','Temporary Items','Thumbs.db',':2e*');

global $mediaTypes;
$mediaTypes = array(
	     "movie" => array('ext'=>array('.avi','.divx','.mpg','.wmv','.mov','.mpeg','.rm','.rmvb','.rmm','.asf','.mkv','.swf','.mp4','.m4v','.mpe','.mpa','.qt','.3pg'),
	     		      'thm'=>array(IMAGE_ROOT.'MovieClip.png',102,120)),
	     "image" => array('ext'=>array('.jpg','.jpeg','.jpe','.gif','.png','.bmp'/*,'.pcx','.tif','.tiff','.pbm','.pgm','.ppm','.tga'*/,'.tbn'/*,'.xbm','.xpm','.xcf'*/), 
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
foreach($mediaTypes as $key => $val)
{
	if($key != "movie" && $key != "image" && $key != "misc")
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

		
function mediatype($path, $type)
{
	global $mediaTypes;
	$e = substr($path, strrpos($path, '.'));
	return (in_array($e, $mediaTypes[$type]['ext']) && !is_dir($path));
}
function isNonMedia($path)
{ 
	global $nonMediaTypes;
	$e = substr($path, strrpos($path, '.'));
	return (in_array($e, $nonMediaTypes) && !is_dir($path));
}
function isimage($path) { return mediatype(strtolower($path), 'image'); }
function ismovie($path) { return mediatype(strtolower($path), 'movie'); }
function ismedia($path) { return isimage($path) || ismovie($path); }
/*
function iscss($path)   { return mediatype(strtolower($path), 'css'); }
function isdmg($path)   { return mediatype(strtolower($path), 'dmg'); }
function isdoc($path)   { return mediatype(strtolower($path), 'doc'); }
function isexe($path)   { return mediatype(strtolower($path), 'exe'); }
function ishtm($path)   { return mediatype(strtolower($path), 'htm'); }
function isini($path)   { return mediatype(strtolower($path), 'ini'); }
function ispdf($path)   { return mediatype(strtolower($path), 'pdf'); }
function isphp($path)   { return mediatype(strtolower($path), 'php'); }
function istxt($path)   { return mediatype(strtolower($path), 'txt'); }
function isthm($path)   { return mediatype(strtolower($path), 'thm'); }
function isxml($path)   { return mediatype(strtolower($path), 'xml'); }
function iszip($path)   { return mediatype(strtolower($path), 'zip'); }
*/
function cleanStr($s)
{
	$s = str_replace('_',' ',$s);
	$s = str_replace('-',' ',$s);
	$s = str_replace('.',' ',$s);
	return $s;
}
function displayName($s)
{
	$letters=array( 'A'=>11,'B'=>11,'C'=>11,'D'=>11,'E'=>12,'F'=>12,'G'=>10,'H'=>11,'I'=>26,
					'J'=>13,'K'=>11,'L'=>12,'M'=>9,'N'=>11,'O'=>10,'P'=>12,'Q'=>10,'R'=>11,
					'S'=>12,'T'=>12,'U'=>11,'V'=>12,'W'=>8,'X'=>12,'Y'=>12,'Z'=>12,
					' '=>13,'+'=>13,'-'=>13,'.'=>25,'_'=>9,
					'a'=>13,'b'=>12,'c'=>13,'d'=>12,'e'=>13,'f'=>21,'g'=>12,'h'=>12,'i'=>25,
					'j'=>25,'k'=>13,'l'=>25,'m'=>9,'n'=>12,'o'=>12,'p'=>12,'q'=>12,'r'=>21,
					's'=>13,'t'=>21,'u'=>12,'v'=>13,'w'=>10,'x'=>13,'y'=>13,'z'=>15,
					'0'=>13,'1'=>13,'2'=>13,'3'=>13,'4'=>13,'5'=>13,'6'=>13,'7'=>13,'8'=>13,'9'=>13);
	//$s = cleanStr($s);
	$size = 0.0;
	$base = 100;
	$width = 97;
	$j = 0;
	for($i = 0; $i < strlen($s) && (int)$size < $width; $i++)
	{
		//echo $s[$i].'=>'.$letters[$s[$i]].' '.$size."\n";
		if(array_key_exists($s[$i], $letters)) {
			$size = $size + ((float)$base / (float)$letters[$s[$i]]);
		} else { echo "<!-- displayName: [".$s[$i]."] is missing -->"; }
		$j = $i;
	}
	$s = wordwrap($s, $j+1, "<br />\n", true);
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
function hasModelDB($d)		{ return  dotFileExists($d,'.modeldb'); } // used to call modeldetails for specific directories
function hasThumbs($d)		{ return  dotFileExists($d,'.thumbs'); } // used in www2.alsscan.com for model pages
function hasGalleryIgnore($d)	{ return  dotFileExists($d,'.gallery_ignore'); } // not sure this is actually required
function hasTitle($d)		{ return  dotFileExists($d,'.title'); } // optional page title
function hasIgnore($d)		{ return  dotFileExists($d,'.ignore'); } // list of files/dirs to ignore
function hasImgsize($d)		{ return  dotFileExists($d,'.imgsize'); } // list of image dimensions
function hasPics($d)		{ return  dotFileExists($d,'.pics'); } // hidden dir full of thumbnails
function hasLogo($d)		{ return  dotFileExists($d,'.logo'); } // list of dir to thumbs associations
function hasAlpha($d)		{ return  dotFileExists($d,'.alpha'); }
function hasAlphabet($d)	{ return  dotFileExists($d,'.alphabet'); }
function hasCalendar($d)	{ return  dotFileExists($d,'.calendar'); } // use calendar links
function hasIndex($d)		{ return (dotFileExists($d,'igallery.html') || dotFileExists($d,'igallery.php')); }
function hasComments($d)	{ return  dotFileExists($d,'comments.php'); }
function hasFavourites($d)	{ return  dotFileExists($d,'.favourites'); }
function hasBookmarks($d)	{ return  dotFileExists($d,'.bookmarks'); }
function hasRollovers($d)	{ return  dotFileExists($d,'.rollovers'); }

function mkOverlay($s,$o=-90) { return '<div style="margin-top:'.$o.'px;">'.$s.'</div>'; }


function getIgnores($path)
{
	$ignores = file($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/.ignore', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if ($ignores == False) { return array(); } else { return $ignores; }
}

function inExcludes($file, $ignores)
{
	if(in_array(basename($file), $ignores))
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
	if(file_exists($path))
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
		return basename(dirname($d))."/".basename($d);
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


?>
