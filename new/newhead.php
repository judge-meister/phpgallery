<?php

require_once("HtmlTag.class.php");


class Page
{
	private $html=null;
	private $head=null;
	private $body=null;

	private $csslink=null;

	private static $_instance=null;
	
	private function __construct()
	{
		return $this;
	}
	public static function createPage()
	{
		self::$_instance = new Page();
		self::$_instance->setUp();
		return self::$_instance;
	}
	public function setUp()
	{
		$this->htmlpage=HtmlTag::createElement('html');
		$this->head=HtmlTag::createElement('head');
		$this->body=HtmlTag::createElement('body');

		$this->csslink=HtmlTag::createElement('link')->set('rev','stylesheet')->set('type','text/css')
			->set('href','/phpgallery/css/style.css')->set('rel','stylesheet')->set('media','screen,all');
		$this->thumbdiv=HtmlTag::createElement('div')->id('thumbnails')->set('align','center');
		$this->table100=HtmlTag::createElement('table')->set('border',0)->set('width','100%');
		$this->table100tr=HtmlTag::createElement('tr');
		$this->table100trtd=HtmlTag::createElement('td');
		$this->gallerydiv=HtmlTag::createElement('div')->addClass('gallery')->set('align','center');
		$this->thumbtable=HtmlTag::createElement('table')->id('thumbnailstable')->set('cellspacing','0')->set('cellpadding',0);
		$this->thumbtabletr=HtmlTag::createElement('tr');
		$this->thumbtabletrtd=HtmlTag::createElement('td')->set('align','center');
	}
	public function html($content)
	{
		//echo "html\n";
		$this->head->addElement($this->csslink);
		$this->htmlpage->addElement($this->head);
		
		$this->thumbtabletrtd->setText($content);
		$this->thumbtabletr->addElement($this->thumbtabletrtd);
		$this->thumbtable->addElement($this->thumbtabletr);
		$this->gallerydiv->addElement($this->thumbtable);
		$this->table100trtd->addElement($this->gallerydiv);
		$this->table100tr->addElement($this->table100trtd);
		$this->table100->addElement($this->table100tr);
		$this->thumbdiv->addElement($this->table100);
		
		$this->body->addElement($this->thumbdiv);
		$this->htmlpage->addElement($this->body);
		return $this->htmlpage;
	}
}

?>