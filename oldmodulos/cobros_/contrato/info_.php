<?php
if (!isset($protect)){
	exit;
}
if (!isset($_REQUEST['id'])){
	exit;
} 



$id_contrato=$_REQUEST['id'];
$contrato=json_decode(System::getInstance()->Decrypt($id_contrato));
$id_nit=$contrato->id_nit;


SystemCache::GI()->doCacheName("detalle_".$contrato->serie_contrato.$contrato->no_contrato); 
$cache=SystemCache::GI()->getCache();
 
$direccion="";

if (isset($contrato->serie_contrato)){
	SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
	SystemHtml::getInstance()->includeClass("client","PersonalData");
	SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	SystemHtml::getInstance()->includeClass("estructurac","Asesores"); 
	
	$cobros= new Cobros($protect->getDBLINK()); 
	$cobros->session_restart();
	
	$caja= new Caja($protect->getDBLINK());
	$caja->session_restart(); 
	$caja->setObject($contrato);
	
	$con=new Contratos($protect->getDBLink()); 
	$person= new PersonalData($protect->getDBLink(),$_REQUEST);
 
	$cdata=$con->getInfoContrato($contrato->serie_contrato,$contrato->no_contrato);
	if (count($cdata)<=0){
		exit;	
	}	
	$ofi_moto=Cobros::getInstance()->getCobradorMotorizadoAreaC($cdata->serie_contrato,$cdata->no_contrato);
	$peron_data=$person->getClientData($cdata->id_nit_cliente);

	$addressData=$person->getAddress($cdata->id_nit_cliente);	
	
	$id_direccion="";
	foreach($addressData as $key=>$val){  
		$val=(array)$val; 
		$id_direccion=System::getInstance()->Encrypt(json_encode($val));
	 
		$direccion=$val['ciudad'] .", ".$val['sector'];
		$direccion.=trim($val['avenida'])!=""?",".$val['avenida']:'';
		$direccion.=trim($val['calle'])!=""?",".$val['calle']:'';
		$direccion.=trim($val['zona'])!=""?",".$val['zona']:'';
		$direccion.=trim($val['manzana'])!=""?",".$val['manzana']:'';
		$direccion.=trim($val['numero'])!=""?",".$val['numero']:'';
		$direccion.=trim($val['referencia'])!=""?",".$val['referencia']:'';
		$direccion.=trim($val['observaciones'])!=""?",".$val['observaciones']:''; 
		if ($val->tipo=="Cobro"){
			break;	
		}
	} 
	
	
	$listContract=$con->getContractListFromPerson($cdata->id_nit_cliente);	
	$capita_interes=$con->getCapitalInteresCuotaFromContrato($contrato->serie_contrato,$contrato->no_contrato);	
	
	$ase= new Asesores($protect->getDBLINK());
	$asesor_data=$ase->getComercialParentData($cdata->codigo_asesor);
	 

	
}else{
	exit;	
}

 
$data=$con->getDetalleGeneralFromContrato($contrato->serie_contrato,$contrato->no_contrato);
$tasa_cambio=$caja->getTasaActual($cdata->tipo_moneda);	

$product=$con->getDetalleProductsFromContrato($contrato->serie_contrato,$contrato->no_contrato);
$servicios=$con->getDetalleServicioFromContrato($contrato->serie_contrato,$contrato->no_contrato);


$addressData=$person->getAddress($cdata->id_nit_cliente);
$phoneData=$person->getPhone($cdata->id_nit_cliente);
  
$phone="";
foreach($phoneData as $key=>$val){   
	$val=(array)$val;
	$phone.=$val['area'].$val['numero']; 
	if ($val->tipo==2){
		$phone.=" Ext.".$val['extencion'];
	}
	$phone.=", ";  
}
$phone=substr($phone,0, strlen($phone)-2); 

	$id_nit=System::getInstance()->Encrypt($cdata->id_nit_cliente);
 
	 

?>
<div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade in active" id="home">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size:12px">
                        <tr>
                          <td height="25" colspan="3" align="left" ><strong>INFORMACION DEL CONTRATO</strong></td>
                        </tr>
                        <tr>
                          <td width="430" align="left" valign="top" ><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tb_detalle">
                            <tr>
                              <td width="137" height="25" align="left"  style="background-color:#CCCCCC"><strong>FECHA COMPRA</strong></td>
                              <td width="219"  ><?php echo $cdata->fecha_venta;?></td>
                            </tr>
                            
                            <tr>
                              <td height="25" align="left"  style="background-color:#CCCCCC"><strong>PRODUCTO</strong></td>
                              <td  ><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td><?php if (count($product)>0){?><?php 		   
		
		foreach($product as $key =>$producto){
			$producto=(array)$producto;
			$id=System::getInstance()->Encrypt(json_encode($producto));
			//print_r($producto);
	//		echo  '<a href="#">'.."</a>"."<br>";
			echo 	'<div class="dropdown">
			  		<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="true">
				'.$producto['jardin']." ".$producto['id_jardin']."-".$producto['id_fases']."-".$producto['bloque']."-".$producto['lote'].$producto['osario'].'
				<span class="caret"></span>
			  </button>';
			  
			  echo '<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">';
			   if ($protect->getIfAccessPageById(192)){  
			 	echo ' <li role="presentation"><a role="menuitem" tabindex="-1" class="_change" href="#" id="'.$id.'" >Cambiar ubicación</a></li> <li class="divider"></li> '; 
			   }
			   
			    if ($protect->getIfAccessPageById(193)){  
					echo '<li role="presentation"><a role="menuitem" tabindex="-1" class="_remove_parcela" href="#" id="'.$id.'" >Remover parcela</a></li> ';
				}
			   
			echo '</ul>';
				
				
			echo '</div>';
		}
		?>
        <?php } ?></td>
                                  <td><?php if ($protect->getIfAccessPageById(174)){ ?>
                                    <input type="submit" name="add_parcela" id="add_parcela" class="btn btn-primary" value="Agregar" contrato="<?php echo $_REQUEST['id'];?>" id_nit="<?php echo System::getInstance()->Encrypt($cdata->id_nit_cliente);?>" />
                                     <?php } ?></td>
                                </tr>
                              </table></td>
                            </tr>
                           
                            <?php if (count($servicios)>0){?>
                            <tr>
                              <td height="25" align="left"  style="background-color:#CCCCCC"><strong>SERVICIO</strong></td>
                              <td><?php 		   
		
		foreach($servicios as $key =>$srv){
			echo  $srv['serv_descripcion']."<br>";
		}
		?></td>
                            </tr>
                            <?php } ?>
                            <tr>
                              <td height="25" align="left"  style="background-color:#CCCCCC"><strong>EMPRESA</strong></td>
                              <td><span style="margin:0px;">
                                <?php //echo $cdata->serie_contrato ." ".$cdata->no_contrato;?>
                              </span></td>
                            </tr>
                            <tr>
                              <td height="25" align="left"  style="background-color:#CCCCCC"><strong>CLIENTE</strong></td>
                              <td><span ><a href="#" id="<?php echo $id_nit;?>" class="edit_person_view"><?php echo utf8_encode($peron_data['primer_nombre']." ".$peron_data['segundo_nombre']." ".$peron_data['primer_apellido']." ".$peron_data['segundo_apellido']);?></a></span>(<a href="./?mod_caja/delegate&amp;operacion&amp;determinate=1&amp;id=<?php echo  System::getInstance()->Encrypt(json_encode(array("id_nit"=>$cdata->id_nit_cliente)));?>">Caja</a>)</td>
                            </tr>
                            <tr>
                              <td height="25" align="left"  style="background-color:#CCCCCC"><strong>CANT. INHUMADO</strong></td>
                              <td>0</td>
                            </tr>
                            <tr>
                              <td height="25" align="left" style="background-color:#CCCCCC"><strong>ESTATUS</strong></td>
                              <td style="margin:0px;padding-left:10px;"><?php echo $data['ESTATUS'];?></td>
                            </tr>
                            <tr>
                              <td height="25" align="left"  style="background-color:#CCCCCC"><strong>DIA DE PAGO</strong></td>
                              <td style="padding-left:10px;"><?php echo $data['dia_pago'];?></td>
                            </tr>
                            <tr>
                              <td height="25" align="left"  style="background-color:#CCCCCC">&nbsp;</td>
                              <td style="padding-left:10px;"><input name="agregar_comentario" type="button" class="agregar_comentary btn btn-primary"  value="Agregar comentario" id="<?php echo $id_contrato?>"  id_nit="<?php echo $id_nit?>" /></td>
                            </tr>
                          </table></td>
                          <td width="409" align="left" valign="top" ><table width="404" border="0" cellpadding="0" cellspacing="0" class="tb_detalle">
                            <tr>
                              <td height="25" align="left"  style="background-color:#CCCCCC"><strong>OFICIAL</strong></td>
                              <td style="padding-left:10px;"><?php echo $ofi_moto['nombre_oficial']?>&nbsp;
                                <?php 
							  if (($protect->getIfAccessPageById(178))){
							  ?>
                                <a href="#" id="change_oficial" alt="<?php
                              echo System::getInstance()->Encrypt(json_encode($ofi_moto));
							  ?>" contrato="<?php echo $_REQUEST['id']; ?>" >Cambiar</a>
                                <?php } ?></td>
                            </tr>
                            <tr>
                              <td width="112" height="25" align="left"  style="background-color:#CCCCCC"><strong>MOTORIZADO</strong></td>
                              <td width="292" style="padding-left:10px;"><?php echo $ofi_moto['nombre_motorizado']?></td>
                            </tr>
                            <tr>
                              <td height="25" align="left"  style="background-color:#CCCCCC"><strong>GERENTE</strong></td>
                              <td style="padding-left:10px;"><?php echo $asesor_data[1]['nombre']." ".$asesor_data[1]['apellido']?></td>
                            </tr>
                            <tr>
                              <td height="25" align="left"  style="background-color:#CCCCCC"><strong>ASESOR</strong></td>
                              <td style="padding-left:10px;"><?php echo $asesor_data[0]['nombre']." ".$asesor_data[0]['apellido']?></td>
                            </tr>
                            <tr>
                              <td height="25" align="left"  style="background-color:#CCCCCC"><strong>MONEDA</strong></td>
                              <td style="padding-left:10px;"><?php echo $data['tipo_moneda'];?>&nbsp;</td>
                            </tr>
                            <tr>
                              <td height="25" align="left"  style="background-color:#CCCCCC"><strong>COMPROMISO</strong></td>
                              <td style="padding-left:10px;"><span class="day_restantes"><?php echo number_format($data['valor_cuota']+$data['monto_penalizacion'],2);?></span>
                                <?php if ($data['tipo_moneda']=='DOLARES'){ echo ' ó (<span class="day_restantes">RD$'.number_format(($data['valor_cuota']+$data['monto_penalizacion'])*$tasa_cambio,2)."</span>)"; } ?></td>
                            </tr>
                            <tr>
                              <td height="25" align="left"  style="background-color:#CCCCCC"><strong>DIRECCION COBRO</strong></td>
                              <td style="padding-left:10px;">
                              <?php echo $direccion;?>
                               <a href="#"  class="direccion_on_map" id_direccion="<?php echo $id_direccion?>">Ver en Mapa</a> </td>
                            </tr>
                            <tr>
                              <td height="25" align="left"  style="background-color:#CCCCCC"><strong>PLAZO</strong></td>
                              <td style="padding-left:10px;"><?php echo $data['cuotas'];?></td>
                            </tr>
                          </table></td>
                          <td width="419" height="25" align="center" valign="top" ><div class="bs-callout bs-callout-warning">
                            <table width="350" border="1" style="border-spacing:0px;font-size:9px;" class="tb_detalle fsDivPage">
                              <tr  >
                                <td width="150"><strong>RESUMEN</strong></td>
                                <td width="60" align="right"><strong>CANCELADO</strong></td>
                                <td width="54" align="right"><strong>PENDIENTE</strong></td>
                                <td width="50" align="right"><strong>TOTAL</strong></td>
                              </tr>
                              <tr >
                                <td><strong>CAPITAL </strong></td>
                                <td align="right"><?php 
 							echo number_format($capita_interes->capital_cancelado,2);							
							?></td>
                                <td align="right"><?php 
 							echo number_format($capita_interes->capital_pendiente,2);							
							?></td>
                                <td align="right"><?php echo  number_format($capita_interes->capital_cancelado+$capita_interes->capital_pendiente,2)?></td>
                              </tr>
                              <tr >
                                <td><strong>INTERESES </strong></td>
                                <td align="right"><?php echo  number_format($capita_interes->interes_pagado,2)?></td>
                                <td align="right"><?php echo  number_format($capita_interes->interes_pendiente,2)?></td>
                                <td align="right"><?php echo  number_format($capita_interes->interes_pagado+$capita_interes->interes_pendiente,2)?></td>
                              </tr>
                              <tr >
                                <td><strong>CUOTAS DE MANTENIMIENTO</strong></td>
                                <td align="right">0</td>
                                <td align="right">0</td>
                                <td align="right">0</td>
                              </tr>
                              <tr >
                                <td><strong>TOTALES</strong></td>
                                <td align="right"><?php echo  number_format($capita_interes->capital_cancelado+$capita_interes->interes_pagado,2)?></td>
                                <td align="right"><?php echo  number_format($capita_interes->capital_pendiente+$capita_interes->interes_pendiente,2)?></td>
                                <td align="right"><?php echo  number_format($capita_interes->capital_cancelado+$capita_interes->capital_pendiente+$capita_interes->interes_pagado+$capita_interes->interes_pendiente,2)?></td>
                              </tr>
                            </table>
                          </div></td>
                        </tr>
                        <tr>
                          <td height="25" colspan="3" align="left" valign="top" ><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td>&nbsp;</td>
                            </tr>
                            <tr>
                              <td><ul id="contract-tb" class="nav nav-tabs">
                                <li ><a href="#bitacora" data-toggle="tab" >BITACORA</a></li>
                                <li class="active"><a href="#saldos_" data-toggle="tab" >SALDOS</a></li>
                                <li ><a href="#cuotas" data-toggle="tab" >CUOTAS</a></li>
                                <li><a href="#movimiento" data-toggle="tab">MOVIMIENTOS</a></li>
                                <li><a href="#lgestion" data-toggle="tab">LISTADO GESTIONES </a></li>
                                <li><a href="#lsolicitud" data-toggle="tab">SOLICITUDES</a></li>
                                <li><a href="#linhumacion" data-toggle="tab">INHUMACION</a></li>
                                <li><a  href="#lbeneficiario" data-toggle="tab">BENEFICIARIOS</a></li>
                                <li><a  href="#lrepresentante" data-toggle="tab">REPRESENTANTES</a></li>
                              </ul>
                                <div  class="tab-content">
                                  <div class="tab-pane" id="bitacora">
                                    <?php include("view/listadobitacora.php"); ?>
                                  </div>
                                  <div class="tab-pane fade in active" id="saldos_">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td colspan="2">&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td  ><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                          <tr>
                                            <td style="font-size:18px;"><strong>ESTADO DE CUENTA</strong></td>
                                          </tr>
                                          <tr>
                                            <td><?php include("view/estado_de_cuenta.php");?></td>
                                          </tr>
                                        </table></td>
                                        <td>&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td colspan="2">&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td colspan="2"><?php 
		//Si el contrato esta activo
		if ($cdata->estatus==1 || $cdata->estatus==20 
			|| $cdata->estatus==25 || $cdata->estatus==24 
			|| $cdata->estatus==23 || $cdata->estatus==28 
			|| $cdata->estatus==24 || $cdata->estatus==54){ ?>


            <div class="btn-group dropup ">
            <button type="button" class="btn btn-default dropdown-toggle btn-success" data-toggle="dropdown"> REQ. COBRO <span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu">
<?php  
		$SQL="SELECT * FROM `acciones_cobros` WHERE `mostrar_en_el_listado`=1";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){            
?>
               <li><a href="#" id="req_cobro_" class="bt_req_cobro_" serie="<?php echo System::getInstance()->Encrypt($contrato->serie_contrato);?>" no_c="<?php echo System::getInstance()->Encrypt($contrato->no_contrato);?>" ><?php echo $row['accion']?></a></li>
              <li class="divider"></li>
<?php } ?>              
 
             </ul>
            </div>
                                              


                                          
                                          
                                          <input type="submit" name="lbr_cobro" id="lbr_cobro" class="btn btn-primary" value="GESTION" />
                                          <?php  } ?>
                                          
                                          
                                          <?php 
		//Si el contrato esta activo
		if ($cdata->estatus==1 || $cdata->estatus==20 
					|| $cdata->estatus==25 || $cdata->estatus==24 
					|| $cdata->estatus==23 || $cdata->estatus==28 
					|| $cdata->estatus==54){ ?>
      
                                              <?php 
/*VALIDO LA CAJA A LA QUE PERTENECE EL OFICIAL*/
$rsx=$protect->getCaja(); 
if (count($rsx)>0){
?>
                                              <div class="btn-group dropup" style="display:none">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> CAJA <span class="caret"></span></button>
                                                <ul class="dropdown-menu" role="menu">
                                                  <?php 
 
$SQL="SELECT tipo_movimiento.* FROM `tipo_movimiento` 
INNER JOIN `tipo_mov_caja` ON (`tipo_mov_caja`.`CAJA_TIPO_MOV_CAJA`=tipo_movimiento.TIPO_MOV)
WHERE  tipo_mov_caja.`CAJA_ID_CAJA`='".$rsx['ID_CAJA']."' ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
                                                  <li><a href="#<?php echo $encriptID?>" id="<?php echo $encriptID?>" onclick="doAction('<?php echo $row['class'];?>',this)"  inf="<?php echo $row['TIPO_MOV']?>"  ><?php echo $row['DESCRIPCION']?></a></li>
                                                  <li class="divider"></li>
                                                  <?php } ?>
                                                </ul>
                                              </div>
                                              <?php } ?>
                                              <?php if ($protect->getIfAccessPageById(180)){ ?>
                                              <input type="button"  id="<?php echo $id_contrato?>" id_nit="<?php echo $id_nit?>" class="_facturar btn btn-primary" value="C X C" />
                                              
                                              
                                              <?php } ?>
                                              <?php  

			 if ($protect->getIfAccessPageById(152) || $protect->getIfAccessPageById(153)
					 || $protect->getIfAccessPageById(171)){ ?>
                                              <div class="btn-group dropup">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> ACCIONES <span class="caret"></span></button>
                                                <ul class="dropdown-menu" role="menu">
                                                  <?php if ($data['id_status']==1 || $cdata->estatus==20 || $cdata->estatus==54){ ?>
                                                  <?php if ($protect->getIfAccessPageById(171)){ ?>
                                                  <li><a href="#" id="gestiones_especiales"  id_nit="<?php echo $id_nit?>" >Generar Gestion</a></li>
                                                  <li class="divider"></li>
                                                  <?php } ?>
                                                  <?php if ($protect->getIfAccessPageById(153)){ ?>
                                                  <li><a href="#" id="anular">Anular contrato</a></li>
                                                  <li class="divider"></li>
                                                  <?php } ?>
                                                  <?php if ($protect->getIfAccessPageById(152)){  ?>
                                                  <li><a href="#" id="desistir">Desistir contrato</a></li>
                                                  <li class="divider"></li>
                                                  <?php 	 }  ?>
                                                  <?php } ?>
                                                </ul>
                                              </div>
                                              <?php 
			} 
		  	?>
 
                                            <?php 
	} 
?>
                                             <?php  
			// if ($protect->getIfAccessPageById(152)){ ?>
                                              <div class="btn-group dropup">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> GESTION <span class="caret"></span></button>
                                                <ul class="dropdown-menu" role="menu">
                                                  <?php 
                    
						$SQL="SELECT * FROM `tipos_gestiones` where idtipogestion in ('INH','ABCAP','ABSAL','CTT','CPLAN','GPMN','NTDBT','REACT')";
						 
						$rs=mysql_query($SQL);
						while($row=mysql_fetch_assoc($rs)){
							$id=System::getInstance()->Decrypt(json_encode($row));
                    ?>
                                                  <li><a href="#" class="gestion_" id="<?php echo $id;?>" object="<?php echo $row['class_obj']?>" contranto="<?php echo $id_contrato;?>" ><?php echo utf8_encode($row['gestion']);?></a></li>
                                                  <li class="divider"></li>
                                                  <?php }?>
                                                </ul>
                                              </div>
                                              <?php 
			//} 
		  	?>
                                          
                                          </td>
                                      </tr>
                                      <tr>
                                        <td colspan="2">&nbsp;</td>
                                      </tr>
                                    </table>
                                  </div>
                                  <div class="tab-pane" id="cuotas">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td valign="top" style="padding:4px;height:240px;"><?php include("view/listado_cuotas.php");?></td>
                                        <td align="left" valign="top" style="font-size:20px;"><table width="350" border="1" style="border-spacing:0px;font-size:9px;" class="tb_detalle fsDivPage">
                                          <tr  >
                                            <td width="150"><strong>RESUMEN</strong></td>
                                            <td width="60" align="right"><strong>CANCELADO</strong></td>
                                            <td width="54" align="right"><strong>PENDIENTE</strong></td>
                                            <td width="50" align="right"><strong>TOTAL</strong></td>
                                          </tr>
                                          <tr >
                                            <td><strong>CAPITAL </strong></td>
                                            <td align="right"><?php 
 							echo number_format($capita_interes->capital_cancelado,2);							
							?></td>
                                            <td align="right"><?php 
 							echo number_format($capita_interes->capital_pendiente,2);							
							?></td>
                                            <td align="right"><?php echo  number_format($capita_interes->capital_cancelado+$capita_interes->capital_pendiente,2)?></td>
                                          </tr>
                                          <tr >
                                            <td><strong>INTERESES </strong></td>
                                            <td align="right"><?php echo  number_format($capita_interes->interes_pagado,2)?></td>
                                            <td align="right"><?php echo  number_format($capita_interes->interes_pendiente,2)?></td>
                                            <td align="right"><?php echo  number_format($capita_interes->interes_pagado+$capita_interes->interes_pendiente,2)?></td>
                                          </tr>
                                          <tr >
                                            <td><strong>CUOTAS DE MANTENIMIENTO</strong></td>
                                            <td align="right">0</td>
                                            <td align="right">0</td>
                                            <td align="right">0</td>
                                          </tr>
                                          <tr >
                                            <td><strong>TOTALES</strong></td>
                                            <td align="right"><?php echo  number_format($capita_interes->capital_cancelado+$capita_interes->interes_pagado,2)?></td>
                                            <td align="right"><?php echo  number_format($capita_interes->capital_pendiente+$capita_interes->interes_pendiente,2)?></td>
                                            <td align="right"><?php echo  number_format($capita_interes->capital_cancelado+$capita_interes->capital_pendiente+$capita_interes->interes_pagado+$capita_interes->interes_pendiente,2)?></td>
                                          </tr>
                                        </table></td>
                                      </tr>
                                      <tr>
                                        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                          <?php 
		//Si el contrato esta activo
		if ($cdata->estatus==1 || $cdata->estatus==20 || $cdata->estatus==24 || $cdata->estatus==54){ ?>
                                          <tr>
                                            <td> 
                                    
                                              <?php 
/*VALIDO LA CAJA A LA QUE PERTENECE EL OFICIAL*/
$rsx=$protect->getCaja(); 
if (count($rsx)>0){
?>
                                              <div class="btn-group dropup" style="display:none">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> CAJA <span class="caret"></span></button>
                                                <ul class="dropdown-menu" role="menu">
                                                  <?php 
 
$SQL="SELECT tipo_movimiento.* FROM `tipo_movimiento` 
INNER JOIN `tipo_mov_caja` ON (`tipo_mov_caja`.`CAJA_TIPO_MOV_CAJA`=tipo_movimiento.TIPO_MOV)
WHERE  tipo_mov_caja.`CAJA_ID_CAJA`='".$rsx['ID_CAJA']."' ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
                                                  <li><a href="#<?php echo $encriptID?>" id="<?php echo $encriptID?>" onclick="doAction('<?php echo $row['class'];?>',this)"  inf="<?php echo $row['TIPO_MOV']?>"  ><?php echo $row['DESCRIPCION']?></a></li>
                                                  <li class="divider"></li>
                                                  <?php } ?>
                                                </ul>
                                              </div>
                                              <?php } ?>
                                              <?php if ($protect->getIfAccessPageById(180)){ ?>
                                              <input type="button"  id="<?php echo $id_contrato?>" id_nit="<?php echo $id_nit?>" class="_facturar btn btn-primary" value="C X C" />
                                              <?php } ?>
       
                     </td>
<?php 
	} 
?>
                                            <td><table width="300" border="0" align="right" cellpadding="0" cellspacing="0">
                                              <tr>
                                                <td width="10"><strong>SALDOS:</strong></td>
                                                <td width="50" style="padding-left:5px;"><span id="cuotas_display" class="badge alert-danger">0</span></td>
                                                <td width="10"><strong>MONTO:</strong></td>
                                                <td style="padding-left:5px;"><span id="monto_display" class="badge alert-danger">0</span></td>
                                              </tr>
                                            </table></td>
                                          </tr>
                                        </table></td>
                                        <td align="left" valign="top" style="font-size:20px;">&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td valign="top">&nbsp;</td>
                                        <td align="left" valign="top" style="font-size:20px;">&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td valign="top">&nbsp;</td>
                                        <td align="left" valign="top" style="font-size:20px;">&nbsp;</td>
                                      </tr>
                                    </table>
                                  </div>
                                  <div class="tab-pane fade in" id="movimiento">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                      <tr>
                                        <td>&nbsp;</td>
                                      </tr>
                                      <tr>
                                        <td><strong><span style="font-size: 18px">MOVIMIENTO DE CUENTA</span></strong></td>
                                      </tr>
                                      <tr>
                                        <td valign="top"><?php include("view/listado_movimientos.php"); ?></td>
                                      </tr>
                                    </table>
                                  </div>
                                  <div class="tab-pane fade in" id="lgestion">
                                    <?php include("view/dt_listado_gestion.php"); ?>
                                  </div>
                                  <div class="tab-pane fade in" id="lsolicitud">
                                    <?php include("view/dt_listado_solicitud.php"); ?>
                                  </div>
                                  <div class="tab-pane fade in" id="linhumacion">
                                    <?php include("view/dt_listado_inhumacion.php"); ?>
                                  </div>
                                    <div class="tab-pane fade in" id="lrepresentante">
                                    <?php //include("view_representante/dt_listado_representante.php"); ?>
                                  </div>
                                 <div class="tab-pane fade in" id="lbeneficiario">
                                    <?php // include("view_beneficiario/dt_listado_beneficiario.php"); ?>
                                  </div>   
                                </div></td>
                            </tr>
                          </table></td>
                        </tr>
                      </table>
                    </div>
                    <div class="tab-pane fade in" id="hlb">
                      <?php include("view/historico_labor_cobro.php"); ?>
                    </div>
                </div>