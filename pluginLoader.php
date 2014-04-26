<?php

interface iPlugin
{
	public function isWhole();
	public function html();
	public function doPage($path);
	public function isActive($path);
}

class PluginLoader
{
	private $plugins = array();
	private $whole = false;
	private $html = "";
	
	function __construct()
	{
		foreach(scandir(PLUGINS_DIR) as $plugin)
		{
			if(substr($plugin, -strlen(".php")) === ".php")
			{
				$className = substr($plugin, 0, -strlen(".php"));
				$this->plugins[] = $className;
			}
		}
	}
	public function getPage($path)
	{
		if(count($this->plugins) > 0)
		{
			foreach($this->plugins as $className)
			{
				if(!$this->whole)
				{
					require_once(PLUGINS_DIR.$className.'.php');
					$class = new $className();
					if($class->isActive($path))
					{
						$class->doPage($path);
						$this->whole = $class->isWhole();
						$this->html .= $class->html();
					}
				}
			}
		}
	}
	public function isWhole() { return $this->whole; }
	public function html()    { return $this->html; }
}

?>