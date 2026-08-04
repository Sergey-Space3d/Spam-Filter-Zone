<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Base class for MVC view */
abstract class CView extends CHtmlElement
{
    /** Argument list */
    protected $args;
    
    /** The constructor */
	final public function __construct(CViewInput $input = null, array $attrs = null, $inner = null)
	{
		parent::__construct('span', $attrs, $inner);

		$this->args = $input ? $input->get_args() : array();
		$this->init_contents($this->args);
	}

	/** Initialize the view contents */
	abstract protected function init_contents(array $args);
}
?>