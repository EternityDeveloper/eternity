<?php
if (!isset($protect)){
	exit;
}

if (1 == $_REQUEST['op']) {
	$sql = "SELECT TRIM(REPLACE(CONCAT(COALESCE(primer_nombre,''),' ',COALESCE(segundo_nombre,''), ' ', COALESCE(tercer_nombre,''), ' ', COALESCE(primer_apellido,''), ' ', COALESCE(segundo_apellido,''), ' ', COALESCE(apellido_conyuge,'')),'  ', ' ')) AS nombre
			  FROM cm_planilla_asesor_tbl a
			  JOIN sys_asesor b ON a.codigo_asesor = b.codigo_asesor
			  JOIN sys_personas c ON b.id_nit = c.id_nit
			 WHERE a.anio = " .$_REQUEST['anio']. "
			   AND a.mes = " .$_REQUEST['mes']. "
			   AND a.tipo_cierre = '" .$_REQUEST['tipo_cierre']. "'
			   AND a.codigo_asesor = " .$_REQUEST['cod_asesor'];

	$result  = mysql_query($sql);
	$row = mysql_fetch_array($result);
	
	echo $row['nombre'];
}

if (2 == $_REQUEST['op']) {
	$val = array();
	$data = array();
	
	$sql = "SELECT idconcepto,
			       descripcion,
			       tipo
			  FROM cm_concepto_tbl
			 ORDER BY idconcepto";
	
	$result  = mysql_query($sql);
	
	while($row = mysql_fetch_array($result)) {
		$data['idconcepto']  = $row['idconcepto'];
		$data['descripcion'] = $row['descripcion'];
		$data['tipo']        = $row['tipo'];
		
		array_push($val, $data);
	}
	
	// Set the JSON header
	header("Content-type: text/json");
	
	print json_encode($val);
	
	//echo $val;
}

if (3 == $_REQUEST['op']) {
	$data = array();

	$sql = "SELECT idconcepto,
			       descripcion,
			       tipo
			  FROM cm_concepto_tbl
		     where idconcepto = " .$_REQUEST['idconcepto']. "
			 ORDER BY idconcepto";

	$result  = mysql_query($sql);

	while($row = mysql_fetch_array($result)) {
		$data['descripcion']  = $row['descripcion'];
		$data['tipo'] = $row['tipo'];
	}

	// Set the JSON header
	header("Content-type: text/json");

	print json_encode($data);
}