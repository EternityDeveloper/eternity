<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

   
$direccion="";
 
if (isset($no_reserva)){
	SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva"); 
	$_reserva=new Reserva($protect->getDBLink());  

		$SQL="SELECT 
			*, 
			`reserva_inventario`.no_reserva AS no_reserva_id,
			reserva_inventario.no_recibo AS serie_recibo_no,
			DATEDIFF(reserva_inventario.`fecha_fin`,CURDATE()) AS day_restantes,
			DATE_FORMAT(reserva_inventario.fecha_reserva, '%d-%m-%Y %h:%m:%s') AS  fecha_reserva,
			DATE_FORMAT(reserva_inventario.fecha_fin, '%d-%m-%Y %h:%m:%s') AS  fecha_fin,
			CONCAT(asesor.`primer_nombre`,' ',asesor.`primer_apellido`) AS nombre_asesor,
			CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`primer_apellido`) AS nombre_cliente,
			sys_personas.id_nit AS nit
		FROM reserva_inventario
		INNER JOIN `reserva_ubicaciones` ON (`reserva_ubicaciones`.`no_reserva`=reserva_inventario.no_reserva) 
		INNER JOIN `sys_status` ON (sys_status.`id_status`=reserva_inventario.`estatus`)
		INNER JOIN tipos_reservas ON (tipos_reservas.`id_reserva`=reserva_inventario.id_reserva)
		LEFT JOIN `sys_personas` ON (sys_personas.`id_nit`=reserva_inventario.`id_nit`)
		LEFT JOIN `sys_personas` AS asesor ON (`asesor`.`id_nit`=reserva_inventario.`nit_comercial`)
		WHERE 
			reserva_ubicaciones.estatus=1 AND
		 	 reserva_inventario.no_reserva='". mysql_real_escape_string($no_reserva) ."' ";
 
		$rs=mysql_query($SQL);
		$data=mysql_fetch_object($rs);
		 
 
}else{
	exit;	
}

	SystemHtml::getInstance()->includeClass("client","PersonalData");
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	
	$caja= new Caja($protect->getDBLINK());
	$caja->session_restart();
	
	$person= new PersonalData($protect->getDBLink());
	$peron_data=$person->getClientData($data->nit);
 

	SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
 	SystemHtml::getInstance()->addTagStyle("css/showLoading.css");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js"); 
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
 	SystemHtml::getInstance()->addTagScript("script/Class.js");   
	SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
	SystemHtml::getInstance()->addTagScript("script/jquery.ui.combogrid-1.6.3.js");  
  	SystemHtml::getInstance()->addTagScript("script/jquery.timeentry.min.js");
	   
	SystemHtml::getInstance()->addTagScriptByModule("class.COperacion.js","caja"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.PagoAbono.js","caja"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.PagoInicial.js","caja"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.PagoReserva.js","caja"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.PagoContrato.js","caja"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.FormaPago.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.TDocSerieRVenta.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.CFactura.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.PagoComponent.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.Facturar.js","caja");
	
	 
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Empresa.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Persona.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Direccion.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Telefono.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Email.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Referencia.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Contactos.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Referidos.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.ModuloPersona.js");
  
	SystemHtml::getInstance()->addTagScript("script/Class.direcciones.js");
	SystemHtml::getInstance()->addTagScript("script/Class.phone.js");
	SystemHtml::getInstance()->addTagScript("script/Class.empresa.js");
	SystemHtml::getInstance()->addTagScript("script/Class.email.js");
	SystemHtml::getInstance()->addTagScript("script/Class.reference.js");
	SystemHtml::getInstance()->addTagScript("script/Class.contactos.js");
	SystemHtml::getInstance()->addTagScript("script/Class.AsesoresTree.js");
	SystemHtml::getInstance()->addTagScript("script/Class.Referidos.js"); 
	SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");	   
	/*BOOSTRAP SCRIPT*/
	SystemHtml::getInstance()->addTagScript("script/bootstrap/js/bootstrap.min.js");
	
	SystemHtml::getInstance()->addTagStyle("css/jquery.ptTimeSelect.css"); 
	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css"); 
	
	SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");
	 
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
 
 ?> 
<style>
  
.tb_detalle > tbody > tr > th,
.tb_detalle > tfoot > tr > th,
.tb_detalle > thead > tr > td,
.tb_detalle > tbody > tr > td,
.tb_detalle > tfoot > tr > td {
  padding: 7px;
  line-height: 1.42857143;
  vertical-align: top;
  border-top: 1px solid #ddd;
}
 

.fixed-table-container {
  height: 200px; 
  position: relative; /* could be absolute or relative */
  width:900px;
} 
.fixed-table-container-inner {
  overflow-x: hidden;
  overflow-y: auto;
  height: 100%;
}
.th-inner {
  position: absolute;
  width:90px;
  height:31px; 
  top: 0;
  line-height: 30px; 
  text-align: center; 
}
.th-inner_hb{
  position: absolute;
  width:150px;
  height:31px; 
  top: 0;
  line-height: 30px; 
  text-align: center; 
}
.first .th-inner {
	border-left: none;
	padding-left: 6px;
}
.header-background {
  background-color: #D5ECFF;
  height: 30px; /* height of header */
  position: absolute;
  top: 0;
  right: 0;
  left: 0;
}
 
</style>
<script> 
 
function doAction(str,tipo_movimiento){ 
	if (str!=null){ 
		var tipo=$(tipo_movimiento).attr("id");
		var obj = new window[str]('content_dialog'); 
		obj.doView('<?php echo System::getInstance()->Encrypt($data->nit);?>','','<?php echo System::getInstance()->Encrypt(json_encode(array("no_reserva"=>$data->no_reserva,"id_reserva"=>$data->id_reserva)));?>',$(tipo_movimiento).attr("inf"));
	}	
}
</script>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:0px;padding:0px">
 
  <tr>
    <td align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="450"><span style="font-size:20px;">
          <input type="submit" name="lb_regresar" id="lb_regresar" value="REGRESAR" class="btn btn-default"  onclick="window.history.back();" />
        </span></td>
        </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><ul id="myTab" class="nav nav-tabs">
   <li class="active"><a href="#home" data-toggle="tab" >CLIENTE</a>
   </li> 
 
</ul></td>
      </tr>
      <tr>
        <td>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade in active" id="home">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size:12px">
                 

                    <tr>
                      <td height="25" colspan="3" align="left" ><strong>INFORMACION DEL CLIENTE</strong></td>
                    </tr>
                    <tr>
                      <td width="442" align="left" valign="top" ><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tb_detalle">
                        <tr>
                          <td width="112" height="25" align="left" style="background-color:#CCCCCC"><strong>CLIENTE</strong></td>
                          <td width="336"><?php echo $peron_data['primer_nombre']." ".$peron_data['segundo_nombre']." ".$peron_data['primer_apellido']." ".$peron_data['segundo_apellido'];?></td>
                        </tr>
                        <tr>
                          <td height="25" align="left"  style="background-color:#CCCCCC"><strong>FECHA NACIMIENTO</strong></td>
                          <td  ><?php echo $peron_data['fecha_nac'];?></td>
                        </tr>
                        <tr>
                          <td height="25" align="left"  style="background-color:#CCCCCC"><strong>TELEFONO</strong></td>
                          <td><span style="margin:0px;"><?php echo $cdata->serie_contrato ." ".$cdata->no_contrato;?></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="left"  style="background-color:#CCCCCC"><strong>LUGAR DE NACIMIENTO</strong></td>
                          <td><?php echo $peron_data['lugar_nacimiento'];?></td>
                        </tr>
                        <tr>
                          <td height="25" align="left"  style="background-color:#CCCCCC"><strong>DIAS RESTANTES</strong></td>
                          <td><span class="day_restantes"><?php echo $data->day_restantes;?></span> Dias</td>
                        </tr>
                      </table></td>
                      <td width="452" align="left" valign="top" ><table width="404" border="0" cellpadding="0" cellspacing="0" class="tb_detalle" >
                        <tr>
                          <td height="25" align="left" style="background-color:#CCCCCC"><strong>NO. RESERVA</strong></td>
                          <td><?php echo $data->no_reserva;?></td>
                        </tr>
                        <tr>
                          <td width="205" height="25" align="left"  style="background-color:#CCCCCC"><strong>TIPO </strong></td>
                          <td width="199"  ><?php echo $data->reserva_descrip;?></td>
                        </tr>
                        <tr>
                          <td height="25" align="left"  style="background-color:#CCCCCC"><strong>RECIBO SERIE</strong></td>
                          <td><?php echo $data->serie_recibo_no;?></td>
                        </tr>
                        <tr>
                          <td height="25" align="left"  style="background-color:#CCCCCC"><strong>INICIA</strong></td>
                          <td style="padding-left:10px;"><?php echo $data->fecha_reserva;?></td>
                        </tr>
                        <tr>
                          <td height="25" align="left"  style="background-color:#CCCCCC"><strong>TERMINA</strong></td>
                          <td style="padding-left:10px;"><?php echo $data->fecha_fin;?></td>
                        </tr>
                      </table></td>
                      <td height="25" align="center" valign="top" >&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan="2" align="left" ><strong>INFORMACION DEL PRODUCTO</strong></td>
                      <td height="25" align="center" >&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan="2" align="left" valign="top" ><table id="tb_items_reservados" width="100%" border="1" class="table table-striped table-hover">
                        <thead>
                          <tr>
                            <td align="center"><strong>Jardin</strong></td>
                            <td align="center"><strong>Fase</strong></td>
                            <td align="center"><strong>Bloque</strong></td>
                            <td align="center"><strong>Lote</strong></td>
                            <td align="center"><strong>Cavidades</strong></td>
                            <td align="center"><strong>Osarios</strong></td>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
   
$SQL="SELECT  
	reserva_inventario.`no_reserva`,
	inventario_jardines.id_jardin, 
	inventario_jardines.id_fases, 
	inventario_jardines.`lote`, 
	inventario_jardines.`bloque`, 
	inventario_jardines.`cavidades`,
	inventario_jardines.`osarios`,
	reserva_inventario.`no_recibo` AS serie_recibo_no,		
	DATE_FORMAT(reserva_inventario.fecha_reserva, '%d-%m-%Y') AS  fecha_reserva,
	DATE_FORMAT(reserva_inventario.fecha_fin, '%d-%m-%Y') AS  fecha_fin
 FROM `reserva_inventario` 
INNER JOIN `sys_status` ON (sys_status.`id_status`=reserva_inventario.`estatus`)
INNER JOIN tipos_reservas ON (tipos_reservas.`id_reserva`=reserva_inventario.id_reserva)
INNER JOIN `inventario_jardines` ON (inventario_jardines.no_reserva=reserva_inventario.no_reserva)
 WHERE reserva_inventario.no_reserva='".$data->no_reserva."'";
		  
	$rs=mysql_query($SQL); 
	$total=mysql_num_rows($rs); 
	while($row=mysql_fetch_object($rs)){
		$encryt=System::getInstance()->Encrypt(json_encode($row));
   ?>
                          <tr>
                            <td align="center" class="display"><?php echo $row->id_jardin; ?></td>
                            <td align="center" class="display"><?php echo $row->id_fases;?></td>
                            <td align="center" class="display"><?php echo $row->bloque;?></td>
                            <td align="center" class="display" ><?php echo $row->lote;?></td>
                            <td align="center" class="display" ><?php echo $row->cavidades?></td>
                            <td align="center" ><?php echo $row->osarios?></td>
                          </tr>
<?php  
	} ?>
                        </tbody>
                      </table></td>
                      <td width="163" height="25" align="center" valign="top" >&nbsp;</td>
                    </tr>
                  </table>
                </div>                               
                
            </div>
           </td>
      </tr>
    </table></td>
  </tr>
 
          <tr>
            <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            
              <tr>
                <td><ul id="contract-tb" class="nav nav-tabs"> 
   <li  class="active"><a href="#movimiento" data-toggle="tab">MOVIMIENTOS</a></li>  
</ul>
<div  class="tab-content">
	<div class="tab-pane" id="bitacora">Bitacora</div> 
    <div class="tab-pane  fade in active" id="movimiento">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><strong><span style="font-size: 18px">MOVIMIENTO DE CUENTA</span></strong></td>
          </tr>
        <tr>
          <td valign="top"><table id="_detalle_contrato2" width="85%" border="1" style="font-size:12px;border-spacing:1px;" class="tb_detalle fsDivPage">
            <thead>
              <tr  style="background-color:#CCC;height:30px;" >
                <td width="208" align="center"><strong>FECHA DE PAGO</strong></td>
                <td width="276" align="center"><strong>TIPO MOVIMIENTO</strong></td>
                <td width="276" align="center"><strong>TIPO DE DOCUMENTO</strong></td>
                <td width="276" align="center"><strong>NO. FACTURA</strong></td>
                <td width="276" align="center"><strong>SERIE DOCUMENTO</strong></td>
                <td width="276" align="center"><strong>NO DOCUMENTO</strong></td>
                <td width="276" align="center"><strong>TIPO DE CAMBIO</strong></td>
                <td width="276" align="center"><strong>MONTO</strong></td>
                <td width="276" align="center"><strong>MONTO RD$</strong></td>
                <td width="276" align="center"><strong>CAJA</strong></td>
                <td align="center">&nbsp;</td>
                </tr>
            </thead>
            <tbody>
              <?php
 
$SQL="SELECT 
	*,
	caja.DESCRIPCION_CAJA AS CAJA ,
	`tipo_documento`.`DOCUMENTO`,
	(SELECT GROUP_CONCAT(tipo_movimiento.DESCRIPCION) FROM `movimiento_factura` AS MF 
INNER JOIN `tipo_movimiento` ON (tipo_movimiento.`TIPO_MOV`=MF.TIPO_MOV)
 WHERE MF.CAJA_SERIE=movimiento_caja.SERIE and MF.CAJA_NO_DOCTO=movimiento_caja.NO_DOCTO) as TMOVIMIENTO
	FROM `movimiento_caja` 
INNER JOIN `caja` ON (caja.ID_CAJA=movimiento_caja.ID_CAJA) 
INNER JOIN `tipo_documento` ON (`tipo_documento`.TIPO_DOC=movimiento_caja.TIPO_DOC)
WHERE movimiento_caja.id_nit='".$data->nit."' and  movimiento_caja.NO_RESERVA='".$data->no_reserva."' 
and movimiento_caja.ID_RESERVA='".$data->id_reserva."' and  movimiento_caja.ANULADO='N'";
 
 $rs=mysql_query($SQL);

$reserva_en=System::getInstance()->Encrypt($data->no_reserva); 	
while($row=mysql_fetch_assoc($rs)){
 	$encriptID=System::getInstance()->Encrypt(json_encode($row)); 	
	 
?>
              <tr style="height:30px;">
                <td align="center"><?php echo $row['FECHA']?></td>
                <td align="center"><?php echo $row['TMOVIMIENTO']?></td>
                <td align="center"><?php echo $row['DOCUMENTO']?></td>
                <td align="center"><?php echo $row['SERIE_FACTURA']?><?php echo $row['NO_DOC_FACTURA']?></td>
                <td align="center"><?php echo $row['SERIE']?></td>
                <td align="center"><?php echo $row['NO_DOCTO']?></td>
                <td align="center"><?php echo $row['TIPO_CAMBIO']?></td>
                <td align="center"><?php echo number_format($row['MONTO'],2)?></td>
                <td align="center"><?php echo number_format($row['MONTO']*$row['TIPO_CAMBIO'],2)?></td>
                <td align="center"><?php echo $row['CAJA']?></td>
                <td align="center"><a href="./?mod_caja/delegate&amp;recibo_factura&id=<?php echo $encriptID;?>" target="dsa" ><img src="images/preferences_desktop_printer.png" width="22" height="26" /></a></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
            </td>
          </tr>
        <tr>
          <td valign="top">&nbsp;</td>
        </tr>
        <tr>
          <td valign="top">
<?php 
/*VALIDO LA CAJA A LA QUE PERTENECE EL OFICIAL*/
$rsx=$protect->getCaja(); 
if (count($rsx)>0){
?>

<?php 
 
$SQL="SELECT 
			tipo_movimiento.* 
		FROM `tipo_movimiento` 
	INNER JOIN `tipo_mov_caja` ON (`tipo_mov_caja`.`CAJA_TIPO_MOV_CAJA`=tipo_movimiento.TIPO_MOV)
	WHERE  tipo_mov_caja.`CAJA_ID_CAJA`='".$rsx['ID_CAJA']."' 
	AND tipo_mov_caja.`CAJA_TIPO_MOV_CAJA` in ('RES','INI','CTI') and tipo_mov_caja.estatus=1  ";
	
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
	$tmov=System::getInstance()->Encrypt($row['TIPO_MOV']);
?>             
       <button type="button" class="btn btn-warning" data-toggle="dropdown" id="<?php echo $encriptID?>" onclick="doAction('<?php echo $row['class'];?>',this)"  inf="<?php echo $tmov;?>"  >
                    <?php echo $row['DESCRIPCION']?>
                  </button>
               
<?php } ?> 
                 
<?php } ?>                 


</td>
        </tr>
        <tr>
          <td valign="top">&nbsp;</td>
        </tr>
      </table>
    </div>   
      
</div>
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table></td>
          </tr>
  </table>
 <div id="content_dialog" ></div>