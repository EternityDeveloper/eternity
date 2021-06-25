<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	



if (!$protect->getIfAccessPageById(101)){exit;}


if (isset($_REQUEST['doPrint']) && isset($_REQUEST['id'])){
	$id=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
	$rt=STCSession::GI()->getSubmit("doPrint");
	if (!is_array($rt)){
		$rt=array();
	}
	$cmd=$_REQUEST['cmd'];
	
	if ($cmd=="add"){
		$rt[$id->id]=$id;
		STCSession::GI()->setSubmit("doPrint",$rt);					
	}
	if ($cmd=="rem"){
		unset($rt[$id->id]);
		STCSession::GI()->setSubmit("doPrint",$rt);					
	}		
	exit;	
} 

	//STCSession::GI()->setSubmit("doPrint",array()); 
	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
	SystemHtml::getInstance()->addTagScript("script/jquery.jqplot.min.js"); 

	SystemHtml::getInstance()->addTagScript("script/Class.js");  
	
	SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
	SystemHtml::getInstance()->addTagScript("script/jquery.ui.combogrid-1.6.3.js");
	
	SystemHtml::getInstance()->addTagScriptByModule("class.Dashboard.js"); 


	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
	SystemHtml::getInstance()->addTagStyle("script/jquery.jqplot.min.css");
	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");


	SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
	SystemHtml::getInstance()->includeClass("contratos","Contratos");   
		  
	$cobros= new Cobros($protect->getDBLINK()); 
		
	STCSession::GI()->setSubmit("doPrint",array());
	
	
	if (isset($_REQUEST['p_fecha_desde'])){
		$_SESSION['p_fecha_desde']=$_REQUEST['p_fecha_desde'];
		$_SESSION['p_fecha_hasta']=$_REQUEST['p_fecha_hasta'];
	}else{
		$_REQUEST['p_fecha_desde']=$_SESSION['p_fecha_desde'];
		$_REQUEST['p_fecha_hasta']=$_SESSION['p_fecha_hasta'];
	}
	
?>
<script> 

var _dashboard= new DashBoard("content_dialog");
$(document).ready(function(){
	_dashboard.doRequerimiento();
});
 
</script>
<form action="?mod_cobros/delegate&requerimiento" method="post" onsubmit="return false;">
<table width="99%" border="0" cellspacing="0" cellpadding="0" style="margin-left:5px;">
  <tr>
    <td width="500" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><strong>BUSQUEDA</strong></td>
        </tr>
      <tr>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size:9px;">
          <tr>
            <td width="82">PERIODO DE</td>
            <td width="144"><input  name="p_fecha_desde" type="text" class="filter_ form-control" style="cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;" id="p_fecha_desde" readonly="readonly"  value="<?php echo isset($_REQUEST['p_fecha_desde'])?$_REQUEST['p_fecha_desde']:''?>" /></td>
            <td width="120"><input  name="p_fecha_hasta" type="text" class="filter_ form-control" style="font-size:12px;cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;" id="p_fecha_hasta" readonly="readonly"  value="<?php echo isset($_REQUEST['p_fecha_hasta'])?$_REQUEST['p_fecha_hasta']:''?>"/></td>
            <td width="59">&nbsp;
              <input type="submit" name="_filtrar_buttom" class="form-control" id="_filtrar_buttom" value="Filtrar" /></td>
            <td width="29">&nbsp;</td>
            <td width="275"><input type="button" name="Imprimir" class="form-control" style="width:80px" id="Imprimir" value="Imprimir" /></td>
            <td width="268"><span style="font-size:20px;"><strong>REQUERIMIENTOS DE COBROS</strong></span></td>
          </tr>
          <tr>
            <td><?php 
						  if ($protect->getIfAccessPageById(184)){
						  ?>OFICIAL<?php } ?></td>
            <td colspan="5"><?php 
						  if ($protect->getIfAccessPageById(184)){
						  ?><select name="_oficial" multiple="multiple" id="_oficial" style="width:300px">
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
            </select><?php } ?></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>PENDIENTE POR COBRAR</td>
            <td colspan="5"><input name="pendiente_x_cobrar" type="checkbox" id="pendiente_x_cobrar" value="1" /></td>
            <td>&nbsp;</td>
          </tr>
        </table></td>
        </tr>
 
    </table></td>
  </tr>
  <tr>
    <td valign="top" id="requerimiento_cobros"></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
</form>
 <div id="content_dialog" ></div>

<?php //SystemHtml::getInstance()->addModule("footer");?>