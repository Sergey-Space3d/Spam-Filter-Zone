<?php 
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements HTML tag */
class CHtmlTag extends CHtmlElement
{
	static protected $instances = array();
	
	/** The constructor */
	public function __construct($tag) 
	{ 
		parent::__construct($tag);
	}
	
	/** Returns [cashed] instance of tag */
	static public function instance($tag)
	{
		if (!self::$instances[$tag]) 
			self::$instances[$tag] = new CHtmlTag($tag);
		return self::$instances[$tag];
	}
	
	/** Covert instance to string */
	protected function out() 
	{
	    $str = "<{$this->tag}/>";
		self::process_out_str($str);
		return $str; 
	}
}
?>