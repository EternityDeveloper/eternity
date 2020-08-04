<?php


//print_r($_FILES);
print_r($_POST);


/*
include("includes/config.inc.php"); 
include("class/lib/Database.class.php"); 

$db = new Database(DB_SERVER, DB_USER, DB_PWD, DB_DATABASE); 
$db->connect(); 


$SQL="select * from localizacion_ciudad ";

$rs=mysql_query($SQL);
while($row=mysql_fetch_object($rs)){
	$SQL="update localizacion_sector set localizacion_ciudad_id='".$row->id."' 
			where cod_ciudad='".$row->cod_ciudad."' and cod_municipio='".$row->municipio_id."'";
	print_r($row);
//	mysql_query($SQL);
	
}*/

/*
$my_db=mysql_connect("localhost","root","");
mysql_select_db("comparacion",$my_db);

$ms_db = mssql_connect("USER-PC\DEBUG","jramos","A123456a");
//$ms_db = mssql_connect("GM_10","jramos","123456");
mssql_select_db("SERVICIOSM",$ms_db);*/


//compararTotales($ms_db,$my_db);
//actualizarTotales($ms_db,$my_db);

function compararTotales($ms_db,$my_db){
	$error=0;
	$colors = new Colors();
	$SQL="SELECT  * FROM sys.Tables";
	$rs=mssql_query($SQL,$ms_db);
	while($row=mssql_fetch_assoc($rs)){
		//print_r($row['name']."  --  ". getTotal_rows($row['name'])."\n");
		$total=getTotalFromComparer($my_db,$row['name']);
		$total_new=getTotal_rows($row['name']);
		if ($total!=$total_new){
			//print_r($row['name']."  --  ". getTotal_rows($row['name'])."\n");
			echo "Diferencia=> ".$row['name'] ." Total Actual ".$total . " Variacion: ".$total_new. "\n";
			$error=1;
		}
	}
	
	if ($error==0){
		echo "No hubo cambios en los registros!". "\n";

	}
}

function getTotalFromComparer($my_db,$name){
	$SQL="SELECT  * FROM  comparer where name='".$name."'";
	$rs=mysql_query($SQL,$my_db);
	$row=mysql_fetch_assoc($rs);
	return $row['total'];
}


function actualizarTotales($ms_db,$my_db){
	$SQL="SELECT  * FROM sys.Tables";
	$rs=mssql_query($SQL,$ms_db);
	while($row=mssql_fetch_assoc($rs)){
		print_r($row['name']."  --  ". getTotal_rows($row['name'])."\n");
		mysql_query("update comparer set total='".getTotal_rows($row['name'])."' where name='".$row['name']."'");
	}
}

function insertarTablas($ms_db,$my_db){
	$SQL="SELECT  * FROM sys.Tables";
	$rs=mssql_query($SQL,$ms_db);
	while($row=mssql_fetch_assoc($rs)){
		print_r($row['name']."  --  ". getTotal_rows($row['name'])."\n");
		mysql_query("insert into comparer (name) values ('".$row['name']."')");
	}
}


function getTotal_rows($table_name){

	$SQL="SELECT count(*) as total FROM ".$table_name;
	$rs=mssql_query($SQL);
	$row=mssql_fetch_assoc($rs);
	return $row['total'];
}
//echo decbin(12) . "\n";

//$var=implode(decbin(12),array());


/*
echo "\n ";
$bin=descToBin(1033);
print_r($bin);
//$bin=str_split("000001000010");
echo "\n ";
print_r(binToDesc($bin));
echo "\n ";


function descToBin($desc){
	$bin=strrev(decbin($desc));
	//$bin=$bin.substr("0000000000000000000000000000000000000000000000000000000000000000",0,64 - strlen($bin));
	return str_split($bin);
}

function binToDesc($bin){
	$desc=bindec(strrev(implode("",$bin)));
	return $desc;
}*/


class Colors {
		private $foreground_colors = array();
		private $background_colors = array();
 
		public function __construct() {
			// Set up shell colors
			$this->foreground_colors['black'] = '0;30';
			$this->foreground_colors['dark_gray'] = '1;30';
			$this->foreground_colors['blue'] = '0;34';
			$this->foreground_colors['light_blue'] = '1;34';
			$this->foreground_colors['green'] = '0;32';
			$this->foreground_colors['light_green'] = '1;32';
			$this->foreground_colors['cyan'] = '0;36';
			$this->foreground_colors['light_cyan'] = '1;36';
			$this->foreground_colors['red'] = '0;31';
			$this->foreground_colors['light_red'] = '1;31';
			$this->foreground_colors['purple'] = '0;35';
			$this->foreground_colors['light_purple'] = '1;35';
			$this->foreground_colors['brown'] = '0;33';
			$this->foreground_colors['yellow'] = '1;33';
			$this->foreground_colors['light_gray'] = '0;37';
			$this->foreground_colors['white'] = '1;37';
 
			$this->background_colors['black'] = '40';
			$this->background_colors['red'] = '41';
			$this->background_colors['green'] = '42';
			$this->background_colors['yellow'] = '43';
			$this->background_colors['blue'] = '44';
			$this->background_colors['magenta'] = '45';
			$this->background_colors['cyan'] = '46';
			$this->background_colors['light_gray'] = '47';
		}
 
		// Returns colored string
		public function getColoredString($string, $foreground_color = null, $background_color = null) {
			$colored_string = "";
 
			// Check if given foreground color found
			if (isset($this->foreground_colors[$foreground_color])) {
				$colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
			}
			// Check if given background color found
			if (isset($this->background_colors[$background_color])) {
				$colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
			}
 
			// Add string and end coloring
			$colored_string .=  $string . "\033[0m";
 
			return $colored_string;
		}
 
		// Returns all foreground color names
		public function getForegroundColors() {
			return array_keys($this->foreground_colors);
		}
 
		// Returns all background color names
		public function getBackgroundColors() {
			return array_keys($this->background_colors);
		}
	}
	

?>