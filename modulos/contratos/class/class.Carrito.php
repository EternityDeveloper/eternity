<?php

class Carrito{
	private $data;
	private $db_link;
	private $message=array("mensaje"=>"","error"=>true);
	private $token=0; //ES EL ID DE REFERENCIA DE LA TRANSAPCION
	public function __construct($db_link,$data=null){
		$this->data=$data;
		$this->db_link=$db_link;
	}
	public function setToken($token){
		$this->token=$token;	
		if (!isset($_SESSION['CARRITO_DATA'][$this->token])){
			$_SESSION['CARRITO_DATA'][$this->token]=array(
												"producto"=>array(),
												"plan"=>array(),
												"descuento"=>array());
		}
	}
	public function getToken(){
		return $this->token;
	}	
	public function getCarritoData(){
		return $_SESSION['CARRITO_DATA'];
	}
	public function saveItem($token,$estatus=false){
		if (!isset($_SESSION['CARRITO_ITEMS'])){
			$_SESSION['CARRITO_ITEMS']=array();
		} 
		/*VERIFICO QUE NO EXISTA*/ 
 		if (!array_key_exists($token,$_SESSION['CARRITO_ITEMS'])){
			$_SESSION['CARRITO_ITEMS'][$token]=$estatus;
		}else{ 
			if (!$_SESSION['CARRITO_ITEMS'][$token]){
				$_SESSION['CARRITO_ITEMS'][$token]=$estatus;
			}			
		}
	}
	/*Remueve la session que no esta en uso*/
	public function removeItem($token=""){
		if ($token==""){
			$items= $this->getListItem(); 
			foreach($items as $key =>$value){
				if (!$value){
					unset($_SESSION['CARRITO_ITEMS'][$key]);	
				}
			}
		}else{
			if (isset($_SESSION['CARRITO_ITEMS'][$token])){
				unset($_SESSION['CARRITO_ITEMS'][$token]);	
			}
		}
	}

	public function getListItem(){
		return $_SESSION['CARRITO_ITEMS'];	
	}
	public function session_restart(){
		$_SESSION['CARRITO_DATA']=array();
		$_SESSION['CARRITO_ITEMS']=array();
	}
	
	public function addProducto($producto){
		/*RECORRIENDO LA LISTA DE PRODUCTOS*/
		if (isset($producto->lote)){
 			SystemHtml::getInstance()->includeClass("inventario","Inventario"); 
			SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
			
			$id=$producto->id_jardin."_".
				$producto->id_fases."_".
				$producto->lote."_".
				$producto->bloque."_".
				$producto->no_reserva."_".
				$producto->osario;
				 
			if (!isset($_SESSION['CARRITO_DATA'][$this->token]['producto']['item'])){
				$_SESSION['CARRITO_DATA'][$this->token]['producto']['item']=array(); 
			}    
			
			
			$contrato= new Contratos($this->db_link); 
			$contratante=$contrato->getContratante();
			/*BUSCO EN EL INVENTARIO PARA SABER CUAL ES LA CANTIDAD DE PARCELA QUE TIENE RESERVADO UN CLIENTE*/		
			$inv= new Inventario($this->db_link); 
			//$filter=$producto->bloque.$producto->id_fases.$producto->id_jardin ;
			$filter=$producto->id_fases.$producto->id_jardin ;
			$total=$inv->getTotalReservaEqualParcela($contratante['idnit'],$filter);		
			/*-------------------------------------------------------------------------*/
	 				
			if (count($_SESSION['CARRITO_DATA'][$this->token]['producto'])>0){ 
				/*VALIDO SI EL PRODUCTO YA ESTA AGREGADO*/
				if (!array_key_exists($id,$_SESSION['CARRITO_DATA'][$this->token]['producto']['item'])){
					$_SESSION['CARRITO_DATA'][$this->token]['producto']['item'][$id]=$producto; 
					//$_SESSION['CARRITO_DATA'][$this->token]['plan']=array();
					return array("valid"=>1,"total_reserva"=>$total); //AGREGADO
				}else{ 
 					return array("valid"=>2,"total_reserva"=>$total); //AGREGADO
				}
			}else{
				$_SESSION['CARRITO_DATA'][$this->token]['producto']['item'][$id]=$producto; 
			//	$_SESSION['CARRITO_DATA'][$this->token]['plan']=array();
				return array("valid"=>1,"total_reserva"=>$total); //AGREGADO
			}  
		} 
		if (isset($producto->serv_codigo)){ 
			$_SESSION['CARRITO_DATA'][$this->token]['producto']['item']=$producto; 
			return array("valid"=>1,"total_reserva"=>0); //AGREGADO
		} 		
				
	}
	/*UTILIZADO PARA ELIMINAR UN PRODUCTO DEL CARRITO*/
	public function removeProducto($producto){ 
		
		if (isset($producto->lote)){ 
			$id=$producto->id_jardin."_".
				$producto->id_fases."_".
				$producto->lote."_".
				$producto->bloque."_".
				$producto->no_reserva."_".
				$producto->osario;  
			if (count($_SESSION['CARRITO_DATA'][$this->token]['producto']['item'])>0){ 
				/*VALIDO SI EL PRODUCTO YA ESTA AGREGADO*/
				if (array_key_exists($id,$_SESSION['CARRITO_DATA'][$this->token]['producto']['item'])){
					unset($_SESSION['CARRITO_DATA'][$this->token]['producto']['item'][$id]);  
				} 
			} 
		} 
		 
		return 1;
	}

	public function getProducto(){
		if (isset($_SESSION['CARRITO_DATA'][$this->token]['producto']['item'])){
			return $_SESSION['CARRITO_DATA'][$this->token]['producto']['item'];	 
		}else{
			return array();	
		}
	}	
	/*
		Agregar el financiamiento a un producto
		retorna 3 codigos invalid, valid, duplicado
	*/
	public function addFinanciamiento($plan){ 
		/*VERIFICO QUE EXISTA UN PRODUCTO */
		$product=$this->getProducto();  
		if (isset($product)){
			if (count($product)==0){
				return array("code"=>"invalid");
			}
		}else{
			return array("code"=>"invalid");	
		}  
		/*RECORRIENDO LA LISTA DE PRODUCTOS*/
		$_SESSION['CARRITO_DATA'][$this->token]['plan']=$plan;
		
		return array("code"=>"valid");
	}	
	/*
		Actualiza el financiamiento a un producto
		retorna 3 codigos invalid, valid, duplicado
	*/
	public function updateMontoFinanciamiento($plan,$monto){ 
		/*VERIFICO QUE EXISTA UN PRODUCTO */
		$product=$this->getProducto(); 
	 
		if ($monto>0){
			$plan->precio=$monto;
		}
		if (isset($product)){
			if (count($product)==0){
				return array("code"=>"invalid");
			}
		}else{
			return array("code"=>"invalid");	
		} 
		 
		/*RECORRIENDO LA LISTA DE PRODUCTOS*/
		$_SESSION['CARRITO_DATA'][$this->token]['plan']=$plan;
		
		return array("code"=>"valid");
	}	  
	public function getFinanciamiento(){
		return $_SESSION['CARRITO_DATA'][$this->token]['plan'];	 
	}
	/*
		Agregar un descuento a un producto
		retorna 3 codigos invalid, valid, duplicado
	*/
	public function addDescuento($desc){  
		/*CHEQUEA QUE EXISTA EL ID DESCUENTO*/
		if (!isset($desc->descuento_id)){
			return array("code"=>"invalid");
		}  
		if (!isset($_SESSION['CARRITO_DATA']['descuento'])){
			$_SESSION['CARRITO_DATA']['descuento']=array();	
		}
		array_push($_SESSION['CARRITO_DATA']['descuento'],$desc);  
		return array("code"=>"valid","index"=>count($_SESSION['CARRITO_DATA']['descuento']));
	}
	
	public function getDescuento(){
		if (!isset($_SESSION['CARRITO_DATA']['descuento'])){
			return array();
		}
		return $_SESSION['CARRITO_DATA']['descuento'];	 
	}
		
	public function removeDescuento($index){  
		if (isset($_SESSION['CARRITO_DATA']['descuento'][$index])){
			unset($_SESSION['CARRITO_DATA']['descuento'][$index]); 
		}
	}		
	
	public function validarItems(){
		/*VERIFICO QUE EXISTA UN PRODUCTO */
		$product=$this->getProducto();
		if (count($product)==0){
			return array("code"=>"invalid");
		} 
		/*VERIFICO QUE EXISTA UN PLAN DE FINANCIAMIENTO */
		$plan=$this->getFinanciamiento();
		if (count($plan)==0){
			return array("code"=>"invalid");
		}	
 
		return array("code"=>"valid");
	}
	
	public function getDetalle(){
		/*VERIFICO QUE EXISTA UN PRODUCTO */
		$product=$this->getProducto(); 
 		
		if (count($product)==0){
			return array("code"=>"invalid");
		} 
		/*VERIFICO QUE EXISTA UN PLAN DE FINANCIAMIENTO */
		$plan=$this->getFinanciamiento();

		
		if (count($plan)==0){
			return array("code"=>"invalid");
		} 
		$product_total=1;
		/*DETERMINO SI ES UN SERVICIO*/
		if (isset($product->serv_codigo)){
			$product_total=$product->cantidad;
		}else{
			$product_total=count($product);
		} 
		
		$detalle=array(
			'precio_lista'=>$plan->precio*$product_total,
			'sum_descuento_por'=>0,
			'porciento_to_monto'=>0,
			'sum_descuento_monto'=>0,
			'total_descuentos'=>0,
			'capital_a_financiar_menos_descuento'=>0,
			'monto_enganche'=>0,
			'porciento_enganche'=>$plan->por_enganche,
			'capital_neto_a_pagar'=>0,
			'interes_anual'=>$plan->por_interes,
			'monto_interes_anual'=>0,
			'monto_total_interes_a_pagar'=>0,
			'plazo'=>$plan->plazo,
			'capital_cuota'=>0,
			'total_interes_cuota'=>0,
			'mensualidades'=>0,
			'sub_total_a_pagar'=>0,
			'total_a_pagar'=>0
		);
		
		$items=$this->getListItem();  
		
		/*EXTRAIGO LA SUMATORIA DE LOS DESCUENTOS MONTO Y PORCIENTO*/
		/*Eliminado por cambio en la creacion de contratos*/
		$desc=$this->getDescuento();
		$aplicar_desc=false;
		foreach($desc as $key => $descuento){ 
			if ($descuento->type=="MONTO"){
				$detalle['sum_descuento_monto']=($detalle['sum_descuento_monto']+$descuento->monto);
			}
			if ($descuento->type=="PORCIENTO"){
				$detalle['sum_descuento_por']=($detalle['sum_descuento_por']+$descuento->porcentaje);
			}			
		}
		
 		/*CALCULO LA SUMA DE LOS PORCIENTO EN MONTO*/
		$detalle['porciento_to_monto']=($detalle['precio_lista']*$detalle['sum_descuento_por']/100);
		$detalle['total_descuentos']=($detalle['porciento_to_monto']+$detalle['sum_descuento_monto']); 		
		/*CALCULO LA SUMA DE LOS PORCIENTO EN MONTO*/
		$detalle['porciento_to_monto']=$detalle['precio_lista']*$detalle['sum_descuento_por']/100;	
		$detalle['total_descuentos']=$detalle['porciento_to_monto']+$detalle['sum_descuento_monto'];
		
		$detalle['capital_a_financiar_menos_descuento']=$detalle['precio_lista']-$detalle['total_descuentos'];
		
		$detalle['monto_enganche']=$detalle['capital_a_financiar_menos_descuento']*$detalle['porciento_enganche']/100;
		
		$detalle['capital_neto_a_pagar']=$detalle['capital_a_financiar_menos_descuento']-$detalle['monto_enganche'];
		
		$detalle['monto_interes_anual']=$detalle['interes_anual']*$detalle['capital_neto_a_pagar']/100;
		
		$detalle['capital_cuota']=0;
 		$detalle['monto_total_interes_a_pagar']=0;
 		
		if ($detalle['plazo']>0){
			$detalle['monto_total_interes_a_pagar']=($detalle['monto_interes_anual']/12)*$detalle['plazo'];
			$detalle['capital_cuota']=$detalle['capital_neto_a_pagar']/$detalle['plazo'];
			$detalle['total_interes_cuota']=$detalle['monto_total_interes_a_pagar']/$detalle['plazo'];
		}
		
		$detalle['mensualidades']=$detalle['total_interes_cuota']+$detalle['capital_cuota'];
		
		$detalle['sub_total_a_pagar']=$detalle['monto_total_interes_a_pagar']+ $detalle['capital_neto_a_pagar'];
		
		$detalle['total_a_pagar']=$detalle['sub_total_a_pagar']+ $detalle['monto_enganche'];
		return $detalle;
	}
	
	public function getDetalleGeneral(){ 
		SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
		SystemHtml::getInstance()->includeClass("caja","Caja"); 
		
		$detalle=array(
			'precio_lista'=>0,
			'total_descuentos'=>0,
			'capital_a_financiar_menos_descuento'=>0,
			'monto_enganche'=>0,  
			'monto_pago_caja'=>0,
			'monto_total_interes_a_pagar'=>0,
			'mensualidades'=>0, 
			'total_a_pagar'=>0,
			'plazo' =>0,
			'interes_anual'=>0
		);
		
		/*EXTRAIGO LA SUMATORIA DE LOS DESCUENTOS MONTO Y PORCIENTO*/
		/*Eliminado por cambio en la creacion de contratos*/
		$desc=$this->getDescuento();
		$_descuento=array();
		foreach($desc as $key => $descuento){ 
			$descuento_id=json_decode(System::getInstance()->Decrypt($descuento->descuento_id));
			$_descuento[$descuento_id->prioridad]=$descuento;
		}
			
		foreach($desc as $key => $descuento){ 
  			if ($descuento->type=="MONTO"){
				$detalle['sum_descuento_monto']=$detalle['sum_descuento_monto']+$descuento->monto;
			}
			if ($descuento->type=="PORCIENTO"){
				$detalle['sum_descuento_por']=$detalle['sum_descuento_por']+$descuento->porcentaje;
			}			
		}			

		$caja=new Caja($this->db_link);
		$_contratos=new Contratos($this->db_link); 

		$items=$this->getListItem();  
		
		foreach($items as $key => $val){  
			$this->setToken($key);
			$prod= $this->getProducto();
			$detail=$this->getDetalle(); 

			$detalle['precio_lista']=$detalle['precio_lista']+$detail['precio_lista'];
			$detalle['monto_enganche']=$detalle['monto_enganche']+$detail['monto_enganche'];
			$detalle['monto_total_interes_a_pagar']=$detalle['monto_total_interes_a_pagar']+$detail['monto_total_interes_a_pagar'];
			$detalle['mensualidades']=$detalle['mensualidades']+$detail['mensualidades'];
			$detalle['total_a_pagar']=$detalle['total_a_pagar']+$detail['total_a_pagar'];
			$detalle['plazo']=$detail['plazo'];
			
			if ($detail['interes_anual']>0){
				$detalle['precio_lista_total']=$detalle['precio_lista_total']+$detail['precio_lista'];				
				$detalle['interes_anual']=$detail['interes_anual'];					
			}
			
			$detalle['porciento_enganche']=$detail['porciento_enganche'];
			$detalle['capital_neto_a_pagar']=$detalle['capital_neto_a_pagar']+$detail['capital_neto_a_pagar'];
			//$detalle['sum_descuento_por']=$detalle['sum_descuento_por']+$detail['sum_descuento_por']; 
			//$detalle['porciento_to_monto']=$detalle['porciento_to_monto']+$detail['porciento_to_monto']; 
			//$detalle['total_descuentos']=$detalle['total_descuentos']+$detail['total_descuentos']; 	

 		}  

		$detalle['capital_a_financiar_menos_descuento']=$detalle['precio_lista'];	

		foreach($desc as $key => $descuento){ 
  			if ($descuento->type=="MONTO"){
				$detalle['capital_a_financiar_menos_descuento']=($detalle['capital_a_financiar_menos_descuento'])
																-$descuento->monto;
				$detalle['total_descuentos']=$detalle['total_descuentos']+$descuento->monto;
			}
			
			if ($descuento->type=="PORCIENTO"){
 				$_monto=$detalle['capital_a_financiar_menos_descuento'];
				$detalle['porciento_to_monto']=$detalle['porciento_to_monto']+(($_monto*$descuento->porcentaje)/100);				
				$detalle['total_descuentos']=$detalle['total_descuentos']+(($_monto*$descuento->porcentaje)/100);
				$detalle['capital_a_financiar_menos_descuento']=$_monto-(($_monto*$descuento->porcentaje)/100);	
			}					

		}	
		
  
 		$detalle['monto_enganche']=($detalle['capital_a_financiar_menos_descuento']*$detalle['porciento_enganche']/100);
		$sp=explode(".",$detalle['monto_enganche']);
		$len=strlen($sp[0])+(strlen($sp[1]));	 
		$detalle['monto_enganche']=$this->truncateFloat($detalle['monto_enganche'],2);
 		/*VALIDA SI DENTRO DE LOS ABONOS EXISTE UN INICIAL*/
		$existe_inicial=false;
		$getitem=$caja->getItemListAbono();	
		$monto=0;
		foreach($getitem as $key=>$val){  
		 	if ($val->TIPO_MOV=="INI"){
				$existe_inicial=true;
			}
		 	if ($val->TIPO_MOV=="CTI"){
				$existe_inicial=true;
			}			
		 	if ($val->TIPO_MOV=="NC"){
				$existe_inicial=true;
			}
			$monto=$monto+$val->MONTO;
		}		

		$sp=explode(".",$monto);
		$len=strlen($sp[0])+(strlen($sp[1]));	 
		$detalle['monto_pago_caja']		= 	round($monto,2);  
		$detalle['monto_enganche_caja']	=	round($monto,2);  

		$detalle['capital_neto_a_pagar']=$detalle['capital_a_financiar_menos_descuento']-$detalle['monto_enganche'];
		
		if (round($detalle['monto_pago_caja'],2)>=round($detalle['monto_enganche'],2))
		{
			$detalle['capital_neto_a_pagar']=$detalle['capital_a_financiar_menos_descuento']-$detalle['monto_enganche_caja'];
		}

		$detalle['monto_interes_anual']=$detalle['interes_anual']*$detalle['capital_neto_a_pagar']/100;

		$detalle['monto_total_interes_a_pagar']=0;
		if ($detalle['plazo']>0){
			$detalle['monto_total_interes_a_pagar']=$detalle['monto_interes_anual']*($detalle['plazo']/12);
		} 
  		    
		if ($detalle['plazo']>0){
			$detalle['total_interes_cuota']=$detalle['monto_total_interes_a_pagar']/$detalle['plazo']; 
		}else{
			$detalle['total_interes_cuota']=0;
		}
		if ($detalle['plazo']>0){
			$detalle['capital_cuota']=$detalle['capital_neto_a_pagar']/$detalle['plazo']; 	
		}else{
			$detalle['capital_cuota']=0;
		}
		$detalle['mensualidades']=$detalle['total_interes_cuota']+ $detalle['capital_cuota'];	

		$detalle['sub_total_a_pagar']=$detalle['monto_total_interes_a_pagar']+ $detalle['capital_neto_a_pagar'];	
		$detalle['total_a_pagar']=$detalle['sub_total_a_pagar'];//+ $detalle['monto_enganche'];
  
		$detalle['doProcesarSolicitud']=false;
		$detalle['doMensaje']="";
		 
		/*VALIDO SI LOS EL MONTO PAGADO EN CAJA ES MAYOR IGUAL AL MONTO DE ENGANCHE Y SI EXISTE INICIAL
		ENTONCES SE PUEDE PROCESAR EL CONTRATO*/ 
//		$sp=explode(".",$detalle['monto_pago_caja']);
//		$len=strlen($sp[0])+(strlen($sp[1])-1);
		if (round($detalle['monto_pago_caja'],2)>=round($detalle['monto_enganche'],2))
		{ 
			/* SI EXISTE UN MONTO INICIAL ENTONCES PERMITE PROCEDER A CREAR LA SOLICITUD*/
			if ($existe_inicial){
				$detalle['doProcesarSolicitud']=true;
			}else{
				$detalle['doMensaje']="No existe un inicial para poder procesar la solicitud";		
			}
		}else{
			$detalle['doMensaje']="El Monto inicial es menor al ".$detalle['porciento_enganche']."%";	
		}
	   
		//print_r($detalle); 
		return $detalle; 
	}
	/**
	* funcion para convertir un numero a decimal con X digitos
	* @param String $number
	* @param Int $digitos cantidad de digitos a mostrar
	* @return Float
	*/
	public function truncateFloat($number, $digitos)
	{
		$raiz = 10;
		$multiplicador = pow ($raiz,$digitos);
		$resultado = ((int)($number * $multiplicador)) / $multiplicador;
		return $resultado;
	 
	}	
	
}

?>