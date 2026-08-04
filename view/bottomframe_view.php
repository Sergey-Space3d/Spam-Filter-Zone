<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/****************************************************************************
 * CLASS BottomFrameView
 ****************************************************************************/

class BottomFrameView extends CView
{
    /** Initialize view contents */
    protected function init_contents(array $args)
	{
		$this->add_inner('&copy;&nbsp;&nbsp;'.date('Y').'&nbsp;&nbsp;'.COMPANY);
	}
}
?>