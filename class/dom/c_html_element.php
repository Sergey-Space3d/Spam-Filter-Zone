<?php 
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** The base class for HTML elements */
class CHtmlElement
{
	const NO_VALUE_ATTR = ' ';
	static protected $buffering = true;
	protected $tag;
	protected $attrs;
	protected $inner;
	
	/** The constructor */
	public function __construct($tag, array $attrs = null, $inner = null)
	{
		$this->tag = strtolower($tag);
		if ($attrs) $this->attrs = $attrs;
		if ($inner || (string)$inner === '0') $this->inner = $inner;
	}
	
	/** Get tag */
	public function get_tag() { return $this->tag; }
	
	/** Inquire if the short tag (<tag />) is allowed */
	protected function is_short_tag_allowed() 
	{ 
	    static $tags = array('textarea', 'script', 'table', 'tr', 'td', 'div');
	    return !in_array($this->tag, $tags);
	}
	
	// Buffering (output to screen vs string) -------------------------------
	
	/** Inquire if buffering is enabled */
	static public function is_buffering_enabled() { return self::$buffering; }
	
	/** Enable/disable buffering. If buffering is enabled, the _toString()'s output 
	goes to the screen's buffer, instead of the returned string */
	static public function enable_buffering($enable) { self::$buffering = (bool)$enable; }
	
	// Attributes -----------------------------------------------------------

	public function get_attrs() { return isset($this->attrs) ? $this->attrs : null; }
	public function get_attr($name) { return isset($this->attrs) ? $this->attrs[$name] : null; }
	public function has_attr($name) { return isset($this->attrs) && isset($this->attrs[$name]); }
	public function set_attrs(array $attrs = null) { $this->attrs = $attrs; }
	public function clear_attrs() { if (isset($this->attrs)) unset($this->attrs); }
	
	/** Merge attributes. Returns merged attributes */
	public static function merge_attrs(array $a1 = null, array $a2 = null)
	{
	    if (!$a2) return $a1;
	    if (!$a1) return $a2;
	    
	    $class = ($a1['class'] && $a2['class']) ? "{$a1['class']} {$a2['class']}" : null;
	    $style = ($a1['style'] && $a2['style']) ? "{$a1['style']}{$a2['style']}" : null;
	    
	    $a = array_merge($a1, $a2);
	    
	    if ($class) $a['class'] = $class;
	    if ($style) $a['style'] = $style;
	    
	    return $a;
	}
	
	/** Set named attribute */
	public function set_attr($name, $value)
	{
		if (!is_array($this->attrs))
			$this->attrs = array();
		
		$this->attrs[$name] = $value;
	}
	
	/** Set CSS classes associated with key (class name) */
	final public static function set_css_classes(array $classes, $key = 'default') 
	{
	    if (!$_SESSION['_ext_classes_']) $_SESSION['_ext_classes_'] = array();
	    $_SESSION['_ext_classes_'][strtolower($key)] = $classes;
	}
	
	/** Get CSS class by name, value, and key */
	final public function get_css_class($name, $value = null, $key = null, $merge = false)
	{
	    $key = strtolower($key ? $key : get_class($this));
	    $ext_value = isset($_SESSION['_ext_classes_'][$key]) ? $_SESSION['_ext_classes_'][$key][$name] : null;
	    
	    if (!$ext_value) 
	    {
	        $ext_value = isset($_SESSION['_ext_classes_']['default']) ? $_SESSION['_ext_classes_']['default'][$name] : null;
	        if (!$ext_value) return $value;
	    }
	    
	    if (!$value || !$merge) return $ext_value;
	    return "{$value} {$ext_value}";
	}
	
	// Inner element --------------------------------------------------------
	
	public function get_inner() { return isset($this->inner) ? $this->inner : null; }
	public function get_inner_count() { return isset($this->inner) ? (is_array($this->inner) ? count($this->inner) : 1) : 0; }
	public function set_inner($inner) { $this->inner = $inner; }
	public function clear_inner() { if (isset($this->inner)) unset($this->inner); }

	/** Add the inner element */
	public function add_inner($inner)
	{
		if ($inner)
		{
			if (isset($this->inner))
			{
				if (!is_array($this->inner))
				{
					$val = $this->inner;
					$this->inner = array();
					$this->inner[] = $val;
				}
				
				$this->inner[] = $inner;
			}
			else
				$this->inner = $inner;
		}
	}
	
	/** Insert the inner element at specified index */
	public function insert_inner($inner, $index)
	{
		if ($inner)
		{
			if (!is_array($this->inner))
			{
				$val = $this->inner;
				$this->inner = array();
				$this->inner[] = $val;
			}
			
			$arr = array();
			
			for ($i = 0; $i < count($this->inner); $i++)
			{
				if ($i == $index)
				{
					$arr[] = $inner;
				}
				
				$arr[] = $this->inner[$i];
			}
			
			$this->inner = $arr;
		}
	}
	
	/** Replace the inner element, specified by the index */
	public function replace_inner($inner, $index)
	{
		if (is_array($this->inner))
		{
			if ($index > -1 && $index < count($this->inner))
			{
				$this->inner[$index] = $inner;
			}
		}
		else
		{
			$this->inner = $inner;
		}
    }
	
	/** Make AJAX request and populate inner HTML */
	public function request_inner($url, array $args = null)
	{
	    $js = new CHtmlHttpRequest();
	    $this->add_inner($js);
	    
	    $elid = $this->get_id(true);
	    $fn = "SetInnerHTML{$elid}";
	    
	    $lines = array("var el = document.getElementById('{$elid}');", "el.innerHTML = response;");
	    $js->add_function($fn, $lines, array('response'));
	    $request = CHtmlHttpRequest::make_post_request($url, $args ? $args : array(), $fn);
	    $js->add_statement($request);
	}
	
	// ID -------------------------------------------------------------------
	
	/** Get the id, create unique if "set" is true */
	public function get_id($set = false)
	{
		if (isset($this->attrs['id'])) return $this->attrs['id'];
		return $set ? $this->set_id() : null;
	}
	
	/** Set the id, create unique if not defined */
	public function set_id($id = null)
	{
		if (!isset($this->attrs)) $this->attrs = array();
		$this->attrs['id'] = $id ? $id : self::get_next_id();
		return $this->attrs['id'];
	}
	
	/** Get next unique id */
	static public function get_next_id()
	{
	    if (@session_status() != PHP_SESSION_ACTIVE)
	    {
	        static $id = 0;
	        $id++;
	        return "elx{$id}";
	    }
	    
	    if (!isset($_SESSION['_next_elid_'])) $_SESSION['_next_elid_'] = 0;
	    $_SESSION['_next_elid_']++;
	    return "el{$_SESSION['_next_elid_']}";
	}
	
	// To string ------------------------------------------------------------
	
	/** Convert the element to string */
	public function __toString() { return $this->out(); }
	
	/** Output the element */
	protected function out()
	{
	    $str = "<{$this->tag}";
		
		if (isset($this->attrs))
		{
			foreach($this->attrs as $key=>$val)
			    $str .= " {$key}".($val !== CHtmlElement::NO_VALUE_ATTR ? "='{$val}'" : null);
		}

		self::process_out_str($str);
		
		if (isset($this->inner))
		{
			$str .= ">";
			self::process_out_str($str);
			
			$inners = is_array($this->inner) ? $this->inner : array($this->inner);
			foreach ($inners as $key=>$inner)
			{
			    $this->inner_out($inner, $key, $str);
			    self::process_out_str($str);
			}
			
			$str .= "</{$this->tag}>";
		}
		else if (!$this->is_short_tag_allowed())
		    $str .= "></{$this->tag}>";
		else
			$str .= "/>";
		
		self::process_out_str($str);
		return $str;
	}
	
	/** Output the inner element */
	protected function inner_out($inner, $key, &$str) { $str .= $inner; }

	/** Echo the string if buffering is enabled */
	static protected function process_out_str(&$str)
	{
	    if (self::$buffering && $str)
	    {
	        echo $str;
	        $str = '';
	    }
	}
	
	// Wrapping -------------------------------------------------------------
	
	/** Wrap the inner element into the tag(s) */
	static public function wrap($inner, $tags)
	{
		if (!is_array($tags))
			return new CHtmlElement($tags, null, $inner);
		else
		{
			$el = $inner;
			foreach ($tags as $tag)
				$el = new CHtmlElement($tag, null, $el);
			return $el;
		}
	}
}
?>