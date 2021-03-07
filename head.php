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

 <meta http-equiv=Content-Type content="text/html, charset=ISO-8859-1" >
 <meta http-equiv="content-style-type" content="text/css" >
 <!--[if IE]>
 <style type="text/css" media="all">@import url(/css/galleryIEfixes.css);</style>
 <![endif]-->

 <link rel="shortcut icon" href="<?php echo IMAGE_ROOT.favicon($G->getPath()); ?>" type="image/x-icon">

 <meta name="viewport" content="width=device-width, initial-scale=1.0" > <!-- maximum-scale=1.0;" -->
 <link rel="apple-touch-icon" href="<?php echo IMAGE_ROOT; ?>template/engage.png" />

 <meta name="googlebot" content="noindex,noarchive,nofollow" >
 <meta name="robots" content="noindex,nofollow">

 <!-- link media="only screen and (max-device-width: 480px)"  href="/css/iPhone.css" type="text/css" rel="stylesheet" / -->

 <!-- link media="only screen"  href="/css/iPhone.css" type="text/css" rel="stylesheet" / -->

 <style>
 img.rollover:hover {margin-top:-<?php echo THUMBSIZE; ?>px; overflow:hidden;}
 #rollover {overflow: hidden; height:<?php echo THUMBSIZE ?>px;}
 #thumbnails span a {font-size: 8pt;
                     color: #fff;
                     text-shadow: 2px 2px 4px #000, -2px 2px 4px #000, 2px -2px 4px #000, -2px -2px 4px #000; 
                     /*text-decoration: none;*/
                     color: #fff;}
  a img {background-color:#fff;}
  a img:hover {opacity: 0.8;}
 </style>

 <script type="text/javascript" src="<?php echo PROGRAM; ?>js/wiredpussy.js"></script>
 <script type="text/javascript" src="<?php echo PROGRAM; ?>js/phpgallery.js"></script>
 <script type="text/javascript" src="<?php echo PROGRAM; ?>js/floatbox_settings.js"></script>

 <?php $cfg = Config::getInstance(); if($cfg->get('logon')==True) { login_links(); } ?>

</head>
