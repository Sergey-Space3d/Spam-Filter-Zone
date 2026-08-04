<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements text form's element */
class CHtmlText extends CHtmlInput
{
    /** The constructor */
    public function __construct($name, $value = null, array $attrs = null)
    {
        parent::__construct($attrs);
        $this->set_value_attr($name, $value);
        self::init_placeholder_attr($this);
    }
    
    static protected function get_type() { return 'text'; }
}
?>