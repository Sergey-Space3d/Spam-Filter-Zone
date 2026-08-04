<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

require_once('./config.php');

$has_database = true;

try
{
	CDbase::connect(DB_HOST, DB_LOGIN, DB_PASSWORD);
	CDbase::open(DB_MSG);
}
catch (Exception $e)
{
	$has_database = false;
}

$args = array(
    'maintenance'=>MAINTENANCE_HOURS, 
    'development'=>DEVELOPMENT, 
    'icon'=>'./favicon.ico', 
    'style'=>array('./style.css', './menu.css', './form.css'),
    'onbeforeunload'=>'onbeforeunload()');

CHtmlPage::begin(TITLE, true, $args);

// Setup classes for common elements
$classes = array('headline'=>'headline', 'toolbar'=>'selector', 'button'=>'deck', 'close_icon'=>'close_icon',
    'header'=>'list_title', 'column_names'=>'list_column_names', 'table'=>'list', 'form'=>'form');
CHtmlElement::set_css_classes($classes);

/************************************************************************
 * FORM PROCESSING
 ***********************************************************************/

// Process form's request, if any
CDispatcher::instance()->process_request();

/************************************************************************
 * PAGE
 ***********************************************************************/

$table = new CHtmlTable(array('class'=>'page_layout'));
$table->add_row('&nbsp;', array('class'=>'page_layout_fixed'));

// Show top frame
$el = CDispatcher::instance()->get_view('TopFrame', array('has_database'=>$has_database));
$table->add_row($el, array('class'=>'top_frame top_frame_fixed'));

if (CHtmlPage::is_last_error())
{
    // Show last error
    $el = CHtmlPage::pop_last_error();
    $table->add_row($el, array('class'=>'error'));
}
else if (CHtmlPage::is_last_info())
{
    // Show last info
    $el = CHtmlPage::pop_last_info();
    $table->add_row($el, array('class'=>'status'));
}

// Show page
$el = CHtmlMenu::get_page_contents();
$table->add_row(CHtmlElement::wrap($el, 'center'), array('class'=>'page', 'style'=>'padding-left:0px;'));

// Show footnote
$el = CDispatcher::instance()->get_view('BottomFrame');
$table->add_row($el, array('class'=>'bottom_frame'));

echo $table;

CHtmlPage::finish();
?>