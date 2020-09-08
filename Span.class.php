<?php
	
require_once( 'Overlay.class.php' );

// -------------- S P A N   C L A S S E S ---------------------------------------
class Span
{
	/* celldata - img_ht path dir width height thumb caption*/
	/* config[wplus] */
	function __construct($cell)
	{
		$cfg = Config::getInstance();
		$this->cell = $cell;
		$this->cell['img_ht'] = 0;
		$this->url = $this->mkUrl(array($cell['path']->str(), $cell['dir']));
		$this->wp = (int)$cell['width']+$cfg->get('wplus');
		$this->h = (int)$cell['height'];
		$this->dflt_ht = 122;
		$this->dflt_wt = 120;
		$this->imgurl = $this->mkImgUrl($cell['path']->str(), $cell['thumb']);
		$this->id = null;
		$this->class = null;

		$this->span = HtmlTag::createElement('span')
			->set('style',createCSS($this->wp,$cfg->get('full_ht')));
		$this->span->showTextBeforeContent(True);
		
		$this->anchor = HtmlTag::createElement('a')->setText(captionName($this->cell['caption'],$this->cell['width']));
		
		$this->img = HtmlTag::createELement('img')->addClass($this->class)->set('src',$this->imgurl);
		$this->imgStyle = createCSS($this->cell['width']+4,$this->cell['height']);
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
	/* celldata - path, dir, thumb, width, height, img_ht, caption, opt */
	/* config[wplus], config[full_ht] */
	function __construct($cell)
	{
		parent::__construct($cell);
		$cfg = Config::getInstance();
		$this->cell = $cell;
		$this->cell['img_ht'] = 0;
		$this->url = $this->mkUrl(array($cell['path']->str(), $cell['dir']));
		$this->wp = (int)$cell['width']+$cfg->get('wplus');
		$this->h = (int)$cell['height'];
		$this->imgurl = $this->mkImgUrl($cell['path']->str(), $cell['thumb']);
		$this->id = null;
		//$this->class = null;  ->addClass($this->class)
		
		$this->html = HtmlTag::createElement('span')
			->set('style',createCSS($this->wp, $cfg->get('full_ht')));
		$this->html->showTextBeforeContent(True);
		
		$this->anchor = HtmlTag::createElement('a')->setText(captionName($this->cell['caption'],$this->cell['width']-2));
		
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
		$this->span->addClass('spanBase spanLogo');

		$this->anchor->set('href',PROGRAM."?opt=".$this->cell['opt']."&path=".$this->url)
			->set('style',CssStyle::createStyle()->set('height',$this->dflt_ht.'px'));
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
	/* celldata - dir thumb moveiLen */
	/* SpanLogo */
	function __construct($cell)
	{
		parent::__construct($cell);
		$this->cell = $cell;
		$this->imgStyle = createCSS($this->cell['width']+4,$this->cell['height']);
	}
	function html()
	{
		$this->span->setText(comment('SpanLogoMovie'));
		$this->span->addClass('spanBase spanLogoMovie');

		$w = $this->span->getStyle()->get('width');
		$w = str_replace('px', '', $w);
		$this->span->getStyle()->set('width', ($w+4).'px');
		
		$this->anchor->set('href',PROGRAM."?media=".$this->mkUrl(array($this->cell['dir'])));
		$this->anchor->set('title',$this->cell['image']);
		if(strpos($this->cell['thumb'], "/")===0) { 
			//echo "\nUse MovieClip\n";
			$this->img->set('src',$this->mkRawUrl(array($this->cell['thumb']))); 
		}
		
		$div = HtmlTag::createElement('div')
			->addClass('spanLogoMovieDiv')
			->set('style',CssStyle::createStyle()
				->set('height',$this->dflt_ht.'px'));

		//$overlayBtn = Overlay::create()->mkBtn(PLAY_BUTTON,'-90')->html();
		//$overlayBtn = Overlay::create()->mkBtn(PLAY_BUTTON,'-50')->html();
		
		$overlayTime = null;
		//list($min,$secs) = $this->cell['movieLen'];
		//if($min != "" || $secs != "") {
		if($this->cell['movieLen']) {
			//$overlayTime = Overlay::create()->mkTimeLabel($min,$secs,5)->html();
			//$overlayTime = Overlay::create()->mkTimeLabel($min,$secs,-30)->html();
			$overlayTime = Overlay::create()->mkTimeLabel($this->cell['movieLen'],-30)->html();
		}
		$this->img->set('style',$this->imgStyle)->addClass('spanLogoMovieImage');
		$div->addElement($this->img);
		//$div->addElement($overlayBtn);
		$div->addElement($overlayTime);
		$this->anchor->addElement($div);
		$this->span->addElement($this->anchor);
		return "\n".$this->span;
	}
}

class SpanPhoto extends SpanLogo // SpanLogo + image
{
	/* celldata - path dir image thumb */
	/* SpanLogo */
	function __construct($cell)
	{
		parent::__construct($cell);
		
		$this->cell = $cell;
		$this->url = $this->mkRawUrl(array($this->cell['path']->str(),$this->cell['dir'],$this->cell['image']));
		$this->picurl = $this->joinUrl(array($this->cell['thumb']));

		$phpThumb = new iPhpThumb($this);
		if($phpThumb->isActive())
		{
			$this->picurl = $phpThumb->picUrl($this->cell, $this->picurl);
		}
		else
		{
			//echo "<!-- SpanPhoto: ".$this->cell['thumb']." -->";
			$this->picurl = $this->mkRawUrl(array($this->cell['thumb']));
		}
	}
	function html()
	{
		$this->span->setText(comment('SpanPhoto ARSE'));
		$this->span->addClass('spanBase spanPhoto');

		$this->anchor->set('href',$this->url); //->set('rel','doSlideshow:true')
		$this->anchor->set('title',$this->cell['image']);
		$div = HtmlTag::createElement('div')->set('style',CssStyle::createStyle()->set('height',$this->dflt_ht.'px'));
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
	/* celldata - path dir image */
	/* SpanLogo */
	function __construct($cell)
	{
		parent::__construct($cell);
		
		$this->cell = $cell;
		$this->ext = getExt($this->cell['image']);
		$this->url = $this->mkUrl(array($this->cell['path']->str(),$this->cell['dir'],$this->cell['image']));
	}
	function html() // photo thumbs
	{
		$this->span->setText(comment('span_icon'));
		$this->span->addClass('spanBase spanIcon');
		$this->span->showTextBeforeContent(True);
		$this->anchor->set('href',$this->url);
		
		// these styles need to be classes and put in css file
		$div1 = HtmlTag::createElement('div')
			->set('style',CssStyle::createStyle()->set('width',$this->dflt_ht.'px')->set('height',$this->dflt_ht.'px')
			->set('background-image',"url('".FILE_BLANK."')")->set('background-size',$this->dflt_ht.'px'));
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
	/* celldata - path dir width opt title */
	/* SpanLogo */
	function __construct($cell, $img_url)
	{
		parent::__construct($cell);
		//global $Config;
		$this->cell = $cell;
		$this->img_url = $img_url;
		$this->url = $this->mkUrl(array($this->cell['path']->str(),$this->cell['dir']));
	}
	function html()
	{
		$this->span->setText(comment('span_dir : '.$this->cell['du']));
		$this->span->addClass('spanBase spanDir');

		$this->span->showTextBeforeContent(True);
		$w = $this->span->getStyle()->get('width');
		$w = str_replace('px', '', $w);
		$this->span->getStyle()->set('width', ($w+2).'px');
		
		//$this->anchor->setText(captionName($this->cell['dir'], $this->cell['width']));
		$this->anchor->set('href',PROGRAM.'?opt='.$this->cell['opt'].'&path='.$this->url);
		if(strlen($this->cell['title']) > 25) {
			$this->anchor->set('title',$this->cell['title']);
		}
		/* no bg image */
		$div1 = HtmlTag::createElement('div')
			->set('style',createCSS($this->dflt_wt+2,$this->dflt_ht-2));

		/* with bg image (needs updating though) */
		//$div1 = HtmlTag::createElement('div')
		//		->set('style',createCSS($this->dflt_wt+2,$this->dflt_ht-2)
				/*->set('margin','0 4px'/*8*//*)*/
				//->set('background-image','url(\''.$this->img_url.'\')')
				//->set('background-size',$this->dflt_ht.'px')
				/*->set('border','1px solid #bdf')*/
				//->set('white-space','nowrap')
				//);

		if($this->img_url == ""){
			$div1->addClass('spanDirDiv');
		}else{
			$div1->addClass('spanDirDivNoBorder');
		}
		$div1->addClass('spanDirDivDiv');

		/* with play button */
		//$div2 = HtmlTag::createElement('div')
		//	->set('style',CssStyle::createStyle()/*->set('width','90px')*/->set('padding','35px 0px'/*35 8*/)
		//			->set('color','#ddd')->set('font','175% arial'/*bold 150% */)->set('text-align','center'/*left*/))
		//	->setText(displayName(cleanStr($this->cell['title'])));
		
		$overlay = null;
		if($this->cell['du'] != null)
		{
			$overlay = Overlay::create()->mkLabel('( '.$this->cell['du'].' )',-30, "caption_format_sm")->html();
		}
		
		/* without play button */
		$div2 = HtmlTag::createElement('div')->addClass('spanDirFormat')
			->set('style',createCSS($this->dflt_wt+2,$this->dflt_ht))
			->setText(displayName(cleanStr($this->cell['title'])));
		
		$div1->addElement($div2);
		$div1->addElement($overlay);
		$this->anchor->addElement($div1);
		$this->span->addElement($this->anchor);
		return "\n".$this->span;
		
	}
}

	
?>
