<?php 
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements HTML table */
class CHtmlTable extends CHtmlElement
{
    protected $curRow = 0;
    protected $colAttrs = array();
    
    /** The constructor */
    public function __construct(array $attrs = null, $inner = null, $name = null) 
	{	    
	    if (!$attrs) 
	        $attrs = array('cellpadding'=>'0', 'cellspacing'=>'0', 'border'=>'0');
	    else 
	    {
	        if (!isset($attrs['cellpadding'])) $attrs['cellpadding'] = '0';
	        if (!isset($attrs['cellspacing'])) $attrs['cellspacing'] = '0';
	        if (!isset($attrs['border'])) $attrs['border'] = '0';
	    }
	    
		parent::__construct('table', $attrs, $inner);
		if ($name) $this->set_attr('name', $name);
    }
	
	/** Returns number of rows */
	public function get_num_rows() { return $this->curRow; }
	
	/** Set column attributes (index is 0-based) */
	public function set_column_attrs($col_index, array $attrs)
	{
	    if (!is_array($col_index)) $col_index = array($col_index);
	    foreach ($col_index as $i) { $this->colAttrs[$i] = $attrs; }
	}
	
	/** Add row(s) to the table. Returns TR element */
	public function add_row($row, array $td_attrs = null, array $tr_attrs = null)
	{
	    $tr = new CHtmlElement('tr', $tr_attrs);
	    $this->add_inner($tr);

	    if (is_array($row))
	    {
	        $i = 0;
	        foreach ($row as $el)
	        {
	            $td = $this->make_row_td($i++, $el, $td_attrs);
	            $tr->add_inner($td);
	        }
	    }
	    else 
	    {
    	    $td = $this->make_row_td(0, $row, $td_attrs);
    	    $tr->add_inner($td);
	    }
	    
	    $tr->set_attr('row', $this->curRow);
	    $this->curRow++;
	    
	    return $tr;
	}
	
	/** Make row's TD element */
	public function make_row_td($col_index, $el = null, $td_attrs = null)
	{
	    if ($el instanceof CHtmlElement && !strcasecmp($el->get_tag(), 'td'))
	    {
	        $td_attrs = self::merge_attrs($this->colAttrs[$col_index], $el->get_attrs());
	        $el->set_attrs($td_attrs);
	        return $el;
	    }
	    
	    if (is_array($this->colAttrs[$col_index]))
	    {
	        $td_attrs = self::merge_attrs($this->colAttrs[$col_index], $td_attrs);
	    }
	    
	    $td = new CHtmlElement('td', $td_attrs, $el);
	    return $td;
	}
	
	/** Select row by index */
	public function select($index)
	{
	    if ($index < 0) return;
	    
	    foreach ($this->get_inner() as $inner)
	    {
	        if ($inner instanceof CHtmlElement && $inner->get_tag() == 'tr' && $inner->get_attr('sel'))
	        {
	            if ($inner->get_attr('row') == $index)
	            {
	                $js = self::setup_js_onclick($this->get_attr('name'));
    	            $js->add_statement("{$inner->get_attr('onclick')};");
    	            return;
	            }
	        }
	    }
	}
	
	/** Set onclick event for TR element */
	public function set_onclick(CHtmlElement $tr, $select = true, $fn = null, CHtmlForm $form = null)
	{
	    $tname = $this->get_attr('name');
	    $table_id = $this->get_id(true);
	    $tr_id = $tr->get_id(true);
	    
	    $onclick = array();
	    
	    if ($select)
	    {
    	    $onclick[] = "_OnSelClickTr_{$tname}(\"{$tr_id}\")";
	    }
	    
	    if ($fn)
	    {
	        $onclick[] = $fn;
	    }
	    
	    if ($form)
	    {
	        $form_id = $form->get_id(true);
	        $tr->add_inner(new CHtmlElement('td', array('style'=>'display:none;'), $form));

	        $onclick[] = "_OnFormClickTr_{$tname}(\"{$form_id}\")";
	    }
	    
	    if ($onclick)
	    {
	        $tr->set_attr('sel', 'selectable');
	        $tr->set_attr('onclick', implode(';', $onclick));
	        $this->set_attr('onscroll', "_OnScroll_{$tname}(\"{$table_id}\")");
	        
	        self::setup_js_onclick($tname);
	    }
	}

	/** Returns JS call (as a string), required when processing AJAX response */
	public static function make_ajax_update_call($tname)
	{
	    return "setTimeout(function(){_InitSelected_{$tname}(\"{$tname}\");}, 0);";
	}
	
	/** Setup JS script for click handling */
	public static function setup_js_onclick($tname, $sel_color = null)
	{
	    if (!CHtmlJavaScript::has_function("_OnSelClickTr_{$tname}"))
	    {
	        if (!$sel_color) $sel_color = 'grey';
	        
	        $selTrId = "selTrId{$tname}";
    	    $selTrColor = "selTrColor{$tname}";
    	    $selIndex = "selIndex{$tname}";
    	    $selColor = "selColor{$tname}";
    	    $scrollLeft = "scrollLeft{$tname}";
    	    $scrollTop = "scrollTop{$tname}";
    	    
    	    $js = CHtmlPage::get_js();
    	    
    	    $js->add_statement("var {$selTrId};");
    	    $js->add_statement("var {$selTrColor};");
    	    $js->add_statement("var {$selIndex}=-1;");
    	    $js->add_statement("var {$selColor}='{$sel_color}';");
    	    $js->add_statement("var {$scrollLeft};");
    	    $js->add_statement("var {$scrollTop};");
    	    
    	    $lines = array(
    	        "if ({$selTrId}) {", // Unselect previous selection
    	        "var pse = document.getElementById({$selTrId});",
    	        "if (pse) {",
    	        "pse.style.backgroundColor={$selTrColor};",
    	        "}}",
    	        "var se = document.getElementById(id);", // Select this element
    	        "{$selTrColor} = se.style.backgroundColor;",
    	        "se.style.backgroundColor={$selColor};",
    	        "{$selTrId} = id;",
    	        "if (se.hasAttribute('sel')) {",  // Remember the row index
    	        "{$selIndex} = se.getAttributeNode('row').value;",
    	        "var table = se.parentElement;",
    	        "if (table.tagName == 'TBODY') table = table.parentElement;",
    	        "if (table.clientHeight < table.scrollHeight) {", // Scroll into view
    	        "if (table.scrollTop > se.offsetTop) {",
    	        "table.scrollTop = {$scrollTop} = se.offsetTop;",
    	        "}",
    	        "else if (table.scrollTop + table.clientHeight < se.offsetTop + se.clientHeight) {",
    	        "table.scrollTop = {$scrollTop} = se.offsetTop + se.clientHeight - table.clientHeight;",
    	        "}}}",
    	    );
    	    $js->add_function("_OnSelClickTr_{$tname}", $lines, array('id'));
    	    
    	    $lines = array(
    	        "var el = document.getElementById(form_id);",
    	        "try {",
    	        "el.submit();",
    	        "}",
    	        "catch(err) {}",
    	    );
    	    $js->add_function("_OnFormClickTr_{$tname}", $lines, array('form_id'));

    	    $lines = array(
    	        "var tables = document.getElementsByName(name);",
    	        "for (var t = 0; t < tables.length; t++) {",
    	        "tables[t].scrollLeft = {$scrollLeft};",
    	        "tables[t].scrollTop = {$scrollTop};",
    	        "var cNodes = tables[t].getElementsByTagName('tr');",
    	        "for (var c = 0; c < cNodes.length; c++) {",
    	        "if (cNodes[c].hasAttribute('sel')) {",
    	        "var atr = cNodes[c].getAttributeNode('row');",
    	        "if (atr.value === {$selIndex}) {",
    	        "cNodes[c].onclick();",
    	        "break;",
        	    "}}}}",
    	    );
    	    $js->add_function("_InitSelected_{$tname}", $lines, array('name'));
    	    
    	    $lines = array(
    	        "var el = document.getElementById(id);",
    	        "{$scrollLeft} = el.scrollLeft;",
    	        "{$scrollTop} = el.scrollTop;",
    	    );
    	    $js->add_function("_OnScroll_{$tname}", $lines, array('id'));
	    }
	}
}
?>