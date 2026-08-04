<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements hidden form's element */
class CHtmlHidden extends CHtmlInput
{
    /** The constructor */
    public function __construct($name, $value, array $attrs = null)
    {
        parent::__construct($attrs);
        $this->set_value_attr($name, $value);
    }
    
    static protected function get_type() { return 'hidden'; }
}
?>