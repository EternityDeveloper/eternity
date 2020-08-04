<?php
include("class/lib/class.ObjectSQL.php");

$obj= new ObjectSQL();
//$SQL=$obj->getSQL("insert","sys_personas");


echo $SQL;



exit;
$fila = 1;
if (($gestor = fopen("REPORTE_AUDITORIA.csv", "r")) !== FALSE) {
    while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
        $numero = count($datos);
       
        for ($c=0; $c < $numero; $c++) {
			$sp=explode(";",$datos[$c]);
		//	if ($fila==0){
				for ($i=0;$i<=count($sp);$i++){

					echo trim(str_replace(" ","_",filter($sp[$i])))."\n";
					
				}
				
				exit;
	
		
			
        }
		exit;
		$fila++;
    }
    fclose($gestor);
}





function filter($data){
	$patterns = array();
	$patterns[0] = '.';
	$patterns[1] = '$';
	for($i=0;$i<=count($patterns);$i++){
		$data= str_replace($patterns[$i],"",$data);
	}
	return $data;
}



?>