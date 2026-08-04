<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements checkbox form's element */
class CHtmlCheckbox extends CHtmlInput
{
    /** The constructor */
    public function __construct($name, $value, $selected = null, array $attrs = null)
    {
        parent::__construct($attrs);
        $this->set_value_attr($name, $value);
        self::init_placeholder_attr($this, null, $name);

        if ($selected) $this->set_attr('checked', 'checked');
        else CHtmlForm::set_input_placeholder($name);// Unselected checkbox won't be in get/post array
    }
    
    static protected function get_type() { return 'checkbox'; }
}
?>