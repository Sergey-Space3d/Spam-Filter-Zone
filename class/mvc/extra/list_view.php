<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** List View */
abstract class ListView extends CView
{
    protected static $classes = array();
    protected $theme_items = null;
    
    /** Returns headline title */
    abstract protected function get_headline_title();
    
    /** Returns array of listed items  */
    abstract protected function get_items($obj, &$objs);
    
    /** Returns theme items */
    protected function get_theme_items() { return 1; }
    
    /** Returns toolbar items */
    protected function get_toolbar_items() { return null; }
    
    /** Returns list title */
    protected function get_title($obj) { return null; }
    
    /** Returns column names */
    protected function get_column_names() { return null; }
    
    /** Set column attributes */
    protected function set_column_attrs(CHtmlTable $table) {}
    
    /** Set row attributes */
    protected function set_row_attrs($obj, CHtmlElement $tr) {}
    
    /** Initialize view contents */
    protected function init_contents(array $args)
    {
        if (!CHtmlStyle::has_selector('TABLE.listview_page'))
        {
            $style_el = new CHtmlStyle();
            $this->add_inner($style_el);
            
            $style_el->add_selector('TABLE.listview_page', 'width:600px;');
            $style_el->add_selector('TD.listview_page', 'padding-bottom:16px;');
            $style_el->add_selector('DIV.listview_headline', 'padding:10px;border:1px solid #888888;box-shadow: 0 0 3px 3px #888888;border-radius:5px;font-size:18px;font-weight:bold;text-align:center;white-space:nowrap;background-color:#EEEEEE;color:#666666;');
            $style_el->add_selector('.listview_selector', 'width:100%;padding:4px 10px;background-color:#F0F0F0;');
            $style_el->add_selector('DIV.listview', 'width:100%;max-height:1200px;margin-bottom:16px;overflow:auto;display:block;border:1px solid #B0B0B0;');
            $style_el->add_selector('TABLE.listview', 'width:100%;border-top:1px solid grey;border-left:1px solid grey;');
            $style_el->add_selector('TD.listview', 'padding:4px 8px;border-bottom:1px solid grey;border-right:1px solid grey;white-space:nowrap;');
            $style_el->add_selector('.listview_title', 'padding:6px 8px;text-align:center;background-color:#E0E0E0;');
            $style_el->add_selector('TD.listview_column_names', 'padding:4px 12px 4px 12px;border-right:1px solid #888888;border-bottom:1px solid #888888;text-align:center;font-weight:bold;white-space:nowrap;');
            
            self::$classes['page'] = $this->get_css_class('page', 'listview_page', __CLASS__);
            self::$classes['headline'] = $this->get_css_class('headline', 'listview_headline', __CLASS__);
            self::$classes['toolbar'] = $this->get_css_class('toolbar', 'listview_selector', __CLASS__);
            self::$classes['table'] = $this->get_css_class('table', 'listview', __CLASS__);
            self::$classes['header'] = $this->get_css_class('header', 'listview_title', __CLASS__);
            self::$classes['column_names'] = $this->get_css_class('column_names', 'listview_column_names', __CLASS__);
        }
        
        $attrs = array('class'=>self::$classes['page']);
        $table = new CHtmlTable($attrs);
        $this->add_inner($table);
        
        $headline = $this->get_headline_title();
        if ($headline)  $table->add_row(new CHtmlElement('div', array('class'=>self::$classes['headline']), $headline), $attrs);
        
        $toolbar = $this->make_toolbar();
        if ($toolbar) $table->add_row($toolbar, $attrs);

        $this->theme_items = $this->get_theme_items();
        
        if ($this->theme_items)
        {
            if (!is_array($this->theme_items)) $this->theme_items = array($this->theme_items);
            
            if (count($this->theme_items) == 1 && ($title = $this->get_title(current($this->theme_items))))
            {
                // Only one table - make the title non-scrollable
                $title = new CHtmlElement('div', array('class'=>self::$classes['header']), $title);
                $table->add_row($title, array_merge($attrs, array('style'=>'padding-bottom:0px;')));
            }
        }
        
        $list = $this->make_list();
        if ($list) $table->add_row($list, $attrs);
    }
    
    /** Make the toolbar */
    protected function make_toolbar()
    {
        $items = $this->get_toolbar_items();
        
        if ($items)
        {
            $table = new CHtmlTable(array('class'=>self::$classes['toolbar']));
            $table->add_row($items);
            return $table;
        }
        
        return null;
    }
    
    /** Make the list */
    protected function make_list()
    {
        if ($this->theme_items)
        {
            $div = new CHtmlElement('div', array('class'=>self::$classes['table']));
            
            $id = $div->get_id(true);
            CHtmlPage::get_js()->add_statement("_SetListViewScroll_(\"{$id}\");");
            
            if (!CHtmlJavaScript::has_function("_SetListViewScroll_"))
            {
                $lines = array(
                    "var el = document.getElementById(id);",
                    "if (el.scrollWidth > el.clientWidth || el.scrollHeight > el.clientHeight) {",
                    //"el.scrollLeft = el.scrollWidth - el.clientWidth;",
                    "el.addEventListener('focusout', (event)=>{ _EndDragListView_(id, event); });",
                    "el.addEventListener('mouseup', (event)=>{ _EndDragListView_(id, event); });",
                    "el.addEventListener('mouseleave', (event)=>{ _EndDragListView_(id, event); });",
                    "el.addEventListener('mousedown', (event)=>{ _StartDragListView_(id, event); });",
                    "el.addEventListener('mousemove', (event)=>{ _MoveDragListView_(id, event); });",
                    "}",
                );
                CHtmlPage::get_js()->add_function("_SetListViewScroll_", $lines, array('id'));
                
                $lines = array(
                    "var el = document.getElementById(id);",
                    "if (el.clientWidth > event.offsetX && el.clientHeight > event.offsetY) {",
                    "listMouseDown = true;",
                    "}",
                );
                CHtmlPage::get_js()->add_function("_StartDragListView_", $lines, array('id', 'event'));
                
                $lines = array(
                    "listMouseDown = false;",
                );
                CHtmlPage::get_js()->add_function("_EndDragListView_", $lines, array('id', 'event'));

                $lines = array(
                    "if (listMouseDown) {",
                    "var el = document.getElementById(id);",
                    "el.scrollLeft -= event.movementX;",
                    "el.scrollTop -= event.movementY;",
                    "}",
                );
                CHtmlPage::get_js()->add_function("_MoveDragListView_", $lines, array('id', 'event'));
                CHtmlPage::get_js()->add_statement("var listMouseDown = false;");
            }
            
            foreach ($this->theme_items as $theme_item)
            {
                $table = new ZebraTable('List', array('class'=>self::$classes['table']));
                $div->add_inner($table);
                
                if (count($this->theme_items) > 1)
                {
                    $title = $this->get_title($theme_item);
                    if ($title) $table->add_title($title, array('class'=>self::$classes['header']));
                }
                
                $col_names = $this->get_column_names();
                if ($col_names) $table->add_column_names($col_names, array('class'=>self::$classes['column_names']));
                
                $objs = null;
                $items = $this->get_items($theme_item, $objs);
                
                if ($items) 
                {
                    $this->set_column_attrs($table);
                    
                    foreach ($items as $item) 
                    {
                        $tr = $table->add_row($item);
                        
                        if ($objs)
                        {
                            $obj = array_shift($objs);
                            $this->set_row_attrs($obj, $tr);
                        }
                    }
                }
                else 
                {
                    $table->add_none_row();
                }
            }
            
            return $div;
        }
        
        return null;
    }
}
?>