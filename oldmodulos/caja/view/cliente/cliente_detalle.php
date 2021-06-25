<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

if (!isset($id)){
	exit;
}	  
$direccion="";

if (isset($id->id_nit)){
	SystemHtml::getInstance()->includeClass("client","PersonalData");
	SystemHtml::getInstance()->includeClass("caja","Caja"); 
	
	$caja= new Caja($protect->getDBLINK());
	$caja->session_restart();
	
	$person= new PersonalData($protect->getDBLink());
	$peron_data=$person->getClientData($id->id_nit);
	
}else{
	exit;	
}



	SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
 	SystemHtml::getInstance()->addTagStyle("css/showLoading.css");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js"); 
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
 	SystemHtml::getInstance()->addTagScript("script/Class.js");   
	SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
	SystemHtml::getInstance()->addTagScript("script/jquery.ui.combogrid-1.6.3.js");  
  	SystemHtml::getInstance()->addTagScript("script/jquery.timeentry.min.js");

	SystemHtml::getInstance()->addTagScriptByModule("class.CierreCaja.js");	   
	
	SystemHtml::getInstance()->addTagScriptByModule("class.COperacion.js","caja"); 
  	SystemHtml::getInstance()->addTagScriptByModule("class.FormaPago.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.TDocSerieRVenta.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.CFactura.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.PagoComponent.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.Facturar.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.GGestion.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.NotasCD.js","caja");	
	SystemHtml::getInstance()->addTagScriptByModule("class.NotasCDCliente.js","caja");		
	
	SystemHtml::getInstance()->addTagScriptByModule("class.PreFacturar.js","caja");	
	SystemHtml::getInstance()->addTagScriptByModule("class.ProcFactura.js","caja");	
	
	
	 
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

$(document).ready(function(){ 
	
	$(".gestion_").click(function(){
  		var obj = new window[$(this).attr("object")]('content_dialog');   
 		obj.doView('<?php echo System::getInstance()->Encrypt($id->id_nit);?>','','',''); 
	});
	$(".notas_cd").click(function(){
  		var obj = new window[$(this).attr("object")]('content_dialog');   
 		obj.doView($(this).attr("id")); 
	});	
	
	 _op= new CierreCaja('content_dialog'); 
	/*
	createTable("_detalle_pago",{
		"bSort": false,
		"bFilter": true,
		"bInfo": false,
		"bPaginate": true,
		"bLengthChange": false,
		"oLanguage": {
				"sLengthMenu": "Mostrar _MENU_ registros por pagina",
				"sZeroRecords": "No se ha encontrado - lo siento",
				"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
				"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
				"sInfoFiltered": "(filtrado de _MAX_ total registros)",
				"sSearch":"Buscar"
			}
		});	*/
});
function toggle(id){
   $("#"+id).toggle();
}
function doAction(str,tipo_movimiento){ 
	if (str!=null){
		var tipo=$(tipo_movimiento).attr("inf"); 
		var obj = new window[str]('content_dialog');  
	//	obj.setToken(_dashboard.getToken());
		obj.doView('<?php echo System::getInstance()->Encrypt($id->id_nit);?>','','',tipo);
	}	
}

function anular(id){
	_op.doAnularReciboCaja(id);
} 

function print_(id){
	var op= new CierreCaja('content_dialog'); 	
	op.doPrintRecibo(id);
} 

</script>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:0px;padding:0px">
 
  <tr>
    <td align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="450"><span style="font-size:20px;">
          <input type="submit" name="lb_regresar" id="lb_regresar" value="REGRESAR" class="btn btn-default" onclick="window.history.back();" />
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
                      <td width="442" align="left" ><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tb_detalle">
                        <tr>
                          <td height="25" align="left" style="background-color:#CCCCCC"><strong>CEDULA</strong></td>
                          <td><?php echo $peron_data['id_nit'];?></td>
                        </tr>
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
                      </table></td>
                      <td width="452" align="left" valign="top" ><table width="404" border="0" cellpadding="0" cellspacing="0" class="tb_detalle" style="display:none">
                        <tr>
                          <td width="112" height="25" align="left"  style="background-color:#CCCCCC"><strong>DIA DE PAGO</strong></td>
                          <td width="292" style="padding-left:10px;"><?php echo $data['dia_pago'];?></td>
                        </tr>
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
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>
  <ul id="contract-tb" class="nav nav-tabs">
   <li><a href="#bitacora" data-toggle="tab" >BITACORA</a>  </li> 
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
          <td valign="top"><table id="_detalle_pago" width="100%" border="1" style="font-size:12px;border-spacing:1px;" class="search_list table table-striped table-hover">
            <thead>
              <tr  style="background-color:#CCC;height:30px;" >
                <td width="208" align="center"><strong>FECHA DE PAGO</strong></td>
                <td width="276" align="center"><strong>CONTRATO</strong></td>
                <td width="276" align="center"><strong>TIPO MOVIMIENTO</strong></td>
                <td width="276" align="center"><strong>TIPO DE DOCUMENTO</strong></td>
                <td width="276" align="center"><strong>NO. RECIBO</strong></td>
                <td width="276" align="center"><strong>NO. FACTURA</strong></td>
                <td width="276" align="center"><strong>SERIE DOCUMENTO</strong></td>
                <td width="276" align="center"><strong>NO DOCUMENTO</strong></td>
                <td width="276" align="center"><strong>TIPO DE CAMBIO</strong></td>
                <td width="276" align="center"><strong>MONTO</strong></td>
                <td width="276" align="center"><strong>MONTO RD$</strong></td>
                <td width="276" align="center"><strong>CAJA</strong></td>
                <td align="center"><strong>COMENTARIO</strong></td>
                <td align="center">&nbsp;</td>
                <td align="center">&nbsp;</td>
                <td align="center">&nbsp;</td>
              </tr>
            </thead>
            <tbody>
              <?php
 
$SQL="SELECT 
	*,
	caja.DESCRIPCION_CAJA AS CAJA ,
	`tipo_documento`.`DOCUMENTO`,
	(SELECT GROUP_CONCAT(DISTINCT tipo_movimiento.DESCRIPCION)  FROM `movimiento_factura` AS MF 
INNER JOIN `tipo_movimiento` ON (tipo_movimiento.`TIPO_MOV`=MF.TIPO_MOV)
 WHERE MF.CAJA_SERIE=movimiento_caja.SERIE AND MF.CAJA_NO_DOCTO=movimiento_caja.NO_DOCTO) AS TMOVIMIENTO,
 (SELECT 
 		CONCAT(sp.`primer_nombre`,' ',sp.`segundo_nombre`,
		' ',sp.`primer_apellido`,' ',sp.segundo_apellido) FROM sys_personas AS sp 
		WHERE sp.id_nit=movimiento_caja.ANULADO_POR_ID_NIT) AS anulado_por 
 
	FROM `movimiento_caja` 
INNER JOIN `caja` ON (caja.ID_CAJA=movimiento_caja.ID_CAJA) 
INNER JOIN `tipo_documento` ON (`tipo_documento`.TIPO_DOC=movimiento_caja.TIPO_DOC)
WHERE movimiento_caja.id_nit='".$id->id_nit."'   
	  AND  movimiento_caja.TIPO_DOC IN ('RBC','NC','ND','RCA') 
ORDER BY movimiento_caja.FECHA ";
 
 $rs=mysql_query($SQL);

$show_anular=false;
if ($protect->getIfAccessPageById(160)){
	$show_anular=true;
}
 $reserva_en=System::getInstance()->Encrypt($data->no_reserva); 	
while($row=mysql_fetch_assoc($rs)){ 
 	$encriptID=System::getInstance()->Encrypt(json_encode($row)); 
	$id=System::getInstance()->Encrypt(json_encode(
				array(
					"serie_contrato"=>$row['SERIE_CONTRATO'],
					"no_contrato"=>$row['NO_CONTRATO'],
					"id_nit"=>$row['ID_NIT']
	)));  
 
?>
              <tr style="<?php echo trim($row['ANULADO'])=='S'?'color:red;cursor:pointer':''?>;" onclick="toggle('<?php echo $row['SERIE'].$row['NO_DOCTO']?>')">
                <td align="center"><?php echo $row['FECHA'];?></td>
                <td align="center"><a href="./?mod_cobros/delegate&contrato_view&id=<?php echo $id;?>" target="dsa" ><?php echo $row['SERIE_CONTRATO']." ".$row['NO_CONTRATO'];?></a></td>
                <td align="center"><?php echo $row['TMOVIMIENTO']?></td>
                <td align="center"><?php echo $row['DOCUMENTO']?></td>
                <td align="center"><?php echo $row['REP_VENTA']?></td>
                <td align="center"><?php echo $row['SERIE_FACTURA']?><?php echo $row['NO_DOC_FACTURA']?></td>
                <td align="center"><?php echo $row['SERIE']?></td>
                <td align="center"><?php echo $row['NO_DOCTO']?></td>
                <td align="center"><?php echo $row['TIPO_CAMBIO']?></td>
                <td align="center"><?php echo number_format($row['MONTO'],2)?></td>
                <td align="center"><?php echo number_format($row['MONTO']*$row['TIPO_CAMBIO'],2)?></td>
                <td align="center"><?php echo $row['CAJA']?></td>
                <td align="center"><?php echo $row['OBSERVACIONES']?></td>
                <td align="center"><?php // if (($row['SERIE']!="NC") && ($row['SERIE']!="ND")){?><a href="./?mod_caja/delegate&amp;recibo_factura&id=<?php echo $encriptID;?>" target="dsa" ><img src="images/preferences_desktop_printer.png" alt="" width="22" height="26" /></a>
                
                <a href="#"  onclick="print_('<?php echo $encriptID;?>')"><img src="images/preferences_desktop_printer.png" alt="" width="22" height="26" /></a> 
                <a href="./?mod_caja/delegate&amp;recibo_nfactura&id=<?php echo $encriptID;?>" target="dsa" ><img src="images/preferences_desktop_printer.png" alt="" width="22" height="26" /></a>
                <?php /// } ?> </td>
                <td align="center">
			<?php if ($protect->getIfAccessPageById(190)){ ?>
				<?php if (($row['SERIE']!="NC") && ($row['SERIE']!="ND") && ($row['ID_ESTATUS']!="44") && ($row['ID_ESTATUS']!="45") && ($row['ANULADO']=="N") && ($row['ID_CIERRE_CAJA']=="")  && ($show_anular==true)){?>
                  <a href="#" id="<?php echo $encriptID;?>" class="notas_cd" object="NotasCD"><img src="images/note_credito.png" alt="" width="32" height="32" /></a>
                  <?php   }
				  
			}?></td>
                <td align="center"><?php if (($row['SERIE']!="NC") && ($row['SERIE']!="ND")  && ($row['ID_ESTATUS']!="44")  && ($row['ID_ESTATUS']!="45") &&($row['ANULADO']=="N") && ($row['ID_CIERRE_CAJA']=="") && ($row['ID_ESTATUS']!=38) && ($show_anular==true)){?>
                  <a href="#"  onclick="anular('<?php echo $encriptID;?>')">ANULAR</a>
                  <?php  } ?>
                  
                </td>
              </tr>
              <?php if ($row['ANULADO']=="S"){?>
              <tr style="display:none"  id="<?php echo $row['SERIE'].$row['NO_DOCTO']?>">
                <td colspan="16" align="center"><table width="500" border="0" cellpadding="0" cellspacing="0" class="tb_detalle" style="background:#FFF">
                  <tr>
                    <td width="150" height="25" align="left" style="background-color:#CCCCCC"><strong>FECHA ANULADO</strong></td>
                    <td width="336"><?php echo $row['ANULADO_DATE'];?></td>
                  </tr>
                  <tr>
                    <td height="25" align="left"  style="background-color:#CCCCCC"><strong>DESCRIPCION</strong></td>
                    <td><?php echo $row['ANULADO_DESCRIPCION'];?></td>
                  </tr>
                  <tr>
                    <td height="25" align="left"  style="background-color:#CCCCCC"><strong> ANULADO POR</strong></td>
                    <td><?php echo $row['anulado_por'];?></td>
                  </tr>
                </table></td>
                </tr>
              <?php } ?>                
              <?php } ?>
            </tbody>
          </table></td> 
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
	AND tipo_movimiento.FACTURA_CLIENTE=1  and tipo_mov_caja.estatus=1 ";
	
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

<!--<input type="button"  id_nit="<?php echo $id_nit?>" class="gestion_ btn btn-primary" value="C X C"  object="ProcFactura"  />     
-->             

<!--<input type="button" id_nit="<?php
 echo System::getInstance()->Encrypt($id->id_nit);?>" class="gestion_ btn btn-primary" value="GESTION" object="GGestion" />   -->
 
 <input type="button" id_nit="<?php
 echo System::getInstance()->Encrypt($id->id_nit);?>" class="gestion_ btn btn-primary" value="Nota de Credito" object="NotasCDCliente" /> 
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
            </table></td>
          </tr>
  </table>
 <div id="content_dialog"  ></div>