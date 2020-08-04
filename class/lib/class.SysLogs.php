<?php
/*
	SE ENCARGA DE LOS LOGS DEL SISTEMA
*/

class SysLog{
	private static $_db = null;
	private static $instance;
		
	public static function getInstance(){
		 if (!SysLog::$instance instanceof self) {
             SysLog::$instance = new SysLog();
        }
        return SysLog::$instance;
	}
	
	public function __construct($db_class){
		if ($db_class!=""){
			self::$_db=$db_class;
		}
		SysLog::$instance=$this;
	}	
	/*
		id_nit del cliente
		descripcion del log
		no_reserva si tiene reserva
		id_reserva si tiene
		objeto si queremos almacenar el objeto json de la transaccion
	*/
	public function Client($id_nit,$descripcion,$obj=NULL){
		$log= new ObjectSQL();
		$log->descripcion=$descripcion;
		$log->id_nit=$id_nit;
		$log->object=$obj;
		$log->no_reserva=$no_reserva;
		$log->id_reserva=$id_reserva;
		$log->creado_por=UserAccess::getInstance()->getIDNIT();
		$log->setTable("sys_logs");
		$SQL=$log->toSQL("insert");
		self::$_db->query($SQL);		
	}	
	public function Log($id_nit,
						$serie_contrato,
						$no_contrato,
						$no_reserva=NULL,
						$id_reserva=NULL,
						$descripcion,
						$obj=NULL,
						$type_log="INFO",
						$serie_docto="",
						$no_docto="",
						$producto_contrato_id=''){
							 
		$log= new ObjectSQL();
		$log->descripcion=$descripcion;
		$log->id_nit=$id_nit;
		$log->object=$obj;
		$log->no_contrato=$no_contrato;
		$log->serie_contrato=$serie_contrato;
		$log->no_reserva=$no_reserva;
		$log->id_reserva=$id_reserva;		
		$log->creado_por=UserAccess::getInstance()->getIDNIT();
		$log->categoria=$type_log;	
		$log->SERIE=$serie_docto;
		$log->NO_DOCTO=$no_docto;
		$log->producto_contrato_id=$producto_contrato_id;
		$log->setTable("sys_logs");
		$SQL=$log->toSQL("insert");
		self::$_db->query($SQL);		
	}
		
	
	public function Reserva($id_nit,$descripcion,$no_reserva=NULL,$id_reserva=NULL,$obj=NULL){
		$log= new ObjectSQL();
		$log->descripcion=$descripcion;
		$log->id_nit=$id_nit;
		$log->object=$obj;
		$log->no_reserva=$no_reserva;
		$log->id_reserva=$id_reserva;
		$log->creado_por=UserAccess::getInstance()->getIDNIT();
		$log->setTable("sys_logs");
		$SQL=$log->toSQL("insert");
		self::$_db->query($SQL);		
	}

}




?>