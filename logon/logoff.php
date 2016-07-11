<?php

if($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_NAME'] == 'localhost')
{
	$SITE_PORT = $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'];
}
else
{
	$SITE_PORT = $_SERVER['SERVER_NAME'];
}

if(!defined('INCLUDE_CHECK')) 
{ 
	// die('You are not allowed to execute this file directly');
	header("Location: http://".$SITE_PORT."/index.php");
	die();
}

function login_links()
{ ?>    
	<link rel="stylesheet" type="text/css" href="/login_panel/css/login.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/login_panel/css/slide.css" media="screen" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	<script type="text/javascript" src="/login_panel/js/slide.js"></script>
	<script>
	function setFocusToTextBox(){
	    document.getElementById("password").focus();
	}
	</script>
<?php
}

function login_panel() // ($activePage)
{ 
?>
<!-- Panel -->
<div id="toppanel">
	<div id="panel">
		<div class="content clearfix">
			<div class="left">
				<!-- Register Form -->
				<div class="clearfix">
				        <?php if(isset($_SESSION['id']) && $_SESSION['id']) 
				        { ?>
					<h2><!--
					<a href="/home">[ thumbs ]</a> 
					<a href="/hegreart">[ hegreart ]</a> 
					<a href="/hegreblog">[ hegreblog ]</a> 
					</h2>

					<h2>
					<a href="/secret">[ secret ]</a> 
					<a href="/femjoy">[ femjoy ]</a> 
					<a href="/blog">[ blog ]</a> 
					</h2>

					<h2>
					<a href="/ramblingblog">[ ramblingblog ]</a> 
					<a href="/squirrelmail">[ squirrelmail ]</a> 
					</h2>

					<h2>
					<a href="/php/cgi.php">[ cgi scripts ]</a>
					</h2> -->
				        <?php } ?>
				</div>
			</div>
			
			<div class="left">
				<div class="clearfix">
				<h1>Members panel</h1>

				<p>You can put member-only data here</p>
				<a href="/">View a special member page</a>
				<p>- or -</p>
				<a href="/?logoff">Log off</a>
				<!--
				<p>Other Links</p>
				<a href="/wordpress">[ wordpress ]</a> &nbsp;
				<a href="/paul">[ paul ]</a> 
				-->
				</div>
			</div>
			
			<div class="left">
				<h1>The Sliding jQuery Panel</h1>
				<!-- h2>A register/login solution</h2>		
				<p class="grey">You are free to use this login and registration system in you sites!</p -->
				<h2>A Big Thanks</h2>
				<p class="grey">This tutorial was built on top of <a href="http://web-kreation.com/index.php/tutorials/nice-clean-sliding-login-panel-built-with-jquery" title="Go to site">Web-Kreation</a>'s amazing sliding panel.</p>
			</div>
		</div>
		<?php if (file_exists('extra.php')) { include('extra.php');} ?>
	</div>
	
    <!-- The tab on top -->
	<div class="tab">
		<form class="xclearfix" action="" method="post">
		<ul class="login">
		<li class="left">&nbsp;</li>
		<?php if(!isset($_SESSION['id']) || !$_SESSION['id']) 
		{ 
			if(isset($_SESSION['msg']['login-err']) && $_SESSION['msg']['login-err'])
			{
				echo '<li class="err" style="">'.$_SESSION['msg']['login-err'].'</li>';
				unset($_SESSION['msg']['login-err']);
			} ?>
			<li>Username</li>
			<li><input class="field" type="text" name="username" id="username" value="" size="13" /></li>
			<li>Password</li>
			<li><input class="field" type="password" name="password" id="password" size="13" /></li>
			<li><input class="bt_login" type="submit" name="submit" value="Login" />
			    <input name="rememberMe" id="rememberMe" type="hidden" checked="checked" value="1" /><!-- Remember me --></li>
			<?php
			if(isset($_GET['activepage'])) 
			{ ?>
				<input type="hidden" name="activepage" value="<?php echo $_GET['activepage']?>">
			<?php 
			} 
		} 
		else 
		{ ?>
		<li id="toggle">
			<a id="open" class="open" href="#"></a>
			<a id="close" style="display: none;" class="close" href="#"></a>
		</li>
		<li class="sep">|</li>
		<li><a href="/?logoff">Log off</a></li>
		<?php 
		} ?>
		<li class="right">&nbsp;</li>
		</ul> 
		</form>
	</div> <!-- / tab -->
</div> <!-- toppanel -->
<?php 
}
?>
