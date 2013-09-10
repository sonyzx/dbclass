dbclass
=======

This is just a secure database PHP Class
Version 1.0

Usage
======
define('DATABASE_USER',''); // Your DB usernamer
define('DATABASE_PASS',''); // Your DB userpass
define('DATABASE_HOST',''); // Hostname / IP / of your Database
define('DATABASE_NAME',''); // Name of your database

$db = new database();
$db->doQuery('select * from content where content_id = %u', 1); // First the SQL Query and then th Values
$data 	= $db->getDataResultSet(); // Stores the result array in $data (If there is any *g*)
/* Debugging*
$debug 	= $db->debug();
echo $debug;
