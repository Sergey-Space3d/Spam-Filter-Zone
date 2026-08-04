<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements 'FORM' HTML element */
class CHtmlForm extends CHtmlElement
{
	const GENERIC_ERROR = 0;
	static protected $errors = array();
	static protected $volatile_keys = array('action', 'saved_action', 'page', 'saved_page', 'redirect_page');
	
    protected $hints = array();
    protected $hotspot = null;
    
    /** The constructor */
    public function __construct($name = null, $url = null, $save_post = false, array $attrs = null, $inner = null)
	{
		parent::__construct('form', $attrs, $inner);

		if (!$name) $name = get_class($this);
		$this->set_attr('name', $name);

		if (!$url) $url = CHtmlPage::get_home_page();
		$this->set_attr('action', $url);

		if (!$this->has_attr('target')) $this->set_attr('target', '_self');
		if (!$this->has_attr('method')) $this->set_attr('method', 'get');
		if (!$this->has_attr('enctype')) $this->set_attr('enctype', 'application/x-www-form-urlencoded');

		if ($save_post)
		{
			// Determine source of previous entries
			$arr = $_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST : $_GET;

			if (is_array($arr))
			{
				// Save action
				$action = self::get_value('saved_action');
				if (!$action) $action = self::get_value('action');
				if ($action) $this->add_inner(new CHtmlHidden('saved_action', $action));
				
				// Save page
				$page = self::get_value('saved_page');
				if (!$page) $page = self::get_value('page');
				if ($page) $this->add_inner(new CHtmlHidden('saved_page', $page));
				
				// Save previous form entries
				$this->array_to_hidden($arr);
			}
		}
		
		if (is_array($_SESSION['_common_fields_']))
		{
		    // Make hidden elements from common fields
		    $this->array_to_hidden($_SESSION['_common_fields_']);
		}

		if (!$_SERVER['HTTPS'] && stristr($url, 'https:')) // Save the session data
			$this->add_inner(new CHtmlHidden('session_encoded', session_encode()));

		$this->add_inner(new CHtmlHidden('action', $name));
	}
	
	/** Make hidden elements from array */
	protected function array_to_hidden(array $arr)
	{
	    foreach ($arr as $key=>$val)
	    {
	        if (is_array($val))
	        {
	            foreach ($val as $k=>$v)
	                $this->add_inner(new CHtmlHidden($key.'[]', $v));
	        }
	        else if (!in_array($key, self::$volatile_keys))
	            $this->add_inner(new CHtmlHidden($key, $val));
	    }
	}
	
	/** Setup control with hint */
	protected function setup_hint(CHtmlElement $ctrl, $def, $last = false)
	{
	    if ($ctrl instanceof CHtmlInput)
	    {
	        CHtmlInput::init_placeholder_attr($ctrl, 'placeholder', $def);
	    }
	    else
	    {
	        CHtmlInput::init_placeholder_attr($ctrl, 'title', $def);
	    }

	    if (!CHtmlJavaScript::has_function('_HideCtrlHint_'))
	    {
	        $lines = array();
            $lines[] = "var el = document.getElementById(name);";
            $lines[] = "if (el)";
            $lines[] = "{";
            $lines[] = "el.style.color = 'black';";
            $lines[] = "if (el.value == hint)";
            $lines[] = "{";
            $lines[] = "el.value = '';";
            $lines[] = "}";
            $lines[] = "if (pwd_ids.indexOf(name) != -1)";
            $lines[] = "{";
            $lines[] = "el.type = 'password';";
            $lines[] = "}";
            $lines[] = "}";
            
            CHtmlPage::get_js()->add_function('_HideCtrlHint_', $lines, array('name', 'hint'));
            
            $lines = array();
            $lines[] = "var el = document.getElementById(name);";
            $lines[] = "if (el)";
            $lines[] = "{";
            $lines[] = "if (el.value == '')";
            $lines[] = "{";
            $lines[] = "if (el.type == 'password')";
            $lines[] = "{";
            $lines[] = "el.type = 'text';";
            $lines[] = "if (pwd_ids.indexOf(name) == -1)";
            $lines[] = "{";
            $lines[] = "pwd_ids.push(name);";
            $lines[] = "}";
            $lines[] = "}";
            $lines[] = "el.value = hint;";
            $lines[] = "el.style.color = 'grey';";
            $lines[] = "}";
            $lines[] = "}";
            
            CHtmlPage::get_js()->add_function('_ShowCtrlHint_', $lines, array('name', 'hint'));
            
            CHtmlPage::get_js()->add_statement('var pwd_ids = [];');
	    }
	    
	    $id = $ctrl->get_id(true);
	    
	    $onClick = "_HideCtrlHint_(\"{$id}\", \"{$def}\");";
	    $ctrl->set_attr('onclick', $onClick);
	    $ctrl->set_attr('onkeydown', $onClick);
	    
	    $onFocusOut = "_ShowCtrlHint_(\"{$id}\", \"{$def}\");";
	    $ctrl->set_attr('onfocusout', $onFocusOut);
	    
	    CHtmlPage::get_js()->add_statement("_ShowCtrlHint_(\"{$id}\", \"{$def}\");");
	    
	    $this->hints[] = "_HideCtrlHint_(\"{$id}\", \"{$def}\");";
	    
	    if ($last)
	    {
	        static $next_fn = 0;
	        $fn_name = 'ClearCtrlHints'.$next_fn;
	        $next_fn++;
	        
	        CHtmlPage::get_js()->add_function($fn_name, $this->hints);
	        $this->set_attr('onsubmit', "{$fn_name}();");
	    }
	}
	
	/** Set confirmation on submit */
	protected function set_confirm($text)
	{
	    if (!CHtmlJavaScript::has_function('_ConfirmForm_'))
	    {
	        $lines = array(
	            "if (!confirm(msg)) {",
	            "var form = document.getElementById(id);",
	            "if (form && typeof _EnableSubmit_ == 'function') {",
	            "var btns = form.getElementsByTagName('button');",
	            "for (var i = 0; i < btns.length; i++) {",
	            "if (btns[i].getAttributeNode('type').value == 'submit') {",
	            "var fn = function(bid){setTimeout(function(){_EnableSubmit_(bid, true);},0);};",
	            "fn(btns[i].id);",
	            "}}}",
	            "return false;",
	            "}",
	            "return true;",
	            );
	        
	        CHtmlPage::get_js()->add_function('_ConfirmForm_', $lines, array('id', 'msg'));
	    }
	    
	    $call = "return _ConfirmForm_(\"{$this->get_id(true)}\",\"{$text}\")";
	    $onsubmit = $this->get_attr('onsubmit');
	    $onsubmit = $onsubmit ? "{$onsubmit};{$call}" : $call;
	    $this->set_attr('onsubmit', $onsubmit);
	}
	
	/** Get hotspot element (optional) - place for user interaction, for ex., "close" icon */
	public function get_hotspot() { return $this->hotspot; }
	
	/** Pop saved action */
	static public function pop_saved_action()
	{
		$ret = self::get_value('saved_action');
		if ($ret) self::set_value('action', $ret);
		return $ret;
	}

	/** Pop saved page */
	static public function pop_saved_page()
	{
		$ret = self::get_value('saved_page');
		if ($ret) self::set_value('page', $ret);
		return $ret;
	}

	/** Get action */
	static public function get_action()
	{
		return self::get_value('action');
	}

	/** Set volatile key */
	static public function set_volatile_key($key)
	{
		self::$volatile_keys[] = $key;
	}

	/** Get form field's value */
	static public function get_value($name, $postonly = false, $use_session = true)
	{
	    $value = null;

	    if ($_SERVER['REQUEST_METHOD'] == 'POST') $value = isset($_POST[$name]) ? $_POST[$name] : null;
	    else if (!$postonly) $value = isset($_GET[$name]) ? $_GET[$name] : null;

	    if ($value === null && $use_session && isset($_SESSION[$name])) $value = $_SESSION[$name];

	    if ($value === null)
	    {
	        $name .= '__input'; // Check input placeholder
	        if ($_SERVER['REQUEST_METHOD'] == 'POST') $value = isset($_POST[$name]) ? $_POST[$name] : null;
	        else if (!$postonly) $value = isset($_GET[$name]) ? $_GET[$name] : null;
	    }

	    return $value;
	}

	/** Set form field's value */
	static public function set_value($name, $value, $use_session = false)
	{
	    if ($use_session)
	        $_SESSION[$name] = $value;
	    
		if ($_SERVER['REQUEST_METHOD'] == 'POST') 
			$_POST[$name] = $value;
		else 
			$_GET[$name] = $value;
	}
	
	/** Get the hidden value common for all forms */
	static public function get_common_field($name)
	{
	    if (!$_SESSION['_common_fields_']) return null;
	    return $_SESSION['_common_fields_'][$name];
	}
	
	/** Set the hidden value common for all forms */
	static public function set_common_field($name, $value)
	{ 
	    if (!$_SESSION['_common_fields_']) $_SESSION['_common_fields_'] = array();
	    $_SESSION['_common_fields_'][$name] = $value; 
	}
	
	/** Set the hidden values common for all forms */
	static public function set_common_fields(array $fields){ $_SESSION['_common_fields_'] = $fields; }
	
	/** Set placeholder for input elements that woun't be present in post/get array if not selected */
	static public function set_input_placeholder($name) { self::set_common_field("{$name}__input", ''); }
	
	// Last error -----------------------------------------------------------

	/** Get all errors (result of processing the last form) */
	static public function get_errors()
	{
	    return self::$errors;
	}

	/** Get named error */
	static public function get_error($name = CHtmlForm::GENERIC_ERROR)
	{
		return isset(self::$errors[$name]) ? self::$errors[$name] : null;
	}

	/** Set named error */
	static public function set_error($error, $name = CHtmlForm::GENERIC_ERROR)
	{
	    self::$errors[$name] = $error;
	}
}
?>