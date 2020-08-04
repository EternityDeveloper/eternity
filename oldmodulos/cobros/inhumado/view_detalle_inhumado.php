<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

/*
if (!isset($_REQUEST['id_nit'])){
	exit;
}
$id_init=System::getInstance()->Decrypt($_REQUEST['id_nit']);
$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));
 
if ((!isset($contrato->serie_contrato)) || (!isset($contrato->no_contrato))){
	exit;
}
if (trim($id_init)==""){
	exit;
}
SystemHtml::getInstance()->includeClass("client","PersonalData");
$person= new PersonalData($protect->getDBLink(),$_REQUEST);
if (!$person->existClient($id_init)){
	exit;	
}

SystemHtml::getInstance()->includeClass("cobros","Servicios"); 
 
$srv=new Servicios($protect->getDBLink()); 
$srv->clearServicioSession();

$data=$person->getClientData($id_init);*/
//print_r($data);
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:0px;padding:0px">
  <tr>
    <td align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="450"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="30" colspan="3" align="center" style="background: #999; font-weight: bold;">INFORMACION DEL DIFUNTO</td>
          </tr>
          <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle fsDivPage">
              <tr>
                <td width="200"><strong>NOMBRE Y APELLIDO:  </strong></td>
                <td><input name="inh_nombre_completo"  type="text" disabled="disabled" class="form-control" id="inh_nombre_completo" value="<?php echo $data['nombre_completo']?>" /></td>
              </tr>
              <tr>
                <td><strong>NO. DOCUMENTO</strong></td>
                <td><input name="inh_no_documento" type="text" disabled="disabled" class="form-control" id="inh_no_documento"  value="<?php echo $data['id_nit']?>" /></td>
              </tr>
            </table></td>
          </tr>
          </table></td>
      </tr>
      <tr>
        <td height="40" align="center"><button type="button" class="orangeButton" id="agregar_inhumado">Buscar</button></td>
      </tr>
      <tr>
        <td><ul id="myTab" class="nav nav-tabs">
          <li class="active"><a href="#zona" data-toggle="tab"  style="display:none">SERVICIO</a></li>
          <li> <a href="#zone_mapa" id="z_map_tb"  style="display:none" data-toggle="tab">MAPA</a></li>
        </ul></td>
      </tr>
      <tr>
        <td><div id="myTabContent" class="tab-content">
          <div class="tab-pane fade in active" id="zona"><span style="font-size:12px;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td>&nbsp;</td>
              </tr>
 
              <tr>
                <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="tb_detalle"  >
                          <tr>
                            <td valign="middle"><strong>Religi<span style="font-size: 12px">ó</span>n<span style="font-size: 12px">: </span></strong></td>
                            <td><select name="inhu_religion" id="inhu_religion" class="form-control required"  >
                              <option value="">Seleccione</option>
                              <?php 
		
		$SQL="SELECT `id_religion`,`descripcion` FROM `sys_religiones` WHERE `status`='A'";
		//echo $SQL;
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$encriptID=System::getInstance()->Encrypt($row['id_religion']);
		?>
                              <option value="<?php echo $encriptID?>" <?php echo $data_address['id_religion']==$row['id_religion']?'selected="selected"':''?>><?php echo $row['descripcion']?></option>
                              <?php } ?>
                            </select></td>
                          </tr>
                          <tr>
                            <td width="180" valign="middle"><strong>Causa del fallecimiento:  </strong></td>
                            <td><input name="causa_fallecimiento"  type="text" class="form-control required" id="causa_fallecimiento"></td>
                            </tr>
                          <tr>
                            <td valign="middle"><strong>Lugar de defunción:  </strong></td>
                            <td><input name="lugar_defuncion"  type="text" class="form-control" id="lugar_defuncion"></td>
                          </tr>
                        </table></td>
                        </tr>
                      <tr>
                        <td width="400"><table width="400" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle fsDivPage">
                          <tr>
                            <td colspan="2" valign="middle"><h2 style="color:#FFF;margin:0px;">INFO. DIFUNTO</h2></td>
                            </tr>
                          <tr>
                            <td width="170" valign="middle"><strong>Medico que firma el acta:  </strong></td>
                            <td><input name="medico"  type="text" class="form-control" id="medico"></td>
                          </tr>
                          <tr>
                            <td valign="middle"><strong>Fecha de defunción:  </strong></td>
                            <td><input name="fecha_defuncion"  type="text" class="form-control date_pick" id="fecha_defuncion"  style="font-size:12px; cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;width:130px;padding-right:10px;"></td>
                          </tr>
                          <tr>
                            <td valign="middle"><strong>No. Acta de defunción:</strong></td>
                            <td height="43"><input name="no_acta_defuncion"  type="text" class="form-control" id="no_acta_defuncion" /></td>
                          </tr>
                          <tr>
                            <td valign="middle"><strong>Ciudad</strong></td>
                            <td><select name="cuidad_id" id="cuidad_id" class="form-control required"  >
                              <option value="">Seleccione</option>
                              <?php 
		
		$SQL="SELECT * FROM `sys_ciudad` WHERE status_2='A'";
		//echo $SQL;
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$encriptID=System::getInstance()->getEncrypt()->encrypt($row['idciudad'],$protect->getSessionID());
		?>
                              <option value="<?php echo $encriptID?>"  <?php echo $data_address['idciudad']==$row['idciudad']?'selected="selected"':''?>><?php echo $row['Descripcion']?></option>
                              <?php } ?>
                            </select></td>
                          </tr>
                        </table></td>
                        <td valign="top"><table width="400" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle fsDivPage">
                          <tr>
                            <td colspan="2" valign="middle"><h2 style="color:#FFF;margin:0px;">SERVICIO</h2></td>
                            </tr>
                          <tr>
                            <td valign="middle"><strong>Fecha Inicio:  </strong></td>
                            <td><input name="serv_fecha_inicio"  type="text" class="form-control date_pick" id="serv_fecha_inicio"  style="font-size:12px; cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;width:130px;padding-right:10px;" /></td>
                          </tr>
                          <tr>
                            <td valign="middle"><strong>Fecha Fin:  </strong></td>
                            <td><input name="serv_fecha_fin"  type="text" class="form-control date_pick" id="serv_fecha_fin"  style="font-size:12px; cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;width:130px;padding-right:10px;" /></td>
                          </tr>
                          <tr>
                            <td width="170" valign="middle"><strong>Hora Inicio:  </strong></td>
                            <td><input name="inicio_servicio"  type="text" class="form-control" id="inicio_servicio"></td>
                          </tr>
                          <tr>
                            <td valign="middle"><strong>Hora Fin:  </strong></td>
                            <td><input name="fin_servicio"  type="text" class="form-control" id="fin_servicio"></td>
                          </tr>
                        </table></td>
                      </tr>
                      <tr>
                        <td colspan="2" align="center"><h2 style="color:#FFF">OTROS DATOS</h2></td>
                        </tr>
                      <tr>
                        <td><table width="400" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle fsDivPage">
                          <tr>
                            <td width="170" valign="middle"><strong>Preparado por:</strong></td>
                            <td><input  name="txt_preparado_por" type="hidden" class="required" id="txt_preparado_por" style="width:250px;" /></td>
                          </tr>
                          <tr>
                            <td valign="middle"><strong>Tipo de Cofre</strong></td>
                            <td height="43"><select name="inhu_tipo_cofre" id="inhu_tipo_cofre" class="form-control required"  >
                              <option value=""></option>
                              <?php 
		
		$SQL="SELECT id,descripcion FROM `sp_tipo_cofre` WHERE estatus=1";
		//echo $SQL;
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$encriptID=System::getInstance()->Encrypt($row['id']);
		?>
                              <option value="<?php echo $encriptID?>"><?php echo $row['descripcion']?></option>
                              <?php } ?>
                            </select></td>
                          </tr>
                        </table></td>
                        <td valign="top"><table width="400" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle fsDivPage">
                          <tr>
                            <td width="170" valign="middle"><strong>Iglesia/Cementerio:</strong></td>
                            <td><input  name="txt_cementerio" type="hidden" class="required" id="txt_cementerio" style="width:250px;" /></td>
                          </tr>
                          <tr>
                            <td valign="middle"><strong>Funeraria:</strong></td>
                            <td><input  name="txt_funeraria" type="hidden" class="required" id="txt_funeraria" style="width:250px;" /></td>
                          </tr>
                        </table></td>
                      </tr>
                    </table></td>
                  </tr>
              
                </table></td>
              </tr>
            </table>
          </span></div>
          <div class="tab-pane" id="zone_mapa">
            <div id="main_map_zona" style="height:500px;width:100%"></div>
          </div>
        </div></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td valign="top">&nbsp;</td>
  </tr>
</table>
 