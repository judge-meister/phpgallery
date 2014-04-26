<?php
require_once('functions.php');
require_once('HtmlTag.class.php');

abstract class Config2
{
	const full_ht=145;
	const wplus = 6;
	const pagesize = 100;
	const phpThumbs = False;
}
abstract class FileType
{
	const image = 0;
	const movie = 1;
	const directory = 2;
	const nonMedia = 3;
	const misc = 4;
	const ignored = 5;
}

interface iFolderItem { public function html(); }
class FolderItem implements iFolderItem
{
	public function __construct($base, $f)
	{
		$this->item = null; //
		$this->thumb = null; //
		$this->tsize = null;
		//$this->dim = null; //
		//$this->path = null; //
		//$this->caption = null; //
		$this->id = null;
		$this->imgurl = null; //
		$this->url = null; //
		$this->opt = "1_100"; //
	
		//$this->file = null;
		$this->base = null;
		$this->width = 120;
		$this->height = 120;
		//echo $f;
		$this->file = $f;
		$this->base = $base;
		$this->caption = $f;
		//$this->opt = "1_100";
		//$this->id = null;
		//$this->class = null;  ->addClass($this->class)
		
		$this->span = HtmlTag::createElement('span')
			->set('style',createCSS($this->width+Config2::wplus, Config2::full_ht));
		//$this->span->showTextBeforeContent(True);
		
		$this->anchor = HtmlTag::createElement('a')->setText(captionName($this->caption,$this->width));
		
		//$this->img = HtmlTag::createELement('img')->set('src',$this->imgurl);
		$this->imgStyle = createCSS($this->width,$this->height);
	}
	public function ignore()
	{
		$this->type = FileType::ignored;
		return $this;
	}
	public function html()
	{
		if($this->type != FileType::ignored)
		{
			return '<span style="border:1px solid #ddd;padding:5px;margin:5px;">'.$this->base.' '.$this->file.'</span>';
		}
		else
		{
			return '';
		}
	}
}
class MovieCell extends FolderItem
{
}
class ImageCell extends FolderItem
{
}
class DirCell extends FolderItem
{
	//private $url = null;
	//private $file = null;
	
	public function __construct($base, $f)
	{
		parent::__construct($base, $f);
		$this->imgurl = FILE_FOLDER;
		$this->url = mkUrl(array($base,$f));
		//echo $f;
	}
	public function html()
	{
		$this->img = HtmlTag::createELement('img')->set('src',$this->imgurl);
		$this->span->setText('<!-- span_dir -->');
		$this->span->showTextBeforeContent(True);
		
		$this->anchor->set('href',PROGRAM.'?opt='.$this->opt.'&path='.$this->url);
		$this->anchor->setText(captionName($this->file, 120));
		
		$div1 = HtmlTag::createElement('div')
			->set('style',createCSS(120,120)->set('margin','0 8px')
					->set('background-image','url(\''.$this->imgurl.'\')')->set('background-size','120px'));
		
		$div2 = HtmlTag::createElement('div')
			->set('style',CssStyle::createStyle()->set('width','90px')->set('padding','35px 8px')
					->set('color','#ddd')->set('font','bold 150% arial')->set('text-align','left'))
			->setText(displayName($this->file));
		//echo displayName($this->file);
		$div1->addElement($div2);
		$this->anchor->addElement($div1);
		$this->span->addElement($this->anchor);
		return $this->span;
		//return '<span style="border:1px solid #ddd;padding:5px;margin:5px;">'.$this->base.' '.$this->file.'</span>';
	}
}
class NonMediaCell extends FolderItem
{
}
class MiscCell extends FolderItem
{
}
class FolderItemFactory
{
	private static $_instance = null;
	private $cells = null;
	
	private function __construct()
	{
		return $this;
	}
	public static function create(){
		self::$_instance = new FolderItemFactory();
		return self::$_instance;
	}
	public function read($base)
	{
		$this->ignores = Ignores::read($base);
		$this->logofiles = LogoFile::read($base);
		
		$files = scandir($base);
		$this->cells = array();
		if($files != false)
		{
			foreach($files as $f)
			{
				//$c = new FolderItem($f, $base, $ignores, $logofiles);
				$c = $this->categorize($f, $base);
				// add to list
				$this->cells[$f] = $c;
			}
		}
		return $this;
	}
	public function html()
	{
		$s='';
		foreach($this->cells as $f => $c)
		{
			$s.=$c->html()."\n";
		}
		return $s;
	}
	private function categorize($f, $base)
	{
		if(ismovie($base.$f))         {return new MovieCell($base,$f);}//{ $this->setAttr('type',FileType::movie); }
		elseif(isimage($base.$f))     {return new ImageCell($base,$f);}//{ $this->setAttr('type',FileType::image); }
		elseif(is_dir($base.$f))      {return new DirCell($base,$f);}//{ $this->setAttr('type',FileType::directory); }
		elseif(isNonMedia($base.$f))  {return new NonMediaCell($base,$f);}//{ $this->setAttr('type',FileType::nonMedia); }
		elseif(!isNonMedia($base.$f)) {return new MiscCell($base,$f);}//{ $this->setAttr('type',FileType::misc); }
		elseif($this->ignores->inIgnores($f)) 
		{ 
			$fi = new FolderItem($base, $f);
			$fi->ignore();
			return $fi;
		}
	}
}

function mkUrl($paths){return str_replace('%2F','/', urlencode(joinUrl($paths)));}
function mkRawUrl($paths){return str_replace('%2F','/', rawurlencode(joinUrl($paths)));}
function joinUrl($paths){return preg_replace('#/+#','/', join("/",$paths));}
function mkImgUrl($path, $thumb){return mkRawUrl(array($path, $thumb));}

class FolderItem2 implements iFolderItem
{
	private $path;
	private $attr;
	
	public function __construct($f, $base, $ignores, $logofiles)
	{
		$this->ignores = $ignores;
		$this->logofiles = $logofiles;
		$this->path = $f;
		$this->attr = array();
		$this->categorize($f, $base);
	}
	//private function categorize($f, $base)
	//{
	//	if(ismovie($base.$f))         { $this->setAttr('type',FileType::movie); }
	//	elseif(isimage($base.$f))     { $this->setAttr('type',FileType::image); }
	//	elseif(is_dir($base.$f))      { $this->setAttr('type',FileType::directory); }
	//	elseif(isNonMedia($base.$f))  { $this->setAttr('type',FileType::nonMedia); }
	//	elseif(!isNonMedia($base.$f)) { $this->setAttr('type',FileType::misc); }
	//	if($this->ignores->inIgnores($f)) { $this->setAttr('ignore',true); }
	//	// process logo
	//	if($this->logofiles->inFiles($f)) { $this->setAttr('logo',true); }
	//}
	public function html() {}
	public function __toString()
	{
		$s=$this->path.' : ';
		//foreach(get_object_vars($this) as $name => $var)
		foreach($this->attr as $name => $val)
		{
			$s.=$name.'=>'.$val." : ";
		} 
		return "<span>".$s."</span>";
	}
	private function setAttr($name, $val)
	{
		if (isset($this->attr[$name]))
		{
			$this->attr[$name] = $val;
		} else {
			$this->attr[$name] = $val;
		}
	}
	private function getAttr($name) 
	{
		if(isset($this->attr[$name]))
		{
			return $this->attr[$name];
		}
		return null;
	}
}
class LogoItem
{
	private $item = null; //
	private $thumb = null; //
	private $tsize = null;
	private $dim = null; //
	private $path = null; //
	private $caption = null; //
	private $id = null;
	private $imgurl = null; //
	private $url = null; //
	private $opt = "1_100"; //
	
	public function __construct($path, $l)
	{
		$this->path = $path;
		list($this->item, $this->thumb, $this->tsize, $this->dim) = $this->load($l);
		$this->url = '/'.mkUrl(array($this->path, $this->item));
		$this->imgurl = '/'.mkImgUrl($this->path, $this->thumb);
	}
	private function load($l)
	{
		list($i,$t,$s,$d)=$l;
		if(strpos($d,'x')!==false)
		{
			list($width, $height) = explode("x",$d);
		}
		else
		{
			list($width, $height, $type, $attr) = getImgSize($_SERVER['DOCUMENT_ROOT'].$this->path.'/'.$t);
		}
		$size = (int)$s;
		return array($i, $t, $size, array('width' => $width, 'height' => $height));
	}
	public function html()
	{
		//return '<a href="'.$this->item.'"><img src="'.$this->thumb.'" style="height:'.$this->height.'; width:'.$this->width.';">'.$this->item.'</a>';
		$anchor = HtmlTag::createElement('a')->setText(captionName($this->caption,$this->dim['width']));
		$anchor->set('href',PROGRAM."?opt=".$this->opt."&path=".$this->url)
			->set('style',CssStyle::createStyle()->set('height','120px')->set('overflow','hidden'));
		$img = HtmlTag::createELement('img')->set('src',$this->imgurl);
		$imgStyle = createCSS($this->dim['width'],$this->dim['height']);

		$div = HtmlTag::createElement('div')->id($this->id);
		$img->set('style',$imgStyle);

		$div->addElement($img);
		$anchor->addElement($div);
		return $anchor;
	}
}
class LogoFile
{
	private static $_instance = null;
	private $files;
	private $logos;
	
	private function __construct()
	{
		return $this;
	}
	public static function read($path){
		self::$_instance = new LogoFile();
		if(DotFiles::hasLogo($path)) 
		{ 
			self::$_instance->load($path);
		}
		return self::$_instance;
	}
	public function inFiles($f) 
	{ 
		return in_array($f, $this->files); 
	}
	private function load($path)
	{
		$files=array();
		$lines = file($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/.logo', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		for($i=0;$i<count($lines);$i++)
		{
			if(substr( $lines[$i], 0, 1 ) != "#")
			{
				$pieces = explode(",", $lines[$i]);
				if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/'.$pieces[0]))
				{
					$files[] = $pieces[0];
					$logos[basename($pieces[0])] = new LogoItem($path, $pieces);
				}
				elseif(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$pieces[0]))
				{
					$files[] = basename($pieces[0]);
					$logos[basename($pieces[0])] = new LogoItem($path, $pieces);
				}
				else
				{
					echo $_SERVER['DOCUMENT_ROOT'].'/'.$path.'/'.$pieces[0]." not found\n";
				}
			}
		}
		$this->logos = $logos;
		$this->files = $files;
	}
	public function html($f)
	{
		if(in_array($f, $this->files))
		{
			return $this->logos[$f]->html();
		}
		else
		{
			return null;
		}
	}
}

class Ignores
{
	private static $_instance = null;
	private $stdIgnores = array(".","..",'reiserfs_priv','.pics','.picasaoriginals',
								'.AppleDB','.AppleDesktop','.AppleDouble',
								'Network Trash Folder','Temporary Items','Thumbs.db',':2e*');
	private $localIgnores = array();
	
	private function __construct()
	{
		return $this;
	}
	public static function read($path){
		self::$_instance = new Ignores();
		self::$_instance->load($path);
		return self::$_instance;
	}
	private function load($path)
	{
		if(DotFiles::hasIgnore($path)) 
		{
			$ignores = file($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/.ignore', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			if ($ignores !== False) 
			{ 
				$this->localIgnores = $ignores; 
			}
		}
	}
	public function inIgnores($f)
	{
		return (in_array($f, $this->localIgnores) || in_array($f, $this->stdIgnores) || substr(basename($f),0,1)=="." 
				|| substr(basename($f),0,3)==":2e" || getExt($f) == ".$$$");
	}
}
class DotFiles
{
	private function __construct() {}
	private function dotFileExists($d,$f)
	{
		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$d.'/'.$f)) { return true; } else {return false; }
	}
	public static function hasModelDB($d)		{ return  dotFileExists($d,'.modeldb'); } // used to call modeldetails for specific directories
	public static function hasThumbs($d)		{ return  dotFileExists($d,'.thumbs'); } // used in www2.alsscan.com for model pages
	public static function hasGalleryIgnore($d)	{ return  dotFileExists($d,'.gallery_ignore'); } // not sure this is actually required
	public static function hasTitle($d)			{ return  dotFileExists($d,'.title'); } // optional page title
	public static function hasIgnore($d)		{ return  dotFileExists($d,'.ignore'); } // list of files/dirs to ignore
	public static function hasImgsize($d)		{ return  dotFileExists($d,'.imgsize'); } // list of image dimensions
	public static function hasPics($d)			{ return  dotFileExists($d,'.pics'); } // hidden dir full of thumbnails
	public static function hasLogo($d)			{ return  dotFileExists($d,'.logo'); } // list of dir to thumbs associations
	public static function hasAlpha($d)			{ return  dotFileExists($d,'.alpha'); }
	public static function hasAlphabet($d)		{ return  dotFileExists($d,'.alphabet'); }
	public static function hasCalendar($d)		{ return  dotFileExists($d,'.calendar'); } // use calendar links
	public static function hasIndex($d)			{ return (dotFileExists($d,'igallery.html') || dotFileExists($d,'igallery.php')); }
	public static function hasComments($d)		{ return  dotFileExists($d,'comments.php'); }
	public static function hasFavourites($d)	{ return  dotFileExists($d,'.favourites'); }
	public static function hasBookmarks($d)		{ return  dotFileExists($d,'.bookmarks'); }
	public static function hasRollovers($d)		{ return  dotFileExists($d,'.rollovers'); }
}
//private $img_ht;
//private $opt;
//private $caption;
//private $thumb;
//private $overlay;
//private $movieLen;

//$this->img_ht = 0;
//$this->opt = null;
//$this->caption = null;
//$this->thumb = null;
//$this->overlay = null;
//$this->movieLen = 0;

//$cd = new CellData("/path/to/file");
//echo $cd->getAttr('path')."\n";
//$cd->setAttr('width',120);
//echo $cd->getAttr('width')."\n";
//$cd->setAttr('unknown',3);
//echo $cd->getAttr('unknown')."\n";

//$ci = new CellImage("/path/to/image");
//echo $ci->getAttr('path')."\n";
//$ci->setAttr('width',120);
//echo $ci->getAttr('width')."\n";
//$ci->setAttr('unknown',3);
//echo $ci->getAttr('unknown')."\n";
//$ci->setAttr('img_ht',12);
//echo $ci->getAttr('img_ht')."\n";

?>