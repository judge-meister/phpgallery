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
	public function toStr($ign) 
	{
		if($ign == FileType::image) { return "::image"; }
		else if($ign == FileType::movie) { return "::movie"; }
		else if($ign == FileType::directory) { return "::directory"; }
		else if($ign == FileType::nonMedia) { return "::nonMedia"; }
		else if($ign == FileType::misc) { return "::misc"; }
		else { return ""; }
	}
}

interface iFolderItem { public function html(); }

class FolderItem implements iFolderItem
{
	public function __construct($base, $f, $ignored, $logo)
	{
		$this->item = null; //
		$this->thumb = null; //
		$this->tsize = null;
		$this->id = null;
		$this->imgurl = null; //
		$this->url = null; //
		$this->opt = "1_100"; //
	
		$this->base = null;
		$this->width = 120;
		$this->height = 120;
		$this->img_ht = 120;
		$this->file = $f;
		$this->base = $base;
		$this->caption = $f;
        $this->type = null;
		
		$this->ignore = $ignored;
		$this->logo = $logo;
		
		$this->span = HtmlTag::createElement('span')
			->set('style',createCSS($this->width+Config2::wplus, Config2::full_ht));
		//$this->span->showTextBeforeContent(True);
		
		$this->anchor = HtmlTag::createElement('a')->setText(captionName($this->caption,$this->width));
		
		//$this->img = HtmlTag::createELement('img')->set('src',$this->imgurl);
		$this->imgStyle = createCSS($this->width,$this->height);
	}
	public function getType() { return $this->type; }
	public function ignore()
	{
		$this->ignore = True; // = FileType::ignored;
		return $this;
	}
	public function html($force = False)
	{
		if((!$this->ignore && !$this->logo->inFiles($this->file)) || $force)
		{
			return $this->html2();
		}
		else if($this->logo->inFiles($this->file))
		{
			return $this->logo->html($this->file);
		}
		else { return ""; }
	}
	public function normalizeThmSize($getsizes=true)
	{
		if($getsizes == true)
		{
			list($this->width, $this->height, $type, $attr) = getImgSize($_SERVER['DOCUMENT_ROOT'].'/'.$this->base.'/'.$this->thumb);
			$this->img_ht = $this->height;
		}
		if((int)$this->height > 120)
		{ 
			$this->width = (int)((float)$this->width / ((float)$this->height / 120.0)); 
			$this->height = 120; 
		}
		if((int)$this->width > 1200)
		{
			$this->width = 1200; 
			$this->height = (int)((float)$this->height / ((float)$this->width / 1200.0)); 
		}
	}
}
class MovieCell extends FolderItem
{
	public function __construct($base, $f, $ignored, $logo)
	{
		parent::__construct($base, $f, $ignored, $logo);
		$this->type = FileType::movie;
		$this->movieLen = $this->movieLength();

		global $mediaTypes;
		$thm = removeExt($this->file).'.thm';
		$tbn = removeExt($this->file).'.tbn';
		if(file_exists($_SERVER['DOCUMENT_ROOT'].$this->base.'/'.$thm))
		{
			$this->thumb = $thm;
			$this->normalizeThmSize();
		}
		else if(file_exists($_SERVER['DOCUMENT_ROOT'].$this->base.'/.pics/'.$thm))
		{
			$this->thumb = '.pics/'.$thm;
			$this->normalizeThmSize();
		}
		else if(file_exists($_SERVER['DOCUMENT_ROOT'].$this->base.'/'.$tbn))
		{
			$this->thumb = $tbn;
			$this->normalizeThmSize();
		}
		else if(file_exists($_SERVER['DOCUMENT_ROOT'].$this->base.'/.pics/'.$tbn))
		{
			$this->thumb = '.pics/'.$tbn;
			$this->normalizeThmSize();
		}
		else
		{
			list($this->thumb,$this->width,$this->height) = $mediaTypes['movie']['thm'];
		}
		//$this->m_html .= "\n<!-- doMovie -->";
		$this->dir = $this->base.'/'.$this->file;

		$this->imgurl = joinUrl(array($this->base, $this->thumb));
		$this->image = $this->file;
		$this->caption = $this->file;
		
		echo "<!-- MovieCell: imgurl ".$this->imgurl." -->\n";
		echo "<!-- MovieCell: thumb  ".$this->thumb." -->\n";
	}
	private function comments()
	{
		if(hasComments($this->base))
		{
			include_once($_SERVER['DOCUMENT_ROOT'].$this->base.'/comments.php');
			$this->m_comments = getComments();
		}
	}
	private function movieLength()
	{
		$this->comments();
		$min = $secs = '';
		if (in_array($this->file, array_keys($this->m_comments)))
		{
			$length = $this->m_comments[$this->file];
			$min = sprintf("%d",(int)($length/60.0));
			$secs = sprintf("%02d",(int)($length-($min*60)));
		}
		return array($min, $secs);
	}
	public function html2()
	{
		$this->span = HtmlTag::createElement('span')
			->set('style',createCSS($this->width+Config2::wplus, Config2::full_ht));
		//$this->span->showTextBeforeContent(True);
		
		$this->anchor = HtmlTag::createElement('a')->setText(captionName($this->caption,$this->width));
		
		//$this->img = HtmlTag::createELement('img')->set('src',$this->imgurl);
		$this->imgStyle = createCSS($this->width,$this->height);
		
		$this->span->setText(comment('SpanLogoMovie'));
		
		$this->img = HtmlTag::createELement('img')->set('src',$this->imgurl);
		$this->anchor->set('href',joinUrl(array($this->dir)));
		if(strpos($this->thumb, "/")===0) { 
			//echo "\nUse MovieClip\n";
			$this->img->set('src',mkRawUrl(array($this->thumb))); 
		}
		
		$div = HtmlTag::createElement('div')->set('style',CssStyle::createStyle()->set('height','120px'));

		$overlayBtn = Overlay::create()->mkBtn(PLAY_BUTTON,'-90')->html();
		
		$overlayTime = null;
		list($min,$secs) = $this->movieLen;
		if($min != "" || $secs != "") {
			$overlayTime = Overlay::create()->mkLabel($min,$secs)->html();
		}
		$this->img->set('style',$this->imgStyle);
		$div->addElement($this->img);
		$div->addElement($overlayBtn);
		$div->addElement($overlayTime);
		$this->anchor->addElement($div);
		$this->span->addElement($this->anchor);
		return "\n".$this->span;
	}
}
class ImageCell extends FolderItem
{
	public function __construct($base, $f, $ignored, $logo)
	{
		parent::__construct($base, $f, $ignored, $logo);
		$this->type = FileType::image;
		
		//echo "<!-- ImageCell:".$_SERVER['DOCUMENT_ROOT'].$this->base.'.pics/'.$this->file." -->\n";
		if(file_exists($_SERVER['DOCUMENT_ROOT'].$this->base.'.pics/'.$this->file))
		{
			$this->thumb = '.pics/'.$this->file;
		}
		else
		{
			$this->thumb = $this->file;
		}
		$this->normalizeThmSize();
		$this->thumb = $this->base.'/'.$this->thumb;
		$this->dir = "";
		$this->image = $this->file;
		$this->caption = $this->file;

		$this->url = mkRawUrl(array($this->base, $this->image));
		$this->imgurl = joinUrl(array($this->thumb));

		$phpThumb = new iPhpThumb($this);
		if($phpThumb->isActive())
		{
			$this->imgurl = $phpThumb->picUrl2($this->img_ht, $this->thumb, $this->imgurl);
			//echo "<!-- ImageCell:".$this->imgurl." -->\n";
		}
		else
		{
			//echo "<!-- ImageCell:".$this->thumb." ".$this->width." ".$this->height." -->\n";
			$this->imgurl = mkRawUrl(array($this->thumb));
		}
	}
	public function html2()
	{
		$this->span->set('style',createCSS($this->width+Config2::wplus, Config2::full_ht));
		$this->span->setText(comment('SpanPhoto ARSE'));

		$this->anchor = HtmlTag::createElement('a')->setText(captionName($this->caption,$this->width));
		$this->anchor->set('href',$this->url)->set('rel','doSlideshow:true')->set('title',$this->caption);
		$div = HtmlTag::createElement('div')->set('style',CssStyle::createStyle()->set('height','120px'));
			//->setText($overlay); // **
		$this->imgStyle = createCSS($this->width,$this->height);
		$img = HtmlTag::createElement('img')->addClass('thumb')
			->set('style',$this->imgStyle)
			->set('src',$this->imgurl);
		$div->addElement($img);
		$this->anchor->addElement($div);
		$this->span->addElement($this->anchor);
		return "\n".$this->span;
	}
}
class DirCell extends FolderItem
{
	public function __construct($base, $f, $ignored, $logo)
	{
		parent::__construct($base, $f, $ignored, $logo);
		$this->imgurl = FILE_FOLDER;
		$this->url = mkUrl(array($base,$f));
		$this->type = FileType::directory;
	}
	public function html2()
	{
		$this->img = HtmlTag::createELement('img')->set('src',$this->imgurl);
		$this->span->setText('<!-- span_dir -->');
		$this->span->showTextBeforeContent(True);
	
		$this->anchor->set('href',PROGRAM.'?opt='.$this->opt.'&path='.$this->url);
		$this->anchor->setText(captionName($this->file, $this->width));
	
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
	}
}
class NonMediaCell extends FolderItem
{
	public function __construct($base, $f, $ignored, $logo)
	{
		parent::__construct($base, $f, $ignored, $logo);
		$this->type = FileType::nonMedia;
		$this->ext = getExt($f);
		global $nonMediaThumbs;
		list($this->imgurl, $this->width, $this->height) = $nonMediaThumbs[$this->ext];
		$this->url = mkUrl(array($base,$f));
	}
	public function html2()
	{
		$this->span->setText(comment('SpanPhoto NonMedia'));

		$this->anchor->set('href',$this->url)->set('rel','doSlideshow:true')->set('title',$this->file);
		$div = HtmlTag::createElement('div')->set('style',CssStyle::createStyle()->set('height','120px'));
			//->setText($overlay); // **
		$img = HtmlTag::createElement('img')->addClass('thumb')
			->set('style',$this->imgStyle)
			->set('src',$this->imgurl);
		$div->addElement($img);
		$this->anchor->addElement($div);
		$this->span->addElement($this->anchor);
		return "\n".$this->span;
	}
}
class MiscCell extends FolderItem
{
	public function __construct($base, $f, $ignored, $logo)
	{
		parent::__construct($base, $f, $ignored, $logo);
		$this->type = FileType::misc;
		$this->ext = getExt($f);
		$this->url = mkUrl(array($base,$f));
	}
	public function html2()
	{
		$this->span->setText('<!-- span_misc -->');
		$this->span->showTextBeforeContent(True);
		$this->anchor->set('href',$this->url);
		
		// these styles need to be classes and put in css file
		$div1 = HtmlTag::createElement('div')
			->set('style',CssStyle::createStyle()->set('width','120px')->set('height','120px')
			->set('background-image',"url('".FILE_BLANK."')")->set('background-size','120px'));
		$div2 = HtmlTag::createElement('div')
			->set('style',CssStyle::createStyle()->set('padding','90px 20px')->set('color','#ddd')
			->set('font','bold 200% arial')->set('text-align','left'));
		
		$unknown = HtmlTag::createElement('font')
			->set('style',CssStyle::createStyle()->set('color','#ddd')->set('font','italic bold 100% arial'))
			->setText('unk');
		
		if($this->ext == NULL) 
		{ 
			$div2->addElement($unknown);
		}
		else
		{ 
			$div2->setText($this->ext);
		}
		$div1->addElement($div2);
		$this->anchor->addElement($div1);
		$this->span->addElement($this->anchor);
		
		return "\n".$this->span;
	}
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
		//echo $this->logofiles->
		
		$files = scandir($_SERVER['DOCUMENT_ROOT'].$base);
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
		if(ismovie($_SERVER['DOCUMENT_ROOT'].$base.$f))         { return new MovieCell   ($base, $f, $this->ignores->inIgnores($f), $this->logofiles ); }
		elseif(isimage($_SERVER['DOCUMENT_ROOT'].$base.$f))     { return new ImageCell   ($base, $f, $this->ignores->inIgnores($f), $this->logofiles ); }
		elseif(is_dir($_SERVER['DOCUMENT_ROOT'].$base.$f))      { return new DirCell     ($base, $f, $this->ignores->inIgnores($f), $this->logofiles ); }
		elseif(isNonMedia($_SERVER['DOCUMENT_ROOT'].$base.$f))  { return new NonMediaCell($base, $f, $this->ignores->inIgnores($f), $this->logofiles ); }
		elseif(!isNonMedia($_SERVER['DOCUMENT_ROOT'].$base.$f)) { return new MiscCell    ($base, $f, $this->ignores->inIgnores($f), $this->logofiles ); }
	}
}

function joinUrl($paths) { return preg_replace('#/+#','/', join("/",$paths)); }
function mkUrl($paths) { return str_replace('%2F','/', urlencode(joinUrl($paths))); }
function mkRawUrl($paths) { return str_replace('%2F','/', rawurlencode(joinUrl($paths))); }
function mkImgUrl($path, $thumb) { return mkRawUrl(array($path, $thumb)); }

class LogoItem extends FolderItem
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
		$this->url = mkUrl(array($this->path, $this->item));
		$this->imgurl = mkImgUrl($this->path, $this->thumb);
		$this->type = null;
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
		return array($i, $t, $size, array('width' => (int)$width, 'height' => (int)$height));
	}
	public function html()
	{
		$this->id='';

		$span = HtmlTag::createElement('span')
			->set('style',createCSS($this->dim['width']+Config2::wplus, Config2::full_ht));
		$span->showTextBeforeContent(True);
		$anchor = HtmlTag::createElement('a')->setText(captionName(basename($this->item),$this->dim['width']));
		$anchor->set('href',PROGRAM."?opt=".$this->opt."&path=".$this->url)
			->set('style',createCSS($this->dim['width']+Config2::wplus, Config2::full_ht)->set('overflow','hidden'));
		$img = HtmlTag::createELement('img')->set('src',$this->imgurl);
		$imgStyle = createCSS($this->dim['width'],$this->dim['height']);

		$div = HtmlTag::createElement('div')->id($this->id);
		$img->set('style',$imgStyle);

		$div->addElement($img);
		$anchor->addElement($div);
		$span->addElement($anchor);
		return $span;
	}
}
class LogoFile
{
	private static $_instance = null;
	
	private function __construct()
	{
		return $this;
	}
	public static function read($path){
		self::$_instance = new LogoFile();
		if(DotFiles::hasLogo($path)) 
		{ 
			//echo "[hasLogo]LogoFile::load(".$path.")\n";
			self::$_instance->load($path);
		}
		//echo "LogoFile::load(".$path.")\n";
		return self::$_instance;
	}
	public function inFiles($f) 
	{ 
		//echo "LogoFiles::files=".$f;
		//var_dump($this->files);
		//echo "\n";
		return in_array($f, $this->files); 
	}
	private function load($path)
	{
		$lines = file($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/.logo', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		for($i=0;$i<count($lines);$i++)
		{
			if(substr( $lines[$i], 0, 1 ) != "#")
			{
				$pieces = explode(",", $lines[$i]);
				if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/'.$pieces[0]))
				{
					$this->files[] = $pieces[0];
					$this->logos[basename($pieces[0])] = new LogoItem($path, $pieces);
				}
				elseif(file_exists($_SERVER['DOCUMENT_ROOT'].$pieces[0])) //
				{
					echo "<!-- LogoFiles: movie? ".$pieces[0]."-->\n";
					$this->files[] = basename($pieces[0]);
					//$this->logos[basename($pieces[0])] = new LogoItem($path, $pieces);
					$this->logos[basename($pieces[0])] = new MovieCell($path, basename($pieces[0]), False, $this);
				}
				else
				{
					echo "LogoFile: ".$pieces[0]." not found\n";
				}
			}
		}
	}
	public function html($f)
	{
		if(in_array($f, $this->files))// && $this->logos[$f]->getType() != FileType::movie)
		{
			return $this->logos[$f]->html(True);
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
//function dotFileExists($d,$f)
//{
//	if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$d.'/'.$f)) { return true; } else {return false; }
//}
class DotFiles
{
	private function __construct() {}
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

/*

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
	public function html() {}
	public function __toString()
	{
		$s=$this->path.' : ';
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

*/
?>
