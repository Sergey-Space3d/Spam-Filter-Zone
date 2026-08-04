<?php 
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements MVC form's controller */
abstract class CController extends CViewInput
{
    private $id = '';
    private $url = null;
	private $is_post = true;
	private $save_post = false;
	private static $success = null;
	private static $processed = null;
	
	/** The constructor */
	public function __construct($action, array $args = null)
	{
	    if (isset($args['url'])) $this->url = $args['url'];
	    parent::__construct($action, $args, false);

	    foreach ($this->args as $key=>$val)
	    {
	        if (strlen($this->id) > 128) break;
	        if (is_numeric($val) || is_string($val)) $this->id .= "{$key}{$val}";
	    }
	    
        $initialize = !$this->is_submitted() || $this->is_processed();
        if ($initialize) $this->initialize();
	}
	
	/** Initialize the form */
	protected function initialize() {}
	
	// Form's input ---------------------------------------------------------
	
	final public function get_url() { return $this->url; }
	final public function is_post() { return $this->is_post; }
	final public function is_save_post_enabled() { return $this->save_post; }

	final public function set_url($val) { $this->url = $val; }
	final public function enable_post($val) { $this->is_post = (bool)$val; }
	final public function save_post($val) { $this->save_post = (bool)$val; }
	
	// Form's processing ----------------------------------------------------
	
	/** Choose between db entry and user input */
	protected function choose_value($db_value, $input_name)
	{
	    if (!$this->is_processed() || $this->is_success()) return $db_value !== null ? $db_value : '';
	    return parent::choose_value($db_value, $input_name);
	}
	
	/** Get the controller's id */
	final public function get_id() { return $this->id; }
	
	/** Match form id. The arglist may grow after adding arguments from controller */
	protected function match_id()
	{
	    return $this->id == CHtmlForm::get_value('controller___id', false, false);
	}
	
	/** Inquire if the form is submitted */
	public function is_submitted()
	{
	    if ($this->action != CHtmlForm::get_action()) return false;
	    if (!CHtmlForm::get_value('has___args', false, false) || !$this->has_args()) return true;
	    return $this->match_id();
	}
	
	/** Inquire if the request is processed */
	public function is_processed() 
	{
	    return $this->action == self::$processed && $this->match_id(); 
	}
	
	/** Inquire if the request is processed with success */
	public function is_success() 
	{
	    return $this->action == self::$success && $this->match_id(); 
	}
	
	/** Process the request */
	public function process_request(IControllerListener $listener = null)
	{
		if ($listener) $listener->on_start();
		
		if ($this->process())
		{
			if ($listener) $listener->on_success();
			self::$processed = $this->action;
			self::$success = $this->action;
			return true;
		}
		
		if ($listener) $listener->on_failure();
		self::$processed = $this->action;
		self::$success = null;
		return false;
	}
	
	/** Process the request (to be overridden) */
	abstract protected function process();
}
?>