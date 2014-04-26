<?php

require_once('include_check.php');


function makeCalendar($base, $path, $yr)
{
	$year = "
	<div class=\"calendar\">
	<table width=\"150\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
	<tr>
	<td valign=\"top\">
	  <table width=\"100%%\" border=\"0\" cellpadding=\"4\" cellspacing=\"3\">
	  <tr>
	    <td><div id='caltitle'>%s</div></td>
	  </tr>
	  </table>

	  <table width=\"100%%\">
	  <tr>
	    %s
	    %s
	    %s
	   </tr>
	   <tr>
	    %s
	    %s
	    %s
	  </tr>
	  <tr>
	    %s
	    %s
	    %s
	   </tr>
	   <tr>
	    %s
	    %s
	    %s
	  </tr>
	  </table>
	</td>
	</tr>
	</table>
	</div>
	";

	$months_u=array('JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC');
	$months_l=array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
	$months_i=array(1,2,3,4,5,6,7,8,9,10,11,12);
	$cal=array(1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>'',8=>'',9=>'',10=>'',11=>'',12=>'');

	foreach(scandir($base.'/'.$yr) as $x)
	{
		if(in_array($x,$months_u)) // x in months_u:
		{
			$mon = array_search($x+1,$months_u);
		}
		elseif(in_array($x,$months_l)) // x in months_l:
		{
			$mon = array_search($x+1,$months_l);
		}
		else if(in_array((int)$x,$months_i)) //int($x) in months_i:
		{
			$mon = (int)$x;
		}
		else
		{
			$mon = 0;
		}

		if($mon >= 1 && $mon<13) // mon in range(1,13):
		{   
			$cal[$mon] = sprintf('<td class="callinkact" width="33%%"><a href="%s?opt=1_50_10_f&path=%s/%s/%s&up=%s">%s</a></td>', 
		                      PROGRAM,$path,$yr,$x,$path,$months_u[$mon-1]);
		}
	}
        foreach(range(1,12) as $m)
	{
		if( $cal[$m] == '')
		{
			$cal[$m] = sprintf('<td class="callinkinact" width="33%%">%s</td>', $months_u[$m-1]);
		}
	}

        return sprintf($year, $yr,$cal[1],$cal[2],$cal[3],$cal[4],$cal[5],$cal[6],$cal[7],$cal[8],$cal[9],$cal[10],$cal[11],$cal[12]);
}


function allYears($path)
{
	$html="";
	$result=array();
	$years=array();
	$dirs=scandir($path);
	//years.sort()
	$dirs = array_reverse($dirs);
	foreach($dirs as $x) //x in years:
	{
		if(is_numeric($x))
		{
			$int_x = (int)$x;
		}
		else
		{
			continue;
		}
		if($int_x>1900 && $int_x<2100) //in range(1900, 2100):
		{
			$years[] = $int_x;
			$result[] = makeCalendar($path, substr($path,15),$x);
		}
	}
	foreach($result as $year)
	{
		$html.='<span style="height: 138px;"><!-- span_icon -->'."\n";
		$html.=$year;
		$html.="</span>\n";
	}
	return array($years, $html);
}

?>