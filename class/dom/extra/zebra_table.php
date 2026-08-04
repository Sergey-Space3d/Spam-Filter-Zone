<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Zebra Table Class */
class ZebraTable extends CHtmlTable
{
    protected static $classes = array();
    
    /** Odd/even row step */
    public $Step = 1;

    /** The constructor */
    public function __construct($name, $attrs = null, $_step = 1)
    {
        if (!CHtmlStyle::has_selector('TABLE.ztbl'))
        {
            $style_el = new CHtmlStyle();
            $this->add_inner($style_el);
            
            $style_el->add_selector('TABLE.ztbl', 'border-left:1px solid #888888;border-top:1px solid #888888;');
            $style_el->add_selector('TR.ztbl', 'background-color:#EEEEEE;');
            $style_el->add_selector('TR.ztbl:hover', 'background-color:#DDDDDD;');
            $style_el->add_selector('TD.ztbl', 'cursor:pointer;padding:4px 12px 4px 12px;border-right:1px solid #888888;border-bottom:1px solid #888888;white-space:nowrap;');
            $style_el->add_selector('TR.ztbl_odd', 'background-color:#FFFFFF;');
            $style_el->add_selector('TR.ztbl_odd:hover', 'background-color:#DDDDDD;');
            $style_el->add_selector('TD.ztbl_title', 'padding:7px 12px 7px 12px;border-right:1px solid #888888;border-bottom:1px solid #888888;text-align:center;font-weight:bold;background-color:#FFFFFF;white-space:nowrap;');
            $style_el->add_selector('TD.ztbl_column_names', 'padding:4px 12px 4px 12px;border-right:1px solid #888888;border-bottom:1px solid #888888;text-align:center;font-weight:bold;white-space:nowrap;');
            $style_el->add_selector('TD.ztbl_footer', 'cursor:default;font-weight:bold;text-align:right;vertical-align:middle;background-color:#EEEEEE;color:#404040;');
            $style_el->add_selector('SELECT.ztbl_select', 'height:20px;border:1px solid black;border-radius:0;margin:1px;padding:1px 6px;font-size:13px;color:#000000;background-color:#F0F0F0;');
            $style_el->add_selector('BUTTON.ztbl_button, INPUT.ztbl_button[type=button], INPUT.ztbl_button[type=submit], INPUT.ztbl_button[type=reset]',
                'width:50px;height:20px;border:1px solid #909090;border-radius:0;cursor:pointer;margin:1px;padding:1px 6px;font-weight:normal;font-size:13px;white-space:nowrap;color:#000000;background-color:#E0E0E0;');
            
            self::$classes['table'] = $this->get_css_class('table', 'ztbl', __CLASS__);
            self::$classes['odd'] = $this->get_css_class('odd', 'ztbl_odd', __CLASS__);
            self::$classes['header'] = $this->get_css_class('header', 'ztbl_title', __CLASS__);
            self::$classes['column_names'] = $this->get_css_class('column_names', 'ztbl_column_names', __CLASS__);
            self::$classes['footer'] = $this->get_css_class('footer', 'ztbl_footer', __CLASS__);
            self::$classes['select'] = $this->get_css_class('select', 'ztbl_select', __CLASS__);
            self::$classes['button'] = $this->get_css_class('button', 'ztbl_button', __CLASS__);
        }
        
        $this->Step = $_step;
        $t_attrs = self::merge_attrs(array('class'=>self::$classes['table']), $attrs);
            
        parent::__construct($t_attrs, null, $name);
    }
    
    /** Make row's TD element */
    public function make_row_td($col_index, $el = null, $td_attrs = null)
    {
        $td_attrs2 = self::merge_attrs(array('class'=>self::$classes['table']), $td_attrs);
        return parent::make_row_td($col_index, $el, $td_attrs2);
    }
    
    /** Add title. Returns TR element */
    public function add_title($title, $td_attrs = null)
    {
        $td_attrs2 = self::merge_attrs(array('class'=>self::$classes['header']), $td_attrs);
        $td_attrs2['colspan'] = '100%';
        $tr = parent::add_row($title, $td_attrs2);
        $this->curRow--; // don't count this row
        return $tr;
    }
    
    /** Add column names row. Returns TR element */
    public function add_column_names($names, $td_attrs = null)
    {
        $td_attrs2 = self::merge_attrs(array('class'=>self::$classes['column_names']), $td_attrs);
        $tr = parent::add_row($names, $td_attrs2);
        $this->curRow--; // don't count this row
        return $tr;
    }
    
    /** Add "none" (no items) row. Returns TR element */
    public function add_none_row($str = null)
    {
        $td_attrs = array('colspan'=>'100%', 'style'=>'text-align:center;');
        $tr_attrs = array('class'=>self::$classes['odd']);
        
        return parent::add_row($str ? $str : 'None', $td_attrs, $tr_attrs);
    }
    
    /** Add row(s) to the table. Returns TR element */
    public function add_row($row, array $td_attrs = null, array $tr_attrs = null)
    {
        $odd = true;
        
        if ($this->Step > 0)
        {
            // Note: row number is 1-based, but row index is 0-based
            $n = $this->curRow + 1;
            if ($this->Step > 1) $n = ceil($n / $this->Step);
            $odd = ($n % 2);
        }
        
        $tr_attrs2 = array('class'=>self::$classes[$odd ? 'odd' : 'table']);
        $tr_attrs2 = self::merge_attrs($tr_attrs2, $tr_attrs);
        $td_attrs2 = self::merge_attrs(array('class'=>self::$classes['table']), $td_attrs);

        return parent::add_row($row, $td_attrs2, $tr_attrs2);
    }
}
?>