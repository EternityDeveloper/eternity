<?php
if (!isset($protect)){
	echo "Security error!";
	exit;
}
/* CARGO LA LIBRERIA DE PILAR */
SystemHtml::getInstance()->includeClass("prospectos","Pilar");

/*PARA REALIZAR CONSULTAS*/
if (isset($_REQUEST['x_search'])){
	$tipo_pilar=json_decode(System::getInstance()->Decrypt($_REQUEST['b_tipo_pilar']));
	$pilar= new Pilar($protect->getDBLink(),$_REQUEST);
	$result=$pilar->getListFromQuestion($tipo_pilar);
	echo json_encode($result);
			 
	exit;
}
  
if (!isset($_REQUEST['id'])){
	echo json_encode(array("error"=>"Error parametro invalido o falta!","error"=>true));
	exit;
}

$data=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));

if (!isset($data->idtipo_pilar)){
	echo json_encode(array("error"=>"error de codigo","error"=>true));
	exit;
}

if (isset($_REQUEST['submit_tipo_pilar'])){
	
	$pilar= new Pilar($protect->getDBLink());
	$result=$pilar->updatePilar($data,$_REQUEST);
	echo json_encode($result);
	exit;	
}
 
 
 
// print_r($data);
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
 <div id="step_2" class="fsPage fsPage2"  >
 
   <table width="100%" border="1">
    <tr>
      <td valign="top"><table width="350" border="1">
     
        <tr>
          <td width="40"><h2>Tipo de Pilar</h2></td>
          </tr>
        <tr>
          <td><form name="form_interfase" id="form_interfase" method="post" action="" class="fsForm  fsSingleColumn">
<table width="390" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
            <tr>
              <td align="right"><strong>Pilar:</strong></td>
              <td><input name="idtipo_pilar" type="text" class="textfield textfieldsize" id="idtipo_pilar" value="<?php if (count($data)>0){ echo $data->idtipo_pilar; }?>" <?php if (count($data)>0){echo "disabled";}?> maxlength="5" ></td>
            </tr>
            <tr>
              <td align="right"><strong>Descripcion:</strong></td>
              <td><input type="text" class="textfield textfieldsize"  name="dscrip_tipopilar" id="dscrip_tipopilar" value="<?php if (count($data)>0){echo $data->dscrip_tipopilar;}?>"></td>
            </tr>
            <tr>
              <td align="right"><strong>Dias de proteccion:</strong></td>
              <td><input  name="dias_proteccion" type="text" class="textfield textfieldsize" id="dias_proteccion" value="<?php if (count($data)>0){echo $data->dias_proteccion;}?>" size="10" style="width:40px;"></td>
            </tr>
            <?php if (count($data)>0){?>  
            <tr>
              <td align="right"><strong>Estatus:</strong></td>
              <td><select name="estatus" id="estatus">
                <option value="">Seleccionar</option>
                <?php 
	 	 $SQL="SELECT * FROM `sys_status` WHERE id_status IN (1,2)";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
	  ?>
                <option value="<?php echo $row['id_status']?>" <?php echo $row['id_status']==$data->estatus?'selected':'' ?>><?php echo $row['descripcion'] ?></option>
                <?php } ?>
              </select></td>
            </tr>
            <?php } ?>
            <tr>
              <td colspan="2"><input name="submit_tipo_pilar" type="hidden" id="submit_tipo_pilar" value="1" />
                <input name="edit_tipo_pilar" type="hidden" id="edit_tipo_pilar" value="1">
                <input name="id" type="hidden" id="id" value="<?php echo $_REQUEST['id']?>"></td>
            </tr>
            <tr>
              <td colspan="2" align="center"><button type="button" class="greenButton" id="bt_prospecto_save"> Guardar</button>
                <button type="button" class="redButton" id="bt_prospecto_cancel"> Cancel</button></td>
            </tr>
          </table> </form></td>
        </tr>
      </table></td>
      <td valign="top"><table  width="100%" border="1">
        <tr>
          <td><h2 id="h_">Listado de preguntas<span id="bt_add_seguir" title="Agregar más item al listado" class="positive">+ </span></h2></td>
        </tr>
        <tr>
          <td><button type="button" class="blueButton" id="bt_v_add_seguir">Crear</button>&nbsp;</td>
        </tr>
        <tr>
          <td><table id="tb_listado_pregunta" width="100%" border="1" class="display">
            <thead>
              <tr>
                <td align="center"><strong>Pregunta ID</strong></td>
                <td align="center"><strong>Pregunta</strong></td>
                <td align="center"><strong>Tipo de respuesta</strong></td>
                <td><strong>Estatus</strong></td>
                <td>&nbsp;</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td valign="top">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
 
  
</div>
