<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Base Selector Controller */
abstract class SelectorController extends CController
{
    protected $label;
    protected $append = '';
    
    /** The constructor */
    public function __construct($action, array $args = null, $class)
    {
    	if (!$class) $class = 'Value';
    	
    	if (!class_exists($class)) $this->label = new CDbLabel($class);
        else $this->label = ($class instanceof CDbLabel) ? $class : $class::label();
        
        parent::__construct($action, $args);
    }
    
    /** Returns selection name */
    abstract public function get_sel_name();
    
    /** Returns array of instances */
    abstract protected function get_items();
    
    /** Inquire if "all" selection is available */
    protected function can_all() { return true; }
    
    /** Select item from the list */
    protected function select(array $items) { return key($items); }
    
    /** Called after the item is selected */
    protected function on_selected() {}
    
    /** Initialize the form */
    protected function initialize()
    {
        $arr = array();
        $items = $this->get_items();
        $sel_name = $this->get_sel_name();
        $id = CHtmlForm::get_value($sel_name);
        
        $all = $this->get_arg('all');
        
        if ($all && $items && count($items) > 1 && $this->can_all()) 
        {
            $arr[0] = is_string($all) ? $all : 'All '.$this->label->lower(true);
        }
        
        if ($items)
        {
            $get_name = null;
            
            foreach ($items as $key=>$item)
            {
                if ($item instanceof CDbRecord)
                {
                    if ($get_name === null) $get_name = method_exists($item,'get_name');
                    $name = $get_name ? $item->get_name() : "Item {$item->get_id()}";
                    
                    if ($this->append && !stristr($name, $this->append)) $name = "{$name} {$this->append}";
                    $arr[$item->get_id()] = $name;
                }
                else
                {
                    $arr[$key] = $item;
                }
            }
        }
        
        if (!array_key_exists($id, $arr))
        {
            if ($items && $this->get_arg('select'))
            {
                $id = $this->select($items);
            }
            else if ($arr)
            {
                reset($arr);
                $id = key($arr);
            }
            else
            {
                $id = null;
            }
            
            if (!$this->is_submitted()) CHtmlForm::set_value($sel_name, $id, true);
            $this->on_selected();
        }
        
        $title = $this->get_arg('title');
        if (!$title) $title = $this->label->camel(true);
        $this->set_arg('title', $title);
        
        $this->set_arg('sel_name', $sel_name);
        $this->set_arg('id', $id);
        $this->set_arg('items', $arr);
        
        $this->enable_post($this->get_arg('post'));
    }
    
    /** Process the form */
    protected function process()
    {
        $sel_name = $this->get_sel_name();
        $id = CHtmlForm::get_value($sel_name, false, false);
        CHtmlForm::set_value($sel_name, $id, true);
        
        $this->on_selected();
        return true;
    }
}
?>