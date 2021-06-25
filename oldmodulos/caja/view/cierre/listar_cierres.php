<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	




if (isset($_REQUEST['view_detalle_cierre'])){
	include("cierre_caja.php");
	exit;	
}
 

if (isset($_REQUEST['view_detalle'])){
	include("view_cierre_detalle.php");
	exit;	
}
  
 
SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js"); 
SystemHtml::getInstance()->addTagScript("script/Class.js");  
 
SystemHtml::getInstance()->addTagScriptByModule("class.CierreCaja.js");
 

SystemHtml::getInstance()->addTagScript("script/jquery.jstree.js");
SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");

SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");
 
SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
	
SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");
SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");
SystemHtml::getInstance()->addTagStyle("css/select2-bootstrap.css");
 
 
/*Cargo el Header*/
SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");
/* cargo el modulo de top menu*/
SystemHtml::getInstance()->addModule("main/topmenu");
 
 
SystemHtml::getInstance()->includeClass("caja","Cierre"); 
$cierre= new Cierre($protect->getDBLINK());   
$valid=$cierre->validateCierres();

	
 
$day_from 	=	isset($_REQUEST['day_from'])?date("Y-m-d", strtotime($_REQUEST['day_from'])):date("d-m-Y");
$day_to		=	isset($_REQUEST['day_to'])?date("Y-m-d", strtotime($_REQUEST['day_to'])):date("d-m-Y");
/*SI ES IGUAL A 0 ENTONCES TIENE CIERRE PENDIENTE*/
if ($valid['TOTAL']==0){
	//echo '<h3><span class="label label-danger">El cierre total de la caja ('.$valid['DESCRIPCION_CAJA'].') dia '. $valid['FECHA'].' no fue relizado.</span></h3>';
}
$_cajas=array();
if (isset($_REQUEST['oficial_hd'])){
	$sp=explode(",",$_REQUEST['oficial_hd']);
	foreach($sp as $key =>$val){
		$valor=System::getInstance()->Decrypt($val);
		$_cajas[$valor]=$valor;
	}	
}


$filter=array("day_from"=>$day_from,"day_to"=>$day_to,"action"=>"filter","cajas"=>$_cajas,"fc"=>$_REQUEST['_filtro'],"forma_pago"=>$_REQUEST['_forma_pago']);

?>
<script>
 
var _op; 
$(function(){ 				
  	_op= new CierreCaja('content_dialog'); 
	_op.doCierreView('<?php echo $valid['TOTAL'];?>','<?php echo $valid['FECHA'];?>');
}); 
 
</script>
<style>
.fpago_evnt{}
.btsavedetalle_fpago{}
.listado_recibo{}
</style>
<div  id="caja_view" style="width:90%;">
  <h2 style="margin:0px;">CIERRES</h2>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
  <tr>
    <td><table width="200%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><table width="100%" border="0" class="header_day">
          <tr>
            <td colspan="2" align="center">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2" align="center"><form method="post" >
              <table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="20"><strong>FECHA</strong></td>
                  <td width="120"><input type="text" name="day_from" id="day_from" class="form-control textfield"   value="<?php echo date("d-m-Y", strtotime($day_from))?>" /></td>
                  <td width="100"><strong>HASTA</strong></td>
                  <td width="100"><input type="text" name="day_to" id="day_to" class="form-control textfield"   value="<?php echo date("d-m-Y", strtotime($day_to))?>" /></td>
                  <td width="100"><strong>OFICIAL</strong></td>
                  <td>
                     <select multiple id="oficial_cierre" style="width:300px">
                    <?php 
						$SQL="SELECT * FROM `caja`";
						$rs=mysql_query($SQL);
						$data=array();
						while($row=@mysql_fetch_assoc($rs)){	
					?>
                      		<option <?
                            	if (isset($_cajas[$row['ID_CAJA']])){
									echo 'selected="selected"';
								}
                            	?>  value="<?php echo System::getInstance()->Encrypt($row['ID_CAJA']); ?>"><?php echo $row['DESCRIPCION_CAJA'];?></option>
                     <?php } ?> 
                    </select>
                     <input type="hidden" name="oficial_hd" id="oficial_hd" value="<?php echo isset($_REQUEST['oficial_hd'])?$_REQUEST['oficial_hd']:''?>" /></td>
                  <td><button type="submit" class="greenButton" id="filtrar">Filtrar</button></td>
                </tr>
              </table>
              <select name="_filtro" id="_filtro">
                <option value="F" <?php echo $_REQUEST['_filtro']=="F"?'selected="selected"':'';?> >F</option>
                <option value="N" <?php echo $_REQUEST['_filtro']=="N"?'selected="selected"':'';?> >N</option>
                <option value="all" <?php echo $_REQUEST['_filtro']=="all"?'selected="selected"':'';?>>Todos</option>
              </select>
              <select name="_forma_pago" id="_forma_pago">
             	 <option value="" >Seleccione..</option>
              	<?php 
				$SQL="SELECT * FROM `formas_pago`";
				$rs=mysql_query($SQL);
				while($row=mysql_fetch_assoc($rs)){
				?>
                <option value="<?php echo $row['forpago']?>" <?php echo $_REQUEST['_forma_pago']==$row['forpago']?'selected="selected"':'';?> ><?php echo $row['descripcion_pago']?></option>
                
                <?php } ?>
              </select>
            </form></td>
          </tr>
          <tr>
            <td colspan="2" align="center"><label for="select"></label></td>
          </tr>
    
        </table></td>
      </tr>
      <tr>
        <td><button type="button" id="procesar_cierre" class="btn btn-primary">PROCESAR CIERRE</button></td>
        </tr>
      <tr>
        <td height="25" align="center">&nbsp;</td>
        </tr>
    </table></td>
    </tr>
  <tr>
    <td>
  <ul id="contract-tb" class="nav nav-tabs">
   <li class="active"><a href="#d_ingreso" data-toggle="tab" >DETALLE DE INGRESO</a>  </li> 
   <li><a href="#resumen_ingreso" data-toggle="tab">RESUMEN DE INGRESO</a></li>  
   <li><a href="#resumen_cierre" data-toggle="tab">RESUMEN DE CIERRE</a></li>     
</ul>    
<div  class="tab-content">
	<div class="tab-pane fade in active" id="d_ingreso">    
    <?php include("listado_cierre_detalle.php");?>
    </div>
	<div class="tab-pane" id="resumen_ingreso"><?php include("resumen_ingreso.php")?></div>    
     
	<div class="tab-pane" id="resumen_cierre"><?php include("resumen_cierre.php");?>
    </div>        
 </div>
    </td>
  </tr>
  <tr>
    <td id="detalle_search"  >&nbsp;</td>
    </tr>
  <tr>
    <td id="detalle_search2" style="padding-top:10px;">&nbsp;</td>
  </tr>
  <tr>
    <td align="center" id="detalle_search4" >&nbsp;</td>
  </tr>
  </table>



</div>
<div id="content_dialog" ></div>