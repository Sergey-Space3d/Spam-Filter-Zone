<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements menu */
class CHtmlMenu extends CHtmlElement
{
	protected $horiz = true;
	protected $cell_root = null;
	
	static protected $pages = array();
	static protected $links = array();
	static protected $handlers = array();
	static protected $args = array();
	
	/** The constructor */
	public function __construct($horiz = true, array $attrs = null)
	{
		$this->horiz = (bool)$horiz;
		
		if ($attrs === null) $attrs = array();
		if (!$attrs['class']) $attrs['class'] = $this->init_def_style();

		parent::__construct('table', $attrs);

		if ($this->horiz)
		{
			// Add table row
		    $this->cell_root = new CHtmlElement('tr', array('class'=>$attrs['class']));
			$this->add_inner($this->cell_root);
		}
		else
		{
			$this->cell_root = $this;
		}
	}
	
	/** Initialize default menu style. Returns class name */
	protected function init_def_style()
	{
	    static $init = false;
	    
	    if (!$init)
	    {
	        $init = true;
	        $el = new CHtmlStyle();
	        
	        $el->add_selector('.menu', 'background-color:#404040;color:#FFFFFF;');
	        $el->add_selector('TABLE.menu', 'margin:0px;border-spacing:0px;padding:0px;');
	        $el->add_selector('TD.menu', 'height:30px;margin:0;border-right:3px solid #404040;padding:0px;text-align:left;vertical-align:middle;background-color:#606060;');
	        $el->add_selector('A.menu', 'margin:0;padding:5px 10px;vertical-align:middle;text-decoration:none;font-size:16px;font-weight:normal;font-family:Arial,Helvetica,sans-serif;');
	        $el->add_selector('A.menu:link, A.menu:visited, A.menu:active', 'background-color:#606060;color:#FFFFFF;display:block;text-decoration:none;');
	        $el->add_selector('A.menu:hover', 'background-color:#808080;color:#FFFFFF;display:block;text-decoration:none;');
	        $el->add_selector('DIV.submenu', 'margin:0;padding-top:2px;padding-left:2px;');
	        $el->add_selector('.selmenuitem', 'color:#FFFFFF !important;background-color:#404040 !important;');
	        
	        $this->add_inner($el);
	    }
	    
	    return 'menu';
	}
	
	/** Add menu item. Returns TD element */
	public function add_item($inner, $href, $page = null, array $args = null)
	{
	    if ($page)
	    {
	        self::$pages[] = $page;
	        
    		if (!isset($_SESSION['main_page']))
    		{
    		    // Initialize current page
    			$_SESSION['main_page'] = $page;
    		}
    		
    		if (is_callable($href))
    		{
    		    self::$handlers[$page] = $href;
    		    if ($args) self::$args[$page] = $args;
    		    $href = './';
    		}
    		
    		$href .= '?page='.$page;
	    }
	    
		// Set style of selected item
		$sel_class = ($page && self::get_page() == $page) ? 'selmenuitem' : null;
		
		// All menu elements share the same class
		$class = implode(' ', array($this->get_attr('class'), $sel_class));
		$attrs = $class ? array('class'=>$class) : array();
		
		// Create link element
		$a = new CHtmlA($href, $attrs, $inner);

		// Create table cell
		$td = new CHtmlElement('td', $attrs, $a);
		$td->set_attr('nowrap', CHtmlElement::NO_VALUE_ATTR);
		
		// Add new cell to the root element
		$cell = $this->horiz ? $td : new CHtmlElement('tr', $attrs, $td);
		$this->cell_root->add_inner($cell);
		
		return $td;
	}
	
	/** Add menu popup. Returns TD element */
	public function add_popup($inner, CHtmlMenu $popup)
	{
	    if (!CHtmlJavaScript::has_function('_ShowPopupMenu_'))
	    {
	        $lines = array(
	            "try {",
	            "var el = document.getElementById(id);",
	            "var td_el = document.getElementById(td_id);",
	            "el.style.display='inline';",
	            "el.style.position='absolute';",
	            "el.style.top=td_el.style.bottom;",
	            "el.style.left=td_el.style.left;",
	            "}",
	            "catch(err){}",
	            "return false;"
	        );
	        CHtmlPage::get_js()->add_function('_ShowPopupMenu_', $lines, array('id', 'td_id'));
	        
	        $lines = array(
	            "var el = document.getElementById(id);",
	            "el.style.display='none';",
	            "return true;"
	        );
	        CHtmlPage::get_js()->add_function('_HidePopupMenu_', $lines, array('id'));
	    }
	    
	    $style = 'display:none;position:fixed;z-index:100;';
	    $popup = new CHtmlElement('div', array('class'=>'submenu', 'style'=>$style), $popup);
	    $id = $popup->get_id(true);
	    
	    // Setup popup event handlers
	    $popup->set_attr('onmouseleave', "_HidePopupMenu_(\"{$id}\")");
	    $popup->set_attr('onclick', "_HidePopupMenu_(\"{$id}\")");
	    $popup->set_attr('onfocusout', "_HidePopupMenu_(\"{$id}\")");
	    
	    // Add menu item and get TD
	    $td = $this->add_item($inner, null);
        $td_id = $td->get_id();
        
	    // Show popup when clicking on cell children
	    $items = $td->get_inner();
	    if (!is_array($items)) $items = array($items);
	    
	    foreach ($items as $item)
	    {
	        if ($item instanceof CHtmlElement)
	        {
                $item->set_attr('onclick', "return _ShowPopupMenu_(\"{$id}\",\"{$td_id}\")");
                $item->set_attr('onmouseover', "_ShowPopupMenu_(\"{$id}\",\"{$td_id}\")");
	        }
	    }
	    
	    $td->set_attr('onmouseleave', "_HidePopupMenu_(\"{$id}\")");
	    
	    // Add popup to the cell
	    $td->add_inner($popup);
	    
	    return $td;
	}

	/** Add link to the page */
	static public function add_link($value, $page)
	{
		self::$links[$value] = $page;
	}

	/** Set current page */
	static public function set_cur_page($page, $handler = null) 
	{
	    $_SESSION['main_page'] = $page;
	    
	    if (CHtmlForm::get_value('page'))
	    {
	        CHtmlForm::set_value('page', $page);
	    }
	    
	    if (is_callable($handler))
	    {
	        self::$handlers[$page] = $handler;
	    }
	}
	
	/** Get current page */
	static public function get_cur_page() { return $_SESSION['main_page']; }

	/** Get page - redirect, posted, get:action, or main */
	static public function get_page()
	{
	    $page = CHtmlForm::get_value('redirect_page');
	    if (!$page) $page = CHtmlForm::get_value('page');
	    if (!$page && $_SERVER['REQUEST_METHOD'] == 'GET') $page = self::$links[$_GET['action']];
	    
	    if (!$page) $page = $_SESSION['main_page'];
	    else $_SESSION['main_page'] = $page;
	    
	    return is_array($page) ? array_pop($page) : $page;
	}
	
	/** Get page contents */
	static public function get_page_contents()
	{
	    $page = self::get_page();

	    if (!$page)
	    {
	        // Something went wrong
	        return new CHtmlElement('div', null, 'Menu page is not found');
	    }
	    else if (self::$pages && !in_array($page, self::$pages))
	    {
	        // Reference to non-existing menu item
	        $page = self::$pages[0];
	        $_SESSION['main_page'] = $page;
	    }
	    
	    if (isset(self::$handlers[$page]))
	    {
	        // Call the menu handler
	        $fn = self::$handlers[$page];
	        $args = self::$args[$page];
	        return $args ? $fn($page, $args) : $fn($page);
	    }
	    
	    if (!file_exists($page))
	    {
            // Switching pages could bring invalid reference to different page
            unset($_SESSION['main_page']);
            return new CHtmlElement('div', null, "Page {$page} is not found");
	    }

	    return new CHtmlInnerFile($page, CHtmlInnerFile::FILE_REQUIRED);
	}
}
?>