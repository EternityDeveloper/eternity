<?php 
if (!isset($protect)){
	exit;	
}
SystemHtml::getInstance()->includeClass("client","PersonalData");
/*GRABA LAS DIRECCIONES DE FACTURACION DE UN CLIENTE*/
if (validateField($_REQUEST,"datos_facturacion")){
	if (validateField($_REQUEST,"vfac_direccion")&& validateField($_REQUEST,"id_nit")){
		 		
		$person= new PersonalData($protect->getDBLink(),$_REQUEST);
		
		$id_nit=System::getInstance()->Decrypt($_REQUEST['id_nit']);
		$emp=json_decode(System::getInstance()->Decrypt($_REQUEST['vfac_empresa']));
		$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));
		$dir=json_decode(System::getInstance()->Decrypt($_REQUEST['vfac_direccion']));
		$num_doc=$id_nit;//$_REQUEST['fact_v_numero_documento'];
		
		$datap=$person->getClientData($id_nit);

 		$nombre_cli=$datap['primer_nombre']." ".$datap['segundo_nombre']." ".
									 $datap['tercer_nombre']." ".$datap['primer_apellido'];//$_REQUEST['fact_a_nombre_de'];
	
 
		$person->addDatosFacturacion($id_nit,
									 $nombre_cli,
									 $num_doc,
									 $dir->id_direcciones,
									 isset($emp->EM_ID)?$emp->EM_ID:'',
									 isset($contrato->serie_contrato)?$emp->serie_contrato:'', 
									 isset($contrato->no_contrato)?$emp->no_contrato:'');
									 
		echo json_encode($person->getMessages());
		 
	}else{
		echo json_encode(array("mensaje"=>'Error campos incompletos','error'=>true));	
	}
	exit;
}

 
$id_nit=System::getInstance()->Decrypt($_REQUEST['id']);
 

$type=isset($_REQUEST['type'])?$_REQUEST['type']:"form";

if ($type=="form"){
?><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><h2 style="color:#FFF">FACTURA</h2></td>
  </tr>
  <tr id="mov_caja_factura">
    <td><table width="1000%" border="0" cellspacing="2" cellpadding="0" style="border-spacing:">
      <tr>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="80">A nombre de</td>
            <td><select name="factura_cliente" id="factura_cliente" class="textfield_input required" style="width:300px;" >
              <option value="" >Seleccione</option>
              <?php 

$SQL="SELECT  *,
	(SELECT
	 CONCAT(sys_ciudad.`Descripcion`,'  ',sys_provincia.`descripcion`,' ',sys_sector.`descripcion`) AS direccion
	  FROM 
	`sys_direcciones` 
	INNER JOIN `sys_ciudad` ON (sys_ciudad.`idciudad`=sys_direcciones.idciudad)
	INNER JOIN `sys_provincia` ON (sys_provincia.`idprovincia`=sys_direcciones.idprovincia)
	INNER JOIN `sys_sector` ON (sys_sector.`idsector`=sys_direcciones.idsector)
	WHERE sys_direcciones.`id_direcciones`=facturacion_cliente.`DIRECCION_FAC_CLI` )  AS direccion 
 FROM `facturacion_cliente`  WHERE id_nit='". mysql_real_escape_string($id_nit)."' ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	  $enc=System::getInstance()->Encrypt(json_encode($row));
	  $serialize=base64_encode(json_encode($row));
?>
              <option value="<?php echo $enc?>" serialize="<?php echo $serialize?>"><?php echo $row['FACTURAR__FAC_CLI']?></option>
              <?php } ?>
            </select></td>
            <td align="left"><button type="button" class="orangeButton" id="agregar_fact">Agregar</button></td>
          </tr>
        </table></td>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>RNC/NIT</td>
            <td><input name="fac_nit_rnc" type="text" id="fac_nit_rnc" readonly /></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="80">Dirección:&nbsp;</td>
            <td><input name="fac_direccion" type="text" class="textfield_input required" id="fac_direccion" style="width:450px;" readonly></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php }else if ($type=="add_frm"){?>
<div class="modal fade" id="modal_view_facturacion_cliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:430px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">DATOS DE FACTURACION</h4>
      </div>
      <div class="modal-body">
<form method="post"  action="" id="dt_fact_form"  name="dt_fact_form" class="fsForm">
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#FFF">
 
  <tr id="mov_caja_factura2">
    <td><table width="1000%" border="0" cellspacing="2" cellpadding="0" style="border-spacing:">
      <tr>
        <td><table width="100%" border="0" cellspacing="5" cellpadding="5">
          <tr>
            <td align="right"><strong>
              <input type="hidden" name="datos_facturacion" id="datos_facturacion" value="1" />
              <input type="hidden" name="id_nit" id="id_nit" value="<?php echo $_REQUEST['id'];?>" />
              Dirección:</strong></td>
            <td><select name="vfac_direccion" id="vfac_direccion" class="textfield_input required" style="width:200px;" >
              <option value="">Seleccione</option>
              <?php 

$SQL="SELECT sys_direcciones.id_direcciones,
 CONCAT(sys_ciudad.`Descripcion`,'  ',sys_provincia.`descripcion`,' ',sys_sector.`descripcion`) AS direccion
  FROM 
`sys_direcciones` 
INNER JOIN `sys_ciudad` ON (sys_ciudad.`idciudad`=sys_direcciones.idciudad)
INNER JOIN `sys_provincia` ON (sys_provincia.`idprovincia`=sys_direcciones.idprovincia)
INNER JOIN `sys_sector` ON (sys_sector.`idsector`=sys_direcciones.idsector)
WHERE sys_direcciones.`id_direcciones` NOT IN (SELECT DIRECCION_FAC_CLI FROM facturacion_cliente WHERE id_nit='".$id_nit."'
)  AND id_nit='".$id_nit."'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	 $encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
              <option value="<?php echo $encriptID;?>" ><?php echo $row['direccion']?></option>
              <?php } ?>
              </select><button type="button" class="orangeButton" id="fac_agregar_direccion">Agregar</button></td>
            </tr>
          <tr >
            <td align="right"><strong>Empresa:</strong></td>
            <td><select name="vfac_empresa" id="vfac_empresa" class="textfield_input" style="width:200px;" >
              <option value="">N/A</option>
              <?php 

$SQL="SELECT EM_ID,EM_NOMBRE FROM `empresa`";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
              <option value="<?php echo $encriptID?>" ><?php echo $row['EM_NOMBRE']?></option>
              <?php } ?>
              </select></td>
          </tr>
          <tr style="display:none" >
            <td align="right"><strong>Contrato:</strong></td>
            <td><select name="contrato" id="contrato" class="textfield_input" style="width:200px;" >
              <option value="">N/A</option>
     
            </select></td>
          </tr>
          <tr  >
            <td colspan="2" align="center" style="display:none"><table width="400" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="center">&nbsp;</td>
                <td>&nbsp;</td>
                </tr>
              <tr>
                <td width="100" align="center"><strong>
                  <label class="fsLabel fsRequiredLabel" for="label">Tipo de documento<span>*</span></label>
                  </strong></td>
                <td width="100"><strong>
                  <label class="fsLabel fsRequiredLabel" for="label">Numero de documento<span>*</span></label>
                  </strong></td>
                </tr>
              <tr>
                <td width="100" align="center"><select name="fact_v_id_documento" id="fact_v_id_documento" class="required" >
                  <option value="">Seleccione</option>
                    <?php 

$SQL="SELECT * FROM `sys_documentos_identidad` WHERE STATUS='A'";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->getEncrypt()->encrypt($row['id_documento'],$protect->getSessionID());
?>
                    <option value="<?php echo $encriptID?>"><?php echo $row['descripcion']?></option>
                    <?php } ?>
                  </select></td>
                <td width="100"><span class="fsLabel fsRequiredLabel">
                  <input type="text" id="fact_v_numero_documento" name="fact_v_numero_documento" size="20" value=""  class="textfield_input required" disabled/>
                  </span></td>
                </tr>
              <tr>
                <td align="center"><strong>A nombre de:</strong></td>
                <td><input type="text" name="fact_a_nombre_de" id="fact_a_nombre_de"  class="textfield_input required"/></td>
                </tr>
              </table></td>
          </tr>
          </table></td>
      </tr>
      <tr>
        <td align="center">&nbsp;</td>
      </tr>
      <tr>
          <td align="center"><button type="button" class="greenButton" id="bt_fac_add_process">&nbsp;Agregar&nbsp;</button>
            <button type="button" class="redButton" id="bt_fact_cancel">Cancelar</button>&nbsp;</td>
      </tr>
      <tr>
        <td align="center">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
</table>
</form>
      </div>
       
    </div>
  </div>
</div>
<?php } ?>