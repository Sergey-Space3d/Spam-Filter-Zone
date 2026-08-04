<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Toggles visibility of the form and external elements (other forms, buttons, etc.) */
class CollapsedForm extends CHtmlElement
{
    protected static $classes = array();
    protected $form = null;
    protected $button = null;
    
    public function get_form() {return $this->form; }
    public function get_button() {return $this->button; }
    
    /** The constructor */
    public function __construct(CForm $form, $button_label, array $button_attrs = null, $init_display = true)
    {
        parent::__construct('span');
        
        $this->form = $form;
        $this->add_inner($form);
        
        $this->button = new CHtmlSubmit($button_label, $button_attrs);
        $this->add_inner($this->button);
        
        if (!CHtmlStyle::has_selector('.cf_close_icon'))
        {
            $style_el = new CHtmlStyle();
            $this->add_inner($style_el);
            
            $style = 'position:absolute;right:7px;top:4px;z-index:100;background-color:#808080;color:white;';
            $style .= 'margin:0px;padding:1px;border:0px none;cursor: pointer;text-align:center;vertical-align:middle;';
            $style_el->add_selector('.cf_close_icon', $style);
            self::$classes['close_icon'] = $this->get_css_class('close_icon', 'cf_close_icon', __CLASS__, true);
        }
        
        if (!CHtmlJavaScript::has_function('_ShowColElement_'))
        {
            $lines = array(
                "var el = document.getElementById(id);",
                "if (el) {",
                "if (show == true) {",
                "el.style.display = 'inline';",
                "if (into_view) {",
                "el.scrollIntoView(false);",
                "}",
                "}",
                "else {",
                "el.style.display = 'none';",
                "}",
                "}",
            );
            CHtmlPage::get_js()->add_function('_ShowColElement_', $lines, array('show', 'id', 'into_view'));
        }

        if ($init_display)
        {
            self::init_display($this);
        }
    }
    
    /** Initialize display - show/hide the elements */
    public static function init_display(CollapsedForm $collapsed = null, array $toggle_forms = null, $collapse = false)
    {
        if ($collapsed)
        {
            $toggle_ids = array();
            
            if ($toggle_forms)
            {
                foreach ($toggle_forms as $f)
                {
                    if ($f && $f != $collapsed)
                    {
                        $toggle_ids[] = $f->get_id(true);
                    }
                }
            }
            
            $form_id = $collapsed->form->get_id(true);
            $button_id = $collapsed->button->get_id(true);
            $toggle_ids[] = $button_id;
            
            $form_inner = $collapsed->form->get_hotspot();
            
            if ($form_inner)
            {
                // Clicking on the title will collapse the form
                $js_calls = $collapsed->make_js_calls(false, $form_id, $toggle_ids);
                $form_inner->set_attr('onclick', $js_calls);
                
                // Make relative position for parental element, to be used as an anchor
                $style = $form_inner->get_attr('style');
                $style .= 'position:relative;';
                $form_inner->set_attr('style', $style);
                
                $el = new CHtmlElement('span', array('class'=>self::$classes['close_icon']), '&nbsp;x&nbsp;');
                $form_inner->add_inner($el);
            }
            
            // Clicking on the button will expand the form
            $js_calls = $collapsed->make_js_calls(true, $form_id, $toggle_ids);
            $collapsed->button->set_attr('onclick', $js_calls);
            
            $expand_form = $collapsed->form->is_processed() && (!$collapse || !$collapsed->form->is_success());
            
            if (!$expand_form) 
            {
                $collapsed->form->set_attr('style', $collapsed->form->get_attr('style').'display:none;');
                
                if (CForm::getScrollId() == $form_id || $collapsed->form->is_processed())
                {
                    // Scroll the button into view
                    $st = "var el = document.getElementById(\"{$button_id}\");\nel.scrollIntoView(false);";
                    CHtmlPage::get_js()->add_statement($st);
                }
            }
            
            $toggle_ids = $expand_form ? $toggle_ids : array($form_id);
            $js_calls = $collapsed->make_js_statement($toggle_ids);
            CHtmlPage::get_js()->add_statement($js_calls);
        }
    }
    
    /** Make set of JS calls, to show/hide the elements */
    protected function make_js_calls($show_form, $form_id, array $toggle_ids)
    {
        $show_form = $show_form ? 'true' : 'false';
        $show_toggled = ($show_form == 'true') ? 'false' : 'true';
        
        $str = "_ShowColElement_({$show_form},\"{$form_id}\",{$show_form});";
        
        foreach ($toggle_ids as $id)
        {
            $str .= "_ShowColElement_({$show_toggled},\"{$id}\");";
        }
        
        return $str;
    }
    
    /** Make set of JS calls as a statement, to hide the elements */
    protected function make_js_statement(array $hide_ids)
    {
        $str = '';
        
        foreach ($hide_ids as $id)
        {
            $str .= "_ShowColElement_(false, \"{$id}\");\n";
        }
        
        return $str;
    }
}
?>