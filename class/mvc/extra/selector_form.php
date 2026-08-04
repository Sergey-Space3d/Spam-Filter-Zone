<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Base Selector Form */
class SelectorForm extends CForm
{
    /** Initialize form contents */
    protected function init_contents(array $args)
    {
        @extract($args);
        
        $this->add_inner(new CHtmlHidden('sel_name', $sel_name));
        
        $page = self::get_value('page');
        if ($page) $this->add_inner(new CHtmlHidden('page', $page));
        
        if ($items)
        {
            $attrs = array('title'=>$title);
            if ($width > 0) $attrs['style'] = "width:{$width}px;";
            if (count($items) == 1)
            {
                $attrs['disabled'] = 'disabled';
                $attrs['style'] .= "opacity:1.0;appearance:none;";
            }
            
            $ctrl = new CHtmlCbox($sel_name, $id, $attrs);
            $this->add_inner($ctrl);
            
            $ctrl->set_attr('onchange', 'this.form.submit();');
            $ctrl->add_items($items);
            
            $elid = $ctrl->get_id(true);
            CHtmlPage::get_js()->add_statement("_RefreshSelectedIndex_(\"{$elid}\", \"{$id}\");");
            
            if (!CHtmlJavaScript::has_function('_RefreshSelectedIndex_'))
            {
                $lines = array(
                    "var el = document.getElementById(elid);",
                    "setTimeout(function() {",
                    "for (var i = 0; i < el.options.length; i++) {",
                    "if (el.options[i].value == value) {",
                    "el.selectedIndex = i;",
                    "break;",
                    "}}}, 0);",
                );
                
                CHtmlPage::get_js()->add_function('_RefreshSelectedIndex_', $lines, array('elid', 'value'));
            }
        }
    }
}
?>