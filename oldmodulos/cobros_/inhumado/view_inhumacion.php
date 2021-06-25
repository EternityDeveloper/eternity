<?php 
if (!isset($protect)){
	exit;	
} 

if (!isset($_REQUEST['contrato'])){
	exit;
}
$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));


if (!$contrato->serie_contrato){
	exit;
}
SystemHtml::getInstance()->includeClass("cobros","FacturarPS"); 
SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
SystemHtml::getInstance()->includeClass("estructurac","Asesores");
SystemHtml::getInstance()->includeClass("client","PersonalData"); 

FacturarPS::getInstance()->clearCarSession();
STCSession::GI()->setSubmit("prc_inhumacion",true);

$con=new Contratos($protect->getDBLink()); 
$cdata=$con->getInfoContrato($contrato->serie_contrato,$contrato->no_contrato);
//print_r($cdata);
$product=$con->getDetalleProductsFromContrato($contrato->serie_contrato,$contrato->no_contrato);

$plan=$product[0]['CODIGO_TP'];

$ase=new Asesores($protect->getDBLink());
$asesor=$ase->getComercialParentData($cdata->codigo_asesor);
$nombre_asesor=$asesor[0]['nombre']." ".$asesor[0]['apellido'];
$nombre_gerente=$asesor[1]['nombre']." ".$asesor[1]['apellido'];

$procesado_por=PersonalData::getInstance()->getClientData(UserAccess::getInstance()->getIDNIT()); 

?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:840px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Orden de Servicio Inhumación</h4>
      </div>
      <div class="modal-body">
 <form id="detalle_inhumado_form" >	
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td><ul id="inhumacion_tab" class="nav nav-tabs">
   <li  class="active" ><a href="#solicitante" data-toggle="tab" >SOLICITANTE</a></li> 
   <li ><a href="#tab_difunto" data-toggle="tab" >DIFUNTO</a></li> 
   <li ><a href="#operaciones" data-toggle="tab" >OPERACIONES</a></li>    
   <li ><a href="#inhu_facturacion" data-toggle="tab" >FACTURACION</a></li> 
   <li ><a href="#inhu_comentario" data-toggle="tab" >COMENTARIOS</a></li>    
         
</ul></td>
    </tr>
    <tr>
      <td>
      <div id="inhumacion_tab_content" class="tab-content">
        <!--INICIO DEL TAB MAIN--> 
        <div class="tab-pane fade in active" id="solicitante">
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td height="30" colspan="3" align="center"  style="background: #999; font-weight: bold;">INFORMACION GENERAL DEL SOLICITANTE</td>
            </tr>
            <tr>
              <td valign="top"><table width="400" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle fsDivPage">
                <tr>
                  <td width="150"><strong>Nombre contacto</strong></td>
                  <td><input name="solicitante_nombre_contacto" type="text" class="form-control required" id="solicitante_nombre_contacto" /></td>
                </tr>
                <tr>
                  <td><strong>Parentesco</strong></td>
                  <td><select name="solicitante_parentesco" id="solicitante_parentesco" class="required form-control" >
                    <option value="">Seleccione</option>
                    <?php 

$SQL="SELECT * FROM `tipos_parentescos` WHERE estatus=1 ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['id_parentesco']);
?>
                    <option <?php echo $data_p['id_parentesco']==$row['id_parentesco']?'selected':''?> value="<?php echo $encriptID;?>"><?php echo $row['parentesco']?></option>
                    <?php } ?>
                  </select></td>
                </tr>
                <tr>
                  <td><strong>Titular del contrato</strong></td>
                  <td><input name="inf_contrato" type="text" disabled="disabled" class="form-control" id="inf_contrato" value="<?php echo $cdata->nombre_titular?>" /></td>
                </tr>
              </table></td>
              <td>&nbsp;</td>
              <td valign="top"><table width="400" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle fsDivPage">
                <tr>
                  <td width="150"><strong>Cedula titular del contrato</strong></td>
                  <td><input name="nuevo_saldo9" type="text" disabled="disabled" class="form-control" id="nuevo_saldo17" value="<?php echo $cdata->id_nit?>" /></td>
                </tr>
                <tr>
                  <td><strong>Teléfono</strong>:</td>
                  <td><input name="solicitante_telefono" type="text" class="form-control required" id="solicitante_telefono" /></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td valign="top">&nbsp;</td>
              <td>&nbsp;</td>
              <td valign="top">&nbsp;</td>
            </tr>
            <tr>
              <td valign="top">&nbsp;</td>
              <td>&nbsp;</td>
              <td valign="top">&nbsp;</td>
            </tr>
          </table>
        </div>
        <div class="tab-pane fade in" id="tab_difunto">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
      		    <tr>
      		       <td id="detalle_inhumado_view"><?php include("view_detalle_inhumado.php");?></td>
    		      </tr>
      		   
    		    </table>
        </div>  
        <div class="tab-pane fade in" id="operaciones">
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td height="30" colspan="3" align="center" style="background: #999; font-weight: bold;"><strong>INFORMACION DE  OPERACIONES</strong></td>
            </tr>
            <tr>
              <td colspan="3"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="tb_detalle fsDivPage">
                <tr>
                  <td width="210" height="25" style="background: #D1D1D1; font-weight: bold;"><strong>Parcela</strong></td>
                  <td width="210" style="background: #D1D1D1; font-weight: bold;"><strong>Plan</strong></td>
                  <td width="210" style="background: #D1D1D1; font-weight: bold;"><strong>Bóveda</strong></td>
                  <td width="210" style="background: #D1D1D1; font-weight: bold;">&nbsp;</td>
                </tr>
                <tr>
                  <td height="25"><select name="servicio_parcela" id="servicio_parcela" class="form-control required"  >
                    <option value="">Seleccione</option>
                    <?php 
		
			 	foreach($product as $key =>$producto){ 
					$id=System::getInstance()->Encrypt(json_encode($producto));
		?>
                    <option value="<?php echo $id?>"><?php echo $producto['id_jardin']."-".$producto['id_fases']."-".$producto['bloque']."-".$producto['lote']?></option>
                    <?php } ?>
                  </select></td>
                  <td id="dt_plan">&nbsp;</td>
                  <td id="dt_boveda">&nbsp;</td>
                  <td id="dt_hora_servicio">&nbsp;</td>
                </tr>
                <tr>
                  <td height="25" style="background: #D1D1D1; font-weight: bold;"><strong>Tipo de Servicio</strong></td>
                  <td style="background: #D1D1D1; font-weight: bold;"><strong>Asesor que Vende</strong></td>
                  <td style="background: #D1D1D1; font-weight: bold;"><strong>Asesor que Atiende</strong></td>
                  <td style="background: #D1D1D1; font-weight: bold;"><strong>Supervisor</strong></td>
                </tr>
                <tr>
                  <td height="25">INHUMACION</td>
                  <td><?php echo $nombre_asesor;?></td>
                  <td id="dt_atendido_por"><input  name="txt_atendido_por" type="hidden" class="required" id="txt_atendido_por" style="width:250px;" /></td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td height="25" style="background: #D1D1D1; font-weight: bold;"><strong>Nombre en Lápida</strong></td>
                  <td colspan="3"><input name="nombre_lapida"  type="text" class="form-control required" id="nombre_lapida" maxlength="24" /></td>
                </tr>
                <tr>
                  <td height="25" style="background: #D1D1D1; font-weight: bold;"><strong>Texto en Lápida</strong></td>
                  <td colspan="3"><input name="esquela"  type="text" class="form-control required" id="esquela" /></td>
                </tr>
              </table></td>
            </tr>
          </table>
        </div>   
        <div class="tab-pane fade in " id="inhu_facturacion">
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
         
            <tr>
              <td height="25"><button type="button" class="orangeButton" id="inh_bt_facturar">Agregar</button></td>
            </tr>
            <tr>
              <td height="25" id="detalle_factura_pro">&nbsp;</td>
            </tr>
          </table>
        </div>                   
        
         <div class="tab-pane fade in" id="inhu_comentario">
           <table width="100%" border="0" cellpadding="0" cellspacing="0">
             <tr>
               <td height="25" colspan="3" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="tb_detalle fsDivPage">
                 <tr>
                   <td height="25"><strong>Comentarios: </strong></td>
                 </tr>
                 <tr>
                   <td height="25"><textarea name="servicio_descripcion" class="form-control" id="servicio_descripcion"></textarea></td>
                 </tr>
               </table></td>
             </tr>
             <tr>
               <td colspan="3"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="tb_detalle fsDivPage">
                 <tr>
                   <td width="274" height="25"><p><strong>Procesado  Por:</strong></p></td>
                   <td width="274"><strong>Recibido Por:</strong></td>
                   <td width="274"><strong>Revisado Por:</strong></td>
                   <td width="279"><strong>Autorizado Por:</strong></td>
                 </tr>
                 <tr>
                   <td height="25"><?php echo $procesado_por['nombre_completo'];?></td>
                   <td>&nbsp;</td>
                   <td>&nbsp;</td>
                   <td>&nbsp;</td>
                 </tr>
               </table></td>
             </tr>
           </table>
         </div>
        
      <!--FIN DEL TAB MAIN-->          
      </div>
      
      </td>
    </tr>
  </table>
  </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="close_view" data-dismiss="modal">Cerrar</button>
        <button type="button" id="procesar_solicitud" class="btn btn-primary">Generar</button>
      </div>
    </div>
  </div>
</div>
 