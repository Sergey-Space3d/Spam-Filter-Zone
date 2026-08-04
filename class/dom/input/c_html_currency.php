<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements currency form's element */
class CHtmlCurrency extends CHtmlInput
{
    /** The constructor */
    public function __construct($name, $value, $min, $max, $step = 0.01, $symbol = '$', array $attrs = null)
    {
        parent::__construct($attrs);
        $this->set_value_attr($name, $value);
        $this->set_attr('min', $min);
        $this->set_attr('max', $max);
        $this->set_attr('step', $step);
        self::init_placeholder_attr($this);
        
        if ($symbol) $this->add_inner("&nbsp;{$symbol}");
    }
    
    static protected function get_type() { return 'number'; }
}
?>