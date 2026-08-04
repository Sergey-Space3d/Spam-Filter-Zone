<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Base class for HTML form's input */
abstract class CHtmlInput extends CHtmlElement
{
    /** The constructor */
    public function __construct(array $attrs = null, $inner = null)
    {
        if (!$attrs) $attrs = array();
        $attrs['type'] = $this->get_type();
        
        parent::__construct('input', $attrs, $inner);
    }
    
    /** Initialize placeholder attribute */
    public static function init_placeholder_attr(CHtmlElement $el, $name = null, $value = null)
    {
        if (!$name) $name = 'placeholder';
        
        if ($value || !$el->get_attr($name))
        {
            if (!$value)
            {
                $value = implode(' ', explode('_', $el->get_attr('name')));
                $value = ucwords(strtolower($value), ' ');
            }
            
            $el->set_attr($name, $value);
        }
    }
    
    /** Normalize input's value */
    public static function normalize($value) { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE); }
    
    /** Set value attribute */
    protected function set_value_attr($name, $value)
    {
        $this->set_attr('name', $name);
        $this->set_attr('value', self::normalize($value));
    }
    
    /** Get input's type */
    protected static function get_type() { return null; }
}
?>