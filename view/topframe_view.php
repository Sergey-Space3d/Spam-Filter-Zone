<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

class TopFrameView extends CView
{
    protected function init_contents(array $args)
	{
		@extract($args);

		$table = new CHtmlTable(array('cellpadding'=>'0', 'cellspacing'=>'0', 'border'=>'0', 'width'=>'100%'));
		$this->add_inner($table);
		
		$logo = new CHtmlElement('img', array('class'=>'logo', 'src'=>'./logo.png', 'alt'=>'Logo'));
		$title = new CHtmlElement('div', array('class'=>'title'), TITLE);
		$title = new CHtmlElement('td', array('style'=>'width:100%;'), $title);
		$table->add_row(array($logo, $title));

		$getview = function($view, array $args = null) { return CDispatcher::instance()->get_view($view, $args); };
		$getform = function($form) { return CDispatcher::instance()->get_form($form); };
		
		$menu = new CHtmlMenu(true, array('class'=>'menu'));
		$table->add_row($menu, array('class'=>'menu', 'colspan'=>'100%', 'style'=>'border-top:1px solid grey;'));
		
		if ($has_database)
		{
			$has_mailboxes = MailboxMan::Instance()->get_table()->get_num_records() > 0;
			
			$menu->add_item('Setup Spam Mailboxes', $getview, 'SetupSpamMailboxes');
			if ($has_mailboxes) $menu->add_item('Identify Spam', $getview, 'IdentifySpam');
			$menu->add_item('Edit Spam Filters', $getview, 'EditSpam');
		}
		else 
		{
			$menu->add_item('No Database', $getview, 'NoDatabase');
		}
		
		$menu->add_item('Help', $getview, 'Help');
	}
}
?>