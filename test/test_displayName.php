<?php

//$SERVER_PORT=80;

//require_once('functions.php');

function displayName($s)
{
	// how many of each letter fit across the cell box
	$letters=array( 'A'=>9, 'B'=>9, 'C'=>8, 'D'=>8, 'E'=>9, 'F'=>10,'G'=>8, 'H'=>8, 'I'=>22,
					'J'=>13,'K'=>9, 'L'=>11,'M'=>6, 'N'=>8, 'O'=>8, 'P'=>9, 'Q'=>8, 'R'=>8,
					'S'=>9, 'T'=>10,'U'=>8, 'V'=>9, 'W'=>5, 'X'=>9, 'Y'=>9, 'Z'=>10,
					
					'a'=>11,'b'=>11,'c'=>12,'d'=>11,'e'=>11,'f'=>22,'g'=>11,'h'=>11,'i'=>26,
					'j'=>27,'k'=>12,'l'=>27,'m'=>6, 'n'=>11,'o'=>11,'p'=>11,'q'=>11,'r'=>17,
					's'=>12,'t'=>21,'u'=>11,'v'=>12,'w'=>8, 'x'=>11,'y'=>12,'z'=>12,
					
					' '=>15,'+'=>11,'-'=>13,'.'=>25,'_'=>9, '('=>9, ')'=>9, '['=>9, ']'=>9,
					'0'=>11,'1'=>13,'2'=>11,'3'=>11,'4'=>11,'5'=>11,'6'=>11,'7'=>11,'8'=>11,'9'=>11);
	//$s = cleanStr($s);
	$size = 0.0;
	$base = 110;
	//$width = 88; //for font 150%
	//$width = 78; //for font 180%
	$width = 109;
	$j = 0;
	$linecount = 1;
	$breaks = array();
	for($i = 0; $i < strlen($s) && (int)$size < $width && $linecount < 5; $i++)
	{
		//echo $s[$i].'=>'.$letters[$s[$i]].' '.$size."\n";
		if(array_key_exists($s[$i], $letters)) {
			$newsize = $size + ((float)$base / (float)$letters[$s[$i]]);
			printf("%d %f %s\n", $i, $newsize, $s[$i]);
			if ($newsize < $width) {
				$size = $newsize;
			} else {
				$j = $i;
				$linecount = $linecount + 1;
				array_push($breaks, $i-1);
				printf( "BREAK AT %d\n", $i-1);
				//var_dump($breaks);
				$size = 0.0;
			}
		} else { echo "<!-- displayName: [".$s[$i]."] is missing -->"; }
		$j = $i;
	}
	$rbreaks = array_reverse($breaks);
	foreach($rbreaks as $p)
	{
		printf("\nbreak %d\n",$p);
		$s = substr($s,0,$p)."\n".substr($s,$p);
		printf("\n%s\n",$s);
	}
	//$s = wordwrap($s, $j+1, "<br />\n", true);
	printf("REPLACE\n");
	$s = str_replace("\n", "<br />\n", $s);
	printf("IMPLODE\n");
	$s = implode("<br />\n", array_slice(explode("<br />\n", $s), 0, 4));
	printf("\nCONVERTED TO HTML\n");

	return $s;
}


	
$s="Night at the Museum Secrets of the Tomb (2014)";
printf("%s\n",$s);
printf("%s\n", displayName($s) );
$s="WetAndPuffy.com_16.03.11.Anabelle.Watch.Me.XXX.IMAGESET-GAGBALL[rarbg]";
printf("%s\n",$s);
printf("%s\n", displayName($s) );
	

echo "\n";
$w="[BigWetButts]<br />\nJada Stevens &<br />\nJessie Rogers<br />\n(Double Anal<br />\nPoolside -<br />\nSeptember 7,<br />\n2012) MP4 +<br />\nPic Set\n";
printf(implode("<br />\n", array_slice(explode("<br />\n", $w), 0, 4)));

?>