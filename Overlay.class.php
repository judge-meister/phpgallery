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
		$this->ovly = HtmlTag::createElement('div')->set('style',CssStyle::createStyle()->set('margin-top',$offset.'px'));
		$this->ovly->addElement('img')->set('src',$img);
		return $this;
	}
	public function mkLabel($min, $secs)
	{
		$this->ovly = HtmlTag::createElement('div')->set('style',CssStyle::createStyle()->set('margin-top','5px'));
		$o = HtmlTag::createElement('div')->addClass('caption_format')
			->set('style',CssStyle::createStyle()->set('font-size','160%'))
				->setText($min.':'.$secs.' mins');
		$this->ovly->addElement($o);
		return $this;
	}
	public function html()
	{
		return $this->ovly;
	}
}


?>