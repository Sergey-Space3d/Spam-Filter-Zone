<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements image form's element */
class CHtmlImage extends CHtmlInput
{
    /** The constructor */
    public function __construct($src, $alt = null, array $attrs = null)
    {
        parent::__construct($attrs);
        $this->set_attr('src', $src);
        $this->set_attr('alt', self::normalize($alt));
    }
    
    static protected function get_type() { return 'image'; }
}
?>