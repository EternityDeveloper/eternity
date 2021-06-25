<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

if (!isset($_REQUEST['id'])){
	exit;	
} 

$cdecode=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
if (!isset($cdecode->ID_CIERRE)){
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
	
SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");
SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.css");
 
 
/*Cargo el Header*/
SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");
/* cargo el modulo de top menu*/
SystemHtml::getInstance()->addModule("main/topmenu");



SystemHtml::getInstance()->includeClass("caja","Cierre"); 
SystemHtml::getInstance()->includeClass("caja","Caja"); 


$cierre= new Cierre($protect->getDBLINK()); 


$data=$cierre->getDetalleCierre($cdecode);	 
 
?>
<script>
 
var _op;

$(function(){ 				
  	_op= new CierreCaja('content_dialog'); 
}); 
 
</script>
<style>
#sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
#sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
#sortable li span { position: absolute; margin-left: -1.3em; }

.sort{
	text-decoration:none;
	list-style:none;
	width:98%; 
	padding:18px;
	background:#CCC;
}
.sort li{
	display:inline;
	width:50px;
	height:10px;
	margin:5px;
	padding:5px;
}

.sort_v{
	text-decoration:none;
	list-style:none;
	width:200px;
	height:300px;
	padding:18px;
	background:#CCC;
}
.sort_v li{
	width:100px;
	height:10px;
	margin:5px;
	padding:5px;
} 
.ui-state-highlight { height: 1.5em; line-height: 1.2em; }

.content_item{
	border:#999 solid 1px;
	border-radius:3px;
	padding:2px;
	background:#D7D7D7;
}
.pvtTriangle{
	display:inline-block;
	cursor:pointer;
	background:url(../cierrre/images/sort_desc.png);
	background-position:3px -6px;
	background-repeat:no-repeat;
	width:19px; 
	height:19px; 
}

h2{
	color:#FFF;	
}
  
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
 h2 { 
	padding:0.5em 0 0.5em 20px; 
	font-size:12pt; 
	font-family:Georgia; 
	color:white; 
	background:silver; 
	text-shadow:1px 1px 2px gray; 
	clear:both; 
	-moz-border-radius:2px; 
	border-radius:2px; 
	-webkit-border-radius:2px;
	background:#65BB56; 
	margin-bottom:5px;
}

</style>

<div  id="caja_view" style="width:800px;">
  <h2>CIERRES</h2>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
  <tr>
    <td align="center"><table width="95%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="33%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="70"><strong>CAJA</strong></td>
            <td><?php echo $cdecode->DESCRIPCION_CAJA;?>&nbsp;</td>
          </tr>
        </table></td>
        <td width="34%"><table width="200" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="130"><strong>TIPO DE CIERRE</strong></td>
            <td><?php  
					switch($cdecode->PARCIAL_TOTAL){
						case "T":
							echo "TOTAL";
						break;	
						case "P":
							echo "PARCIAL";
						break;					
					}
			?>&nbsp;</td>
          </tr>
        </table></td>
        <td width="33%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="80"><strong>FECHA DE CIERRE</strong></td>
            <td width="50"><?php  echo $cdecode->FECHA;?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
    </tr>
  <tr>
    <td id="detalle_search" style="padding-top:10px;"><table width="800" border="1" style="border-spacing:10px;" class="tb_detalle fsDivPage">
      <tr  style="background:#CCC" >
        <td width="215" height="36"><strong>DOCUMENTO</strong></td>
        <td width="212" align="right"><strong>FORMA DE PAGO</strong></td>
        <td width="164" align="right"><strong>INGRESOS</strong></td>
        <td width="181" align="right"><strong>EGRESOS</strong></td>
      </tr>
<?php
 
$total_monto=0;
	foreach($data as $key =>$row){ 
 		
?>
      <tr>
        <td><?php echo $key; ?></td>
        <td align="right"></td>
        <td align="right"></td>
        <td align="right"></td>
     </tr>
	<?php 
		$acumulado_ingreso=0;
		foreach($row as $keys =>$data){
			$acumulado_ingreso=$acumulado_ingreso+$data['MONTO'];
			$total_monto=$total_monto+$data['MONTO'];
		?>     
          <tr>
            <td align="center"><?php echo $data['TOTALES']?></td>
            <td align="right"><?php echo $data['forma_pago'];?></td>
            <td align="right"><?php echo  number_format($data['MONTO'],2)?></td>
            <td align="right"><?php  ?></td>
         </tr>
    <?php } ?>
          <tr>
            <td> </td>
            <td align="right">&nbsp;</td>
            <td align="right"><strong><?php echo number_format($acumulado_ingreso,2);?></strong></td>
            <td align="right"><strong>0.00</strong></td>
         </tr>       
<?php } ?>
      <tr >
        <td>&nbsp; </td>
        <td align="right">CIERRE PARCIAL</td>
        <td align="right"><strong><?php echo number_format($monto_cierre_parcial,2); ?></strong></td>
        <td align="right"><strong><?php echo number_format($monto_cierre_parcial_egresos,2); ?></strong></td>
      </tr>
      <tr >
        <td>&nbsp;</td>
        <td align="right">TOTALES</td>
        <td align="right"><strong><?php echo number_format($monto_cierre_parcial+$total_monto,2); ?></strong></td>
        <td align="right"><strong>0.00</strong></td>
      </tr>
      <tr >
        <td>&nbsp;</td>
        <td align="right">GRAN TOTAL</td>
        <td align="right"><strong>0.00</strong></td>
        <td align="right"><strong>0.00</strong></td>
      </tr>
    </table></td>
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