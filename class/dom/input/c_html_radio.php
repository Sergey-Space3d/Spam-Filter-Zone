<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements radio form's element */
class CHtmlRadio extends CHtmlInput
{
    /** The constructor */
    public function __construct($name, $value, $selected = null, array $attrs = null)
    {
        parent::__construct($attrs);
        $this->set_value_attr($name, $value);
        self::init_placeholder_attr($this);
        
        if ($selected === null && CHtmlForm::get_value($name) == $value)
            $selected = true;

        if ($selected)
            $this->set_attr('checked', CHtmlElement::NO_VALUE_ATTR);
    }
    
    static protected function get_type() { return 'radio'; }
}
?>