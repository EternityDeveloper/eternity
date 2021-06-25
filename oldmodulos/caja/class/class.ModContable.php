<?php 


class ModContable{
	private $db_link;
	private $_data;
	private $_dbh;
	private $fecha="";
	
	public function __construct($db_link){
		$this->db_link=$db_link;
		//$this->_dbh = ibase_pconnect(SIAD_DB_DATABASE,SIAD_DB_USER,SIAD_DB_PWD,'ISO8859_1') or die('die message');
	}
	public function setFecha($fecha){
		$this->fecha=$fecha;
	}
	/*
		ID_EMPRESA => EMPRESA CONTABLE DONDE SE REGISTRARA EL ASIENTO
		PERIODO => PERIDODO CONTABLE
		CODIGO_TRANS => CODIGO DE TRANSACCION CONTABLE
		SECUENCIA => SECUENCIA EN QUE SERA APLICADO EL ASIENTO
		CUENTA => CUENTA A LA QUE SERA APLICADA LA TRANSACION
		TIPO_TRANS => TIPO DE TRANSACCION SI ES CREDITO O DEBITO
		MONTO => EL MONTO APLICADO A
		CENTRO_COSTO => SI HAY CENTRO DE COSTO
		REFERENCIA => REFERENCIA DE LA TRANS, EN MI CASO NUMERO DE FACTURA
		DESCRIPCION => ALGUNA DESCRIPCION DE LA TRANS.
		SERIE_DOC => SERIE DE RECIBO DE LA TRANSACCION
		NO_DOCTO => NO DEL RECIBO
		NCF => EN CASO DE GENERAR NCF PONERLO AQUI,
		RNC_CEDULA => EN CASO DE SER FACTURA FISCAL HAY QUE LLENAR ESTE CAMPO CON EL DOCUMENTO
		NCF_REFERENCIA => REFERENCIA AL NCF DE UNA TRANSACCION DE NOTA DE CREDITO/DEBITO
		TIPO_TRS => DEFINE SI ES FISCAL O NO FISCAL 

		///PARA LLEBAR EL REGISTRO DE ACTIVIDADES
		$LOG=array(
			"ID_NIT"=>"",
			"SERIE_CONTRATO"=>"",
			"NO_CONTRATO"=>"",
			"NO_RESERVA"=>"",
			"ID_RESERVA"=>"",												
		);		
	*/
	public function registrarAsientoC($LOG,
									  $ID_EMPRESA,
									  $PERIODO,
									  $CODIGO_TRANS,
									  $SECUENCIA,
									  $CUENTA,
									  $TIPO_TRANS,
									  $MONTO,
									  $REFERENCIA,
									  $DESCRIPCION="N/A",
									  $CENTRO_COSTO="N/A",
									  $SERIE_DOC="",
									  $NO_DOCTO="",
									  $NCF="",
									  $RNC_CEDULA="",
									  $NCF_REFERENCIA="",
									  $TIPO_TRS="",
									  $MONTO_TOTAL=0){
		/*PROCEDO A REGISTRAR LA TRANSACCION FISCAL AL LIBRO DE MOVIMIENTOS QUE SERA 
		IMPORTADO AL SISTEMA CONTABLE DESPUES DEL CIERRE*/ 
			$siad = new ObjectSQL();
			$siad->IDCIA=$ID_EMPRESA;
			$siad->PERIODO=$PERIODO;
			$siad->CODIGOTRS=$CODIGO_TRANS;
			$siad->NUMEROTRS=rand();
			$siad->SECUENCIA=$SECUENCIA;
			$siad->CUENTA=$CUENTA;
			$siad->DESCRIPCION=$DESCRIPCION; 
			$siad->SERIE=$SERIE_DOC;
			$siad->NO_DOCTO=$NO_DOCTO; 
			$siad->RNC_CEDULA=$RNC_CEDULA;
			$siad->NCF_REFERENCIA=$NCF_REFERENCIA;
			$siad->NCF=$NCF;
			$siad->TIPO_TRS=$TIPO_TRS;
			$siad->CREDITO=0;
			$siad->DEBITO=0;
			$siad->MONTO_TOTAL=$MONTO_TOTAL;
			if ($TIPO_TRANS=="CREDITO"){
				$siad->CREDITO=$MONTO; 
			}
			if ($TIPO_TRANS=="DEBITO"){
				$siad->DEBITO=$MONTO;
			} 
			if (!(trim($this->fecha)=="")){
				$siad->FECHATRS=$this->fecha;
			}
			$siad->ESTATUS=1;
			$siad->CENTRO=$CENTRO_COSTO;
			$siad->REFERENCIA=$REFERENCIA;
			
			$siad->setTable('siad_fncbdmaytrs'); 
			$SQL=$siad->toSQL('insert');	 
		 
			mysql_query($SQL);  
			SysLog::getInstance()->Log($LOG['ID_NIT'], 
								 $LOG['SERIE_CONTRATO'],
								 $LOG['NO_CONTRATO'] ,
								 $LOG['NO_RESERVA'],
								 $LOG['ID_RESERVA'],
								 $TIPO_TRANS." ".$DESCRIPCION." ".$siad->SERIE."-".$siad->NO_DOCTO,
								 json_encode($siad),
								 "TRANSAPCION_CONTABLE");
								 							 		 
	}	
	public function getCatalogo($tipo){
		$SQL="SELECT * FROM catalogo_cuenta WHERE tipo_cuenta='".$tipo."'";
		$rs=mysql_query($SQL,$this->db_link->link_id);
		$rt=array();
		while($row=mysql_fetch_assoc($rs)){
			$rt[$row['codigo_interno']]=$row;			
		}
		return $rt;
	}
	
}
?>