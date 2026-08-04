<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

define('SELECT_ALL_ID', 0);
define('SELECTOR_DATE_RANGE', 'sel_date_range');
define('SELECTOR_DATE_FROM', 'sel_date_from');
define('SELECTOR_DATE_TO', 'sel_date_to');
define('SELECTOR_LEDGER_ID', 'sel_id_cdbledger');

$__dir = dirname(__FILE__);
require_once($__dir.'/c_html_element.php');
require_once($__dir.'/c_html_tag.php');
require_once($__dir.'/c_html_table.php');
require_once($__dir.'/c_html_a.php');
require_once($__dir.'/c_html_form.php');
require_once($__dir.'/c_html_style.php');
require_once($__dir.'/c_html_java_script.php');

// Setup JIT class autoload
$__classes = array(
    'CHtmlAjax'=>$__dir.'/c_html_ajax.php',
    'CHtmlAjaxRequest'=>$__dir.'/c_html_ajax_request.php',
    'CHtmlHttpRequest'=>$__dir.'/c_html_http_request.php',
    'CHtmlInnerFile'=>$__dir.'/c_html_inner_file.php',
    'CHtmlMenu'=>$__dir.'/c_html_menu.php',
    'CHtmlPage'=>$__dir.'/c_html_page.php',
);
require_once($__dir.'/../db/extra/c_autoloader.php');
CAutoloader::register($__classes);

$__sudir = $__dir.'/extra';
$__classes = array(
    'CHtmlPasswordEx'=>$__sudir.'/c_html_password_ex.php',
	'FieldValidator'=>$__sudir.'/field_validator.php',
	'FormView'=>$__sudir.'/form_view.php',
    'Tooltip'=>$__sudir.'/tooltip.php',
    'ZebraTable'=>$__sudir.'/zebra_table.php',
);
CAutoloader::register($__classes);

$__sudir = $__dir.'/input';
$__classes = array(
    'CHtmlButton'=>$__sudir.'/c_html_button.php',
    'CHtmlCbox'=>$__sudir.'/c_html_cbox.php',
    'CHtmlCheckbox'=>$__sudir.'/c_html_checkbox.php',
    'CHtmlCurrency'=>$__sudir.'/c_html_currency.php',
    'CHtmlDate'=>$__sudir.'/c_html_date.php',
    'CHtmlFile'=>$__sudir.'/c_html_file.php',
    'CHtmlHidden'=>$__sudir.'/c_html_hidden.php',
    'CHtmlImage'=>$__sudir.'/c_html_image.php',
    'CHtmlInput'=>$__sudir.'/c_html_input.php',
    'CHtmlNumber'=>$__sudir.'/c_html_number.php',
    'CHtmlPassword'=>$__sudir.'/c_html_password.php',
    'CHtmlRadio'=>$__sudir.'/c_html_radio.php',
    'CHtmlReset'=>$__sudir.'/c_html_reset.php',
    'CHtmlSubmit'=>$__sudir.'/c_html_submit.php',
    'CHtmlTextArea'=>$__sudir.'/c_html_text_area.php',
    'CHtmlText'=>$__sudir.'/c_html_text.php',
    'CHtmlTime'=>$__sudir.'/c_html_time.php',
);
CAutoloader::register($__classes);
?>