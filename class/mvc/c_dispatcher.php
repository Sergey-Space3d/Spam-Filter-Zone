<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements MVC form's dispatcher, providing access to the forms */
class CDispatcher
{
	/** The last form's processing success */
	protected $success = true;
	
	/** Collection of MVC directories */
	protected $mvc_dirs = null;
	
	// Initialization -------------------------------------------------------

	/** The constructor */
	protected function __construct()
	{
	}

	/** Returns singleton's instance */
	static public function instance()
	{
	    static $instance = null;
		if (!$instance) $instance = new CDispatcher();
		return $instance;
	}

	/** Get filename by action's name */
	protected function get_filename_by_action($action, $folder, $suffix, $ext = 'php')
	{
	    if (!isset($_SESSION['__class_mvc_dir__']))
	    {
	        // Get the class library folders
	        $libdir = dirname(dirname(__FILE__));
	        $_SESSION['__class_mvc_dir__'] = $this->get_folders($libdir);
	    }
	    
	    if (!$this->mvc_dirs)
	    {
	        $this->mvc_dirs = array('.'); // First, test current folder, then common folders, then class folders
    	    if ($_SESSION['__dcommon_dir__']) $this->mvc_dirs = array_merge($this->mvc_dirs, $_SESSION['__dcommon_dir__']);
    	    if ($_SESSION['__class_mvc_dir__']) $this->mvc_dirs = array_merge($this->mvc_dirs, $_SESSION['__class_mvc_dir__']);
    	    $this->mvc_dirs = array_unique($this->mvc_dirs);
    	    //foreach ($this->mvc_dirs as $path) CLogger::Instance()->log($path);
	    }
	    
	    $subpath = '/'.$folder.'/'.strtolower($action).$suffix.'.'.$ext;
	    
	    foreach ($this->mvc_dirs as $path)
		{
		    // Test MVC folder
		    $filename = $path.$subpath;
		    
		    if (file_exists($filename)) 
		    {
		        //CLogger::Instance()->log("Found file {$filename}");
		        return $filename;
		    }
		}
		
		if ($suffix == '_form' || $suffix == '_view')
		{
		    // Log as error
		    CLogger::Instance('error.log')->log("MVC file is missing: {$subpath}");
		}
		
		return null;
	}
	
	/** Returns top folders under which MVC folders exist */
	protected function get_folders($libdir)
	{
	    $folders = array();
	    
	    foreach (scandir($libdir) as $file)
	    {
	        if ($file != '.' && $file != '..')
	        {
	            $path = "{$libdir}/{$file}";
	            $file = strtolower($file);
	            
	            if ($file == 'view' || $file == 'controller')
	            {
	                $folders[] = $libdir;
	            }
	            else if (is_dir($path))
	            {
	                $subfolders = $this->get_folders($path);
	                if ($subfolders) $folders = array_merge($folders, $subfolders);
	            }
	        }
	    }
	    
	    return $folders;
	}

	/** Get class by action's name */
	protected function get_class_by_action($action, $suffix)
	{
		return ucfirst($action).$suffix;
	}
	
	// Locations ------------------------------------------------------------
	
	/** Get common directory (other than current) */
	public function get_common_dir() { return $_SESSION['__dcommon_dir__']; }
	
	/** Set common directory, without trailing slash (other than current) */
	public function set_common_dir($common_dir) 
	{ $_SESSION['__dcommon_dir__'] = is_array($common_dir) ? $common_dir : array($common_dir); }

	// View -----------------------------------------------------------------

	/** Get view by name. Note, that the input could be the arglist */
	public function get_view($name, $input = null, array $attrs = null, $inner = null)
	{
		if (!$name) return null;
		$filename = $this->get_filename_by_action($name, 'view', '_view');
		if (!$filename) return null;
		
		$class = $this->get_class_by_action($name, 'View');
		if (!$class) return null;
		
		if (!$input) $input = $this->get_view_input($name);
		else if (is_array($input)) $input = $this->get_view_input($name, $input);

		if (!$inner && (!$attrs || !isset($attrs['class'])))
		{
			$style_fname = $this->get_filename_by_action($name, 'view', null, 'css');

			if ($style_fname)
			{
				$inner = new CHtmlStyle(new CHtmlInnerFile($style_fname));
				if (!$attrs) $attrs = array();
				$attrs['class'] = $name;
			}
		}

		require_once($filename);
		return new $class($input, $attrs, $inner);
	}

	/** Get form by action. Note, that the controller could be the arglist */
	public function get_form($action, $controller = null, array $attrs = null, $inner = null)
	{
		if (!$action) return null;
		$filename = $this->get_filename_by_action($action, 'view', '_form');
		if (!$filename) return null;
		
		$class = $this->get_class_by_action($action, 'Form');
		if (!$class) return null;
		
		if (!$controller) $controller = $this->get_controller($action);
		else if (is_array($controller)) $controller = $this->get_controller($action, $controller);

		if (!$inner && (!$attrs || !isset($attrs['class'])))
		{
			$style_fname = $this->get_filename_by_action($action, 'view', null, 'css');

			if ($style_fname)
			{
				$inner = new CHtmlStyle(new CHtmlInnerFile($style_fname));
				if (!$attrs) $attrs = array();
				$attrs['class'] = $action;
			}
		}

		require_once($filename);
		return new $class($action, $controller, $attrs, $inner);
	}
	
	/** Get form with submit button */
	public function get_submit_form($action, array $args = null, array $attrs = null, $inner = null)
	{
		if (!$attrs) $attrs = array();
		if (!$attrs['style']) $attrs['style'] = 'margin:1px;';

		$form = new CHtmlForm($action, null, false, $attrs);
		
		if ($args)
		{
			foreach ($args as $key=>$val)
				$form->add_inner(new CHtmlHidden($key, $val));
		}
		
		if ($inner && ($inner instanceof CHtmlElement)) $form->add_inner($inner);
		else $form->add_inner(new CHtmlSubmit($inner ? $inner : 'Submit'));
		
		return $form;
	}

	// Controller -----------------------------------------------------------

	/** Inquire if form has the request (action) */
	public function has_request()
	{
		return CHtmlForm::get_action() != null;
	}
	
	/** Inquire if the last form's processing is a success */
	public function is_success()
	{
	    return $this->success;
	}
	
	/** Process request */
	public function process_request($action = null, IControllerListener $listener = null)
	{
		if (!$action) $action = CHtmlForm::get_action();
		$controller = $this->get_controller($action);
		if (!$controller) return false;
		if (!$listener) $listener = $this->get_controller_listener($action);
		
		try { $this->success = $controller->process_request($listener); }
		catch (Exception $e) { $this->success = false; }
		
		return $this->success;
	}

	/** Process saved request */
	public function process_saved_request($exclude_action = null)
	{
		$action = CHtmlForm::get_action(); // Remember current action
		$saved_action = CHtmlForm::pop_saved_action(); // Get saved action (will update current value)

		if (!$saved_action || $saved_action == $action || $saved_action == $exclude_action || $this->process_request($saved_action))
			CHtmlForm::pop_saved_page(); // Restore saved page
	}

	/** Get view's input */
	public function get_view_input($action, array $args = null)
	{
		if (!$action) return null;
		$filename = $this->get_filename_by_action($action, 'controller', '_input');
		if (!$filename) return new CDefViewInput($action, $args);
		$class = $this->get_class_by_action($action, 'Input');
		if (!$class) return null;

		require_once($filename);
		$class = new $class($action, $args);
		
		return $class;
	}

	/** Get controller */
	public function get_controller($action, array $args = null)
	{
		if (!$action) return null;
		$filename = $this->get_filename_by_action($action, 'controller', '_controller');
		if (!$filename) return null;
		$class = $this->get_class_by_action($action, 'Controller');
		if (!$class) return null;

		require_once($filename);
		$class = new $class($action, $args);
		
		return $class;
	}

	/** Get controller's listener */
	public function get_controller_listener($action)
	{
		if (!$action) return null;
		$filename = $this->get_filename_by_action($action, 'controller', '_listener');
		if (!$filename) return null;
		$class = $this->get_class_by_action($action, 'Listener');
		if (!$class) return null;

		require_once($filename);
		return new $class();
	}
}
?>