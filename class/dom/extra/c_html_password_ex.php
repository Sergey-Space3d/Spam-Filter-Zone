<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

class CHtmlPasswordEx extends CHtmlText
{
    /** The constructor */
    public function __construct($name, $value = null, array $attrs = null)
    {
        parent::__construct($name, $value, $attrs);
        $this->set_attr('autocomplete', 'off');
        
        $style = '-webkit-text-security:disc;';
        if ($attrs && $attrs['style']) $style .= $attrs['style'];
        $this->set_attr('style', $style);
    }
}
?>