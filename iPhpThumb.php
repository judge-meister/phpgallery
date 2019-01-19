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

    public function picurl2($img_ht, $thumb, $pic)
	{
		$cell = array('img_ht' => $img_ht, 'thumb' => $thumb);
		$picurl = "";
		//echo "<!-- iPhpThumb:picurl2: ".$cell['img_ht']." -->\n";
		if((int)$cell['img_ht']>160 && $this->isActive()==True)
		{
			$query = 'src='.urlencode($pic).'&h='.$this->thmHeight.'&q='.$this->quality;
			$picurl = '/phpthumb/'.htmlspecialchars(phpThumbURL($query));//, 'phpThumb.php'));
			//echo "<!-- iPhpThumb:picurl2: ".$picurl." -->";
			return str_replace($_SERVER['DOCUMENT_ROOT'],"",$picurl);
		}
		else
		{
			//echo "<!-- iPhpThumb:picurl2: ".$cell['thumb']." -->\n";
			//$this->picurl = $this->span->mkRawUrl(array($cell['thumb']));
			$this->picurl = $cell['thumb']; //mkRawUrl(array($cell['thumb']));
			return $this->picurl;
		}
	}
	public function picUrl($cell, $pic) // img_ht thumb
	{
		$picurl = "";
		//echo "<!-- iPhpThumb:picurl: ".$cell['img_ht']." -->\n";
		if((int)$cell['img_ht']>160 && $this->isActive()==True)
		{
			$query = 'src='.urlencode($pic).'&h='.$this->thmHeight.'&q='.$this->quality;
			$picurl = '/phpthumb/'.htmlspecialchars(phpThumbURL($query));//, 'phpThumb.php'));
			//echo "<!-- iPhpThumb: ".$picurl." -->";
			return str_replace($_SERVER['DOCUMENT_ROOT'],"",$picurl);
		}
		else
		{
			//echo "<!-- iPhpThumb:picurl: ".$cell['thumb']." -->\n";
			$this->picurl = $this->span->mkRawUrl(array($cell['thumb']));
			//$this->picurl = mkRawUrl(array($cell['thumb']));
			return $this->picurl;
		}
	}
}

?>
