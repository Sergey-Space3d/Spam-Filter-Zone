<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements HTML java script */
class CHtmlJavaScript extends CHtmlElement
{
	const PREFIX = "\n<!--\n";
	const SUFFIX = "// -->\n";
	protected $commented = false;
	protected static $functions = array();
	
	/** The constructor */
	public function __construct($inner = null)
	{
		parent::__construct('script', null, $inner);
		$this->set_attr('language', 'javascript');
		$this->set_attr('type', 'text/javascript');
	}
	
	/** Create instance. Returns instance on success, null otherwise */
	public static function create($filename, $required = true)
	{
	    $file = CHtmlInnerFile::create($filename, $required);
	    if (!$file) return null;
	    return new CHtmlJavaScript($file);
	}
	
	/** Add JS statement */
	public function add_statement($statement)
	{
		if ($statement)
		{
			$str = $statement."\n";
			$this->add_inner($str);
		}
	}

	/** Add JS function */
	public function add_function($name, array $lines, array $args = null)
	{
		if (!in_array($name, self::$functions))
		{
			// Remember the name to avoid multiple definitions
			self::$functions[] = $name;
			
			if ($name && count($lines) > 0)
			{
				$str = 'function '.$name.'(';
	
				if ($args)
				{
				    $str .= implode(',', $args);
				}
	
				$str .= "){\n";
	
				foreach ($lines as $line)
					$str .= $line."\n";
	
				$str .= "}\n";
	
				$this->add_inner($str);
			}
		}
	}
	
	/** Inquire if named function is already defined */
	public static function has_function($name)
	{
	    return in_array($name, self::$functions);
	}

	/** Covert instance to string */
	protected function out()
	{
	    if (!isset($this->inner)) return '';
	    
	    if (!$this->commented)
		{
			if (is_array($this->inner))
				array_unshift($this->inner, CHtmlJavaScript::PREFIX);
			else
			{
				$inner = $this->inner;
				$this->inner = array();
				$this->inner[] = CHtmlJavaScript::PREFIX;
				$this->inner[] = $inner;
			}

			$this->inner[] = CHtmlJavaScript::SUFFIX;
			$this->commented = true;
		}

		return parent::out();
	}
}
?>