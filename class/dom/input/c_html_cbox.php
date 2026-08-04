<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements combobox form's element */
class CHtmlCbox extends CHtmlElement
{
    protected $sel_item;
    
    /** The constructor */
    public function __construct($name, $sel_item = null, array $attrs = null, $inner = null)
    {
        parent::__construct('select', $attrs, $inner);
        $this->set_attr('name', $name);
        $this->sel_item = $sel_item === null ? CHtmlForm::get_value($name) : $sel_item;
        CHtmlInput::init_placeholder_attr($this, 'title');
    }
    
    /** Add item to combobox */
    public function add_item($name, $key = null)
    {
        if ($name)
        {
            $name = CHtmlInput::normalize($name);
            if ($key === null) $key = $name;
            if (!is_array($this->inner)) $this->inner = array();
            $this->inner[$key] = $name;
        }
    }
    
    /** Add items to combobox */
    public function add_items($items)
    {
        if (is_array($items))
        {
            foreach ($items as $key=>$name)
            {
                $this->add_item($name, $key);
            }
        }
    }
    
    /** Submit on change */
    public function submit_onchange()
    {
        $this->set_attr('onchange', 'this.form.submit()');
    }
    
    /** Refresh page on cbox on change */
    public function refresh_onchange()
    {
        if (!CHtmlJavaScript::has_function('_RefreshOnCBoxChange_'))
        {
            $lines = array(
                "var el = document.getElementById(elid);",
                "if (self.location.href.indexOf('?') == -1)",
                "{",
                "self.location=self.location + '?' + el.name + '=' + el.value;",
                "}",
                "else",
                "{",
                "var arg = el.name + '=' + el.value;",
                "var arr = self.location.href.split('?');",
                "var href = arr[0] + '?';",
                "var args = arr[1].split('&');",
                "var index = args.findIndex(function(v){ return v.search(el.name) > -1; });",
                "if (index == -1)",
                "{",
                "args.push(arg);",
                "}",
                "else",
                "{",
                "args[index] = arg;",
                "}",
                "self.location.href = href + args.join('&');",
                "}",
            );
            
            CHtmlPage::get_js()->add_function('_RefreshOnCBoxChange_', $lines, array('elid'));
        }
        
        $this->set_attr('onchange', "_RefreshOnCBoxChange_(\"{$this->get_id(true)}\")");
    }
    
    /** Add inner element to combobox */
    public function add_inner($inner)
    {
        if (($inner instanceof CHtmlElement) && $inner->get_tag() == 'option')
            $this->add_item($inner->get_inner(), $inner->get_attr('value'));
            else
                $this->add_item($inner);
    }
    
    /** Make string for the item */
    protected function inner_out($inner, $key, &$str)
    {
        $selected = (!is_null($this->sel_item) && $key == $this->sel_item) ? "selected='selected'" : null;
        $str .= "<option value='".$key."' ".$selected.">".$inner."</option>";
    }
}
?>