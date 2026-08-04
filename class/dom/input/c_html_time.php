<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements time form's element */
class CHtmlTime extends CHtmlInput
{
    /** The constructor */
    public function __construct($name, $value = null, array $attrs = null)
    {
        parent::__construct($attrs);
        $this->set_value_attr($name, $value);
    }
    
    static protected function get_type() { return 'time'; }
    
    /** Convert timestamp to time value */
    static public function to_value($ts, $seconds = null)
    {
        return date($seconds ? 'H:i:s' : 'H:i', $ts);
    }
    
    /** Convert time value to timestamp */
    static public function to_ts($value)
    {
        $arr = explode(':', $value);
        return mktime($arr[0], $arr[1], count($arr) < 3 ? 0 : $arr[2], 0, 0, 0);
    }
}
?>