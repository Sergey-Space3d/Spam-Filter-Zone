<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements file form's element */
class CHtmlFile extends CHtmlInput
{
    protected $maxsize = 0;
    
    /** The constructor */
    public function __construct($name, $value = null, $maxsize = 0, array $attrs = null)
    {
        parent::__construct($attrs);
        $this->maxsize = $maxsize;
        $this->set_value_attr($name, $value);
    }
    
    /** Covert instance to string */
    protected function out()
    {
        $str = parent::out();
        
        if ($this->maxsize > 0)
        {
            $h = "<input type='hidden' name='MAX_FILE_SIZE' value='{$this->maxsize}'/>";
            self::process_out_str($h);
            $str .= $h;
        }
        
        return $str;
    }
    
    static protected function get_type() { return 'file'; }
}
?>