<?php

class Overlay
{
	private static $_instance = null;
	private static $ovly = null;
	
	private function __construct() {
		return $this;
	}
	public static function create(){
		self::$_instance = new Overlay();
		return self::$_instance;
	}
	public function mkBtn($img,$offset)
	{
		self::$ovly = HtmlTag::createElement('div')->set('style',CssStyle::createStyle()->set('margin-top',$offset.'px'));
		self::$ovly->addElement('img')->set('class','playbutton');//->set('src',$img)
		return $this;
	}
	//public function mkTimeLabel($min, $secs, $offset)
	public function mkTimeLabel($timestr, $offset)
	{
		self::$ovly = HtmlTag::createElement('div')->addClass('caption_offset');
		//	set('style',CssStyle::createStyle()->set('margin-top',$offset.'px'));
		$o = HtmlTag::createElement('div')->addClass('caption_format')
				->setText($timestr);
		self::$ovly->addElement($o);
		return $this;
	}
	public function mkLabel($text, $offset, $css_class="caption_format")
	{
		self::$ovly = HtmlTag::createElement('div')->addClass('caption_offset');
		//	set('style',CssStyle::createStyle()->set('margin-top',$offset.'px'));
		$o = HtmlTag::createElement('div')->addClass($css_class)
				->setText($text);
		self::$ovly->addElement($o);
		return $this;
	}
	public function html()
	{
		return self::$ovly;
	}
}


?>
