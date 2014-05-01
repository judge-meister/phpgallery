<?php
// --------------------------------------------------------------------
// Re-write of gallery.py cgi script into php to allow addition of 
// login system.
//
// Basics done - .logo processing, dir_name buttons, photo set thumbs
//               page navigation
//
// Still To Do
//
// - more values in ignore list
// - handle .title .calendar files
// - more media types - including movie thumbs
// - special pages like wiredpussy, alsscan, alsangel, hegreart, femjoy
// - pull down menu navigation - [low priority]
// 
// need to analyse original gallery.py code for more features
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
// cell types
// - ignored, calendar, favourite, logo(dir/movie), image, movie, non-media, misc
// --------------------------------------------------------------------
if(!defined('INCLUDE_CHECK')) { define('INCLUDE_CHECK',true); }

if(!isset($_POST['PHPUNIT']))      { include( gethostname().'/config.php' ); } 
else   if($_POST['PHPUNIT']!=True) { include( gethostname().'/config.php' ); }

require_once( 'functions.php' );
require_once( 'Span.class.php' );
require_once( 'pluginLoader.php' );


$Config['logon']=False;
if(LOGIN_ENABLED == True && param('PHPUNIT') != True)
{
	require $_SERVER['DOCUMENT_ROOT'].LOGIN_PATH.'/user_check.php';
	require $_SERVER['DOCUMENT_ROOT'].LOGIN_PATH.'/logoff.php';
	$Config['logon']=True;
}


class Gallery
{
	private $m_ignores = array();
	private $m_parent_ignores = array();
	private $m_logofiles = array();
	
	private $m_start = 0;
	private $m_end = 0;
	private $m_item_count = 0;
	private $m_pagenum = 1;
	
	private $celldata = array('path'=>null,'dir'=>null,'width'=>0,'height'=>0,'img_ht'=>0,'opt'=>null,
				  'caption'=>null,'thumb'=>null,'image'=>null,'overlay'=>null,'movieLen'=>0);
	private $celldata_default;
	private $m_html = "";
	
	public function getPath() { return $this->celldata['path']; }
	public function getHtml() { return $this->m_html; }
	public function getThumbWidth() 
	{ 
		if($this->prevRowWidth > 0)
		{
			return "width:".($this->prevRowWidth + 5)."px";
		}
		else
		{ 
			return "";
		} 
	}
	
	public function __construct($stdIgnores, $Config, $path, $opt)
	{
		$this->Config = $Config;
		$this->celldata['path'] = $path;
		if($this->celldata['path'] == "")
		{
			$this->celldata['path'] = TOP;
		}
		else if(substr($this->celldata['path'], 1) != "/")
		{
			$this->celldata['path'] = "/".$this->celldata['path'];
		}
		if($opt)
		{
			$o = explode('_',$opt);
			$this->Config['pagesize'] = (int)$o[1];
			$this->m_pagenum = (int)$o[0];
		}
		$this->m_start = ($this->m_pagenum-1) * $this->Config['pagesize'];
		$this->m_end = ($this->m_pagenum) * $this->Config['pagesize'];
		
		$this->m_ignores = $stdIgnores;
		$this->m_parent_ignores = $stdIgnores;
		$this->m_comments = array();
		$this->favourites = array();
		$this->bookmarks = array();
		$this->rowWidth = 0;
		$this->prevRowWidth = 0;
	}
	public function findPageWidth($w)
	{
		if(($this->rowWidth + $w) < 1230) 
		{ 
			$this->rowWidth = $this->rowWidth + $w;
			echo "<!-- ".$this->rowWidth." ".$w." -->\n";
		}
		else //if($this->prevRowWidth == 0)
		{
			if($this->rowWidth > $this->prevRowWidth) 
			{
				$this->prevRowWidth = $this->rowWidth;
			}
			echo "<!-- row=".$this->prevRowWidth." -->\n";
			$this->rowWidth = 0;
		}
		//echo "<!-- ".$w." -->\n";
	}
	public function resetCellData()
	{
		$this->celldata = array('path'=>$this->celldata['path'],
					'dir'=>null,'width'=>0,'height'=>0,'img_ht'=>0,'opt'=>null,
					'caption'=>null,'thumb'=>null,'image'=>null,'overlay'=>null,
					'movieLen'=>0);
	}
	public function pagebreakcomment()
	{
		echo comment("pagesize=".$this->Config['pagesize'])."\n";
		echo comment("pagenum=".$this->m_pagenum)."\n";
		echo comment("start=".$this->m_start)."\n";
		echo comment("end=".$this->m_end)."\n";
		echo comment("item_count=".$this->m_item_count)."\n";
		if(count($this->m_logofiles) > 0)
		{
			echo "<!-- m_logofiles ".count($this->m_logofiles)." ";
			//var_dump($this->m_logofiles);
			echo " -->";
		}
	}
	
	public function options($num=0)
	{
		if($num > 0)
		{
			return $num.'_'.$this->Config['pagesize'];
		}
		else
		{
			return $this->m_pagenum.'_'.$this->Config['pagesize'];
		}
	}
	public function span_logo() //.logo thumb
	{
		$this->celldata['opt'] = $this->options(1);
		$s = new SpanLogo($this->celldata, $this->Config);
		if(hasRollovers($this->celldata['path']))
		{
			$s->setRollover();
		}
		$this->findPageWidth($s->getWidth());
		return $s->html();
	}

	public function span_logo_movie() // movie thumb
	{
		$s = new SpanLogoMovie($this->celldata, $this->Config);
		$this->findPageWidth($s->getWidth());
		return $s->html();
	}

	public function span_photo() // photo thumbs
	{
		$s = new SpanPhoto($this->celldata, $this->Config);
		$this->findPageWidth($s->getWidth());
		return $s->html();
	}

	public function span_dir() //dir name no thumbs
	{
		$this->celldata['width'] = 132;
		$img_url = FILE_FOLDER;
		if (in_array($this->celldata['dir'], $this->favourites))
		{
			$img_url = FAV_FOLDER;
		}
		$s = new SpanDir($this->celldata, $this->Config, $img_url);
		$this->findPageWidth($s->getWidth());
		return $s->html();
	}
	
	public function span_icon($image,$caption) // photo thumbs
	{
		$this->celldata['caption'] = $caption; // $file or $dir
		$this->celldata['image'] = $image;
		$this->celldata['opt'] = $this->options(1);
		$s = new SpanIcon($this->celldata, $this->Config);
		$this->findPageWidth($s->getWidth());
		return $s->html();
	}

	public function normalizeThmSize($getsizes=true)
	{
		if($getsizes == true) 
		{ 
			list($this->celldata['width'], $this->celldata['height'], $type, $attr) = getImgSize($_SERVER['DOCUMENT_ROOT'].'/'.$this->celldata['path'].'/'.$this->celldata['thumb']);
			$this->celldata['img_ht'] = $this->celldata['height'];
		}
		if((int)$this->celldata['height'] > 120) 
		{ 
			$this->celldata['width'] = (int)((float)$this->celldata['width'] / ((float)$this->celldata['height'] / 120.0)); 
			$this->celldata['height'] = 120; 
			//$this->celldata['img_ht'] = $this->celldata['height'];
		}
		if((int)$this->celldata['width'] > 1200)
		{
			$this->celldata['width'] = 1200; 
			$this->celldata['height'] = (int)((float)$this->celldata['height'] / ((float)$this->celldata['width'] / 1200.0)); 
			//$this->celldata['img_ht'] = $this->celldata['height'];
		}
	}
	private function readBookmarks()
	{
		foreach(file($_SERVER['DOCUMENT_ROOT'].$this->celldata['path'].'/.logo', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line)
		{
			if(strpos($line,',') !== False && substr( $line, 0, 1 ) != "#") 
			{
				//echo "<p>".$lines[$i];
				$pieces = explode(",", $line);
				if(!in_array($pieces[0], $this->m_ignores))
				{
					if(file_exists($_SERVER['DOCUMENT_ROOT'].$this->celldata['path'].'/'.$pieces[0]) || file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$pieces[0]))
					{
						// add to bookmark array stored in this->
						$this->bookmarks[$pieces[0]]=$pieces[1];
					}
				}
			}
		}
	}
	private function readFavourites()
	{
		$lines = file($_SERVER['DOCUMENT_ROOT'].$this->celldata['path'].'/.favourites', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach($lines as $line)//for($i=0;$i<count($lines);$i++)
		{
			if(substr( $line, 0, 1 ) != "#") 
			{
				//echo "<p>".$lines[$i];
				//$pieces = explode(",", $line);
				if(!in_array($line, $this->m_ignores))
				{
					if(file_exists($_SERVER['DOCUMENT_ROOT'].$this->celldata['path'].'/'.$line) || file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$line))
					{
						// add to favourites array stored in this->
						$this->favourites[] = $line;
						//echo $line;
					}
				}
			}
		}
	}
	private function doLogo()
	{
		//read .imgsize
		$lines = file($_SERVER['DOCUMENT_ROOT'].$this->celldata['path'].'/.logo', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach($lines as $line)//for($i=0;$i<count($lines);$i++)
		{
			$this->resetCellData();
			if(strpos($line,',') !== False && substr( $line, 0, 1 ) != "#") 
			{
				if($this->m_item_count >= $this->m_start && $this->m_item_count < $this->m_end)
				{
					//echo "<p>".$lines[$i];
					$pieces = explode(",", $line);
					if(!in_array($pieces[0], $this->m_ignores))
					{
						if(file_exists($_SERVER['DOCUMENT_ROOT'].$this->celldata['path'].'/'.$pieces[0]) || file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$pieces[0]))
						{
							$this->celldata['thumb'] = $pieces[1];
							if(count($pieces) == 4 && strpos($pieces[3],'x') !== false)
							{
								list($this->celldata['width'], $this->celldata['height']) = explode("x",$pieces[3]);
							}
							else
							{
								list($this->celldata['width'], $this->celldata['height'], $type, $attr) = getImgSize($_SERVER['DOCUMENT_ROOT'].$this->celldata['path'].'/'.$this->celldata['thumb']);
							}
							//$this->celldata['img_ht'] = $this->celldata['height'];
							$this->normalizeThmSize(false);
							$this->celldata['dir'] = $pieces[0];
							if(file_exists($_SERVER['DOCUMENT_ROOT'].$this->celldata['path'].'/'.$pieces[0]))
							{
								$this->celldata['caption'] = str_replace("_"," ",$pieces[0]);
								$this->m_html .= $this->span_logo()."\n";
								$this->m_item_count++;
							}
							else if(file_exists($_SERVER['DOCUMENT_ROOT'].$pieces[0]))
							{
								$this->celldata['caption'] = str_replace("_"," ",basename($pieces[0]));
								$this->m_html .= $this->span_logo_movie()."\n";
								$this->m_logofiles[] = $pieces[1];
								$this->m_item_count++;
								$this->celldata['dir'] = "";
							}
							$this->m_logofiles[] = basename($pieces[0]);
						}
					}
				}
				else if($this->m_item_count < $this->m_start || $this->m_item_count >= $this->m_end)
				{
					$pieces = explode(",", $line);
					$this->m_logofiles[] = basename($pieces[0]);
					$this->m_item_count++;
				}
			}
		}
		$this->celldata['dir'] = "";
	}
	private function doImage($file)
	{
		if(file_exists($_SERVER['DOCUMENT_ROOT'].$this->celldata['path'].'/.pics/'.$file))
		{
			$this->celldata['thumb'] = '.pics/'.$file;
		}
		else
		{
			$this->celldata['thumb'] = $file;
		}
		$this->m_html .= "<!-- doImage -->";
		$this->normalizeThmSize();
		$this->celldata['thumb'] = $this->celldata['path'].'/'.$this->celldata['dir'].'/'.$this->celldata['thumb'];
		$this->celldata['dir'] = "";
		$this->celldata['image'] = $file;
		$this->celldata['caption'] = $file;
		$this->m_html .= $this->span_photo()."\n";
		$this->m_item_count++;
	}
	private function doMovie($file)
	{
		global $mediaTypes;
		$thm = removeExt($file).'.thm';
		$tbn = removeExt($file).'.tbn';
		if(file_exists($_SERVER['DOCUMENT_ROOT'].$this->celldata['path'].'/'.$thm))
		{
			$this->celldata['thumb'] = $thm;
			$this->normalizeThmSize();
		}
		else if(file_exists($_SERVER['DOCUMENT_ROOT'].$this->celldata['path'].'/.pics/'.$thm))
		{
			$this->celldata['thumb'] = '.pics/'.$thm;
			$this->normalizeThmSize();
		}
		else if(file_exists($_SERVER['DOCUMENT_ROOT'].$this->celldata['path'].'/'.$tbn))
		{
			$this->celldata['thumb'] = $tbn;
			$this->normalizeThmSize();
		}
		else if(file_exists($_SERVER['DOCUMENT_ROOT'].$this->celldata['path'].'/.pics/'.$tbn))
		{
			$this->celldata['thumb'] = '.pics/'.$tbn;
			$this->normalizeThmSize();
		}
		else
		{
			list($this->celldata['thumb'],$this->celldata['width'],$this->celldata['height']) = $mediaTypes['movie']['thm'];
		}
		$this->m_html .= "\n<!-- doMovie -->";
		$this->celldata['dir'] = $this->celldata['path'].'/'.$file;

		$this->celldata['image'] = $file;
		$this->celldata['caption'] = $file;
		$this->celldata['movieLen'] = $this->movieLength($file);
		$this->m_html .= $this->span_logo_movie();
		$this->m_item_count++;
	}
	function movieLength($file)
	{
		$min = $secs = '';
		if (in_array($file, array_keys($this->m_comments)))
		{
			$length = $this->m_comments[$file];
			$min = sprintf("%d",(int)($length/60.0));
			$secs = sprintf("%02d",(int)($length-($min*60)));
		}
		return array($min, $secs);
	}
	function inExcludes($file)
	{
		if(in_array(basename($file), $this->m_ignores) || in_array(basename($file), $this->m_logofiles))
		{
			$c = count($this->m_ignores) + count($this->m_logofiles);
			//comment("true inExcludes ".$c);
			return true;
		}
		else
		{
			 if(substr(basename($file),0,1) == ".") { return true; }
			 if(substr(basename($file),0,3) == ":2e") { return true; }
			 if(getExt($file) == ".$$$") { return true; }
		}
		$c = count($this->m_ignores) + count($this->m_logofiles);
		//comment("false inExcludes ".$c);
		return false;
	}
	
	public function buildThumbs()
	{
		global $mediaTypes;
		$this->celldata['dir'] = "";
		if(hasIgnore($this->celldata['path']))
		{
			$this->m_ignores = array_merge($this->m_ignores, getIgnores($this->celldata['path']));
		}
		if(hasCalendar($this->celldata['path']) && file_exists('calendar.php'))
		{
			include('calendar.php');
			list($yrs,$html) = allYears($_SERVER['DOCUMENT_ROOT'].'/'.$this->celldata['path']);
			$this->m_html .= $html;
			$this->m_ignores = array_merge($this->m_ignores, $yrs);
		}
		if(hasComments($this->celldata['path']))
		{
			include($_SERVER['DOCUMENT_ROOT'].$this->celldata['path'].'/comments.php');
			$this->m_comments = getComments();
		}
		if(hasBookmarks($this->celldata['path']))
		{
			$this->readBookmarks();
		}
		if(hasFavourites($this->celldata['path']))
		{
			$this->readFavourites();
		}
		if(hasLogo($this->celldata['path'])) // .logo
		{
			$this->doLogo();
		}
		$files = scandir($_SERVER['DOCUMENT_ROOT'].$this->celldata['path']);
		if($files != false)
		{
			foreach($files as $file)//for($i = 0; $i < count($files); $i++)
			{
				$this->resetCellData();
				if(!$this->inExcludes($file) && ($this->m_item_count >= $this->m_start && $this->m_item_count < $this->m_end))
				{
					//echo "<p>".$files[$i];
					if(is_dir($_SERVER['DOCUMENT_ROOT'].$this->celldata['path'].'/'.$file)) // dir no thumb
					{
						$this->celldata['dir'] = $file;
						$this->m_html .= $this->span_dir()."\n";
						$this->m_item_count++;
						$this->celldata['dir'] = "";
					}
					else if(isimage($file)) //photo thumb
					{
						$this->doImage($file);
					}
					else if(ismovie($file))
					{
						$this->doMovie($file); // calls span_logo_movie
					}
					// --- filetype icon handling ---
					else if(isNonMedia($file))
					{
						global $nonMediaThumbs;
						$this->m_html .= "\n<!-- isNonMedia -->";
						$e = substr($file, strrpos($file, '.'));
						list($this->celldata['thumb'],$this->celldata['width'],$this->celldata['height']) = $nonMediaThumbs[$e];
						$this->celldata['img_ht'] = $this->celldata['height'];
						$this->celldata['image'] = $file;
						$this->celldata['caption'] = $file;
						$this->m_html .= $this->span_photo()."\n";
						$this->m_item_count++;
					}
					// --- unknown file types ---
					else if(!isNonMedia($file))
					{
						list($this->celldata['thumb'],$this->celldata['width'],$this->celldata['height']) = $mediaTypes['misc']['thm'];
						$this->m_html .= $this->span_icon($file,$file)."\n";
						$this->m_item_count++;
					}
				}
				else if(!$this->inExcludes($file) && ($this->m_item_count < $this->m_start || $this->m_item_count >= $this->m_end))
				{
					$this->m_item_count++;
					//comment($file." ".$this->m_item_count);
				}
			}
		}
		else
		{
			?>
			<div style="width:1000px;text-align:center;color:#f44">Permission Denied</div>
			<?php
		}
	}	
	public function pageNavigation()
	{
		if(isset($this->celldata['path']))
		{
			if(hasIgnore(dirname($this->celldata['path']))){
				$this->m_parent_ignores = array_merge($this->m_parent_ignores, getIgnores(dirname($this->celldata['path'])));
			}
			// check if parent dir has .logo file
			$listofdirs = array();
			if(hasLogo(dirname($this->celldata['path'])))
			{
				$listofdirs = getFilesFromLogo(dirname($this->celldata['path']));
			}
			if(count($listofdirs) == 0)
			{
				$dirlist = scandir( $_SERVER['DOCUMENT_ROOT'].'/'.dirname($this->celldata['path']) );
				for($i=0; $i<count($dirlist); $i++)
				{
					if(is_dir( $_SERVER['DOCUMENT_ROOT'].'/'.dirname($this->celldata['path']).'/'.$dirlist[$i] ) &&
						!in_array($dirlist[$i], $this->m_parent_ignores) && !in_array($dirlist[$i], $listofdirs))
					{
						$listofdirs[] = $dirlist[$i];
					}
				}
			}
			// previous - next dir
			$pos = array_search(basename($this->celldata['path']),$listofdirs);
			$before = ""; 
			$beforestr = "";
			if($pos > 0) 
			{
				$before = $listofdirs[$pos-1];
				$beforestr = "[".$before."]";
			} 
			if(param('up') != NULL)
			{
				$up = param('up');
			}
			else
			{
				$up = dirname(param('path'));
			}
			$parent = dirname(param('path'));
			$after = ""; 
			$afterstr = "";
			if($pos < (count($listofdirs) - 1)) 
			{ 
				$after = $listofdirs[$pos + 1]; 
				$afterstr = "[".$after."]";
			} 
			// previous next page
			$prevnum = $nextnum = 0;
			$last = (int)($this->m_item_count / $this->Config['pagesize']) + 1;
			if($this->m_pagenum > 1)     {$prevnum = $this->m_pagenum - 1;$prevnumstr = "[".$prevnum."]";} else {$prevnumstr = "";}
			if($this->m_pagenum < $last) {$nextnum = $this->m_pagenum + 1;$nextnumstr = "[".$nextnum."]";} else {$nextnumstr = "";}

			if($last > 1) {$laststr='['.$last.']'; $firststr = "[1]";} else {$laststr = ""; $firststr = "";}
                        if($nextnum >= $last || $this->m_pagenum >= $last) {$laststr = "";}
                        if($prevnum <= 1) {$firststr = "";}
			?>
	<div id="pagenavigation">

	     <table><tr>
	       <td class="spacel"   >       </td>
	       <td class="prevdir"  ><a href="<?php echo PROGRAM; ?>?opt=<?php echo $this->options(1);        ?>&path=<?php echo preg_replace('#/+#','/',str_replace('%2F','/',urlencode($parent.'/'.$before))); ?>"><?php echo $beforestr;  ?></a></td>
	       <td class="firstpage"><a href="<?php echo PROGRAM; ?>?opt=<?php echo $this->options(1);        ?>&path=<?php echo preg_replace('#/+#','/',str_replace('%2F','/',urlencode($this->celldata['path'])));       ?>"><?php echo $firststr;   ?></a></td>
	       <td class="prevpage" ><a href="<?php echo PROGRAM; ?>?opt=<?php echo $this->options($prevnum); ?>&path=<?php echo preg_replace('#/+#','/',str_replace('%2F','/',urlencode($this->celldata['path'])));       ?>"><?php echo $prevnumstr; ?></a></td>
	       <td class="up"       ><a href="<?php echo PROGRAM; ?>?opt=<?php echo $this->options(1);        ?>&path=<?php echo preg_replace('#/+#','/',str_replace('%2F','/',urlencode($up)));                 ?>">[up]</a></td>
	       <td class="nextpage" ><a href="<?php echo PROGRAM; ?>?opt=<?php echo $this->options($nextnum); ?>&path=<?php echo preg_replace('#/+#','/',str_replace('%2F','/',urlencode($this->celldata['path'])));       ?>"><?php echo $nextnumstr; ?></a></td>
	       <td class="lastpage" ><a href="<?php echo PROGRAM; ?>?opt=<?php echo $this->options($last);    ?>&path=<?php echo preg_replace('#/+#','/',str_replace('%2F','/',urlencode($this->celldata['path'])));       ?>"><?php echo $laststr;    ?></a></td>
	       <td class="nextdir"  ><a href="<?php echo PROGRAM; ?>?opt=<?php echo $this->options(1);        ?>&path=<?php echo preg_replace('#/+#','/',str_replace('%2F','/',urlencode($parent.'/'.$after)));  ?>"><?php echo $afterstr;   ?></a></td>
	       <td class="spacer"   >       </td>
	     </tr></table>

	</div>
		<?php
		}
	}
	function wholePages()
	{
		$whole = false;
		//printf(" whole=%s",$whole?"True":"False");

		$plgnLdr = new PluginLoader();
		$plgnLdr->getPage($this->celldata['path']);
		$this->m_html .= $plgnLdr->html();
		//printf(" whole=%s",$whole?"True":"False");
		$whole = $plgnLdr->isWhole();
		//printf(" whole=%s",$whole?"True":"False");
		return $whole;
	}
}


if(param('PHPUNIT') != True)
{

	$G = new Gallery($stdIgnores, $Config, param('path'), param('opt'));


	//if(param('path') != NULL && strpos(param('path'), "alsvideo") !== false)
	//{
		//header("Location: http://skynet/cgi-bin/alsmpegs.py");
		//die();
	//}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/DTD/strict.dtd">
<html>
<?php require('head.php');?>
<body>
<?php if($Config['logon'] == True) { login_panel(); } ?>

	<form name="gallery" action="/<?php echo PROGRAM; ?>" method="get">

	<div id="title">

	    <?php echo title($G->getPath()); ?>

	</div>

<?php 
if(!$G->wholePages())
{
	$G->buildThumbs();
}
$G->pagebreakcomment();
$G->pageNavigation(); 

?>

<div id="thumbnails" align="center">
 <table  border=0 width="100%"><tr><td align="">
  <div class="gallery" align="center">
   <table id="thumbnailstable" style="<?php echo $G->getThumbWidth(); ?>" cellspacing=0 cellpadding=0 ><tr><td align="center">

<?php echo $G->getHtml(); ?>

    </td></tr>
   </table>
  </div>
 </table>
</div>
</body>
</html>

<?php
}	
?>
