<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

chdir(dirname(__FILE__));
require_once('../config.php');

try
{
	CDbase::connect(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_MSG);
	
    $filter = 'UNSEEN';
    $max_len = 2000;
    
    foreach (MailboxMan::Instance()->get() as $mailbox)
    {
    	$mail_server = new EmailServer($mailbox);
    	
    	if ($mail_server->open())
    	{
    		foreach ($mail_server->read($filter, $max_len) as $email)
    		{
    			$score = 0;
    			$spam = SpamFilterMan::Instance()->find_spam($email, $score);
    			
    			if ($spam)
    			{
    				if ($mail_server->move($email->get_msg_uid(), SpamFilter::FOLDER))
    				{
    					$spam->set_spam_count($spam->get_spam_count() + 1, true);
    					echo "Email {$email->get_msg_uid()} was moved to ", SpamFilter::FOLDER, ' folder<br/>';
    				}
    			}
    		}
    		
    		$mail_server->close();
    	}
    	else
    	{
    		$errors = EmailServer::pop_errors();
    		if ($errors) echo implode('<br/>', $errors);
    	}
    }
}
catch (Exception $e)
{
    echo $e->getMessage();
}

?>