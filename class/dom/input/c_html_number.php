<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements number form's element */
class CHtmlNumber extends CHtmlInput
{
    /** The constructor */
    public function __construct($name, $value, $min, $max, $step = 1, array $attrs = null)
    {
        parent::__construct($attrs);
        $this->set_value_attr($name, $value);
        $this->set_attr('min', $min);
        $this->set_attr('max', $max);
        $this->set_attr('step', $step);
        self::init_placeholder_attr($this);
    }
    
    static protected function get_type() { return 'number'; }
}
?>