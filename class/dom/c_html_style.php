<?php 
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements HTML style */
class CHtmlStyle extends CHtmlElement
{
	const PREFIX = "\n<!--\n";
	const SUFFIX = "\n-->\n";
	protected $commented = false;
	protected static $selectors = array();
	
	/** The constructor */
	public function __construct($inner = null)
	{
		parent::__construct('style', null, $inner);
		$this->set_attr('type', 'text/css');
		$this->set_attr('media', 'all');
	}
	
	/** Create instance. Returns instance on success, null otherwise */
	public static function create($filename, $required = true)
	{
	    $file = CHtmlInnerFile::create($filename, $required);
	    if (!$file) return null;
	    return new CHtmlStyle($file);
	}
	
	/** Add selector with properties */
	public function add_selector($selector, $properties)
	{
	    if ($selector && $properties && !in_array($selector, self::$selectors))
	    {
	        self::$selectors[] = $selector;
	        
    	    if (is_array($properties)) 
    	    {
    	        $str = '';
    	        foreach ($properties as $key=>$val) $str .= "{$key}:{$val};";
    	        $properties = $str;
    	    }
    
    	    $this->add_inner("\n{$selector}\n{\n{$properties}\n}");
	    }
	}
	
	/** Inquire if selector is already defined */
	public static function has_selector($selector)
	{
	    return in_array($selector, self::$selectors);
	}
	
	/** Covert instance to string */
	protected function out()
	{
	    if (!isset($this->inner)) return '';
	    
		if (!$this->commented)
		{
			if (is_array($this->inner))
				array_unshift($this->inner, CHtmlStyle::PREFIX);
			else 
			{
				$inner = $this->inner;
				$this->inner = array();
				$this->inner[] = CHtmlStyle::PREFIX;
				$this->inner[] = $inner;
			}
				
			$this->inner[] = CHtmlStyle::SUFFIX;
			$this->commented = true;
		}
		
		return parent::out();
	}
}
?>