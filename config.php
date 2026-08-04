<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

define('DB_HOST', 'localhost');
define('DB_LOGIN', 'your_database_login');
define('DB_PASSWORD', 'your_database_password');
define('DB_MSG', 'your_database_name');

define('COMPANY', 'Sergey Shustov');
define('TITLE', 'Spam Filter Zone');
define('DEVELOPMENT', 0);
define('MAINTENANCE_HOURS', 0);

define('ERROR_FILENAME', 'error.log');
ini_set('error_log', './'.ERROR_FILENAME);
ini_set('error_reporting', DEVELOPMENT ? E_ALL & ~E_NOTICE : E_ERROR);
ini_set('display_errors', 0);

@date_default_timezone_set('UTC');

if (!@constant('CONFIG_NO_INCLUDE'))
{
    $_curpath___ = dirname(__FILE__);
    require_once($_curpath___.'/class/db/all.php');
    require_once($_curpath___.'/class/msg/all.php');
    require_once($_curpath___.'/class/dom/all.php');
    require_once($_curpath___.'/class/mvc/all.php');
    
    MsgDb::set_name(DB_MSG);
}
?>