<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Form View */
class FormView extends CHtmlTable
{
	private $horizontal = true;
	
	/** The constructor */
	public function __construct($horizontal = true, array $attrs = null)
	{
		$this->horizontal = $horizontal;
		parent::__construct($attrs);
	}
	
	/** Add form */
	public function add_form($form)
	{
	    if ($form)
	    {
    	    static $attrs = array('style'=>'padding:1px;margin:0;');
    	    
    	    if ($form instanceof CHtmlElement)
    	    {
    	        $form->set_attrs(CHtmlElement::merge_attrs($form->get_attrs(), $attrs));
    	    }
    	    
    	    if ($this->horizontal)
    	    {
    	        $tr = $this->get_inner();
    	        
    	        if (!$tr) $this->add_row($form, $attrs);
    	        else $tr->add_inner(new CHtmlElement('td', $attrs, $form));
    	    }
    	    else
    	    {
    	        $this->add_row($form, $attrs);
    	    }
	    }
	}
}
?>