<?php

require_once('include_check.php');

?>

<head>
<?php
echo pageTitle($G->getPath());
?>

 <!-- for floatbox -->
 <link type="text/css" rel="stylesheet" href="/floatbox/floatbox.css" />
 <script type="text/javascript" src="/floatbox/floatbox.js"></script>

 <link rev="stylesheet" type="text/css" href="<?php echo CSS; ?>" rel="stylesheet" media="screen,all">

 <meta http-equiv=Content-Type content="text/html; charset=ISO-8859-1" >
 <meta http-equiv="content-style-type" content="text/css" >
 <!--[if IE]>
 <style type="text/css" media="all">@import url(/css/galleryIEfixes.css);</style>
 <![endif]-->

 <link rel="shortcut icon" href="<?php echo IMAGE_ROOT.favicon($G->getPath()); ?>" type="image/x-icon">

 <meta name="viewport" content="width=device-width; initial-scale=1.0;" > <!-- maximum-scale=1.0;" -->
 <link rel="apple-touch-icon" href="<?php echo IMAGE_ROOT; ?>template/engage.png" />

 <meta name="googlebot" content="noindex,noarchive,nofollow" >
 <meta name="robots" content="noindex,nofollow">

 <!-- link media="only screen and (max-device-width: 480px)"  href="/css/iPhone.css" type="text/css" rel="stylesheet" / -->

 <!-- link media="only screen"  href="/css/iPhone.css" type="text/css" rel="stylesheet" / -->

 <script language="JavaScript" type="text/javascript">

var url
function changepage(formObject)
{
  url = formObject.options[formObject.options.selectedIndex].value;
  if(url != "empty") {
    window.location = url;
    url = "";
  }
}

function DoSubmission()
{
  document.gallery.submit();
}

// WiredPussy scripts
function showall()
{
  var wpsites=['devicebondage','boundgangbangs','electrosluts','everythingbutt','theupperfloor','fuckingmachines','hogtied','sexandsubmission','thetrainingofo','waterbondage','whippedass','wiredpussy','pissing','publicdisgrace'];
  for (i=0;i<wpsites.length;i++) {
    document.getElementById("1"+wpsites[i]).style.display = "block";
    element = document.getElementById("2"+wpsites[i]);
    if (element != null) { element.style.display = "block"; }
    document.getElementById("x"+wpsites[i]).style.backgroundColor = "#000000";
    //document.getElementById("x"+wpsites[i]+"1").style.backgroundColor = "#000000";
  }
  //document.getElementById("all").style.display = "block";
  document.getElementById("xall").style.backgroundColor = "#f47c0e";
}

// WiredPussy scripts
function hideshow(id, action)
{
  var wpsites=['devicebondage','boundgangbangs','electrosluts','everythingbutt','theupperfloor','fuckingmachines','hogtied','sexandsubmission','thetrainingofo','waterbondage','whippedass','wiredpussy','pissing','publicdisgrace'];
  if (action=="show") {
    for (i=0;i<wpsites.length;i++) {
      //document.getElementById(wpsites[i]).style.display = "none";
      document.getElementById("1"+wpsites[i]).style.display = "none";
      element = document.getElementById("2"+wpsites[i]);
      if (element != null) { element.style.display = "none"; }
      document.getElementById("x"+wpsites[i]).style.backgroundColor = "#000000";
      //document.getElementById("x"+wpsites[i]+"1").style.backgroundColor = "#000000";
    }
    document.getElementById("1"+id).style.display = "block";
    element = document.getElementById("2"+id);
    if (element != null) { element.style.display = "block"; }
    document.getElementById("x"+id).style.backgroundColor = "#f47c0e";
    //document.getElementById("x"+id+"1").style.backgroundColor = "#f47c0e";
  }
}
// read a cookie by name and return the value
function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0)
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}
// read and update the currBrowserWidth cookie value
// write the previous width and if different to the current width then reload the page
function browserResCookie() {
	var prevBrowserWidth = 0;
	var x = readCookie('currBrowserWidth')
	if (x) { prevBrowserWidth = +x; }
	document.cookie='prevBrowserWidth='+prevBrowserWidth+'; expires=; path=/';
	document.cookie='currBrowserWidth='+window.outerWidth+'; expires=; path=/';
	if (prevBrowserWidth != window.outerWidth) { location.reload(); }
}
browserResCookie(); // called to insturgate the browser width checking

// create an 'onresize' event handler that checks changes in browser width
window.onresize = function(event) {
	browserResCookie();
};

// options for the floatbox image overlay code
fbPageOptions =
{
  theme: 'auto',
  preloadAll: false,
  enableWrap: false,
  autoGallery: true,
  activateMedia: true,
  doAnimations: false,
  resizeDuration: 0.7,
  imageFadeDuration: 0.0,
  overlayFadeDuration: 0,
  overlayOpacity:  95,
  graphicsType: 'auto',
  //numIndexLinks: 10,
  indexLinksPanel: 'control',
  showIndexThumbs: false,
  outsideClickCloses: true,
  slideInterval: 5,
  startPaused: true,
  showPlayPause: false,
  outerBorder: 0,
  innerBorder: 0,
  padding: 0,
  shadowType: 'none',
  showCaption: false,
  showItemNumber: false,
  showClose: false,
  showControlsText: false,
  navType: 'none',
  outerBorderRadius: 0,
  addPlayButton: false,
  //autoPlayVideo: true,
  //autoEndVideo: true,
  titleAsCaption: false,
  enableKeyboardNav: true,
  inFrameResize: false,
  keepCentered: false,
  boxScroll: true,
  pageScroll: true,
};

 </script>
 <?php $cfg = Config::getInstance(); if($cfg->get('logon')==True) { login_links(); } ?>

</head>
