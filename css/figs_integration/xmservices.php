<?php
include("includes/db_config.php");

	error_reporting(E_ALL);
	ini_set("display_errors", 1);
 
	if (isset($_REQUEST['consult']))
	{	
		
		switch($_REQUEST['consult']){
			case 1:
				$search="";
				if (!isset($_REQUEST['search']))
				{
					exit;
				}
				if (strlen($_REQUEST['search'])<4){
					exit;	
				} 
				$search=mysql_escape_string(trim($_REQUEST['search']));
		
				include("listado_clientes.php");
				break;
			case 2:
				include("estado_cuenta.php");
			break;
		}
		
	}

?>
