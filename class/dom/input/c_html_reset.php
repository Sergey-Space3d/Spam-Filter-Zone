<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements reset form's element */
class CHtmlReset extends CHtmlElement
{
    /** The constructor */
    public function __construct($value, array $attrs = null)
    {
        $attrs['type'] = 'reset';
        parent::__construct('button', $attrs, CHtmlInput::normalize($value));
    }
}
?>