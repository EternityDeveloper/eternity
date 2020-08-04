<?php


class Inhumacion{
	private static $db_link;
	private $_data;
	private $token;
	private static $instance;
	
	public function __construct($db_link=""){
		if ($db_link!=""){
			self::$db_link=$db_link;
			Inhumacion::$instance = $this;
		}
	} 
	public static function getInstance(){
		 if (!Inhumacion::$instance instanceof self) {
             Inhumacion::$instance = new Inhumacion();
        }
        return Inhumacion::$instance;
	}	
	
	/*OPTIENE EL LISTADO DE INHUMADO DE UN CONTRATO */
	function getListadoInhumacion($con_serie,$no_contrato){ 
		$SQL="SELECT sp.*,
					fds.*,
					sc.*,
					stt.descripcion AS estatus,
					CONCAT(ps.`primer_nombre`,' ',ps.`segundo_nombre`,' ',ps.`primer_apellido`,' ',ps.segundo_apellido) AS generado_por
				
				FROM `sp_servicios_prestados`  AS sp
				LEFT JOIN `sp_fallecidos` AS fds ON (fds.`no_servicio`=sp.`no_servicio` AND
fds.`serie_contrato`=sp.serie_contrato AND fds.no_contrato=sp.no_contrato)		
				INNER JOIN `sp_servicios_cementerio` AS sc ON (sc.`no_servicio`=sp.`no_servicio` AND
sc.`serie_contrato`=sp.serie_contrato AND sc.no_contrato=sp.no_contrato)		
				INNER JOIN sys_status AS stt ON (stt.`id_status` = sp.`id_status`)
				LEFT JOIN `sys_personas` ps ON (ps.id_nit=sp.`generado_por`)
			 WHERE sp.no_contrato='". mysql_real_escape_string($no_contrato) ."' 
			 AND sp.serie_contrato='". mysql_real_escape_string($con_serie) ."'	 ";		
		 
		$rs=mysql_query($SQL); 
		$data=array();
		while($row=mysql_fetch_assoc($rs)){
			array_push($data,$row);
		}	 
		return $data;
	}
		
	
}