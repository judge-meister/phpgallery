<?php

require_once( 'include_check.php' );
require_once( 'pluginLoader.php' );


class WiredDB
{
	/* Database config */
	private $db_host		= 'localhost';
	private $db_user		= 'judge';
	private $db_pass		= 'r0adster';
	private $db_database		= 'wiredpussy'; 

	/* End config */
	function __construct() 
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
	public $sites = array('devicebondage',
	         'fuckingmachines',
	         'hogtied',
	         'electrosluts',
	         'everythingbutt',
	         'boundgangbangs',
	         'theupperfloor',
	         'sexandsubmission',
	         'thetrainingofo',
	         'waterbondage',
	         'whippedass',
	         'wiredpussy',
	         'pissing',
	         'publicdisgrace'
	         );

	private function summary($table, $watched, $limit)
	{
		$clause="";
		if($watched=='N') 
		{
			$clause=" where watched='N'";
		}
		//sql="""select vid from %s %s order by vid desc limit %d;""" % (table, clause, limit)
		$sql="select vid from ".$table." ".$clause." order by added desc limit ".$limit.";";
		//echo $sql;
		return $this->sqlquery($sql);
	}
	private function getSummary($watched)
	{
		//global $sites;
		$summ=array();
		for($i=0; $i<count($this->sites); $i++) //site in sites:
		{
			$summ[$this->sites[$i]]=$this->summary($this->sites[$i], $watched, 8);
		}
		return $summ;
	}
	public function getSiteVids($site, $watched)
	{
		return array($site => $this->summary($site, $watched, 48));
	}

	public function shorten($s)
	{
		if(strlen($s)>18){ return substr($s,0,13).'<br>'.substr($s,13); }
		else{ return $s; }
	}
}

class WiredPussy implements iPlugin
{
	private $html = "";
	private $whole = false;
	
	public function __construct() {}

	// -------------------------------------------------------------------------
	private function wiredpussyPage($path)
	{
		$html = '';
		$wdb = new WiredDB();

		// buttons
		for($i=0; $i<count($wdb->sites); $i++)// x in sites:
		{
			$x = $wdb->sites[$i];
			$html = $html.'    <span style="display: block;background-color:#000000;" id="x'.$x.'"><a class="wplink" ' 
			            .'onclick="hideshow(\''.$x.'\',\'show\');" ><b>['.$x.']</b></a></span>';
			if($i == 6){ $html=$html.'<div style="clear:both;"></div>'; }
		}

		$html = $html.'    <span style="display: block;background-color:#000000;" id="xall"><a class="wplink" onclick="showall();" >'
		        .'<b>[all]</b></a></span>';
		$html = $html.'    <span style="height: 1px;width: 1125px;clear:both">&nbsp;</span>';


		for($i=0; $i<count($wdb->sites); $i++)// x in sites:
		{
			$x = $wdb->sites[$i];

			$vids=scandir($_SERVER['DOCUMENT_ROOT'].'/'.$path);
			$videos = $wdb->getSiteVids($x, 'N');

			$html = $html.'      <div id="1'.$x.'" style="display: block;clear:both;">';

			$html = $html.'      <span style="height: 125px;"><a style="text-transform:uppercase;" href="'.PROGRAM.'?opt=1_100&path='.$path.'/'.$x.'">'. 
			              '<img src="/secret/sdd1/videos/www.wiredpussy.com/titles/'.$x.'_thm.jpg"  style="color:#ccc;border:none; margin: 2px '. 
			              '2px 2px 2px; height: 100px;padding-top:5px;" ><br><b>'.$wdb->shorten($x).'</b></a></span>';
			$dates=array();
			for($j=0; $j<count($vids); $j++)// v in vids:
			{
				$v=$vids[$i];
				if(substr($v,0,1) != '.' && strpos($v,'_1.') !== false)
				{
					if(file_exists('/data/sdd1/videos/www.wiredpussy.com/'.$x.'/'.$v))
					{
					        $stat=stat('/data/sdd1/videos/www.wiredpussy.com/'.$x.'/'.$v);
						$date=$stat['mtime'];

					        if(!array_key_exists($date, $dates)) // date not in dates.keys():
						{
					            $dates[$date]=$v;
						}
					        else
						{
					            $html = $html.$date.'exists';
						}
					}
				}
			}

			$c=0;
			for($j=0; $j<count($videos[$x]); $j++) //code in _videos[x]:
			{
				$code=$videos[$x];
				$v = $code[$j]['vid']."_1.wmv";
				$img = explode('_',$v);//v.split('_')[0]
				$html = $html.'      <span style="height: 125px;width: 100px"><a href="/secret/sdd1/videos/www.wiredpussy.com/'.$x.'/'.$v.'">'.
				          '<img src="/secret/sdd1/videos/www.wiredpussy.com/thumbs/'.$img[0].'.jpg" style="height: 100px;margin-top:5px;" >'.
				          '<br>'.$v.'</a></span>';
				$c=$c+1;
				if($c == 8)
				{
					$html = $html.'      <span style="height: 1px;width: 1125px;clear:both">&nbsp;</span>';
					$html = $html.'      </div>      <div id="2'.$x.'" style="display: none;clear:both;">';
				}
			}
			$html = $html.'<span style="height: 1px;width: 1125px;clear:both">&nbsp;</span>';
			$html = $html.'</div>';
		}
		return $html;
	}

	// interface methods
	public function doPage($path)
	{
		if($this->isActive($path))
		{
			$this->html = $this->wiredpussyPage($path);
			$this->whole = true;
		}
	}
	public function isWhole()       { return $this->whole; }
	public function html()          { return $this->html; }
	public function isActive($path) { return (strpos($path, "wiredpussy") !== false); }
}


?>