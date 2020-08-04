<?php

if (!isset($protect)){
	echo "Security error!";
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
 
if (isset($_REQUEST['process'])){
	if ($_REQUEST['process']){
		
		//print_r($forma_pago);
		
		if ((count($data)>0) && (trim($data[0])!='')){
		
			if (isset($forma_pago->tipo_reserva) && isset($forma_pago->forma_pago) && isset($forma_pago->personal_data->id_nit)){
				SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva");
				
	//			print_r($forma_pago);
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
							"error"=>true,
							"typeError"=>503)
							);
		
		exit;
	}
} 
 
 
// print_r($data);
// print_r($forma_pago);

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
<form name="form_reserva_<?php echo $_REQUEST['rand']?>" id="form_reserva_<?php echo $_REQUEST['rand']?>" method="post">
 <div id="step_2" class="fsPage fsPage2"  >
 
   <table width="100%" border="1">
    <tr>
      <td valign="top"><table width="350" border="1">
     
        <tr>
          <td colspan="2"><h2>Reserva</h2></td>
          </tr>
        <tr >
          <td width="40%" align="right"><strong>Cliente:</strong></td>
          <td><table border="1">
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
          <td><table border="1">
            <tr>
              <td style="background-color:#CCCCCC;color:#C30;border-radius:2px;padding:2px;" id="rs_asesor"><?php 
				if(isset($forma_pago->personal_data->nombre)){
					echo $forma_pago->personal_data->nombre. " ".$forma_pago->personal_data->apellido;
				}
				?></td>
              <td><button type="button" class="positive" id="bt_asesor_buscar">Buscar</button></td>
            </tr>
          </table></td>
        </tr>
        <tr >
          <td align="right"><strong>Tipo de reserva: </strong></td>
          <td><select name="tipo_reserva" id="tipo_reserva">
            <option value="0">Seleccione</option>
            <?php 

$SQL="SELECT  id_reserva,abono,gerencia,reserva_descrip FROM `tipos_reservas` ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
?>
            <option <?php echo $forma_pago->tipo_reserva==$row['id_reserva']?'selected':''?> value="<?php echo $row['id_reserva']."_".$row['abono']."_".$row['gerencia']?>" ><?php echo $row['reserva_descrip']?></option>
            <?php } ?>
          </select></td>
        </tr>
        <tr class="fp_tipo_reserva">
          <td align="right"><strong>Forma de pago:</strong></td>
          <td><select name="forma_pago" id="forma_pago">
            <option value="0">Seleccione</option>
            <?php 

$SQL="SELECT * FROM `formas_pago`   ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
?>
            <option  <?php echo $forma_pago->forma_pago==$row['forpago']?'selected':''?>  value="<?php echo $row['forpago'];?>" ><?php echo $row['descripcion_pago']?></option>
            <?php } ?>
            </select></td>
        </tr>
        <tr class="fp_efectivo">
          <td align="right"><strong>Serie recibo:</strong></td>
          <td><input name="serie_recibo" type="text" id="serie_recibo" value="<?php echo $forma_pago->serie_recibo?>"></td>
        </tr>
        <tr  class="fp_efectivo">
          <td align="right"><strong>No. Recibo:</strong></td>
          <td><input name="no_recibo" type="text" id="no_recibo" value="<?php echo $forma_pago->no_recibo?>"></td>
        </tr>
        <tr  class="fp_efectivo">
          <td align="right"><strong>Monto:</strong></td>
          <td><input name="monto" type="text" id="monto" value="<?php echo $forma_pago->monto?>"></td>
        </tr>
        <tr  class="fp_efectivo">
          <td align="right"><strong>Tipo de cambio:</strong></td>
          <td><input name="tipo_cambio" type="text" id="tipo_cambio" value="<?php  echo $forma_pago->tipo_cambio!=""?$forma_pago->tipo_cambio:"1"?>"></td>
        </tr>
        <tr class="fp_transferencia">
          <td align="right"><strong>No. Documento:</strong></td>
          <td><input name="no_documento" type="text" id="no_documento" value="<?php echo $forma_pago->no_documento?>"></td>
        </tr>
        <tr  class="fp_transferencia">
          <td align="right"><strong>Aprobacion:</strong></td>
          <td><input name="aprobacion" type="text" id="aprobacion" value="<?php echo $forma_pago->aprobacion?>"></td>
        </tr>
        <tr  class="fp_transferencia">
          <td align="right"><strong>Banco:</strong></td>
          <td><select name="banco" id="banco">
            <option value="">Seleccione</option>
            <?php 

$SQL="SELECT * FROM `bancos` ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
?>
            <option  <?php echo $forma_pago->banco==$row['ban_id']?'selected':''?>  value="<?php echo $row['ban_id'];?>" ><?php echo $row['ban_descripcion']?></option>
            <?php } ?>
          </select></td>
        </tr>
      </table></td>
      <td valign="top"><table  width="100%" border="1">
        <tr>
          <td><h2 id="h_">Listado de items por reservar<span id="bt_seguir" title="Agregar más item al listado" class="positive">+ </span></h2></td>
        </tr>
        <tr>
          <td><table id="tb_reserva_<?php echo $_REQUEST['rand']?>" width="100%" border="1" class="display">
            <thead>
              <tr>
                <td align="center"><strong>Jardin</strong></td>
                <td align="center"><strong>Fase</strong></td>
                <th>Modulo</th>
                <th>Parcela</th>
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
                <td align="center" class="display" ><?php echo $row->lote;?></td>
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
      <td valign="top">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" align="center" valign="top"> 
        <button type="button" class="positive" id="bt_rs_save" disabled> <img src="images/next.png"  width="16" height="16" alt=""/>Reservar</button>
        <button type="button" class="positive" id="bt_rs_cancel"> <img src="images/cross.png"  alt=""/>Cancel</button>
&nbsp;</td>
      </tr>
  </table>
 
  
</div>
</form>