<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements MVC form */
abstract class CForm extends CHtmlForm
{
    private $submitted = null;
	private $processed = null;
	private $success = null;
	
	private static $scrollClass = null;
	private static $scrollArgs = null;
	private static $scrollId = null;
	
	/** The constructor */
	public function __construct($action, CController $controller = null, array $attrs = null, $inner = null)
	{
		$url = $controller ? $controller->get_url() : null;
		$save_post = $controller ? $controller->is_save_post_enabled() : false;
		
		if ($controller)
		{
			if (!$attrs) $attrs = array();
			$attrs['method'] = $controller->is_post() ? 'post' : 'get';
		}
		
		parent::__construct($action, $url, $save_post, $attrs, $inner);

		if ($controller) 
		{
		    $this->add_inner(new CHtmlHidden('has___args', $controller->has_args() ? true : false));
		    $this->add_inner(new CHtmlHidden('controller___id', $controller->get_id()));
		    
		    $this->submitted = $controller->is_submitted();
		    $this->processed = $controller->is_processed();
		    $this->success = $controller->is_success();
		    
		    if ($this->checkScrollIntoView($controller) || (!self::$scrollId && $this->submitted))
		    {
		        // Scroll the form into view
		        $st = "var el = document.getElementById(\"{$this->get_id(true)}\");\nel.scrollIntoView(false);";
		        CHtmlPage::get_js()->add_statement($st);
		    }
		}
		
		$args = $controller ? $controller->get_args() : null;
		$this->init_contents($args);
	}
	
	/** Set a form for scrolling into view */
	public static function setScrollIntoView($class, array $args = null)
	{
	    self::$scrollClass = $class;
	    self::$scrollArgs = $args;
	    self::$scrollId = null;
	}
	
	/** Returns id of the element to be scrolled */
	public static function getScrollId()
	{
	    return self::$scrollId;
	}
	
	/** Returns true if the form should be scrolled into view */
	protected function checkScrollIntoView(CController $controller)
	{
	    if (!self::$scrollClass || self::$scrollId) return false;
	    if (!($this instanceof self::$scrollClass)) return false;
	    
	    if (self::$scrollArgs) foreach (self::$scrollArgs as $key=>$val)
	    {
	        if ($controller->get_arg($key) != $val) return false;
	    }
	    
	    self::$scrollId = $this->get_id(true);
	    return true;
	}
	
	/** Inquire if the form is submitted */
	final public function is_submitted() { return $this->submitted; }
	
	/** Inquire if the form is processed */
	final public function is_processed() { return $this->processed; }
	
	/** Inquire if the form is processed with success */
	final public function is_success() { return $this->success; }
	
	/** Initialize the form contents */
	abstract protected function init_contents(array $args);
}
?>