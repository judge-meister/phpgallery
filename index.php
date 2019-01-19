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

$cfg = Config::getInstance();
$cfg->set('logon',False);
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
	private $cfg = null;//Config::getInstance();
	
	public function getPath() { return $this->celldata['path']->str(); }
	public function getHtml() { return $this->m_html; }
	public function getThumbWidth() 
	{ 
		if($this->prevRowWidth > 0)
		{
			return "width:".($this->prevRowWidth + 5)."px";
		}
		else
		{ 
			return "width:".($this->rowWidth + 5)."px";
			return "";
		} 
	}
	
	public function __construct($stdIgnores, $screenWidth, $path, $opt)
	{
		$this->cfg = Config::getInstance();
		$this->cfg->set('screenWidth', $screenWidth);
		$this->celldata['path'] = new Path($path);

		if($opt)
		{
			$o = explode('_',$opt);
			$this->cfg->set('pagesize', (int)$o[1]);
			//Config::pagesize = (int)$o[1];
			$this->m_pagenum = (int)$o[0];
		}
		$this->m_start = ($this->m_pagenum-1) * $this->cfg->get('pagesize'); //$this->Config['pagesize'];
		$this->m_end = ($this->m_pagenum) * $this->cfg->get('pagesize'); //$this->Config['pagesize'];
		
		$this->m_ignores = $stdIgnores;
		$this->m_parent_ignores = $stdIgnores;
		$this->m_comments = array();
		$this->favourites = array();
		$this->bookmarks = array();
		$this->rowWidth = 0;
		$this->prevRowWidth = 0;
		if($this->celldata['path']->hasDebug())
		{
			//$this->Config['debug']=True;
			$this->cfg->set('debug', True);
			//Config::debug = True;
		}
		$this->debug = new DebugLogger(/*$this->Config*/);
	}
	public function setPageWidth($w)
	{
		if(($this->rowWidth + $w + 4) < ($this->cfg->get('screenWidth') - 50)) 
		//if(($this->rowWidth + $w + 2) < (Config::screenWidth - 50)) 
		{ 
			$this->rowWidth = $this->rowWidth + $w +4;
		}
		else //if($this->prevRowWidth == 0)
		{
			if($this->rowWidth > $this->prevRowWidth) 
			{
				$this->prevRowWidth = $this->rowWidth;
			}
			$this->rowWidth = 0;
		}
	}
	public function resetCellData()
	{
		$this->celldata = array('path'=>$this->celldata['path'],
					'dir'=>null,'width'=>0,'height'=>0,'img_ht'=>0,'opt'=>null,
					'caption'=>null,'thumb'=>null,'image'=>null,'overlay'=>null,
					'movieLen'=>0, 'title'=>null );
	}
	public function pagebreakcomment()
	{
		echo comment("pagesize=".$this->cfg->get('pagesize'))."\n";
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
			return $num.'_'.$this->cfg->get('pagesize');
			//return $num.'_'.Config::pagesize;
		}
		else
		{
			return $this->m_pagenum.'_'.$this->cfg->get('pagesize');
			//return $this->m_pagenum.'_'.Config::pagesize;
		}
	}
	public function span_logo() //.logo thumb
	{
		$this->celldata['opt'] = $this->options(1);
		$s = new SpanLogo($this->celldata);
		if($this->celldata['path']->hasRollovers())
		{
			$s->setRollover();
		}
		$this->setPageWidth($s->getWidth());
		return $s->html();
	}

	public function span_logo_movie() // movie thumb
	{
		$s = new SpanLogoMovie($this->celldata);
		/*if($this->celldata['path']->hasRollovers())
		{
			$s->setRollover();
		}*/
		$this->setPageWidth($s->getWidth());
		return $s->html();
	}

	public function span_photo() // photo thumbs
	{
		$s = new SpanPhoto($this->celldata);
		$this->setPageWidth($s->getWidth());
		return $s->html();
	}

	public function span_dir() //dir name no thumbs
	{
		$this->celldata['width'] = $this->cfg->get('cell_wt');//132;
		$img_url = ""; //BORDER_ONLY;//FILE_FOLDER;
		if (in_array($this->celldata['dir'], $this->favourites))
		{
			$img_url = "";//FAV_FOLDER;
		}
		$s = new SpanDir($this->celldata, $img_url);
		$this->setPageWidth($s->getWidth());
		return $s->html();
	}
	
	public function span_icon($image,$caption) // photo thumbs
	{
		$this->celldata['caption'] = $caption; // $file or $dir
		$this->celldata['image'] = $image;
		$this->celldata['opt'] = $this->options(1);
		$s = new SpanIcon($this->celldata);
		$this->setPageWidth($s->getWidth());
		return $s->html();
	}

	public function normalizeThmSize($getsizes=true)
	{
		if($getsizes == true) 
		{ 
			list($this->celldata['width'], $this->celldata['height'], $type, $attr) = $this->celldata['path']->getImgSize($this->celldata['thumb']);
			$this->celldata['img_ht'] = $this->celldata['height'];
		}
		if((int)$this->celldata['height'] > $this->cfg->get('cell_ht')) 
		{ 
			$this->celldata['width'] = (int)((float)$this->celldata['width'] / ((float)$this->celldata['height'] / (float)$this->cfg->get('cell_ht'))); 
			$this->celldata['height'] = $this->cfg->get('cell_ht'); 
			//$this->celldata['img_ht'] = $this->celldata['height'];
		}
		if((int)$this->celldata['width'] > $this->cfg->get('maxPageWt'))
		{
			$this->celldata['width'] = $this->cfg->get('maxPageWt'); 
			$this->celldata['height'] = (int)((float)$this->celldata['height'] / ((float)$this->celldata['width'] / (float)$this->cfg->get('maxPageWt'))); 
			//$this->celldata['img_ht'] = $this->celldata['height'];
		}
	}
	private function readBookmarks()
	{
		foreach($this->celldata['path']->openLogo() as $line)
		{
			if(strpos($line,',') !== False && substr( $line, 0, 1 ) != "#") 
			{
				//echo "<p>".$lines[$i];
				$pieces = explode(",", $line);
				if(!in_array($pieces[0], $this->m_ignores))
				{
					if($this->celldata['path']->fileExists($pieces[0]))
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
		foreach($this->celldata['path']->openFavourites() as $line)//for($i=0;$i<count($lines);$i++)
		{
			if(substr( $line, 0, 1 ) != "#") 
			{
				//echo "<p>".$lines[$i];
				//$pieces = explode(",", $line);
				if(!in_array($line, $this->m_ignores))
				{
					if($this->celldata['path']->fileExists($line))
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
		foreach($this->celldata['path']->openLogo() as $line)//for($i=0;$i<count($lines);$i++)
		{
			$this->resetCellData();
			if(strpos($line,',') !== False && substr( $line, 0, 1 ) != "#") 
			{
				if($this->m_item_count >= $this->m_start && $this->m_item_count < $this->m_end)
				{
					//echo "<p>".$lines[$i];
					// .logo [(0 => path), (1 => image), (2 => ?), (3 => dimensions)]
					$pieces = explode(",", $line);
					if(!in_array($pieces[0], $this->m_ignores))
					{
						if($this->celldata['path']->fileExists($pieces[0]))
						{
							$this->celldata['thumb'] = $pieces[1];
							if(count($pieces) == 4 && strpos($pieces[3],'x') !== false)
							{
								list($this->celldata['width'], $this->celldata['height']) = explode("x",$pieces[3]);
							}
							else
							{
								list($this->celldata['width'], $this->celldata['height'], $type, $attr) = $this->celldata['path']->getImgSize($this->celldata['thumb']);
							}
							//$this->celldata['img_ht'] = $this->celldata['height'];
							$this->normalizeThmSize(false);
							$this->celldata['dir'] = $pieces[0];
							if(file_exists($_SERVER['DOCUMENT_ROOT'].$this->celldata['path']->str().'/'.$pieces[0]))
							{
								if(0 !== strcmp($pieces[1],""))
								{
									$this->celldata['caption'] = str_replace("_"," ",$pieces[0]);
									$this->m_html .= $this->span_logo()."\n";
									$this->m_item_count++;
								}
								else
								{
									if(hasTitle($this->celldata['path']->str().'/'.$pieces[0]))
									{
										$this->celldata['title']=title($this->celldata['path']->str().'/'.$pieces[0]);
									}
									else
									{
										$this->celldata['title']=$pieces[0];
									}
									$this->celldata['dir'] = $pieces[0];
									$this->m_html .= $this->span_dir()."\n";
									$this->m_item_count++;
									$this->celldata['dir'] = "";
								}
							}
							else if(file_exists($_SERVER['DOCUMENT_ROOT'].$pieces[0]))
							{
								$this->celldata['caption'] = str_replace("_"," ",basename($pieces[0]));
								$this->celldata['movieLen'] = $this->movieLength(basename($pieces[0]));
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
		if($this->celldata['path']->fileExists('/.pics/'.$file))
		{
			$this->celldata['thumb'] = '.pics/'.$file;
		}
		else
		{
			$this->celldata['thumb'] = $file;
		}
		$this->m_html .= "<!-- doImage -->";
		$this->normalizeThmSize();
		$this->celldata['thumb'] = $this->celldata['path']->str().'/'.$this->celldata['dir'].'/'.$this->celldata['thumb'];
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
		if($this->celldata['path']->fileExists($thm))
		{
			$this->celldata['thumb'] = $thm;
			$this->normalizeThmSize();
		}
		else if($this->celldata['path']->fileExists('/.pics/'.$thm))
		{
			$this->celldata['thumb'] = '.pics/'.$thm;
			$this->normalizeThmSize();
		}
		else if($this->celldata['path']->fileExists($tbn))
		{
			$this->celldata['thumb'] = $tbn;
			$this->normalizeThmSize();
		}
		else if($this->celldata['path']->fileExists('/.pics/'.$tbn))
		{
			$this->celldata['thumb'] = '.pics/'.$tbn;
			$this->normalizeThmSize();
		}
		else
		{
			list($this->celldata['thumb'],$this->celldata['width'],$this->celldata['height']) = $mediaTypes['movie']['thm'];
		}
		$this->m_html .= "\n<!-- doMovie -->";
		$this->celldata['dir'] = $this->celldata['path']->str().'/'.$file;

		$this->celldata['image'] = $file;
		$this->celldata['caption'] = $file;
		$this->celldata['movieLen'] = $this->movieLength($file);
		$this->m_html .= $this->span_logo_movie();
		$this->m_item_count++;
	}
	function movieLength($file)
	{
		$min = $secs = '';
		$this->debug->display("[".$file."]");
		if (in_array($file, array_keys($this->m_comments)))
		{
			$this->debug->display($file);
			$length = $this->m_comments[$file];
			$min = sprintf("%d",(int)($length/60.0));
			$secs = sprintf("%02d",(int)($length-($min*60)));
		}
		return array($min, $secs);
	}
	function inExcludes($file)
	{
		//$this->debug->display("called inExcludes(".$file.")<br>");
		//$this->debug->display(var_dump($this->m_ignores));
		//$this->debug->display(var_dump($this->m_logofiles));
		if(in_array(basename($file), $this->m_ignores) || in_array(basename($file), $this->m_logofiles))
		{
			$c = count($this->m_ignores) + count($this->m_logofiles);
			//$this->debug->display("true inExcludes ".$c."<br>");
			return true;
		}
		else
		{
			 if(substr(basename($file),0,1) == ".") { return true; }
			 if(substr(basename($file),0,3) == ":2e") { return true; }
			 if(getExt($file) == ".$$$") { return true; }
		}
		$c = count($this->m_ignores) + count($this->m_logofiles);
		//$this->debug->display("false inExcludes ".$c."<br>");
		return false;
	}
	
	public function buildThumbs()
	{
		global $mediaTypes;
		$this->celldata['dir'] = "";
		if($this->celldata['path']->hasIgnore())
		{
			$this->m_ignores = array_merge($this->m_ignores, getIgnores($this->celldata['path']->str()));
		}
		if($this->celldata['path']->hasCalendar() && file_exists('calendar.php'))
		{
			include('calendar.php');
			list($yrs,$html) = allYears($_SERVER['DOCUMENT_ROOT'].'/'.$this->celldata['path']->str());
			$this->m_html .= $html;
			$this->m_ignores = array_merge($this->m_ignores, $yrs);
		}
		if($this->celldata['path']->hasComments())
		{
			include($_SERVER['DOCUMENT_ROOT'].$this->celldata['path']->str().'/comments.php');
			$this->m_comments = getComments();
		}
		if($this->celldata['path']->hasBookmarks())
		{
			$this->readBookmarks();
		}
		if($this->celldata['path']->hasFavourites())
		{
			$this->readFavourites();
		}
		if($this->celldata['path']->hasLogo()) // .logo
		{
			$this->doLogo();
		}
		$files = scandir($_SERVER['DOCUMENT_ROOT'].$this->celldata['path']->str());
		if($files != false)
		{
			if($this->celldata['path']->hasReverse())
			{
				$files = array_reverse($files);
			}
			foreach($files as $file)//for($i = 0; $i < count($files); $i++)
			{
				//$this->debug->display("[ Filename ".$file." ]");
				//$this->debug->display("<br>buildthumbs [".$file."] ");
				$this->resetCellData();
				if(!$this->inExcludes($file) && ($this->m_item_count >= $this->m_start && $this->m_item_count < $this->m_end))
				{
					if(hasTitle($this->celldata['path']->str().'/'.$file))
					{
						$this->celldata['title']=title($this->celldata['path']->str().'/'.$file);
					}
					else
					{
						$this->celldata['title']=$file;
					}
					if(is_dir($_SERVER['DOCUMENT_ROOT'].$this->celldata['path']->str().'/'.$file)) // dir no thumb
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
					//$this->debug->display($file." included but not displayed on this page.");
				}
				else
				{
					//printf(" Excluded ");
					//$this->debug->display($file." Excluded <br>");
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
		if(null !== $this->celldata['path']->str())
		{
			if(hasIgnore(dirname($this->celldata['path']->str()))){
				$this->m_parent_ignores = array_merge($this->m_parent_ignores, getIgnores(dirname($this->celldata['path']->str())));
			}
			// check if parent dir has .logo file
			$listofdirs = array();
			if(hasLogo(dirname($this->celldata['path']->str())))
			{
				$listofdirs = getFilesFromLogo(dirname($this->celldata['path']->str()));
			}
			//if(count($listofdirs) == 0)
			//{
				$dirlist = scandir( $_SERVER['DOCUMENT_ROOT'].'/'.dirname($this->celldata['path']->str()) );
				for($i=0; $i<count($dirlist); $i++)
				{
					if(is_dir( $_SERVER['DOCUMENT_ROOT'].'/'.dirname($this->celldata['path']->str()).'/'.$dirlist[$i] ) &&
						!in_array($dirlist[$i], $this->m_parent_ignores) && !in_array($dirlist[$i], $listofdirs))
					{
						$listofdirs[] = $dirlist[$i];
					}
				}
			//}
			// previous - next dir
			$pos = array_search(basename($this->celldata['path']->str()),$listofdirs);
			$before = ""; 
			$beforestr = "";
			if($pos > 0) 
			{
				$before = $listofdirs[$pos-1];
				$beforestr = "[".title2(dirname($this->celldata['path']->str()).'/'.$before)."]";
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
				$afterstr = "[".title2(dirname($this->celldata['path']->str()).'/'.$after)."]";
			} 
			// previous next page
			$prevnum = $nextnum = 0;
			$last = (int)(($this->m_item_count-1) / $this->cfg->get('pagesize')) + 1;
			if($this->m_pagenum > 1)      {$prevnum = $this->m_pagenum - 1;$prevnumstr = "[".$prevnum."]";} else {$prevnumstr = "";}
			if($this->m_pagenum < $last) {$nextnum = $this->m_pagenum + 1;$nextnumstr = "[".$nextnum."]";} else {$nextnumstr = "";}

			if($last > 1) {$laststr='['.$last.']'; $firststr = "[1]";} else {$laststr = ""; $firststr = "";}
			if($nextnum >= $last || $this->m_pagenum >= $last) {$laststr = "";}
			if($prevnum <= 1) {$firststr = "";}
			?>
	<div id="pagenavigation">

	     <table><tr>
	       <td class="spacel"   >       </td>
	       <td class="prevdir"  ><a href="<?php echo PROGRAM; ?>?opt=<?php echo $this->options(1);        ?>&path=<?php echo preg_replace('#/+#','/',str_replace('%2F','/',urlencode($parent.'/'.$before))); ?>"><?php echo $beforestr;  ?></a></td>
	       <td class="firstpage"><a href="<?php echo PROGRAM; ?>?opt=<?php echo $this->options(1);        ?>&path=<?php echo preg_replace('#/+#','/',str_replace('%2F','/',urlencode($this->celldata['path']->str())));       ?>"><?php echo $firststr;   ?></a></td>
	       <td class="prevpage" ><a href="<?php echo PROGRAM; ?>?opt=<?php echo $this->options($prevnum); ?>&path=<?php echo preg_replace('#/+#','/',str_replace('%2F','/',urlencode($this->celldata['path']->str())));       ?>"><?php echo $prevnumstr; ?></a></td>
	       <td class="up"       ><a href="<?php echo PROGRAM; ?>?opt=<?php echo $this->options(1);        ?>&path=<?php echo preg_replace('#/+#','/',str_replace('%2F','/',urlencode($up)));                 ?>">[up]</a></td>
	       <td class="nextpage" ><a href="<?php echo PROGRAM; ?>?opt=<?php echo $this->options($nextnum); ?>&path=<?php echo preg_replace('#/+#','/',str_replace('%2F','/',urlencode($this->celldata['path']->str())));       ?>"><?php echo $nextnumstr; ?></a></td>
	       <td class="lastpage" ><a href="<?php echo PROGRAM; ?>?opt=<?php echo $this->options($last);    ?>&path=<?php echo preg_replace('#/+#','/',str_replace('%2F','/',urlencode($this->celldata['path']->str())));       ?>"><?php echo $laststr;    ?></a></td>
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
		$plgnLdr->getPage($this->celldata['path']->str());
		$this->m_html .= $plgnLdr->html();
		//printf(" whole=%s",$whole?"True":"False");
		$whole = $plgnLdr->isWhole();
		//printf(" whole=%s",$whole?"True":"False");
		return $whole;
	}
}

// ----------------------------------- //
// ------- S T A R T   H E R E ------- //
// ----------------------------------- //

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
	//$Config['screenWidth'] = getBrowserWidth();
	//Config::screenWidth = getBrowserWidth();
	$G = new Gallery($stdIgnores, getBrowserWidth(), param('path'), param('opt'));
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/DTD/strict.dtd">
<html>
<?php require('head.php'); ?>
<body>
<?php if($cfg->get('logon') == True) { login_panel(); } ?>

	<form name="gallery" action="/<?php echo PROGRAM; ?>" method="get">
	</form>
	
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

<div style="width:100%;height:200px;" id="thumbnails">
 <div style="margin: 0 auto; <?php echo $G->getThumbWidth(); ?>">
  <?php echo $G->getHtml(); ?>
 </div>
</div>

<!-- div id="thumbnails" align="center" width="100%">
 <table  border=0 width="100%">
  <tr>
   <td align="">
    <div class="gallery" align="center">
     <table id="thumbnailstable" style="<!-- ? php echo $G->getThumbWidth(); ?>" cellspacing=0 cellpadding=0 >
      <tr>
       <td align="center" -->

<!-- ?php echo $G->getHtml(); ? -->

       <!-- /td>
      </tr>
     </table>
    </div>
   </td>
  </tr>
 </table -->

<script type="text/javascript" src="/js/lazy.js"></script>
<div id="appModeNote" style="display:none;">
	<em><a href="">Refresh!</a></em>
</div>

</body>
</html>

<?php
}	
?>
