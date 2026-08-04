<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

class NoDatabaseView extends CView
{
	/** Initialize view contents */
	protected function init_contents(array $args)
	{
		$table = new CHtmlTable(array('width'=>600));
		$this->add_inner($table);
		
		$style = 'padding:10px;text-align:center;font-weight:bold;color:white;background-color:red;';
		$table->add_row('NO DATABASE DETECTED', array('style'=>$style));
		
		$style = 'padding:20px;text-align:left;border:5px solid red;color:#990000;';
		$div = new CHtmlElement('div', array('style'=>$style));
		$table->add_row($div);
		
		$str = "MySql database shall be created and its properties defined in <b><i>config.php</i></b> file.
		It's more preferrable to use new database, in order to avoid naming conflicts.";
		$div->add_inner($str);
		$div->add_inner('<br/><br/>');
		$str = "Once the database is created, open <b><i>config.php</i></b> file for editing.
		Here, you substitute the placeholders values with host, login, database's name, and password values:";
		$div->add_inner($str);
		$div->add_inner('<br/><br/>');
		$div->add_inner("define('DB_HOST', 'localhost');");
		$div->add_inner('<br/>');
		$div->add_inner("define('DB_LOGIN', 'your_database_login');");
		$div->add_inner('<br/>');
		$div->add_inner("define('DB_PASSWORD', 'your_database_password');");
		$div->add_inner('<br/>');
		$div->add_inner("define('DB_MSG', 'your_database_name');");
	}
}
?>