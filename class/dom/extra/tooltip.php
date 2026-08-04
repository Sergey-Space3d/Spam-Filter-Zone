<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Tooltip Class */
class Tooltip extends CHtmlElement
{
    /** The constructor */
    public function __construct($el, $tip, array $attrs = null)
    {
        if (!($tip instanceof CHtmlElement)) $tip = new CHtmlElement('div', $attrs, $tip);
        $tip->set_attr('class', 'tooltiptext');
        
        parent::__construct('div', array('class'=>'tooltip'), $el);
        $this->add_inner($tip);
        
        if (!CHtmlStyle::has_selector('.tooltip'))
        {
            $style_el = new CHtmlStyle();
            $this->add_inner($style_el);
            
            $style_el->add_selector('.tooltip', 'cursor:pointer;position:relative;display:inline-block;');
            
            // Tooltip text
            $style = 'visibility:hidden;width:120px;background-color:#555;color:#fff;text-align:center;padding:5px 0;border-radius:6px;';
            $style .= 'position:absolute;z-index:1;bottom:125%;left:50%;margin-left:-60px;'; // Position the tooltip text
            $style .= 'opacity:0;transition:opacity 0.3s;'; // Fade in tooltip
            $style_el->add_selector('.tooltip .tooltiptext ', $style);
            
            // Tooltip arrow
            $style = 'content:"";position:absolute;top:100%;left:50%;margin-left:-5px;';
            $style .= 'border-width:5px;border-style:solid;border-color:#555 transparent transparent transparent;';
            $style_el->add_selector('.tooltip .tooltiptext::after', $style);
            
            // Show tooltip text on mouse over
            $style_el->add_selector('.tooltip:hover .tooltiptext', 'visibility:visible;opacity:1;');
        }
    }
}
?>