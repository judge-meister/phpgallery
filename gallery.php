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
//   - ignore now overridden with a whitelist
// - handle .title .calendar files
//   - .title done. .calendar works all except back/up function
// - more media types - including movie thumbs
//   - movie thumbs handled
// - special pages like wiredpussy, alsscan, alsangel, hegreart, femjoy
//   - special flat html pages are mostly working
//   - still need to handle database pages
// - pull down menu navigation - [low priority]
// 
// need to analyse original gallery.py code for more features - can't find it, so thats a problem.
// --------------------------------------------------------------------
//
// I M P R O V E M E N T S - not necessarily achieved
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
// N E W   I M P R O V E M E N T S
//
// - make pageNavigation a class. 
// - collect info first before trying to create thumbnail html code 
//   that way html can be mode abstracted from the gallery class
//
// --------------------------------------------------------------------

if(!defined('INCLUDE_CHECK')) { define('INCLUDE_CHECK',true); }

require_once( 'functions.php' );
require_once( 'Span.class.php' );
require_once( 'pluginLoader.php' );

$cfg = Config::getInstance();
$cfg->set('logon',False);

class Gallery
{
	private $m_ignores = array();
	private $m_parent_ignores = array();
	private $m_logofiles = array();
	private $m_du = array();
	
	private $m_start = 0;
	private $m_end = 0;
	private $m_item_count = 0;
	private $m_pagenum = 1;
	private $m_pageNavHtml = "";
	
	private $celldata = array('path'=>null,'dir'=>null,'width'=>0,'height'=>0,'img_ht'=>0,'opt'=>null,
				  'caption'=>null,'thumb'=>null,'image'=>null,'overlay'=>null,'movieLen'=>0);
	private $celldata_default;
	private $ordered_file_list = array();
	private $m_html = "";
	private $cfg = null;//Config::getInstance();
	
	public function getHtml() // RETURNS HTML
	{ 
		return $this->m_html; 
	}
	public function getThumbWidth($wholePage) // THIS IS HTML
	{ 
		$out = '';
		if($this->prevRowWidth > 0)
		{
			$out .= "width:".($this->prevRowWidth + 5)."px";
		}
		else
		{ 
			$out .= "width:".($this->rowWidth + 5)."px";
		}
		if($wholePage)
		{
			$out = "";
		}
		return $out;
	}
	
	public function __construct($stdIgnores, $screenWidth, $path, $opt, $server=null)
	{
		if ($server != null)
		{ 
			global $_SERVER;
			$_SERVER = $server;
		}
		//printf($screenWidth." ".$path." ".$opt." ".$_SERVER['DOCUMENT_ROOT']."\n");
		$this->init($stdIgnores, $screenWidth, $path, $opt);
	}
	private function init($stdIgnores, $screenWidth, $path, $opt) // PART OF CONSTRUCTOR
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
	private function setPageWidth($w) // RELATED TO HTML
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
	private function resetCellData() // LOADING DATA
	{
		$this->celldata = array('path'=>$this->celldata['path'],
					'dir'=>null,'width'=>0,'height'=>0,'img_ht'=>0,'opt'=>null,
					'caption'=>null,'thumb'=>null,'image'=>null,'overlay'=>null,
					'movieLen'=>0, 'title'=>null );
	}
	public function pagebreakcomment() // THIS IS HTML
	{
		//echo comment("pagesize=".$this->cfg->get('pagesize'))."\n";
		//echo comment("pagenum=".$this->m_pagenum)."\n";
		//echo comment("start=".$this->m_start)."\n";
		//echo comment("end=".$this->m_end)."\n";
		//echo comment("item_count=".$this->m_item_count)."\n";
		if(count($this->m_logofiles) > 0)
		{
			echo "<!-- m_logofiles ".count($this->m_logofiles)." ";
			//var_dump($this->m_logofiles);
			echo " -->";
		}
	}
	
	private function options($num=0) // COULD BE A CLASS
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
	private function span_logo() //.logo thumb // RETURNING HTML // FACTORY
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

	private function span_logo_movie() // movie thumb // RETURNING HTML // FACTORY
	{
		$s = new SpanLogoMovie($this->celldata);
		/*if($this->celldata['path']->hasRollovers())
		{
			$s->setRollover();
		}*/
		$this->setPageWidth($s->getWidth());
		return $s->html();
	}

	private function span_photo() // photo thumbs // RETURNING HTML // FACTORY
	{
		$s = new SpanPhoto($this->celldata);
		$this->setPageWidth($s->getWidth());
		return $s->html();
	}

	private function span_dir() //dir name no thumbs // RETURNING HTML // FACTORY
	{
		$this->celldata['width'] = $this->cfg->get('cell_wt');//132;
		$img_url = ""; //BORDER_ONLY;//FILE_FOLDER;
		if (in_array($this->celldata['dir'], $this->favourites))
		{
			$img_url = "";//FAV_FOLDER;
		}
		if (in_array($this->celldata['dir'], array_keys($this->m_du)))
		{
			$this->celldata['du'] = $this->m_du[$this->celldata['dir']];
		}
		else
		{
			$this->celldata['du'] = null;
		}
		$s = new SpanDir($this->celldata, $img_url);
		$this->setPageWidth($s->getWidth());
		return $s->html();
	}
	
	private function span_icon($image,$caption) // photo thumbs // RETURNING HTML // FACTORY
	{
		$this->celldata['caption'] = $caption; // $file or $dir
		$this->celldata['image'] = $image;
		$this->celldata['opt'] = $this->options(1);
		$s = new SpanIcon($this->celldata);
		$this->setPageWidth($s->getWidth());
		return $s->html();
	}

	private function normalizeThmSize($getsizes=true) // HEIGHTS AND WIDTHS ARE HTML BUT COULD ALSO BE LOADING INFO
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
	
	//////////////////////////////////////////////////////////////////////////////
	// JUST PATH - START
	public function getPath() // ACCESSOR
	{ 
		return $this->celldata['path']->str(); 
	}
	/* DEBUG function to display the current thumbs on the page in order */
	public function display_ordered_file_list() // DEBUG
	{
		foreach($this->ordered_file_list as $file)
		{
			print $file."\n";
		}
	}
	public function getPageNavHtml() // PUBLISH HTML // ACCESSOR?
	{
		return $this->m_pageNavHtml;
	}
	/*
	* Displays a table containing links to previous and next directory and page and parent
	*
	* values displayed
	* - prev and next dir (contained in correctly ordered list of the parent dir)
	*   - parent path plus names
	* - parent directory
	*   - needs parent path
	* - page numbers (prev, next) of current directory if more than 'n' items
	*   - needs current path
	*
	* data requirements :
	* - $this->celldata['path'] - current directory
	* - $this->ordered_file_list - this needs to contain correctly ordered list of items for parent
	* - $this->m_item_count - count of items in current directory
	* - $this->m_pagenum - the current page num from the options
	* - $this->option() - provides new options to add to new urls
	* - PROGRAM
	* - param('path')
	*/
	public function pageNavigation($current="") // THIS SHOULD BE A NEW CLASS
	{
		if(null !== $this->celldata['path']->str() && $current != "")
		{
			$listofdirs = array();
			foreach($this->ordered_file_list as $file)
			{
				//print $_SERVER['DOCUMENT_ROOT'].'/'.$this->celldata['path']->str().'/'.$file ."\n";
				if(is_dir( $_SERVER['DOCUMENT_ROOT'].'/'.$this->celldata['path']->str().'/'.$file ))// is a dir
				{
					//print $file."\n";
					array_push($listofdirs, $file);
				}
			}
			// previous - next dir
			$pos = array_search($current, $listofdirs);
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
			if($this->m_pagenum > 1)      {
				$prevnum = $this->m_pagenum - 1;
				$prevnumstr = "[".$prevnum."]";
			} else {
				$prevnumstr = "";
			}
			if($this->m_pagenum < $last) {
				$nextnum = $this->m_pagenum + 1;
				$nextnumstr = "[".$nextnum."]";
			} else {
				$nextnumstr = "";
			}

			if($last > 1) {
				$laststr='['.$last.']';
				$firststr = "[1]";
			} else {
				$laststr = "";
				$firststr = "";
			}
			if($nextnum >= $last || $this->m_pagenum >= $last) {
				$laststr = "";
			}
			if($prevnum <= 1) {
				$firststr = "";
			}
			
			// should really use HtmlTag classes for this

			$html = ' <div id="pagenavigation">'."\n";

		        $html .= '<table><tr>'."\n";
		        $html .= '<td class="spacel"   >       </td>'."\n";
		        $html .= '<td class="prevdir"  ><a href="'.PROGRAM.'?opt='.$this->options(1)       .'&path='.preg_replace('#/+#','/',str_replace('%2F','/',urlencode($parent.'/'.$before))).'">'.$beforestr.'</a></td>'."\n";
		        $html .= '<td class="firstpage"><a href="'.PROGRAM.'?opt='.$this->options(1)       .'&path='.preg_replace('#/+#','/',str_replace('%2F','/',urlencode($this->celldata['path']->str()))).'">'.$firststr.'</a></td>'."\n";
		        $html .= '<td class="prevpage" ><a href="'.PROGRAM.'?opt='.$this->options($prevnum).'&path='.preg_replace('#/+#','/',str_replace('%2F','/',urlencode($this->celldata['path']->str()))).'">'.$prevnumstr.'</a></td>'."\n";
		        $html .= '<td class="up"       ><a href="'.PROGRAM.'?opt='.$this->options(1)       .'&path='.preg_replace('#/+#','/',str_replace('%2F','/',urlencode($up))).'">[up]</a></td>'."\n";
		        $html .= '<td class="nextpage" ><a href="'.PROGRAM.'?opt='.$this->options($nextnum).'&path='.preg_replace('#/+#','/',str_replace('%2F','/',urlencode($this->celldata['path']->str()))).'">'.$nextnumstr.'</a></td>'."\n";
		        $html .= '<td class="lastpage" ><a href="'.PROGRAM.'?opt='.$this->options($last)   .'&path='.preg_replace('#/+#','/',str_replace('%2F','/',urlencode($this->celldata['path']->str()))).'">'.$laststr.'</a></td>'."\n";
		        $html .= '<td class="nextdir"  ><a href="'.PROGRAM.'?opt='.$this->options(1)       .'&path='.preg_replace('#/+#','/',str_replace('%2F','/',urlencode($parent.'/'.$after))).'">'.$afterstr.'</a></td>'."\n";
		        $html .= '<td class="spacer"   >       </td>'."\n";
		        $html .= '</tr></table>'."\n";

		        $html .= '</div>'."\n";
			$this->m_pageNavHtml = $html;
		}
		else
		{
			print "ERROR: current is empty.\n";
		}
	}
	// JUST PATH - END
	//////////////////////////////////////////////////////////////////////////////

	private function doLogo() // LOADING INFO BUT ALSO CREATING HTML
	{
		if($this->celldata['path']->hasLogo()) // .logo
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
										array_push($this->ordered_file_list, $pieces[0]);
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
										array_push($this->ordered_file_list, $pieces[0]);
										$this->m_item_count++;
										$this->celldata['dir'] = "";
									}
								}
								else if(file_exists($_SERVER['DOCUMENT_ROOT'].$pieces[0]))
								{
									$this->celldata['caption'] = str_replace("_"," ",basename($pieces[0]));
									$this->celldata['movieLen'] = $this->movieLength(basename($pieces[0]));
									$this->m_html .= $this->span_logo_movie()."\n";
									array_push($this->ordered_file_list, $pieces[0]);
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
	}
	private function doImage($file) // RETURNS HTML
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
		array_push($this->ordered_file_list, $file);
		$this->m_item_count++;
	}
	private function doMovie($file) // LOADING INFO BUT ALSO RETURNS HTML
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
		array_push($this->ordered_file_list, $file);
		$this->m_item_count++;
	}
	private function movieLength($file) // LOADING INFO
	{
		$min = $secs = '';
		//$this->debug->display("[".$file."]");
		if (in_array($file, array_keys($this->m_comments)))
		{
			//$this->debug->display($file);
			$length = $this->m_comments[$file];
			$min = sprintf("%d",(int)($length/60.0));
			$secs = sprintf("%02d",(int)($length-($min*60)));
		}
		return array($min, $secs);
	}
	private function inExcludes($file) // LOADING INFO - REALLY A UTILITY
	{
		global $stdIncludes;
		//$this->debug->display("called inExcludes(".$this->celldata['path']->str().'/'.$file.")<br>");
		//$this->debug->display(var_dump($this->m_ignores));
		//$this->debug->display(var_dump($this->m_logofiles));
		if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/'.$this->celldata['path']->str().'/'.$file) && !in_array(getExt($file), $stdIncludes))
		{
			//$this->debug->display("true not in stdIncludesi<br>");
			return true;
		}
		else if(in_array(basename($file), $this->m_ignores) || in_array(basename($file), $this->m_logofiles))
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
	
	/* if .pages file exists then read the images from the file and create full width images as anchors to floatbox gallery */
	public function kindgirls() // RETURNS HTML - NEARLY
	{
		$kd = False;
		if($this->celldata['path']->hasPages())
		{
			$SITE_PORT = $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'];
			$kd = True;
			$this->m_html .= "<div style=\"text-align:center\">";
			foreach($this->celldata['path']->openPages() as $line)
			{
				$this->m_html .= "<a href=\"http://".$SITE_PORT.$this->celldata['path']->str()."/".$line."\">";
				$this->m_html .= "<img src=\"http://".$SITE_PORT.$this->celldata['path']->str()."/".$line."\" style=\"width:100%;margin:3px;\">";
				$this->m_html .= "</a><br>";
			}
			$this->m_html .= "</div>";
		}
		return $kd;
	}

	/* build the thumbnails
	 * - remove ignored items
	 * - look for calendars, comments, bookmarks, favourites, folder disk sizes, .logo files
	*/
	public function buildThumbs() // MAIN ENTRY POINT AND CREATES HTML
	{
		global $mediaTypes;
		$this->celldata['dir'] = "";
		if($this->celldata['path']->hasIgnore())
		{
			$this->m_ignores = array_merge($this->m_ignores, getIgnores($this->celldata['path']->str()));
		}
		if($this->celldata['path']->hasCalendar() && file_exists('calendar.php'))
		{
			include_once('calendar.php');
			list($yrs,$html) = allYears($_SERVER['DOCUMENT_ROOT'].'/'.$this->celldata['path']->str());
			$this->m_html .= $html;
			$this->m_ignores = array_merge($this->m_ignores, $yrs);
		}
		if($this->celldata['path']->hasComments() && !function_exists("getComments"))
		{
			include_once($_SERVER['DOCUMENT_ROOT'].$this->celldata['path']->str().'/comments.php');
			$this->m_comments = getComments();
		}
		$this->bookmarks = readBookmarks($this->celldata['path'], $this->m_ignores);
		$this->favourites = readFavourites($this->celldata['path'], $this->m_ignores);
		$this->m_du = readDu($this->celldata['path']);
		$this->doLogo();

		// scan the directory for all the files using either descending modification time order or ascending alphanumeric order
		if($this->celldata['path']->hasLatest())
		{
			// get files folders in descending modification date/time order
			$files = myscandir($_SERVER['DOCUMENT_ROOT'].$this->celldata['path']->str(), '/.*/', 'mtime', 1);
		}
		else
		{
			// get files/folders in ascending alphabetic name order 
			$files = myscandir($_SERVER['DOCUMENT_ROOT'].$this->celldata['path']->str(), '/.*/', 'name', 0);
		}

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
				// exclude files already processed by the .logo and .ignore files as well as other standard excludes
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
						array_push($this->ordered_file_list, $file);
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
						array_push($this->ordered_file_list, $file);
						$this->m_item_count++;
					}
					// --- unknown file types ---
					else if(!isNonMedia($file))
					{
						list($this->celldata['thumb'],$this->celldata['width'],$this->celldata['height']) = $mediaTypes['misc']['thm'];
						$this->m_html .= $this->span_icon($file,$file)."\n";
						array_push($this->ordered_file_list, $file);
						$this->m_item_count++;
					}
				}
				else if(!$this->inExcludes($file) && ($this->m_item_count < $this->m_start || $this->m_item_count >= $this->m_end))
				{
					$this->m_item_count++;
					//comment($file." ".$this->m_item_count);
					//$this->debug->display($file." included but not displayed on this page.");
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
	public function wholePages() // RETURNS HTML
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

//
// FUNCTIONS REMOVED FROM GALLERY CLASS
//
function readBookmarks($path, $ignores) // LOADING INFO
{
	$bookmarks = array();
	if($path->hasBookmarks())
	{
		foreach($path->openLogo() as $line)
		{
			if(strpos($line,',') !== False && substr( $line, 0, 1 ) != "#") 
			{
				//echo "<p>".$lines[$i];
				$pieces = explode(",", $line);
				if(!in_array($pieces[0], $ignores))
				{
					if($path->fileExists($pieces[0]))
					{
						// add to bookmark array stored in this->
						$bookmarks[$pieces[0]]=$pieces[1];
					}
				}
			}
		}
	}
	return $bookmarks;
}
function readFavourites($path, $ignores) // LOADING INFO
{
	$favourites = array();
	if($path->hasFavourites())
	{
		foreach($path->openFavourites() as $line)//for($i=0;$i<count($lines);$i++)
		{
			if(substr( $line, 0, 1 ) != "#") 
			{
				//echo "<p>".$lines[$i];
				//$pieces = explode(",", $line);
				if(!in_array($line, $ignores))
				{
					if($path->fileExists($line))
					{
						// add to favourites array stored in this->
						$favourites[] = $line;
						//echo $line;
					}
				}
			}
		}
	}
	return $favourites;
}
function readDu($path) // LOADING INFO
{
	$du = array();
	if($path->hasDu())
	{
		foreach($path->openDu() as $line)
		{
			$pieces = explode("	", $line);
			$du[$pieces[1]] = $pieces[0];
		}
	}
	return $du;
}

