<?php 

require_once( 'include_check.php' );
require_once( 'functions.php' );
require_once( 'pluginLoader.php' );


class AlsScansDB
{
	/* Database config */
	private $db_host		= 'localhost';
	private $db_user		= 'judge';
	private $db_pass		= 'r0adster';
	private $db_database		= 'alsgallery'; 

        private $webbase     = 'secret/sdc1';
        private $webroot     = '../html';
        private $website     = 'www2.alsscan.com';
        private $photosdir   = 'members';
        private $modelthumbs = 'members';
	private $span_height = 138;
	
	/* returned by plugin interface methods */ 
	private $html = "";
	private $whole = false;
	
	/* End config */
	function __construct() 
	{
		$link = mysql_connect($this->db_host,$this->db_user,$this->db_pass) or die('Unable to establish a DB connection');

		mysql_select_db($this->db_database,$link);
		mysql_query("SET names UTF8");
	}
	
	public function sqlquery($sql)
	{
		$result = mysql_query($sql); 
		while(($resultArray[] = mysql_fetch_assoc($result)) || array_pop($resultArray)); 
		return $resultArray;
	}
	
	public function AlsScansModel()
	{
		$html="";
		$model=param('model');
		$sql="SELECT DISTINCT modelid, modelname, modelcode, website, hasVideos, videoid, videothumb, thumbnail, height, weight, age, vitals, occupation, ";
		$sql.='hobbies, music, fantasy, blurb FROM modeldetails where modelcode = "'.$model.'" and website = "www.alsscan.com";';
		$res = $this->sqlquery($sql);
		if(is_array($res) and count($res)>0)
		{
			$details = $res[0];

			$sql="SELECT DISTINCT pset, thumbnail, piccount, description FROM photosets where modelid = ".$details['modelid']." and website REGEXP 'scan';";
			$psets = $this->sqlquery($sql);
			if(is_array($psets) and count($res)>0)
			{
				$html = '    <div style="text-align:left; border:0px">';
				$html .= '     <table style="width:200; border:0px solid #555;margin:0px;padding:0px;">';
				$html .= '      <tr style:"border:0px;margin:0px;padding:0px;">';
				$html .= '       <td valign="top" style="width:200px;border: 0px;margin:0px;padding:0px;">';
				$html .= '        <span style="margin:0px 1px 0px 0px;"><img style="width:200px; height:290px;" ';
				$html .= 'src="/'.$this->webbase.'/'.$this->website.'/'.$this->modelthumbs.'/'.$details['thumbnail'].'">';

				$html .= '        <p style="font-size:16px;font-weight:bold;text-align:left">'.$details['modelname'];
				$html .= '        <p style="text-align:left">';
				if ($details['age'] != "") { $html .= '      <b>Age</b>: '.$details['age'].'<br>';}
				if ($details['height'] != "") { $html .= '<b>Height</b>: '.$details['height'].'<br>';}
				if ($details['weight'] != "") { $html .= '<b>Weight</b>: '.$details['weight'].'<br>';}
				if ($details['vitals'] != "") { $html .= '<b>Measurements</b>: '.$details['vitals'].'<br>';}
				if ($details['occupation'] != "") { $html .= '<b>Occupation</b>: '.$details['occupation'].'<br>';}
				if ($details['hobbies'] != "") { $html .= '<b>Hobbies</b>: '.$details['hobbies'].'<br>';}
				if ($details['music'] != "") { $html .= '<b>Favourite Music</b>: '.$details['music'].'<br>';}
				if ($details['fantasy'] != "") { $html .= '<b>Favourite Fantasy</b>: '.$details['fantasy'].'<br>';}
				$html .= '';

				$html .= '        <br>';
				if ($details['hasVideos'] == 1)
				{
					$html .= '        <a href="/cgi-bin/alsmpegs.py?m='.$details['videoid'].'&f=scan" style="font-size:10pt;">';
					$html .= '        <img src="/secret/sdc1/www.alsvideo.com/scanimg/'.$details['videothumb'].'" style="width:120px; height:90px;float:left;margin-right:5px;"><!--br>Videos--></a>';
				}

				$html .= '        <p style="text-align:left">'.$details['blurb'];
				$html .= '       </td>';
				$html .= '       <td valign="top" style="border: 0px solid #505; margin:0px; padding:0px;">';
				$html .= '     <div style="width:990px">';

				foreach($psets as $row)
				{
					//pset, thumbnail, piccount, description = row
					$pset=$row['pset'];
					$thumbnail=$row['thumbnail'];
					$piccount=$row['piccount'];
					$description=$row['description'];

					$size=array();
					list($size['w'], $size['h'], $type, $attr) = getimagesize($_SERVER['DOCUMENT_ROOT'].'/'.$this->webbase.'/'.$this->website.'/'.$thumbnail);

					$ihtml='<a href="'.PROGRAM.'?opt=1_100_f&path='.$this->webbase.'/'.$this->website.'/'.$this->photosdir.'/'.$pset.'">';
					$ihtml.='<img src="/'.$this->webbase.'/'.$this->website.'/'.$this->modelthumbs.'/'.$thumbnail.'" ';
					$ihtml.='style="height: '.$this->thumbsize.'px;float:left;" >';
					$thtml = '<p style="text-align:left;padding-left:70px">'.$description.'<br><br>('.$piccount.' pics)</p>';
					$w=990;
					$html .= '     <span style="height: '.$this->span_height.'px;width: '.$w.'px;">'.$ihtml.' '.$thtml.'</a></span>';
				}
				$html .= '<div id="thumbnails">';

				foreach(glob($_SERVER['DOCUMENT_ROOT'].'/secret/sdc1/www2.alsscan.com/members/models/'.$model.'/*.jpg') as $pic)
				{
					$w=90;
					$h=125;
					$img=str_replace($_SERVER['DOCUMENT_ROOT'],"",$pic);
					$thm=dirname($img).'/.pics/'.basename($img);
					if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$thm))
					{
						list($w, $h, $type, $attr) = getimagesize($_SERVER['DOCUMENT_ROOT'].'/'.$thm);
						$ws=((int)$w)+6;
						$hs=$this->span_height+6;
					}
					else
					{
						$thm=$img;
					}
					$title=removeExt(basename($img));
					$html .= '<span style="height:'.$hs.'px;width:'.$ws.'px;"><a href="'.$img.'"><img style="width:'.$w.'px; height:'.$h.'px;" src="'.$thm.'"></a><br>'.$title.'</span>';
				}
			}
			$html .= '</div>';
		}
		return $html;
	}
	// interface methods
	public function doPage($path)
	{
		if($this->isActive($path))
		{
			$_POST['model']=basename($path);
			$this->html = $this->AlsScansModel();
			$this->whole = true;
		}
	}
	public function isWhole()       { return $this->whole; }
	public function html()          { return $this->html; }
	public function isActive($path) { return (hasModelDB($path) && strpos($path, 'www2.alsscan.com/members/models')!== false); }
}


?>