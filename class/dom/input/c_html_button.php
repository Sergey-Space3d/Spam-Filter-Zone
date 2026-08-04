<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements button form's element */
class CHtmlButton extends CHtmlElement
{
    /** The constructor */
    public function __construct($value, array $attrs = null)
    {
        $attrs['type'] = 'button';
        parent::__construct('button', $attrs, CHtmlInput::normalize($value));
    }
}
?>