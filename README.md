receipts_from_QBO_report
========================

Takes a memorized report from Quickbooks Online and Screenshots each of the online transaction details.

Requirements:  
1. A memorized report in your Quickbooks Online account.
2. Automatically downloaded banking transactions therein.
3. Firefox installed (you can change to another browser in the script.)

=========================
To run on a mac perform the following steps:

1. brew install selenium-server-standalone

2. brew install php54

3. brew install composer

4. brew install phpunit

5. launch selenium-server-standalone (via the notes provided by brew or just run java -jar on the download)

6. clone this repo into a directory on your computer

7. create a creds.php in the parent directory from the git clone similar to the following:

<?php
  define('QBUser', 'user@example.com');
  define('QBPass', 'mypass');
?>

8. run 'composer install' from the git clone directore

9. run 'vendor/bin/phpunit QBTest' 


5 minutes later you should have receipt copies in the receipts folder.
