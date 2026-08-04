<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Base class for MVC input view */
abstract class CViewInput
{
    /** Argument list */
    protected $args = array();
	
	/** The input's action */
	protected $action = null;

	/** The constructor */
	public function __construct($action, array $args = null, $initialize = true)
	{
	    $this->action = $action;
	    
	    if ($args) $this->args = $args;
		if ($initialize) $this->initialize();
	}
	
	/** Initialize the view */
	abstract protected function initialize();
	
	final public function has_args() { return count($this->args) > 0; }
	final public function get_args() { return $this->args; }
	final public function set_args(array $val) { $this->args = $val; }
	final public function add_args(array $val) { $this->args += $val; }
	
	final public function get_arg($name) { return $this->args[$name]; }
	final public function set_arg($name, $val, $is_input = false) 
	{ 
	    if ($is_input) $val = $this->choose_value($val, $name);
	    $this->args[$name] = $val; 
	}
	
	/** Get value - argument, or form's input */
	final public function get_value($name) 
	{
		$val = $this->get_arg($name);
		return $val !== null ? $val : CHtmlForm::get_value($name);
	}
	
	/** Choose between db entry and user input */
	protected function choose_value($db_value, $input_name)
	{
	    $value = CHtmlForm::get_value($input_name);
	    return $value !== null ? $value : $db_value;
	}
}
?>