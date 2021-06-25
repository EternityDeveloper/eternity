<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

//if (!$protect->getIfAccessPageById(101)){exit;}
 

	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
//	SystemHtml::getInstance()->addTagScript("script/jquery.jqplot.min.js"); 

	SystemHtml::getInstance()->addTagScript("script/Class.js");  
	
	SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
	SystemHtml::getInstance()->addTagScript("script/jquery.ui.combogrid-1.6.3.js");
	
	SystemHtml::getInstance()->addTagScriptByModule("class.Dashboard.js"); 


//	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
//	SystemHtml::getInstance()->addTagStyle("script/jquery.jqplot.min.css");
	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");


	SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
	SystemHtml::getInstance()->includeClass("contratos","Contratos");   
		  
	$cobros= new Cobros($protect->getDBLINK()); 
		
 	$detalle_c=$cobros->detalleCartera($protect->getIDNIT());
	
	$detall_cc=$cobros->getDetalleCarteraCobrada($protect->getIDNIT());
	
	$detalle_des_anul=$cobros->detalle_desestimiento_anulacion($protect->getIDNIT());
 
	 
?>
<script> 
 
var _dashboard= new DashBoard("content_dialog");

$(document).ready(function(){
	_dashboard.doDashboard();
	  
	$('#myTab a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});
		 
	$("ul.nav-tabs > li > a").on("shown.bs.tab", function (e) {
		var id = $(e.target).attr("href").substr(1);
		window.location.hash = id;
	});
	// on load of the page: switch to the currently selected tab
	var hash = window.location.hash;
	$('#myTab a[href="' + hash + '"]').tab('show');

});
 

</script>
<style>
.AlertColorDanger td
{
 color:#FFF;
 background-color: #D90000 !important;
}
.AlertColor5 td
{
 color:#000;
 background-color: #FFD24D !important;
}

.green_selected
{
 color:#000;
 background-color: #5BFF85 !important;
}
._document{}

.round_meta{
	background:#0F0;
	color:#FFF;
	padding:3px 6px;
	border-radius:50%;	
	font-weight:bold;
}

</style>
<table width="95%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="200"><div class="col-lg-3 col-md-6" style="width:200px;">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-3"> <i class="fa fa-comments fa-5x"></i></div>
                  <div class="col-xs-9 text-left" style="width:200px;">
                    <div class="huge" style="font-size:24px;">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="100"><span class="huge" style="font-size:24px;"><?php echo $detalle_c['total_clientes'];?> </span></td>
                          <td><?php echo $detall_cc['NO_CLIENTES']?></td>
                        </tr>
                      </table>
                    </div>
                    <div >TOTAL CLIENTES</div>
                  </div>
                </div>
              </div>
            </div>
          </div></td>
          <td>&nbsp;</td>
          <td width="200">
          <div class="col-lg-3 col-md-6" style="width:200px;">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-3"> <i class="fa fa-comments fa-5x"></i></div>
                  <div class="col-xs-9 text-left" style="width:200px;">
                    <div class="huge" style="font-size:24px;">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="100"><span class="huge" style="font-size:24px;"><?php echo $detalle_c['total_contratos'];?></span></td>
                          <td><?php echo $detall_cc['NO_CONTRATO']?></td>
                        </tr>
                      </table>
                    </div>
                    <div >TOTAL CONTRATOS</div>
                  </div>
                </div>
              </div> </div>
          </div>
          </td>
          <td  width="400">
          <div class="col-lg-3 col-md-6" style="width:400px;">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-3"> <i class="fa fa-comments fa-5x"></i></div>
                  <div class="col-xs-9 text-left" style="width:400px;">
                    <div class="huge" style="font-size:24px;">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="50px"><span class="huge" style="font-size:24px;"><?php echo number_format($detalle_c['monto_meta'],2);?></span></td>
                          <td width="50px"><?php echo number_format($detall_cc['MONTO'],2);?></td>
                        </tr>
                      </table>
                    </div>
                    <div >META</div>
                  </div>
                </div>
              </div> </div>
          </div>
          </td>

  		<td>
          <div class="col-lg-3 col-md-6" style="width:250px;">
            <div class="panel panel-danger">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-3"> <i class="fa fa-comments fa-5x"></i></div>
                  <div class="col-xs-9 text-left" style="width:250px;">
                    <div class="huge" style="font-size:24px;">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="50px"><span class="huge" style="font-size:24px;"><?php echo $detalle_des_anul['POSIBLE_DES'];?></span></td>
                          <td width="50px"><?php echo $detalle_des_anul['POSIBLE_ANUL'];?></td>
                        </tr>
                        <tr>
                          <td style="font-size:12px;">POR DESISTIR </td>
                          <td style="font-size:12px;">POR ANULAR</td>
                        </tr>
                      </table>
                    </div>
                    <div ></div>
                  </div>
                </div>
              </div> </div>
          </div>
          </td>          
        </tr>
    </table> </td>
  </tr>
  <tr>
    <td><ul id="myTab" class="nav nav-tabs">
    	 <li class="active"><a href="#dash_board" data-toggle="tab" >DASHBOARD</a></li>    
   		<li ><a href="#mi_cartera" data-toggle="tab" >MI CARTERA</a> </li> 
         <?php if ($protect->getIfAccessPageById(181)){?>
         <li ><a href="#detalle_ingreso" data-toggle="tab" >DETALLE GESTION</a> </li> 
		<?php } ?>
         <?php if ($protect->getIfAccessPageById(182)){?>
         <li ><a href="#detalle_pendiente" data-toggle="tab" >COBRADO POR OFICIAL</a> </li> 
		<?php } ?>  
         <?php if ($protect->getIfAccessPageById(173)){?>
         <li ><a href="#detalle_cobro" data-toggle="tab" >COBRADO DETALLE</a> </li> 
		<?php }   ?>                
        
</ul></td>
  </tr>
  <tr>
    <td> <div id="myTabContent" class="tab-content">
    			 <div class="tab-pane fade in active" id="dash_board">
    			   <table width="99%" border="0" cellspacing="0" cellpadding="0" style="margin-left:5px;">
    			     <tr>
    			       <td rowspan="2" valign="top"><table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
    			         <tr>
    			           <td style="height:30px;"><strong>GESTIONES</strong></td>
  			           </tr>
    			         <tr>
    			           <td><table id="actividad" width="100%" border="1" style="font-size:9px;border-spacing:1px;" class="tb_detalle fsDivPage">
    			             <thead>
    			               <tr  style="background-color:#CCC;height:30px;" >
    			                 <td width="208" align="center"><strong>EMPRESA</strong></td>
    			                 <td width="276" align="center"><strong>RESPONSABLE</strong></td>
    			                 <td width="276" align="center"><strong>CEDULA</strong></td>
    			                 <td width="276" align="center"><strong>CLIENTE</strong></td>
    			                 <td width="276" align="center"><strong>CONTRATO</strong></td>
    			                 <td width="276" align="center"><strong>ESTATUS</strong></td>
    			                 <td width="276" align="center"><strong>DESCRIPCION</strong></td>
  			                 </tr>
  			               </thead>
    			             <tbody>
    			               <?php 
	//$gestiones= $cobros->getGestiones(UserAccess::getInstance()->getIDNIT());
	$gestiones=array();
	foreach($gestiones as $key =>$row){
 
?>
    			               <tr style="height:30px;">
    			                 <td align="center"><?php echo $row['EM_NOMBRE'];?></td>
    			                 <td align="center" ><?php  echo $row['nombre_oficial'];?></td>
    			                 <td align="center" ><?php echo $row['responsable'];?></td>
    			                 <td align="center"><span >
    			                   <?php  echo $row['nombre_cliente'];?>
  			                   </span></td>
    			                 <td align="center"><span ><?php echo $row['contrato'];?></span></td>
    			                 <td align="center"><span ><?php echo $row['estatus'];?></span></td>
    			                 <td align="center">&nbsp;</td>
  			                 </tr>
    			               <?php } ?>
  			               </tbody>
  			             </table></td>
  			           </tr>
    			         <tr>
    			           <td>&nbsp;</td>
  			           </tr>
    			         <tr>
    			           <td style="height:30px;"><strong>ACTIVIDADES</strong></td>
  			           </tr>
    			         <tr>
    			           <td><table id="gestiones" width="100%" border="1" style="font-size:9px;border-spacing:1px;" class="tb_detalle fsDivPage">
    			             <thead>
    			               <tr  style="background-color:#CCC;height:30px;" >
    			                 <td width="208" align="center"><strong>ACTIVIDAD</strong></td>
    			                 <td width="208" align="center"><strong>EMPRESA</strong></td>
    			                 <td width="276" align="center"><strong>RESPONSABLE</strong></td>
    			                 <td width="276" align="center"><strong>CEDULA</strong></td>
    			                 <td width="276" align="center"><strong>CLIENTE</strong></td>
    			                 <td width="276" align="center"><strong>CONTRATO</strong></td>
    			                 <td width="276" align="center"><strong>ESTATUS</strong></td>
    			                 <td width="276" align="center"><strong>DESCRIPCION</strong></td>
  			                 </tr>
  			               </thead>
    			             <tbody>
    			               <?php 
	//$gestiones= $cobros->getActividades(UserAccess::getInstance()->getIDNIT());
	$gestiones=array();
	//$gestiones=array();
	foreach($gestiones as $key =>$row){
 
?>
    			               <tr style="height:30px;">
    			                 <td align="center"><?php echo $row['actividad'];?></td>
    			                 <td align="center"><?php echo $row['EM_NOMBRE'];?></td>
    			                 <td align="center" ><?php  echo $row['nombre_oficial'];;?></td>
    			                 <td align="center" ><?php echo $row['responsable'];?></td>
    			                 <td align="center"><span >
    			                   <?php  echo $row['nombre_cliente'];?>
  			                   </span></td>
    			                 <td align="center"><span ><?php echo $row['contrato'];?></span></td>
    			                 <td align="center"><span ><?php echo $row['estatus'];?></span></td>
    			                 <td align="center">&nbsp;</td>
  			                 </tr>
    			               <?php } ?>
  			               </tbody>
  			             </table></td>
  			           </tr>
    			         <tr>
    			           <td>&nbsp;</td>
  			           </tr>
  			         </table></td>
    			       <td width="500" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
    			         <tr>
    			           <td><div id="chart1" style="height:150px;"></div></td>
  			           </tr>
    			         <tr>
    			           <td><div id="chart2" style="height:150px;"></div></td>
  			           </tr>
  			         </table></td>
  			       </tr>
    			     <tr>
    			       <td>&nbsp;</td>
  			       </tr>
    			     <tr>
    			       <td colspan="2">&nbsp;</td>
  			       </tr>
  			     </table>
    			 </div>
                <div class="tab-pane fade in" id="mi_cartera">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td><strong>BUSQUEDA</strong></td>
                    </tr>
                    <tr>
                      <td>
                      <form action="./?mod_cobros/delegate&dashboard#mi_cartera" method="post">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0"  >
                          <tr>
                            <td width="148">PERIODO DE</td>
                            <td width="115"><input  name="p_fecha_desde" type="text" class="form-control" style="width:110px; font-size:12px;cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;padding-right:10px;" id="p_fecha_desde"  value="<?php echo isset($_REQUEST['p_fecha_desde'])?$_REQUEST['p_fecha_desde']:''?>" /></td>
                            <td width="112"><input  name="p_fecha_hasta" type="text" class="form-control" style="font-size:12px;cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;width:110px;" id="p_fecha_hasta"  value="<?php echo isset($_REQUEST['p_fecha_hasta'])?$_REQUEST['p_fecha_hasta']:''?>"/></td>
                            <td width="86">&nbsp;
                              <input type="button" name="_filtrar_reporte" id="_filtrar_reporte" value="Filtrar" /></td>
                            <td width="713"><input type="button" name="dash_filtro_avanzado" id="dash_filtro_avanzado" value="Filtro Avanzado" disabled="disabled" style="display:none" />
                           <?php 
						  if ($protect->getIfAccessPageById(184) || $protect->getIfAccessPageById(199)){
						  ?>  
						  <div class="btn-group">
                                <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown">Exportar <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                  <li><a href="#" id="exp_to_excel" >a Excel</a></li>
                                  <li><a href="#" id="exp_to_pdf">a PDF</a></li> 
                                </ul>
                              </div>
						  
						  <?php } ?></td>
                          </tr>
                          <tr>
                            <td colspan="5">&nbsp;</td>
                          </tr>
                          <tr>
                            <td colspan="5"><table width="100%" style="font-size:9px;padding:10px;">
                              <tr>
                                <td width="100"><strong>ESTATUS</strong></td>
                                <td colspan="2"><select name="f_estatus" id="f_estatus" class="form-control"  style="width:200px;">
                                  <option value="">Toda la cartera</option>
                                  <?php  
	$SQL="SELECT id_status,descripcion FROM `sys_status` WHERE id_status IN (1,23,28,47,'13')";
	$rs=mysql_query($SQL);
	while($row=mysql_fetch_assoc($rs)){
		$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
                                  <option value="<?php echo $encriptID?>" <?php echo $id_status==$row['id_status']?'selected':''?>  ><?php echo $row['descripcion']?></option>
                                  <?php } ?>
                                </select></td>
                                <td><strong>FORMA DE PAGO:</strong></td>
                                <td><select name="f_forma_pago" id="f_forma_pago"  class="form-control"  style="width:200px;">
                                  <option value="">Seleccione</option>
                                  <?php 

$SQL="SELECT cmc_codigo,`cmc_descripcion` FROM `contratos_metodo_cobro` where cmc_estatus=1 ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	///$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
                                  <option value="<?php echo $row['cmc_codigo']?>" <?php echo $forpago==$row['cmc_codigo']?'selected':''?> ><?php echo $row['cmc_descripcion']?></option>
                                  <?php } ?>
                                </select></td>
                                <td>&nbsp;</td>
                                <td><strong>CUOTA</strong>:</td>
                                <td><select name="TIPO_CUOTA" id="TIPO_CUOTA"  class="form-control"  style="width:200px;">
                                  <option value="">Seleccione</option>
                                  <option value="PRIMERA_CUOTA">PRIMERA CUOTA</option>
                                  <option value="CUOTA">CUOTA</option>
                                </select></td>
                              </tr>
                              <tr>
                                <td><strong>POR SALDOS</strong></td>
                                <td colspan="2"><select name="por_saldos" id="por_saldos"  multiple="multiple"  class="form-control" style="width:450px;"> 
                                  <option value="saldo_0_30">SALDO DE 0 A 30</option>
                                  <option value="saldo_31_60">SALDO DE 31 A 60</option>
                                  <option value="saldo_61_90">SALDO DE 61 A 90</option>
                               <option value="saldo_91_120">SALDO DE 91 A 120</option>
                                   <option value="saldo_mas_120">SALDO MAS DE 120</option> 
                                </select></td>
                                <td><strong>FECHA PAGO:</strong></td>
                                <td><table width="100" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td><select name="fecha_p_condicion" id="fecha_p_condicion" class="form-control" style="width:130px;">
                                      <option value="">Seleccione</option>
                                      <option value="MQ">Mayor</option>
                                      <option value="MIGQ">Mayor igual</option>
                                      <option value="MNQ">Menor</option>
                                      <option value="MNIGQ">Menor igual</option>
                                      <option value="IQ">Igual</option>
                                    </select></td>
                                    <td><input  name="f_fecha_pago" type="text" class="form-control" style="cursor:pointer;width:80px;padding-right:10px;" id="f_fecha_pago" value="<?php echo isset($_REQUEST['fecha_contrato'])?$_REQUEST['fecha_contrato']:''?>"  /></td>
                                  </tr>
                                </table></td>
                                <td>&nbsp;</td>
                                <td><?php 
						  if ($protect->getIfAccessPageById(184)){
						  ?>
                                  <strong>OFICIAL</strong>:
                                  <?php } ?></td>
                                <td><?php 
						  if ($protect->getIfAccessPageById(184)){
						  ?>
                                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td><select name="_oficial" multiple="multiple" id="_oficial" style="width:300px">
                                        <?php 
						$oficical=$cobros->getOficialesC();
						foreach($oficical as $key=>$row_data){	
					?>
                                        <option <?
                            	if (isset($_cajas[$row['ID_CAJA']])){
									echo 'selected="selected"';
								}
                            	?>  value="<?php echo System::getInstance()->Encrypt($row_data['id_nit']); ?>"><?php echo $row_data['nombre_oficial'];?></option>
                                        <?php } ?>
                                      </select></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                  </table>
                                  <?php } ?></td>
                              </tr>
                              <tr>
                                <td><strong>COMPROMISO</strong></td>
                                <td width="50"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td><select name="por_compromiso" id="por_compromiso" class="form-control" style="width:130px;">
                                      <option value="">Seleccione</option>
                                      <option value="MQ">Mayor</option>
                                      <option value="MIGQ">Mayor igual</option>
                                      <option value="MNQ">Menor</option>
                                      <option value="MNIGQ">Menor igual</option>
                                      <option value="IQ">Igual</option>
                                    </select></td>
                                    <td><input name="monto_compromiso" type="text" class="form-control" id="monto_compromiso" style="width:100px;" value="0"/></td>
                                  </tr>
                                </table></td>
                                <td width="50">&nbsp;</td>
                                <td><strong>CUOTA CONTRATOS</strong>:</td>
                                <td><table width="100" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td><select name="contrato_condicion" id="contrato_condicion" class="form-control" style="width:130px;">
                                      <option value="">Seleccione</option>
                                      <option value="MQ">Mayor</option>
                                      <option value="MIGQ">Mayor igual</option>
                                      <option value="MNQ">Menor</option>
                                      <option value="MNIGQ">Menor igual</option>
                                      <option value="IQ">Igual</option>
                                    </select></td>
                                    <td><input  name="contrato_cuota" type="text" class="form-control" style="cursor:pointer;width:80px;padding-right:10px;" id="contrato_cuota" value="<?php echo isset($_REQUEST['contrato_cuota'])?$_REQUEST['contrato_cuota']:''?>"  /></td>
                                  </tr>
                                </table></td>
                                <td>&nbsp;</td>
                                <td><?php 
						  if ($protect->getIfAccessPageById(199)){
						  ?>
                                  <strong>MOTORIZADO</strong>:
                                  <?php } ?></td>
                                <td><?php 
						  if ($protect->getIfAccessPageById(199)){
						  ?>
                                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td><select name="_motorizado" multiple="multiple" id="_motorizado" style="width:300px">
                                        <?php 
						$oficical=$cobros->getMotorizadoC();
						foreach($oficical as $key=>$row_data){	
					?>
                                        <option <?
                            	if (isset($_cajas[$row['ID_CAJA']])){
									echo 'selected="selected"';
								}
                            	?>  value="<?php echo System::getInstance()->Encrypt($row_data['id_nit']); ?>"><?php echo $row_data['nombre_oficial'];?></option>
                                        <?php } ?>
                                      </select></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                  </table>
                                  <?php } ?></td>
                              </tr>
                              <tr>
                                <td><strong>PENDIENTE DE PAGO</strong></td>
                                <td><input name="pendiente_de_pago" type="checkbox" id="pendiente_de_pago" value="1" /></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td><?php 
						  if ($protect->getIfAccessPageById(184)){
						  ?>
                                  <strong>GERENTE</strong>:
                                <?php } ?></td>
                                <td><?php 
						  if ($protect->getIfAccessPageById(184)){
						  ?>
                                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td><select name="_gerente" multiple="multiple" id="_gerente" style="width:300px">
                                        <?php 
						$oficical=$cobros->getGerentes();
						foreach($oficical as $key=>$row_data){	
					?>
                                        <option <?
                            	if (isset($_cajas[$row['ID_CAJA']])){
									echo 'selected="selected"';
								}
                            	?>  value="<?php echo System::getInstance()->Encrypt($row_data['codigo_gerente']); ?>"><?php echo $row_data['nombre_gerente'];?></option>
                                        <?php } ?>
                                      </select></td>
                                      <td>&nbsp;</td>
                                    </tr>
                                  </table>
                                <?php } ?></td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                            </table></td>
                          </tr>
                        </table>
                      </form></td>
                    </tr>
                    <tr>
                      <td align="right" tyle="font-size:25px;"><strong>CARTERA DE COBRO ASIGNADA</strong></td>
                    </tr>
                    <tr>
                      <td id="cartera_asignada"><?php // include("cartera_cobro_asignada.php");?>&nbsp;</td>
                    </tr>
                  </table>
                </div>
                
                 <div class="tab-pane fade in" id="detalle_ingreso"> 
                 <?php
                 if ($protect->getIfAccessPageById(173)){
				 ?>
                   <table width="100%" border="0" cellspacing="0" cellpadding="0">
                     <tr>
                       <td align="center">&nbsp;</td>
                     </tr>
                     <tr>
                       <td align="center"><table width="500" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:9px;">
                         <tr>
                           <td width="98">PERIODO DE</td>
                           <td><input  name="p_fdesde" type="text" class="form-control fecha" style="width:110px; font-size:12px;cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;padding-right:10px;" id="p_fdesde"  value="<?php echo isset($_REQUEST['p_fecha_desde'])?$_REQUEST['p_fecha_desde']:''?>" /></td>
                           <td><input  name="p_fhasta" type="text" class="form-control fecha" style="font-size:12px;cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;width:110px;" id="p_fhasta"  value="<?php echo isset($_REQUEST['p_fecha_hasta'])?$_REQUEST['p_fecha_hasta']:''?>"/></td>
                           <td>&nbsp;
                             <input type="submit" name="_filter_call" id="_filter_call" value="Filtrar" /></td>
                         </tr>
                       </table></td>
                     </tr>
                     <tr>
                       <td align="center">&nbsp;</td>
                     </tr>
                     <tr>
                         <td id="detalle_requerimiento"><?php  //include("detalle_requerimiento.php");?></td>
                     </tr>
                   </table>
                   <?php } ?>
                 </div>
				<div class="tab-pane fade in" id="detalle_pendiente"> 
                 <?php
                 if ($protect->getIfAccessPageById(173)){
				 ?>
                   <table width="100%" border="0" cellspacing="0" cellpadding="0">
                     <tr>
                       <td align="center">&nbsp;</td>
                     </tr>
                     <tr>
                       <td align="center"><table width="500" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:9px;">
                         <tr>
                           <td width="98">PERIODO DE</td>
                           <td><input  name="pp_fdesde" type="text" class="form-control fecha" style="width:110px; font-size:12px;cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;padding-right:10px;" id="pp_fdesde"  value="<?php echo isset($_REQUEST['p_fecha_desde'])?$_REQUEST['p_fecha_desde']:''?>" /></td>
                           <td><input  name="ppp_fhasta" type="text" class="form-control fecha" style="font-size:12px;cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;width:110px;" id="pp_fhasta"  value="<?php echo isset($_REQUEST['p_fecha_hasta'])?$_REQUEST['p_fecha_hasta']:''?>"/></td>
                           <td>&nbsp;
                             <input type="submit" name="_cXOficial" id="_cXOficial" value="Filtrar" /></td>
                         </tr>
                       </table></td>
                     </tr>
                     <tr>
                       <td align="center">&nbsp;</td>
                     </tr>
                     <tr>
                         <td align="center" id="detalle_pendiente_x_cobrar"><?php include("cobros_por_oficial.php");?></td>
                     </tr>
                   </table>
                   <?php } ?>
                 </div>                   
                 <div class="tab-pane fade in" id="detalle_cobro"> 
                 <?php
                 if ($protect->getIfAccessPageById(173)){
				 ?>
                   <table width="100%" border="0" cellspacing="0" cellpadding="0">
                     <tr>
                       <td align="center">&nbsp;</td>
                     </tr>
                     <tr>
                       <td align="center"><table width="500" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:12px;">
                         <tr>
                           <td width="98">PERIODO DE</td>
                           <td><input  name="ppp_fdesde" type="text" class="form-control fecha" style="width:110px; font-size:12px;cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;padding-right:10px;" id="ppp_fdesde"  value="<?php echo isset($_REQUEST['p_fecha_desde'])?$_REQUEST['p_fecha_desde']:''?>" /></td>
                           <td><input  name="ppp_fhasta" type="text" class="form-control fecha" style="font-size:12px;cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;width:110px;" id="ppp_fhasta"  value="<?php echo isset($_REQUEST['p_fecha_hasta'])?$_REQUEST['p_fecha_hasta']:''?>"/></td>
                           <td><input type="submit" name="_cXOficialMoto" id="_cXOficialMoto" value="Filtrar" /></td>
                           <td align="center"><a href="#" class="exportar_excel" >Exportar a Excel</a></td>
                         </tr>
                       </table></td>
                     </tr>
                     <tr>
                       <td align="center">&nbsp;</td>
                     </tr>
                     <tr>
                         <td align="center" id="detalle_cobros_oficial_moto"><?php include("report/detalle_cobro.php");?></td>
                     </tr>
                   </table>
                   <?php } ?>
                 </div>  
<div class="tab-pane fade in" id="detalle_ingreso"> 
                 <?php
                 if ($protect->getIfAccessPageById(173)){
				 ?>
                   <table width="100%" border="0" cellspacing="0" cellpadding="0">
                     <tr>
                       <td align="center">&nbsp;</td>
                     </tr>
                     <tr>
                       <td align="center"><table width="500" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:9px;">
                         <tr>
                           <td width="98">PERIODO DE</td>
                           <td><input  name="p_fdesde" type="text" class="form-control fecha" style="width:110px; font-size:12px;cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;padding-right:10px;" id="p_fdesde"  value="<?php echo isset($_REQUEST['p_fecha_desde'])?$_REQUEST['p_fecha_desde']:''?>" /></td>
                           <td><input  name="p_fhasta" type="text" class="form-control fecha" style="font-size:12px;cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;width:110px;" id="p_fhasta"  value="<?php echo isset($_REQUEST['p_fecha_hasta'])?$_REQUEST['p_fecha_hasta']:''?>"/></td>
                           <td>&nbsp;
                             <input type="submit" name="_filter_call" id="_filter_call" value="Filtrar" /></td>
                         </tr>
                       </table></td>
                     </tr>
                     <tr>
                       <td align="center">&nbsp;</td>
                     </tr>
                     <tr>
                         <td id="detalle_requerimiento"><?php  //include("detalle_requerimiento.php");?></td>
                     </tr>
                   </table>
                   <?php } ?>
                 </div>                                
    </div>     </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<div id="content_dialog" ></div>

<?php //SystemHtml::getInstance()->addModule("footer");?>