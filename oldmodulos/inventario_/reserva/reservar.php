<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}

if (validateField($_REQUEST,"dt_list")){
	SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva");
	
	$reserva= new Reserva($protect->getDBLink());
	echo json_encode($reserva->getClientWithProspecto());
	exit;
}

if (validateField($_REQUEST,"getDataAsesorByCode")&& validateField($_REQUEST,"code")){
	SystemHtml::getInstance()->includeClass("estructurac","Asesores");
	 
	$asesor= new Asesores($protect->getDBLink());
	echo json_encode($asesor->getDataAsesor(System::getInstance()->Decrypt($_REQUEST['code'])));
	exit;
}

//remove_item
if (validateField($_REQUEST,"remove_item") && 
			validateField($_REQUEST,"reserva") && 
			validateField($_REQUEST,"items")){
	
	SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva");

 	$items=json_decode(System::getInstance()->Decrypt($_REQUEST['items']));
	
	
	$reserva= new Reserva($protect->getDBLink());
	echo json_encode($reserva->inactiveReserva(
												$items->no_reserva,
												$items->id_jardin,
												$items->id_fases,
												$items->lote,
												$items->bloque
											));
	 

	exit;	
}


if (isset($_REQUEST['listar_clientes'])){
	include("listado_cliente.php");
	exit;
}

if (isset($_REQUEST['listar_asesores'])){
	include("listado_asesor.php");
	exit;
}

if (isset($_REQUEST['tipo_reserva'])){
	include("mantenimiento/tipo_reserva.php");
	exit;
}
if (isset($_REQUEST['tipo_reserva_add'])){
	include("mantenimiento/tipo_reserva_add.php");
	exit;
}

if (isset($_REQUEST['tipo_reserva_add'])){
	include("mantenimiento/tipo_reserva_add.php");
	exit;
}
if (isset($_REQUEST['view_reserva_edit'])){
	include("view/reserva_edit.php");
	exit;
}
if (isset($_REQUEST['view_reserva_pago'])){
	include("view/pago_reserva.php");
	exit;
}

if (isset($_REQUEST['view_recibo_ventas'])){
	include("view_recibos/reserva_recibo_ventas.php");
	exit;
}
 
if (!isset($_REQUEST['json'])){
	echo "Error parametro invalido o falta!";
	exit;
}



$forma_pago=json_decode($_REQUEST['forma_pago']);
$data=json_decode($_REQUEST['json']);
 
 
if (count($data)<=0){
	echo json_encode(array("error"=>"Error parser JSON"));
	exit;
}
 
 
/*
	CAPTURO LOS DATOS PARA REALIZAR UNA RESERVA
*/ 
if (isset($_REQUEST['process'])){
	if ($_REQUEST['process']){
		 
		
		if ((count($data)>0) && (trim($data[0])!='')){
	
		 
			if (isset($forma_pago->tipo_reserva) && isset($forma_pago->personal_data->id_nit)){
				SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva");
				 
				/* SI ES ABONO */
				if (($forma_pago->tipo_reserva=="1")){
					//if ($forma_pago->forma_pago=="1"){ //FORMA DE PAGO EN EFECTIVO
		 
					$reserva= new Reserva($protect->getDBLink(),$_REQUEST);
					$reserva->reservaAbono();
					
					echo json_encode($reserva->getMessages());
					//}
				 
					exit;
					
				} 
				
				/* SI ES HORAS */
				if (($forma_pago->tipo_reserva=="2")){
					$reserva= new Reserva($protect->getDBLink(),$_REQUEST);
					$reserva->reservaXHoras();
					
					echo json_encode($reserva->getMessages());
					exit;
					
				} 
				
				/* RESERVA X GERENCIA */
				if (($forma_pago->tipo_reserva=="3")){
					$reserva= new Reserva($protect->getDBLink(),$_REQUEST);
					$reserva->reservaXGerencia();
					
					echo json_encode($reserva->getMessages());
					exit;
					
				} 			
			
			}
		}else{
			echo json_encode(array(
							"mensaje"=> "Debe de seleccionar un producto!",
							"error"=>true,
							"typeError"=>0)
							);
			exit;				
		}
		
		echo json_encode(array(
							"mensaje"=> "Debe de seleccionar un cliente",
							"error"=>false,
							"typeError"=>503)
							);
		
		exit;
	}
} 
   

?>
<style>
.fsPage2{
	width:900px; 
}
.dataTables_wrapper{
	min-height:80px;	
}
.fp_transferencia{
	display:none;	
}
.ft_reporte_venta{
	<?php if ($forma_pago->tipo_reserva=="0"){?>		
	display:none;
	<?php } ?>		
	
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
<form name="form_reserva_<?php echo $_REQUEST['rand']?>" id="form_reserva_<?php echo $_REQUEST['rand']?>" method="post">
 <div id="step_2" class="fsPage fsPage2"  >
 
   <table width="100%" border="1">
    <tr>
      <td width="46%" valign="top"><table width="390" border="1" class="display">
     
        <tr>
          <td colspan="2"><h2>Reserva</h2></td>
          </tr>
        <tr >
          <td width="40%" align="right"><strong>Cliente:</strong></td>
          <td><table width="100%" border="1">
              <tr>
                <td style="background-color:#CCCCCC;color:#C30;border-radius:2px;padding:2px;" id="rs_cliente">
                <?php 
				if(isset($forma_pago->personal_data->nombre)){
					echo $forma_pago->personal_data->nombre. " ".$forma_pago->personal_data->apellido;
				}
				?>
                </td>
                <td><button type="button" class="positive" id="bt_rs_buscar">Buscar</button></td>
              </tr>
            </table></td>
        </tr>
        <tr >
          <td align="right"><strong>Asesor:</strong></td>
          <td><table width="100%" border="1">
            <tr>
              <td style="background-color:#CCCCCC;color:#C30;border-radius:2px;padding:2px;" id="rs_asesor"><?php 
				if(isset($forma_pago->asesor_data->nombre)){
					echo $forma_pago->asesor_data->nombre. " ".$forma_pago->asesor_data->apellido;
				}
				?>&nbsp;</td>
              <td><button type="button" class="positive" id="bt_asesor_buscar" style="display:none">Buscar</button></td>
            </tr>
          </table></td>
        </tr>
        <tr >
          <td width="180" align="right"><strong>Tipo de reserva: </strong></td>
          <td><select name="tipo_reserva" id="tipo_reserva">
            <option value="0">Seleccione</option>
            <?php 

$SQL="SELECT  id_reserva,abono,gerencia,reserva_descrip,horas FROM `tipos_reservas` where estatus='1' ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
?>
            <option <?php echo trim($forma_pago->tipo_reserva)==($row['id_reserva'])?'selected':''?> value="<?php echo $row['id_reserva']."_".$row['abono']."_".$row['gerencia']."_".$row['horas']?>" ><?php echo $row['reserva_descrip']?></option>
            <?php } ?>
          </select></td>
        </tr>
        <tr id="abono_reserva_monto" style="display:none" >
          <td align="right"><strong>Monto abono:</strong></td>
          <td><label for="monto_abono"></label>
            <input type="text" name="monto_abono" id="monto_abono" /></td>
        </tr>
      </table></td>
      <td width="54%" valign="top"><table  width="100%" border="1">
        <tr>
          <td><h2 id="h_">Listado de items por reservar<span id="bt_seguir" title="Agregar más item al listado" class="positive">+ </span></h2></td>
        </tr>
        <tr>
          <td><table id="tb_reserva_<?php echo $_REQUEST['rand']?>" width="100%" border="1" class="display">
            <thead>
              <tr>
                <td align="center"><strong>Jardin</strong></td>
                <td align="center"><strong>Fase</strong></td>
                <td align="center"><strong>Bloque</strong></td>
                <td align="center"><strong>Lote</strong></td>
                <td align="center"><strong>Cavidades</strong></td>
                <td align="center"><strong>Osarios</strong></td>
                <td>&nbsp;</td>
              </tr>
            </thead>
            <tbody>
              <?php
   
 //  print_r($data);
   	foreach($data as $key => $val){
		$row=json_decode(System::getInstance()->Decrypt($val));
		if (isset($row->id_jardin)){
		//print_r($row);
   ?>
              <tr>
                <td align="center" class="display"><?php echo $row->id_jardin." - ".$row->jardin?></td>
                <td align="center" class="display"><?php echo $row->id_fases." - ". $row->fase?></td>
                <td align="center" class="display"><?php echo $row->bloque;?></td>
                <td align="center" class="display" ><?php echo $row->lotes;?></td>
                <td align="center" class="display" ><?php echo $row->cavidades?></td>
                <td align="center" class="display" ><?php echo $row->osarios?></td>
                <td><a href="#" class="res_links" rel="<?php echo $key;?>"><img src="images/cross.png"  alt=""/></a></td>
              </tr>
              <?php }
	} ?>
            </tbody>
          </table></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td colspan="2" valign="top">&nbsp;</td>
      </tr>
    <tr>
      <td colspan="2" align="center" valign="top"> 
        <button type="button" class="greenButton" id="bt_reserva_save" disabled>Reservar</button>
        <button type="button" class="redButton" id="bt_rs_cancel">Cancel</button>
&nbsp;</td>
      </tr>
    <tr>
      <td colspan="2" align="center" valign="top">&nbsp;      
      </td>
    </tr>
   </table>
 
  
</div>
</form>
