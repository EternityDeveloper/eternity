<?php
if (!isset($protect)){
	echo "Security error!";
	exit;
}


SystemHtml::getInstance()->includeClass("financiamiento","PlanFinanciamiento");

$producto=json_decode(System::getInstance()->Decrypt($_REQUEST['producto']));

 
?>
<style>
 .fsPage2{
	width:98%; 
	}
	.dataTables_wrapper{
		min-height:80px;	
	}
	.fp_transferencia{
		display:none;	
	}
	.fp_efectivo{
		display:none;
	 }
	.fp_tipo_reserva{
		display:none;		
	}

#h_ span{
	float:right;
	margin:0;
	margin-right:10px;
	color:#FFF;
	border-radius:10px;
	font-size:20px;
	height:21px;
	width:21px;
	font-weight:bold;
	text-align:center;
	cursor:pointer;
}	
#h_ span:hover{
	background-color:#FFF;
	color:#000;
}
	
</style>
<form name="form_financiamiento" id="form_financiamiento" method="post">
 
   <div id="accordion">
     <h2>MONEDA <?php echo $_REQUEST['tipo_moneda'];?></h2>
  <div >
    <table id="tb_financiamiento" width="100%" border="1" class="display fsPage tb_financiamient" style="border-spacing:2px;">
      <thead>
        <tr>
          <td align="center"><strong>Codigo</strong></td>
          <td align="center"><strong>Plazo</strong></td>
          <td align="center"><strong>% Enaganche</strong></td>
          <td align="center"><strong>Monto Eng.</strong></td>
          <td align="center"><strong>Capital financiar</strong></td>
          <td align="center"><strong>Intereses mensual</strong></td>
          <td align="center"><strong>Capital mensual</strong></td>
          <td align="center"><strong>Valor cuota</strong></td>
          <td align="center"><strong>Total financiar</strong></td>
          <td align="center"><strong>Total intereses</strong></td>
          <td align="center"><strong>% Interes</strong></td>
          <td align="center"><strong>% Impuesto</strong></td>
          <td align="center"><strong>Impuesto enganche</strong></td>
          <td align="center"><strong>impuesto cuota</strong></td>
          <td>&nbsp;</td>
        </tr>
      </thead>
      <tbody>
        <?php
		
		
if (isset($_REQUEST['tipo_moneda'])){
	 
	$plan_fin= new PlanFinanciamiento($protect->getDBLink(),$_REQUEST);
 
	$filter=array();
	if (isset($_REQUEST['type_plan_filter'])){
		$obj=json_decode($_REQUEST['type_plan_filter']);
		if (($obj->moneda!="") && ($obj->plazo!="") && ($obj->enganche!="")){
			$filter['moneda']=$obj->moneda;
			$filter['plazo']=$obj->plazo;
			$filter['enganche']=$obj->enganche;
		}
		
		if (isset($obj->situacion)){
			$filter['situacion']=$obj->situacion;	
		}
			
	
		/*VALIDA SI ES UN PRODUCTO O UN SERVICIO*/	 
		if (isset($obj->type_product)){
			SystemHtml::getInstance()->includeClass("contratos","Carrito");  
			$carrito = new Carrito($protect->getDBLink());
			$carrito->setToken($obj->token);			
			if ($obj->type_product=="producto"){ 
				$productos=$carrito->getProducto(); 
				$total=0;
				$obj= new ObjectSQL();
				foreach($productos as $key =>$val){
					$obj->id_jardin=$val->id_jardin;
					$obj->id_fases=$val->id_fases;					
 					if (trim($val->id_fases)==""){
						$obj->id_fases="NA";	
					}
					$obj->bloque=$val->bloque;
					$obj->lote=$val->lote; 
					$obj->osario=$val->osario; 
					$obj->EM_ID=$val->eEM_ID; 

					$total++;  
				} 
				$obj->total=$total;  
				$producto=$obj;
			}elseif ($obj->type_product=="servicio"){
				$producto=$carrito->getProducto();  
			}
		}		
	}
	
	
 	$filter['MONEDA']=$_REQUEST['tipo_moneda'];
	
	
	if ($_REQUEST['tipo_moneda']=="LOCAL"){ 
	
		if (!isset($producto->serv_codigo)){
			//print_r($producto);
			$data=$plan_fin->getPlanFinanByProductMonedaLocalCustom($producto,$filter);
		}else{ 
			$data=$plan_fin->getPlanFinanByServicioMonedaLocalCustom($producto,$filter);
		}
	} 
	if ($_REQUEST['tipo_moneda']=="DOLARES"){
		
	 
		if (!isset($producto->serv_codigo)){
			$data=$plan_fin->getPlanFinanByProductMonedaLocalCustom($producto,$filter);
		}else{
			$data=$plan_fin->getPlanFinanByServicioMonedaLocalCustom($producto,$filter);
		}
	} 
	 
	foreach($data as $key =>$plan){
		$EncryptID=System::getInstance()->Encrypt(json_encode($plan));
		
?>
        <tr class="enganche_<?php echo $plan['POR_ENGANCHE']?>">
          <td align="center"><?php echo $plan['CODIGO_TP']?></td>
          <td align="center"><?php echo $plan['PLAZO_CUOTAS_PF']?></td>
          <td align="center"><?php echo $plan['POR_ENGANCHE']?></td>
          <td align="center"><?php echo number_format($plan['MONTO_ENGANCHE_PF'],2)?></td>
          <td align="center"><?php echo number_format($plan['CAPITAL_FINANCIAR'],2);?></td>
          <td align="center"><?php echo number_format($plan['INTERES_MENSUAL'],2);?></td>
          <td align="center"><?php echo number_format( $plan['CAPITAL_MENSUAL'],2);?></td>
          <td align="center"><?php echo number_format( $plan['VALOR_CUOTA'],2)?></td>
          <td align="center"><?php echo number_format($plan['TOTAL_FINANCIAR'],2);?></td>
          <td align="center"><?php echo number_format($plan['TOTAL_INTERES'],2);?></td>
          <td align="center"><?php echo $plan['PORC_INTERES']?></td>
          <td align="center"><?php echo $plan['PORC_IMPUESTO']?></td>
          <td align="center"><?php echo $plan['IMPUESTO_ENGANCHE']?></td>
          <td align="center"><?php echo $plan['IMPUESTO_CUOTA']?></td>
          <td><a href="#" class="select_plan_local" id="<?php echo $EncryptID ?>"><img src="images/plus.png"></a></td>
        </tr>
        <?php
	}
}
?>
      </tbody>
    </table>
 
  </div>
 
  </div> 
 
</form>