<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements standard form's layout */
abstract class FormLayout extends CForm
{
    protected static $classes = array();
    protected $m_table = null;
    protected $m_has_title = false;
    protected $align_label = 'right';
    protected $align_ctrl = 'left';
    
    /** The constructor */
    public function __construct($action, CController $controller = null, array $attrs = null, $inner = null)
    {
        if (!CHtmlStyle::has_selector('TABLE.formlayout'))
        {
            $style_el = new CHtmlStyle();
            $this->add_inner($style_el);
            
            $style_el->add_selector('TABLE.formlayout', 'margin:0px;border:2px solid grey;border-spacing:0px;padding:0px;');
            $style_el->add_selector('TD.formlayout', 'padding:3px 5px;outline-width:0px;background-color:#CCCCCC;');
            $style_el->add_selector('TD.formlayout_top', 'padding:10px 5px;outline-width:0px;text-align:center;color:white;background-color:grey;');
            $style_el->add_selector('TD.formlayout_bottom', 'padding:5px;outline-width:0px;background-color:#E6E6E6;');
            $style_el->add_selector('TD.formlayout_error', 'padding:5px;color:red;background-color:#FFFFFF;font-size:11px;');
            $style_el->add_selector('TD.formlayout_hr', 'height:8px;margin:0px;padding:0px;outline-width:0px;background-color:#CCCCCC;');
            $style_el->add_selector('.formlayout_asterisk', 'color:red;font-weight:bold;vertical-align:super;');
            
            self::$classes['form'] = $this->get_css_class('form', 'formlayout', __CLASS__);
            self::$classes['header'] = $this->get_css_class('header', 'formlayout_top', __CLASS__);
            self::$classes['footer'] = $this->get_css_class('footer', 'formlayout_bottom', __CLASS__);
            self::$classes['error'] = $this->get_css_class('error', 'formlayout_error', __CLASS__);
            self::$classes['hr'] = $this->get_css_class('hr', 'formlayout_hr', __CLASS__);
        }
        
        $this->m_table = new CHtmlTable(array('class'=>self::$classes['form'], 'style'=>'border-radius:0;'));
        $this->add_inner($this->m_table);
        
        parent::__construct($action, $controller, $attrs, $inner);
    }
    
    /** Set the title */
    protected function set_title($title)
    {
        if ($title)
        {
            $tr = new CHtmlElement('tr', array('class'=>self::$classes['header']));
            $attrs = array('class'=>self::$classes['header'], 'colspan'=>'100%');
            $this->hotspot = new CHtmlElement('td', $attrs, $title);
            $tr->add_inner($this->hotspot);
            
            if ($this->m_has_title)
            {
                $this->m_table->replace_inner($tr, 0);
            }
            else
            {
                $this->m_table->add_inner($tr);
                $this->m_has_title = true;
            }
        }
    }
    
    /** Add control to the form */
    protected function add_control($label, $ctrl, $horizontal = true, $must_enter = false)
    {
        $tr = new CHtmlElement('tr', array('class'=>self::$classes['form']));
        $this->m_table->add_inner($tr);
        
        if ($must_enter)
        {
            $m_must = new CHtmlElement('span', array('class'=>'formlayout_asterisk'), '*');
            
            $label = new CHtmlElement('span', null, $label);
            $label->add_inner($m_must);
        }
        
        if ($this->is_processed() && !$this->is_success() && $ctrl instanceof CHtmlElement)
        {
            $name = $ctrl->get_attr('name');
            
            if (CHtmlForm::get_error($name))
            {
                $style = $ctrl->get_attr('style');
                $style .= 'background-color:#FFE8DD;';
                $ctrl->set_attr('style', $style);
            }
        }
        
        if ($horizontal)
        {
            // Some labels could be inputs (for ex, checkboxes arranged in two columns)
            $attrs = array('class'=>self::$classes['form'], 'align'=>$this->align_label, 'nowrap'=>'nowrap');
            $tr->add_inner(new CHtmlElement('td', $attrs, $label));
            
            $attrs = array('class'=>self::$classes['form'], 'align'=>$this->align_ctrl, 'nowrap'=>'nowrap');
            $tr->add_inner(new CHtmlElement('td', $attrs, $ctrl));
        }
        else
        {
            $attrs = array('class'=>self::$classes['form'], 'align'=>$this->align_ctrl, 'nowrap'=>'nowrap', 'colspan'=>'100%');
            $td = new CHtmlElement('td', $attrs, $label);
            $td->add_inner(CHtmlTag::instance('br'));
            $td->add_inner($ctrl);
            $tr->add_inner($td);
        }
    }
    
    /** Add control deck */
    protected function add_control_deck($ctrls)
    {
        $tr = new CHtmlElement('tr', array('class'=>self::$classes['footer']));
        $this->m_table->add_inner($tr);
        
        $attrs = array('class'=>self::$classes['footer'], 'colspan'=>'100%', 'align'=>'right');
        $td = new CHtmlElement('td', $attrs);
        $tr->add_inner($td);
        
        $attrs = array('class'=>self::$classes['footer'], 'cellspacing'=>'4');
        $table = new CHtmlTable($attrs);
        $td->add_inner($table);
        
        $tr = new CHtmlElement('tr');
        $table->add_inner($tr);
        
        if (!is_array($ctrls))
        {
            $ctrls = array($ctrls);
        }
        
        foreach($ctrls as $ctrl)
        {
            $td = new CHtmlElement('td');
            $td->add_inner($ctrl);
            $tr->add_inner($td);
        }
        
        if ($this->is_processed() && !$this->is_success())
        {
            $errors = self::get_errors();
            
            if ($errors)
            {
                $this->add_error(implode('<br/>', $errors));
            }
        }
    }
    
    /** Add the error message. Returns TR element */
    protected function add_error($error)
    {
        $attrs = array('class'=>self::$classes['error'], 'colspan'=>'100%');
        return $this->m_table->add_row($error, $attrs, array('class'=>self::$classes['form']));
    }
    
    /** Add horizontal ruler (divider). Returns TR element */
    public function add_hr()
    {
        $el = new CHtmlElement('hr', array('class'=>self::$classes['hr']));
        $attrs = array('colspan'=>'100%', 'class'=>self::$classes['form'], 'style'=>'padding:0;');
        return $this->m_table->add_row($el, $attrs, array('class'=>self::$classes['form']));
    }
    
    /** Add row to table. Returns TR element */
    public function add_row($row, $td_attrs = null, $tr_attrs = null)
    {
        $td_attrs = self::merge_attrs($td_attrs, array('class'=>self::$classes['form']));
        $tr_attrs = self::merge_attrs($tr_attrs, array('class'=>self::$classes['form']));
        if (!is_array($row)) $td_attrs['colspan'] = '100%';
        return $this->m_table->add_row($row, $td_attrs, $tr_attrs);
    }
}
?>