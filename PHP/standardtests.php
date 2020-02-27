<?php

	/* Extra methods for fetching data */
	function getLoads() {
		$str = exec("uptime"); // This may be disabled on some systems.
		// If we are able to install a small cronjob, we should be able to fetch the load with a Bash script and storing the results in a DB
		// In that case, we can access the DB-value and use this, optional with a timemark
		
		/* Default Linux */
		$ar = explode("average: ", $str);
		if (count($ar) == 2) {
			 return explode(", ", $ar[1]);
		}
		
		/* Mac OS X Server */
		$ar = explode("averages: ", $str);
		if (count($ar) == 2) {
			 return explode(" ", $ar[1]);
		}
		
		return array();
	}
	
	function load($i) {
		if ($i > 35) return "2";
		if ($i > 15) return "1";
		return "0";
	}
	
	function performStandardTest($test) {
		global $host, $dbuser, $dbpass, $database, $dbtable;
		global $quotaUser;
		global $disks_to_monitor;
		
		// quick PHP Test
		if ($test == "php-lus") {
				
			$o = 1;// simple lus
			for ($i = 0; $i < 5000; $i++) $o += $i; 

			$totaltime = getRunningTime();
			$status = 0;
			if ($totaltime > 0.5) $status = 1;
			if ($totaltime > 1.0) $status = 2;
			throwStatus($status, $totaltime);
		}

		
		// check the Load (if we can access this data)
		if ($test == "load-now") {
			$ar = getLoads();
			throwStatus(load($ar[0]),$ar[0]);
		}

		if ($test == "load-5m") {
			$ar = getLoads();
			throwStatus(load($ar[1]),$ar[1]);
		}

		if ($test == "load-15m") {
			$ar = getLoads();
			throwStatus(load($ar[2]),$ar[2]);
		}
		
		
		// MySQL Database
		if ($test == "mysql-ping") {
			set_time_limit(0);
			$conn = mysqli_connect($host, $dbuser, $dbpass); // Make a connection
			if (!mysqli_ping($conn)) throwStatus("4","Failed to ping, connect or login on the db-server");
			else throwStatus("0", getRunningTime()); // OK
		}

		if ($test == "mysql-select") {
			set_time_limit(0);
			$conn =mysqli_connect($host, $dbuser, $dbpass); // Make a connection
			if (!$conn) throwStatus("4","Failed to connect or login on the db-server");
			$db   = mysqli_select_db($conn, $database); // Select the database
			if (!$db) throwStatus("4","Failed to select the database");
			else throwStatus("0", getRunningTime()); // OK
		}

		if ($test == "mysql-execTable") {
			set_time_limit(0);
			$conn =mysqli_connect($host, $dbuser, $dbpass); // Make a connection
			if (!$conn) throwStatus("4","Failed to connect or login on the db-server");
			$db   = mysqli_select_db($conn, $database); // Select the database
			if (!$db) throwStatus("4", "Failed to select the database");
			$sql  = "SELECT * FROM $dbtable LIMIT 50"; // Try to fetch some data
			$res = mysqli_query($conn, $sql) or die("4"); // Try to read this data
			if (!$res) throwStatus("4", getRunningTime()); // Oops
			else throwStatus("0", getRunningTime()); // OK
		}
		
		// Quota
		if ($test == "quota") {
			if ($quotaUser == "") throwStatus("2", "No user specified");

			$str = exec("quota -g $quotaUser"); // This request should be allowed on your server
			if ($str == "") throwStatus("4","Not Supported on this system (or wrong username)");
			$ar = explode(" ", $str);
			$i = $ar[9] / $ar[11];
			if ($i < 0.95) throwStatus("0",(round($i*100, 2)));
			else if ($i <= 0.985) throwStatus("1",(round($i*100, 2)));
			else throwStatus("2",(round($i*100, 2)));
		}
		
		global $diskspacewarn, $diskspaceerror;
		
		// DiskSpace
		if ($test == "diskspace") {
			$diskusage = shell_exec("df -h"); // This request should be allowed on your server
			$diskusage = explode("\n", $diskusage);
			
			if (count($disks_to_monitor) == 0) {
				throwStatus("2","No disks to monitor. Please check your configuration file.");
			}

			$error = false;
			$warning = false;

			$details = false;
			$max_percent = 0;
			
			for ($i = 1; $i < count($diskusage); $i++) {
				$data = explode(" ",$diskusage[$i]);
				$name = $data[0];
				
				if (in_array($name, $disks_to_monitor)) {					
					$proc = explode("%",$diskusage[$i]);
					$proc = $proc[0];
					$proc = explode(" ",$proc);
					$proc = $proc[count($proc)-1];
										
					$max_percent = max($max_percent, $proc);
					
					if ($details) $details .= ", ";
					$details .= $name." (".$proc."%)";
					
					if ($proc > $diskspacewarn) $warning = true;
					if ($proc > $diskspaceerror) $error = true;
				}
			}

			if ($error) 	throwStatus("2",$max_percent." | ".$details);
			if ($warning) 	throwStatus("1",$max_percent." | ".$details);
			throwStatus("0", $max_percent." | ".$details);
		}
		
	}
?>