<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements submit form's element */
class CHtmlSubmit extends CHtmlElement
{
    /** The constructor */
    public function __construct($value, array $attrs = null)
    {
        $attrs['type'] = 'submit';
        parent::__construct('button', $attrs, CHtmlInput::normalize($value));
    }
    
    /** Disable button when clicked */
    public function disable_on_click()
    {
        if (!CHtmlJavaScript::has_function('_EnableSubmit_'))
        {
            $lines = array(
                "var el = document.getElementById(id);",
                "if (enable) {",
                "el.disabled=false;",
                "}",
                "else {",
                "el.disabled=true;", // TODO: temporary measure to re-enable submit if form failed
                "setTimeout(function(){_EnableSubmit_(id, true);},2000);",
                "}",
            );
            CHtmlPage::get_js()->add_function('_EnableSubmit_', $lines, array('id', 'enable'));
        }
        
        $call = "setTimeout(function(){_EnableSubmit_(\"{$this->get_id(true)}\", false);},0)";
        $onclick = $this->get_attr('onclick');
        $onclick = $onclick ? "{$onclick};{$call}" : $call;
        $this->set_attr('onclick', $onclick);
    }
}
?>