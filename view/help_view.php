<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

class HelpView extends CView
{
	/** Initialize view contents */
	protected function init_contents(array $args)
	{
		$table = new CHtmlTable(array('width'=>600));
		$this->add_inner($table);
		
		$title = "OVERVIEW";
		$body = "The project goal is to create server-side web application, 
		capable of detecting and blocking spam for multiple mailboxes.
		The application is written in PHP language.";
		$this->make_body($table, $title, $body);
		
		$title = "FEATURES";
		$body = [
		"The application uses custom and generic filters to detect spam.",
		"Generic filters include:",
		"&#9830;&nbsp;&nbsp;detecting different sender addresses;",
		"&#9830;&nbsp;&nbsp;detecting different sender domains;",
		"&#9830;&nbsp;&nbsp;detecting orphan IPs for sender domains (fake domains);",
		"&#9830;&nbsp;&nbsp;detecting DAT files attached;",
		"&#9830;&nbsp;&nbsp;detecting calendar files attached;",
		"&nbsp;",
		"The custom filters allow user to identify spam by the following properties:",
		"&#9830;&nbsp;&nbsp;sender IP group (as in 123.456.789.XXXX);",
		"&#9830;&nbsp;&nbsp;sender IP;",
		"&#9830;&nbsp;&nbsp;sender domain;",
		"&#9830;&nbsp;&nbsp;sender address;",
		"&#9830;&nbsp;&nbsp;tracing regular expression or text in message subject or body.",
		"&nbsp;",
		"Note, that matching message with filter adds to overall message's score, and,",
		"when the score reaches the threshold (5 scores), the message is considered a spam.",
		"&nbsp;",
		"The IP group filter's usage: recently, there is a new kind of spam attacks from multiple IPs and domain names, 
		residing under the same IP group. These spam messages have clean SPF, DKIM, and DMARC fields, and the 		
		content that doesn't raise any red flags. Note that spam is sent from different groups each week, 
		with total number of groups around 4 or 6.",
		];
		$this->make_body($table, $title, $body);
		
		$title = "SETUP";
		$body = [
		"Unzip the project file and place its content under the public folder (<b><i>public_html</i></b>, <b><i>htdocs</i></b>, or <b><i>html</i></b>).
		For example, if the scripts are placed under <b><i>spamfilter</i></b> folder, then the website link is 
		<b><i>https://yourdomain.com/spamfilter/</i></b>.",
		"&nbsp;",
		"First, you must have access to MySql admin panel, to create the database. 
		Alternatively, the database may be created at command prompt.
		The host, database's name, login, and password must be defined.",
		"&nbsp;",
		"Once the database is created, open <b><i>config.php</i></b> file for editing.
		Here, you substitute the placeholder values with host, name, login, and password values:",
		"&#9830;&nbsp;&nbsp;define('DB_HOST', 'localhost');",
		"&#9830;&nbsp;&nbsp;define('DB_LOGIN', 'your_database_login');",
		"&#9830;&nbsp;&nbsp;define('DB_PASSWORD', 'your_database_password');",
		"&#9830;&nbsp;&nbsp;define('DB_MSG', 'your_database_name');",
		"&nbsp;",
		"Finally, add <b><i>process_spam.php</i></b> script, located under 
		<b><i>cron</i></b> directory, to the cron jobs. The job will clean up spam at regular time intervals.",
		"&nbsp;",
		"Requirements:",
		"&#9830;&nbsp;&nbsp;PHP 5.6 - 7.x",
		"&#9830;&nbsp;&nbsp;MySqli extension",
		"&#9830;&nbsp;&nbsp;IMAP extension",
		];
		$this->make_body($table, $title, $body);
		
		$title = "CUSTOMIZATION";
		$body = [	
		"This section is written primerally for PHP developers. Below is the list of most obvious changes:",
		"&#9830;&nbsp;&nbsp;Changing the threshold's score. Goto <b><i>SpamFilter</i></b> Class and change the <b><i>SpamFilter::COUNT_THRESHOLD</i></b> value.",
		"&#9830;&nbsp;&nbsp;Changing scores for generic filters. Goto <b><i>SpamFilterMan</i></b> Class and edit values for <b><i>SpamFilterMan::\$Scores</i></b> array.",
		"&#9830;&nbsp;&nbsp;Changing the name of Junk Folder. By default, spam messages are placed under \"Junk\" folder. 
		The folder's name is defined in <b><i>SpamFilter::FOLDER</i></b>.",
		];
		$this->make_body($table, $title, $body);
		
	}
	
	protected function make_body($table, $title, $body)
	{
		$style = 'padding:10px;text-align:left;font-weight:bold;color:green;background-color:#EEEEEE';
		$div = new CHtmlElement('div', array('style'=>$style), $title);
		$table->add_row($div);
		
		if (is_array($body)) $body = implode('<br/>', $body);
		$style = 'padding:20px 0px 30px 0px;text-align:justify;';
		$div = new CHtmlElement('div', array('style'=>$style), $body);
		$table->add_row($div);
		
	}
}
?>