<?php
	
require_once( 'Overlay.class.php' );

// -------------- S P A N   C L A S S E S ---------------------------------------
class Span
{
	function __construct($cell, $Config)
	{
		$this->cell = $cell;
		$this->cell['img_ht'] = 0;
		$this->url = $this->mkUrl(array($cell['path'], $cell['dir']));
		$this->wp = (int)$cell['width']+$Config['wplus'];
		$this->h = (int)$cell['height'];
		$this->imgurl = $this->mkImgUrl($cell['path'], $cell['thumb']);
		$this->id = null;
		$this->class = null;

		$this->span = HtmlTag::createElement('span')
			->set('style',CssStyle::createStyle()->set('width',$this->wp.'px')->set('height',$Config['full_ht'].'px'));
		$this->span->showTextBeforeContent(True);
		
		$this->anchor = HtmlTag::createElement('a')->setText(captionName($this->cell['caption'],$this->cell['width']));
		
		$this->img = HtmlTag::createELement('img')->addClass($this->class)->set('src',$this->imgurl);
		$this->imgStyle = CssStyle::createStyle()->set('width',$this->cell['width'].'px')->set('height',$this->cell['height'].'px');
	}
	function mkUrl($paths)
	{
		return str_replace('%2F','/', urlencode($this->joinUrl($paths)));
	}
	function mkRawUrl($paths)
	{
		return str_replace('%2F','/', rawurlencode($this->joinUrl($paths)));
	}
	function joinUrl($paths)
	{
		return preg_replace('#/+#','/', join("/",$paths));
	}
	function mkImgUrl($path, $thumb)
	{
		return $this->mkRawUrl(array($path, $thumb));
	}
}
class SpanLogo extends Span // path, dir, thumb, width, height, img_ht, caption, config[wplus], config[full_ht], opt
{
	function __construct($cell, $Config)
	{
		parent::__construct($cell, $Config);
		$this->cell = $cell;
		$this->cell['img_ht'] = 0;
		$this->url = $this->mkUrl(array($cell['path'], $cell['dir']));
		$this->wp = (int)$cell['width']+$Config['wplus'];
		$this->h = (int)$cell['height'];
		$this->imgurl = $this->mkImgUrl($cell['path'], $cell['thumb']);
		$this->id = null;
		//$this->class = null;  ->addClass($this->class)
		
		$this->html = HtmlTag::createElement('span')
			->set('style',createCSS($this->wp, $Config['full_ht']));
		$this->html->showTextBeforeContent(True);
		
		$this->anchor = HtmlTag::createElement('a')->setText(captionName($this->cell['caption'],$this->cell['width']));
		
		$this->img = HtmlTag::createELement('img')->set('src',$this->imgurl);
		$this->imgStyle = createCSS($this->cell['width'],$this->cell['height']);
	}
	function setRollover()
	{
		$this->h = (int)$this->cell['height'] * 2;
		$this->id = "rollover";
		$this->img->addClass("rollover");
		$this->imgStyle = createCSS($this->cell['width'], $this->h);
	}
	function getWidth() { return $this->wp; }
	function html()
	{
		$this->span->setText(comment('SpanLogo '.$this->id));

		$this->anchor->set('href',PROGRAM."?opt=".$this->cell['opt']."&path=".$this->url)
			->set('style',CssStyle::createStyle()->set('height','120px')->set('overflow','hidden'));
		$div = HtmlTag::createElement('div')->id($this->id);
		$this->img->set('style',$this->imgStyle);

		$div->addElement($this->img);
		$this->anchor->addElement($div);
		$this->span->addElement($this->anchor);
		return "\n".$this->span;
	}
}
class SpanLogoMovie extends SpanLogo // SpanLogo + movieLen
{
	function __construct($cell, $Config)
	{
		parent::__construct($cell, $Config);
		$this->cell = $cell;
	}
	function html()
	{
		$this->span->setText(comment('SpanLogoMovie'));
		
		$this->anchor->set('href',$this->joinUrl(array($this->cell['dir'])));
		if(strpos($this->cell['thumb'], "/")===0) { 
			//echo "\nUse MovieClip\n";
			$this->img->set('src',$this->mkRawUrl(array($this->cell['thumb']))); 
		}
		
		$div = HtmlTag::createElement('div')->set('style',CssStyle::createStyle()->set('height','120px'));

		$overlayBtn = Overlay::create()->mkBtn(PLAY_BUTTON,'-90')->html();
		
		$overlayTime = null;
		list($min,$secs) = $this->cell['movieLen'];
		if($min != "" || $secs != "") {
			$overlayTime = Overlay::create()->mkLabel($min,$secs)->html();
		}
		$this->img->set('style',$this->imgStyle);
		$div->addElement($this->img);
		$div->addElement($overlayBtn);
		$div->addElement($overlayTime);
		$this->anchor->addElement($div);
		$this->span->addElement($this->anchor);
		return "\n".$this->span;
	}
}

class SpanPhoto extends SpanLogo // SpanLogo + image
{
	function __construct($cell, $Config)
	{
		parent::__construct($cell, $Config);
		
		$this->cell = $cell;
		$this->url = $this->mkRawUrl(array($this->cell['path'],$this->cell['dir'],$this->cell['image']));
		$this->picurl = $this->joinUrl(array($this->cell['thumb']));

		$phpThumb = new iPhpThumb($this);
		if($phpThumb->isActive())
		{
			$this->picurl = $phpThumb->picUrl($this->cell, $this->picurl);
		}
		else
		{
			echo "<!-- SpanPhoto: ".$this->cell['thumb']." -->";
			$this->picurl = $this->mkRawUrl(array($this->cell['thumb']));
		}
	}
	function html()
	{
		$this->span->setText(comment('SpanPhoto ARSE'));

		$this->anchor->set('href',$this->url)->set('rel','doSlideshow:true')->set('title',$this->cell['image']);
		$div = HtmlTag::createElement('div')->set('style',CssStyle::createStyle()->set('height','120px'));
			//->setText($overlay); // **
		$img = HtmlTag::createElement('img')->addClass('thumb')
			->set('style',$this->imgStyle)
			->set('src',$this->picurl);
		$div->addElement($img);
		$this->anchor->addElement($div);
		$this->span->addElement($this->anchor);
		return "\n".$this->span;
	}
}

class SpanIcon extends SpanLogo // SpanLogo + image
{
	function __construct($cell, $Config)
	{
		parent::__construct($cell, $Config);
		
		$this->cell = $cell;
		$this->ext = getExt($this->cell['image']);
		$this->url = $this->mkUrl(array($this->cell['path'],$this->cell['dir'],$this->cell['image']));
	}
	function html() // photo thumbs
	{
		$this->span->setText('<!-- span_icon -->');
		$this->span->showTextBeforeContent(True);
		$this->anchor->set('href',$this->url);
		
		// these styles need to be classes and put in css file
		$div1 = HtmlTag::createElement('div')
			->set('style',CssStyle::createStyle()->set('width','120px')->set('height','120px')
			->set('background-image',"url('".FILE_BLANK."')")->set('background-size','120px'));
		$div2 = HtmlTag::createElement('div')
			->set('style',CssStyle::createStyle()->set('padding','90px 20px')->set('color','#ddd')
			->set('font','bold 200% arial')->set('text-align','left'));
		
		$unknown = HtmlTag::createElement('font')
			->set('style',CssStyle::createStyle()->set('color','#ddd')->set('font','italic bold 100% arial'))
			->setText('unk');
		
		if($this->ext == NULL) 
		{ 
			$div2->addElement($unknown);
		}
		else
		{ 
			$div2->setText($this->ext);
		}
		$div1->addElement($div2);
		$this->anchor->addElement($div1);
		$this->span->addElement($this->anchor);
		
		return "\n".$this->span;
	}
}
class SpanDir extends SpanLogo // SpanLogo + img_url
{
	function __construct($cell, $Config, $img_url)
	{
		parent::__construct($cell, $Config);
		global $Config;
		$this->cell = $cell;
		$this->img_url = $img_url;
		$this->url = $this->mkUrl(array($this->cell['path'],$this->cell['dir']));
	}
	function html()
	{
		$this->span->setText('<!-- span_dir -->');
		$this->span->showTextBeforeContent(True);
		
		$this->anchor->setText(captionName($this->cell['dir'], $this->cell['width']));
		$this->anchor->set('href',PROGRAM.'?opt='.$this->cell['opt'].'&path='.$this->url);
		
		$div1 = HtmlTag::createElement('div')
			->set('style',createCSS(120,120)->set('margin','0 8px')
					->set('background-image','url(\''.$this->img_url.'\')')->set('background-size','120px'));
		
		$div2 = HtmlTag::createElement('div')
			->set('style',CssStyle::createStyle()->set('width','90px')->set('padding','35px 8px')
					->set('color','#ddd')->set('font','bold 150% arial')->set('text-align','left'))
			->setText(displayName($this->cell['dir']));
		
		$div1->addElement($div2);
		$this->anchor->addElement($div1);
		$this->span->addElement($this->anchor);
		return "\n".$this->span;
		
	}
}

	
?>