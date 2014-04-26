<?php

require_once( gethostname().'/config.php' );
define('PHPTHUMB_CFG', $_SERVER['DOCUMENT_ROOT'].PHPTHUMB.'phpThumb.config.php');

class iPhpThumb
{
	private $phpThumbs = False;
	private $thmHeight = 120;
	private $quality = 85;
	
	public function __construct($span)
	{
		$this->span = $span;
		if(defined('PHPTHUMB')) 
		{
			if(file_exists(PHPTHUMB_CFG)) 
			{
				require_once( PHPTHUMB_CFG );
				$this->phpThumbs = True;
			}
		}
	}
	public function isActive() { return $this->phpThumbs; }

	public function picUrl($cell, $pic)
	{
		$picurl = "";
		echo "<!-- ".$cell['img_ht']." -->";
		if((int)$cell['img_ht']>160 && $this->isActive()==True)
		{
			$query = 'src='.urlencode($pic).'&h='.$this->thmHeight.'&q='.$this->quality;
			$picurl = htmlspecialchars(phpThumbURL($query));//, 'phpThumb.php'));
			echo "<!-- ".$picurl." -->";
			return str_replace($_SERVER['DOCUMENT_ROOT'],"",$picurl);
		}
		else
		{
			echo "<!-- ".$cell['thumb']." -->";
			$this->picurl = $this->span->mkRawUrl(array($cell['thumb']));
			return $this->picurl;
		}
	}
}

?>
