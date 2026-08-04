<?php 
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements 'A' HTML element */
class CHtmlA extends CHtmlElement
{
	/** The constructor */
	public function __construct($href, array $attrs = null, $inner = null) 
	{
		parent::__construct('a', $attrs, $inner);
		$this->set_attr('href', htmlspecialchars($href, ENT_QUOTES | ENT_SUBSTITUTE));
	}
	
	/** Make mail-to reference */
	public static function mailto($email, $title = null)
	{
	    $a = new CHtmlA("mailto:{$email}");
	    $a->add_inner($title ? $title : $email);
	    return $a;
	}
}
?>