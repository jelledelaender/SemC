<?php
/*
* Semonto Code
*
* Written by Jelle De Laender - CodingMammoth
* 01/09/2009
* 20/12/2010
*
*	Please don't remove any file!
*   Please don't alter any file, except 'extensions.php'
*
*   For support, email to info@semonto.com
*
* Version 3
*
*/

include_once('config.php');
include_once('functions.php');

error_reporting(0); // Disable warnings: Uncomment this row, if you want to ignore warnings/errors, disable this line to detect possible errors in the test-script.

if (!isset($_GET['test'])) {
	throwStatus("-1"); // Test parameter is missing...
}

/** General time-measure **/
$starttime = explode(' ', microtime());  
$starttime =  $starttime[1] + $starttime[0];


include_once('standardtests.php');	/* Load default tests */
include_once('extensions.php');		/* Load custom tests */

/* Default test 'version' */
if ($_GET['test'] == "version") {
	$version = 3;
	throwStatus($version);
}


performStandardTest($_GET['test']); /* Check for a standard-test */
performExtensionTest($_GET['test']); /* Check for a custom test */

throwStatus("-1"); // test not found

// Be sure, no empty line after the PHP-close state
?>