<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements text area form's element */
class CHtmlTextArea extends CHtmlElement
{
    /** The constructor */
    public function __construct($name, $value = null, $rows = 0, $cols = 0, array $attrs = null)
    {
        parent::__construct('textarea', $attrs, CHtmlInput::normalize($value));
        $this->set_attr('name', $name);
        CHtmlInput::init_placeholder_attr($this);
        
        if ($rows > 0) $this->set_attr('rows', (int)$rows);
        if ($cols > 0) $this->set_attr('cols', (int)$cols);
    }
}
?>