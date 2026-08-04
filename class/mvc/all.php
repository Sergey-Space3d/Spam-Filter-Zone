<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

$__dir = dirname(__FILE__);
require_once($__dir.'/i_controller_listener.php');
require_once($__dir.'/c_view_input.php');
require_once($__dir.'/c_view.php');
require_once($__dir.'/c_controller.php');
require_once($__dir.'/c_form.php');
require_once($__dir.'/c_dispatcher.php');

// Setup JIT class autoload
$__sudir = $__dir.'/extra';
$__classes = array(
    'CDefViewInput'=>$__dir.'/c_def_view_input.php',
    'CollapsedForm'=>$__sudir.'/collapsed_form.php',
    'FormLayout'=>$__sudir.'/form_layout.php',
    'ListView'=>$__sudir.'/list_view.php',
    'SelectorController'=>$__sudir.'/selector_controller.php',
    'SelectorForm'=>$__sudir.'/selector_form.php',
);
require_once($__dir.'/../db/extra/c_autoloader.php');
CAutoloader::register($__classes);
?>