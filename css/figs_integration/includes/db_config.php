<?php 
 
	$ms_db = mssql_connect("192.168.0.4","memorial","A123456a") or die("Unable to connect to server");
	mssql_select_db("SERVICIOSM",$ms_db);



?>
