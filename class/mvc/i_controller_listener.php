<?php 
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Definition of controller listener's interface */
interface IControllerListener
{
    /** Called on start processing MVC form */
	function on_start();
	
	/** Called on success processing MVC form */
	function on_success();
	
	/** Called on failure processing MVC form */
	function on_failure();
}
?>