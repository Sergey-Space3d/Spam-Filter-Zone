<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements date form's element */
class CHtmlDate extends CHtmlInput
{
    /** The constructor */
    public function __construct($name, $value = null, array $attrs = null)
    {
        parent::__construct($attrs);
        $this->set_value_attr($name, $value);
    }
    
    static protected function get_type() { return 'date'; }
    
    /** Convert timestamp to date value */
    static public function to_value($ts)
    {
        return date('Y-m-d', $ts);
    }
    
    /** Convert date value to timestamp */
    static public function to_ts($value)
    {
        $arr = explode('-', $value);
        return mktime(0, 0, 0, $arr[1], $arr[2], $arr[0]);
    }
}
?>