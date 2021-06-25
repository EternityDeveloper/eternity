<?php 
include("class/lib/class.ObjectSQL.php");
include("includes/config.inc.php");   
include("class/lib/Database.class.php");  
//include($_PATH."class/modulos/main/class.topMenu.php");

$db = new Database(DB_SERVER, DB_USER, DB_PWD, DB_DATABASE); 
$db->connect(); 
  
$dbh = ibase_pconnect("192.168.0.4:d:/infosis/database/mfsbase.ib", "sysdba", "masterkey",'ISO8859_1') or die('die message');
/*$q = ibase_query($dbh, "select * from siad_fncbdmaytrs");
while ($r = ibase_fetch_object($q)) {
	print_r($r);
}*/
ibase_query($dbh, "DELETE from siad_fncbdmaytrs");
 
//exit;
 
$rs=mysql_query("SELECT * FROM `siad_fncbdmaytrs` where NCF='A010010010100000108'",$db->link_id);
while ($row = mysql_fetch_assoc($rs)) {
	 
	$ob= new ObjectSQL();
	$ob->push($row);
	unset($ob->FECHANEW);
	unset($ob->ESTATUS);
	unset($ob->SERIE);
	unset($ob->NO_DOCTO);  
	$ob->DEBITO="(".$ob->DEBITO.")";
	$ob->CREDITO="(".$ob->CREDITO.")";
	$ob->setTable("siad_fncbdmaytrs");
	$SQL=$ob->toSQL("insert"); 
	ibase_query($dbh,$SQL);
	print_r($ob);
 	exit;
}

 

exit; 

//ibase_query($dbh, "delete from msfcatalogo");

$fichero = 'duplicados.csv';
$actual = file_get_contents($fichero);
 
 

function saveFile($data,$file){
	file_put_contents(print_r($data,true),$file);
}

if (($gestor = fopen("CUENTAS.csv", "r")) !== FALSE) {
	$control=0;
    while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
		$obj= new ObjectSQL();
		$obj->NOMBRE=str_replace(")","",str_replace("(","",substr(trim($datos[4]),0,40)));
		
		if (trim($datos[0])!=""){
			if ($datos[0]>0){ 
				$obj->CUENTA=trim($datos[0]);
 				$obj->TIPO="C";
				$obj->GRUPO=trim($datos[0]); //PRIMER CARACTER DE LA CUENTA
				$obj->NIVEL="0";
				$obj->CONTROL=$control;
				$obj->IDCIA="FNC";
				$obj->setTable("MSFCATALOGO");
				$SQL=$obj->toSQL("insert");
				$result=ibase_query($dbh,$SQL);   
				if (!$result) {
					saveFile($datos,$actual);
					
					echo "Error. Can't insert the record with the query: $query!";
					exit;
				}
			} 
		}else if (trim($datos[1])!=""){
			$obj->CUENTA=trim($datos[1]);
 			$obj->TIPO="C";
			$obj->GRUPO=substr(trim($datos[1]),0,1); //PRIMER CARACTER DE LA CUENTA
			$obj->NIVEL="0";
			$obj->CONTROL=0;
			$obj->IDCIA="FNC";
			$obj->setTable("MSFCATALOGO");
			$SQL=$obj->toSQL("insert");
			$result=ibase_query($dbh,$SQL);   
			if (!$result) {
				saveFile($datos,$actual);
				echo "Error. Can't insert the record with the query: $query!"; 
			}
			$control=$obj->CUENTA;			 
	 
		}else if (trim($datos[2])!=""){
			$obj->CUENTA=trim($datos[2]); 
			$obj->TIPO="A";
			$obj->GRUPO=substr(trim($datos[2]),0,1); //PRIMER CARACTER DE LA CUENTA
			$obj->NIVEL="0";
			$obj->CONTROL=$control;
			$obj->IDCIA="FNC";
			$obj->setTable("MSFCATALOGO");
			$SQL=$obj->toSQL("insert");
			$result=ibase_query($dbh,$SQL);   
			if (!$result) {
				saveFile($datos,$actual);
				echo "Error. Can't insert the record with the query: $query!"; 
			}
			$control=$obj->CUENTA;			
			print_r($obj);
			
			 
		} 
    }
    fclose($gestor);
}



?>