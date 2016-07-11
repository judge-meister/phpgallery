<?php 

require_once( 'include_check.php' );
require_once( 'functions.php' );
require_once( 'pluginLoader.php' );


class AlsAngelsDB implements iPlugin
{
	/* Database config */
	private $db_host		= 'localhost';
	private $db_user		= 'judge';
	private $db_pass		= 'r0adster';
	private $db_database		= 'alsgallery'; 

	private $webbase     = 'secret/sdc1/ALSScans';
	private $webroot     = '../html';
	private $website     = 'www.alsangels.com';
	private $photosdir   = 'members';
	private $modelthumbs = 'freepics';
	
	/* returned by plugin interface methods */ 
	private $html = "";
	private $whole = false;
	
	/* End config */
	function __construct() 
	{
		if(gethostname() == "skynet")
		{
			$this->sqlConnect();
		}
	}
	
	private function sqlConnect()
	{
		$link = mysql_connect($this->db_host,$this->db_user,$this->db_pass) or die('Unable to establish a DB connection');

		mysql_select_db($this->db_database,$link);
		mysql_query("SET names UTF8");
	}

	private function sqlquery($sql)
	{
		$result = mysql_query($sql); 
		while(($resultArray[] = mysql_fetch_assoc($result)) || array_pop($resultArray)); 
		return $resultArray;
	}

	private function AlsAngelsModel()
	{
		$html="";
		$model=param('model');
		$sql="SELECT DISTINCT modelid, modelname, modelcode, website, hasVideos, videoid, videothumb, thumbnail, height, weight, age, vitals, occupation, ";
		$sql.="hobbies, music, fantasy, blurb FROM modeldetails where modelcode = \"".$model."\" and website = \"www.alsangels.com\";";
		$res = $this->sqlquery($sql);
		if(is_array($res) and count($res)>0)
		{
			$details = $res[0];
			$sql="SELECT DISTINCT pset, thumbnail, piccount, description FROM photosets where modelid = ".$details['modelid']." and website REGEXP 'angels';";
			$psets = $this->sqlquery($sql);
			if(is_array($psets) and count($res)>0)
			{
				$html = '';
				$html .= '  <div style="text-align:left; border:0px; width:1050px;">';
				$html .= '   <table style="width:200; border:0px solid #555;margin:0px;padding:0px;">';
				$html .= '    <tr style:"border:0px;margin:0px;padding:0px;">';
				$html .= '     <td valign="top" style="width:200px;border: 0px;margin:0px;padding:0px;">';
				$html .= '      <span style="margin:0px 1px 0px 0px;"><img style="width:200px; height:290px;" src="/'.$this->webbase.'/'.$this->website.'/'.$details['thumbnail'].'">';
				$html .= '      <p style="font-size:16px;font-weight:bold;text-align:left">'.$details['modelname'];
				$html .= '      <p style="text-align:left">';
				if ($details['age'] != "") { $html .= '      <b>Age</b>: '.$details['age'].'<br>';}
				if ($details['height'] != "") { $html .= '<b>Height</b>: '.$details['height'].'<br>';}
				if ($details['weight'] != "") { $html .= '<b>Weight</b>: '.$details['weight'].'<br>';}
				if ($details['vitals'] != "") { $html .= '<b>Measurements</b>: '.$details['vitals'].'<br>';}
				if ($details['occupation'] != "") { $html .= '<b>Occupation</b>: '.$details['occupation'].'<br>';}
				if ($details['hobbies'] != "") { $html .= '<b>Hobbies</b>: '.$details['hobbies'].'<br>';}
				if ($details['music'] != "") { $html .= '<b>Favourite Music</b>: '.$details['music'].'<br>';}
				if ($details['fantasy'] != "") { $html .= '<b>Favourite Fantasy</b>: '.$details['fantasy'].'<br>';}

				$html .= '      </td>';
				$html .= '      <td valign="top" style="border: 0px solid #505; margin:0px; padding:0px;">';
				$html .= '    <div style="width:820px;">';

				$ihtml='';
				if( $details['hasVideos'] == "1")
				{
					$ihtml=  '       <a href="/cgi-bin/alsmpegs.py?m='.$details['videoid'].'&f=angel" style="font-size:10pt;">';
					$ihtml.= '       <img src="/'.$this->webbase.'/www.alsvideo.com/angelimg/'.$details['videothumb'].'" style="width:120px; height:90px;float:left;margin-right:5px;"></a>';
				}
				$thtml = '<p style="text-align:left;padding-left:0px">'.$details['blurb'].'</p>';
				$html .= '<span style="width: 805px;padding-bottom:5px;">'.$ihtml.' '.$thtml.'</a></span>';
    
				foreach($psets as $row)
				{
					$pset=$row['pset'];
					$thumbnail=$row['thumbnail'];
					$piccount=$row['piccount'];
					$description=$row['description'];
					$size=array();
					list($size['w'], $size['h'], $type, $attr) = getimagesize($_SERVER['DOCUMENT_ROOT'].'/'.$this->webbase.'/'.$this->website.'/'.$this->modelthumbs.'/'.$thumbnail);
					$ihtml='<a href="'.PROGRAM.'?opt=1_100_f&path='.$this->webbase.'/'.$this->website.'/'.$this->photosdir.'/'.$pset.'">'; 
					$ihtml.='<img src="/'.$this->webbase.'/'.$this->website.'/'.$this->modelthumbs.'/'.$thumbnail.'" style="height: 94px width: 64px;float:left;margin-right:5px" >'; 
					$thtml = '<p style="text-align:left;">'.$description.'<br><br>('.$piccount.' pics)</p>';
					$h=94;
					$w=396;
					$html .= '      <span style="height: '.$h.'px;width: '.$w.'px;padding-bottom:5px">'.$ihtml.'</a> '.$thtml.'</span>';
				}
			}
		}
		return $html;
	}
	// interface methods
	public function doPage($path)
	{
		if($this->isActive($path))
		{
			$_POST['model']=basename($path);
			$this->html = $this->AlsAngelsModel();
			$this->whole = true;
		}
	}
	public function isWhole()       { return $this->whole; }
	public function html()	  { return $this->html; }
	public function isActive($path) 
	{ 
		return (hasModelDB($path) && strpos($path, 'www.alsangels.com/members') !== false && gethostname() == "skynet" ); 
	}
}


?>
