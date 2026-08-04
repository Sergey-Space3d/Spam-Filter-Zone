SETUPUnzip the project file and place its content under the public folder (public_html, htdocs, or html). For example, if the scripts are placed under spamfilter folder, then the website link is https://yourdomain.com/spamfilter/. First, you must have access to MySql admin panel, to create the database. Alternatively, the database may be created at command prompt. The host, database's name, login, and password must be defined. Once the database is created, open config.php file for editing. Here, you substitute the placeholder values with host, name, login, and password values:-  define('DB_HOST', 'localhost');-  define('DB_LOGIN', 'your_database_login');-  define('DB_PASSWORD', 'your_database_password');-  define('DB_MSG', 'your_database_name'); Finally, add process_spam.php script, located under cron directory, to the cron jobs. The job will clean up spam at regular time intervals. Requirements:-  PHP 5.6 - 7.x-  MySqli extension-  IMAP extensionOVERVIEWThe project goal is to create server-side web application, capable of detecting and blocking spam for multiple mailboxes. The application is written in PHP language.FEATURESThe application uses custom and generic filters to detect spam.
Generic filters include:
-  detecting different sender addresses;
-  detecting different sender domains;
-  detecting orphan IPs for sender domains (fake domains);
-  detecting DAT files attached;
-  detecting calendar files attached;
 
The custom filters allow user to identify spam by the following properties:
-  sender IP group (as in 123.456.789.XXXX);
-  sender IP;
-  sender domain;
-  sender address;
-  tracing regular expression or text in message subject or body.
 
Note, that matching message with filter adds to overall message's score, and,
when the score reaches the threshold (5 scores), the message is considered a spam.
 
The IP group filter's usage: recently, there is a new kind of spam attacks from multiple IPs and domain names, residing under the same IP group. These spam messages have clean SPF, DKIM, and DMARC fields, and the content that doesn't raise any red flags. Note that spam is sent from different groups each week, with total number of groups around 4 or 6.CUSTOMIZATIONThis section is written primerally for PHP developers. Below is the list of most obvious changes:
-  Changing the threshold's score. Goto SpamFilter Class and change the SpamFilter::COUNT_THRESHOLD value.
-  Changing scores for generic filters. Goto SpamFilterMan Class and edit values for SpamFilterMan::$Scores array.
-  Changing the name of Junk Folder. By default, spam messages are placed under "Junk" folder.    The folder's name is defined in SpamFilter::FOLDER.
