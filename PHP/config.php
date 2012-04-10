<?php

// Default config file for SemC - PHP

// Database Tests
$host 			= '';	// Database-hostname (default: localhost)
$dbuser 		= '';	// Database-username
$dbpass 		= '';	// Database-password
$database 		= '';	// Database name (database to select)
$dbtable		= '';	// Database table (to perform a little `select` on)

// Quota Test
$quotaUser  = ''; // Username on the system

// DiskSpace Test
$diskspacewarn = 90; // Gives a warning if a disk-usage is higher than this value, in %
$diskspaceerror = 95; // Gives an error if a disk-usage is higher than this value, in %

// List of the names of the disks you want to monitor (same name as the output of the df-command (unix)
$disks_to_monitor = array();

?>