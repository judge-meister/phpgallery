<?php

require_once( 'include_check.php' );
require_once( 'functions.php' );
require_once( 'pluginLoader.php' );


class WholePage implements iPlugin
{
	private $html = "";
	private $whole = false;
	
	public function isWhole()       { return $this->whole; }
	public function html()          { return $this->html; }
	public function isActive($path) { return (hasIndex($path)); }

	public function doPage($path)
	{
		$html='';
		$igallery = false;
		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/igallery.php'))
		{
			$igallery = file($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/igallery.php', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		}
		if($igallery == false && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/igallery.html'))
		{
			$igallery = file($_SERVER['DOCUMENT_ROOT'].'/'.$path.'/igallery.html', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		}
		if($igallery != false)
		{
			$display=false;
			for($i=0; $i<count($igallery); $i++)// line in page:
			{
				$line = $igallery[$i];
				if(strpos(strtolower($line),'<body') !== false) 
				{
					$line='';
					$display=true;
				}
				if(strpos(strtolower($line),'</body') !== false) //line.lower().find('</body')>-1:
				{	
					$line='';
					$display=false; 
				}
				if(preg_match('/[hH][Rr][Ee][Ff]=.*jpg/', strtolower($line)) !== False) //re.search('[hH][Rr][Ee][Ff]=.*jpg', line.lower()):
				{	
					$line=str_replace('href="', 'href="../'.$path.'/', $line);
					$line=str_replace('HREF="', 'href="../'.$path.'/', $line);
				}
				else if(strpos(strtolower($line),'href="') !== false) //line.lower().find('href="')>-1: # or line.find('HREF="')>-1 :
				{	
					$line=str_replace('href="', 'href="/'.$path.'/', $line);
					$line=str_replace('HREF="', 'HREF="/'.$path.'/', $line);
				}
				if(strpos(strtolower($line),'src="') !== false) //line.lower().find('src="')>-1: # or line.find('SRC="')>-1 :
				{	
					$line=str_replace('src="', 'src="/'.$path.'/', $line);
					$line=str_replace('SRC="', 'SRC="/'.$path.'/', $line);
				}
				if(strpos(strtolower($line),'bgcolor="') !== false) //line.lower().find('bgcolor="')>-1:
				{	
					$line=str_replace('bgcolor','bggcolor',$line);//line.lower().replace('bgcolor','bggcolor')
				}
				if(strpos(strtolower($line),'color="') !== false) //line.lower().find('color="')>-1:
				{	
					$line=str_replace('color=','colur=',$line);
				}
				if($display)
				{
					if(strlen($line) > 0)
					{    
						$html = $html.$line;
					}
				}
			}
		}
		$this->html = $html;
		$this->whole = true;
	}
}

?>