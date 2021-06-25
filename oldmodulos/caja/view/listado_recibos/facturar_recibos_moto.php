<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	



//if (!$protect->getIfAccessPageById(101)){exit;}


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

	SystemHtml::getInstance()->addTagScriptByModule("class.PagoComponent.js","caja");  
	SystemHtml::getInstance()->addTagScriptByModule("class.COperacion.js","caja"); 
 	SystemHtml::getInstance()->addTagScriptByModule("class.PagoContrato.js","caja"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.AbonoCapital.js","caja"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.PagoCuota.js","caja"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.FormaPago.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.TDocSerieRVenta.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.CFactura.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.GAbonoCapital.js","cobros");
	SystemHtml::getInstance()->addTagScriptByModule("class.CAbonoASaldo.js","cobros");
	SystemHtml::getInstance()->addTagScriptByModule("class.CCancelacionTotal.js","cobros");	
	SystemHtml::getInstance()->addTagScriptByModule("class.CCambioPlan.js","cobros");	
	SystemHtml::getInstance()->addTagScriptByModule("class.GPagoMenor.js","cobros");
	SystemHtml::getInstance()->addTagScriptByModule("class.GNotaDebito.js","cobros");
	SystemHtml::getInstance()->addTagScriptByModule("class.GGestion.js","caja");
    SystemHtml::getInstance()->addTagScriptByModule("class.Facturar.js","caja");
	
	
	SystemHtml::getInstance()->addTagScriptByModule("class.Dashboard.js","cobros");  


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

SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
$cobros= new Cobros($protect->getDBLINK()); 
$oficial=$cobros->getOficial();

if (validateField($_REQUEST,"motorizado")){
 	$motorizado=System::getInstance()->Decrypt($_REQUEST['motorizado']);
}
?>
<script> 

var _dashboard= new DashBoard("content_dialog");
$(document).ready(function(){
	_dashboard.doFacturarRecibos();
});


 
</script>

<table width="99%" border="0" cellspacing="0" cellpadding="0" style="margin-left:5px;">
  <tr>
    <td width="500" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan="2"><strong>BUSQUEDA</strong></td>
        </tr>
      <tr>
        <td>
        <form action="?mod_caja/delegate&listado_recibo_motorizado" method="post"><table width="500" border="0" cellspacing="0" cellpadding="0" style="font-size:9px;">
          <tr>
            <td width="98">MOTORIZADO</td>
            <td><select name="motorizado" id="motorizado" class="form-control">
              <option value="">Seleccione</option>
              <?php foreach($oficial['motorizado'] as $key =>$row){?>
              <option value="<?php echo System::getInstance()->Encrypt($key);?>" <?php echo $key==$motorizado?' selected ':''?>><?php echo $row?></option>
              <?php } ?>
            </select></td>
            <td>&nbsp;</td>
            <td>&nbsp;              <input type="submit" name="button" class="form-control" id="button" value="Filtrar" /></td>
            </tr>
     
          </table>
        </form>
                  <table width="600" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><input type="text" name="no_codigo_barra" id="no_codigo_barra" class="form-control"></td>
            </tr>
          </table>
        </td>
        <td width="350" align="center" valign="bottom" style="font-size:20px;"><strong>REQUERIMIENTOS DE COBROS</strong></td>
        </tr>
    </table>
      <table width="100%" border="0" cellpadding="0" cellspacing="0" class="fsDivPage" id="list_cartera" style="font-size:12px;border-spacing:1px;">
        <thead>
          <tr style="background-color:#CCC;height:30px;">
            <td align="center">
              <input type="checkbox" class="selectAll"  name="checkbox2" id="checkbox2" /></td>
            <td align="center"><strong>MOTORIZADO</strong></td>
            <td align="center"><strong>RECIBO</strong></td>
            <td align="center"><strong>CONTRATO</strong></td>
            <td align="center"><strong>ACCION</strong></td>
            <td align="center"><strong>NOMBRES/APELLIDOS</strong></td>
            <td align="center"><strong>FECHA REQ.</strong></td>
            <td align="center"><strong>FECHA PAGO</strong></td>
            <td align="center"><strong>MONTO</strong></td>
            <td align="center"><strong>MONEDA</strong></td>
            <td align="center">&nbsp;</td>
            </tr>
        </thead>
        <tbody>
          <?php

 


$SQL="SELECT  labor_cobro.*,
	mf.*,
	mc.*,
	mf.fecha AS creacion,
	(mf.MONTO*mf.TIPO_CAMBIO) AS MONTO,
	CONCAT(mf.serie_contrato,' ',mc.no_contrato) AS contrato,
	tipo_movimiento.`DESCRIPCION` as TMOV,
	( 
	SELECT CONCAT(motorizado.`primer_nombre`,' ',
		motorizado.`segundo_nombre`,' ',
		motorizado.`primer_apellido`,' ',
		motorizado.segundo_apellido) FROM sys_personas AS motorizado WHERE id_nit=mf.ID_NIT_MOTORIZADO) AS responsable, 
	CONCAT(cliente.`primer_nombre`,' ',cliente.`segundo_nombre`,' ',cliente.`primer_apellido`,' ',cliente.segundo_apellido) AS cliente ,
	contratos.tipo_moneda as moneda	
FROM contratos 
INNER JOIN `movimiento_caja` AS mc ON (contratos.serie_contrato=mc.serie_contrato AND 
	mc.no_contrato=contratos.no_contrato)
INNER JOIN `labor_cobro` ON (labor_cobro.aviso_cobro=mc.NO_DOCTO AND labor_cobro.serie=mc.SERIE)	 
INNER JOIN `movimiento_factura` AS mf ON (mf.SERIE=mc.SERIE AND mf.NO_DOCTO=mc.NO_DOCTO)
INNER JOIN `sys_personas` AS cliente ON (cliente.id_nit=`contratos`.`id_nit_cliente`)
INNER JOIN `tipo_movimiento` ON (`tipo_movimiento`.TIPO_MOV=mf.TIPO_MOV)
WHERE  mc.FECHA_DOC=CURDATE() AND mc.ANULADO='N' AND mf.TIPO_MOV IN ('CT','CUOTA','DESESP','ABO') AND mc.ID_CAJA!='C06'  AND mc.id_usuario NOT IN ('rsandoval','1','IVALDEZ','JEMERA','EHERRERA','SYNC')   ";

if (validateField($_REQUEST,"motorizado")){
 $motorizado=System::getInstance()->Decrypt($_REQUEST['motorizado']);
 $SQL.=" AND mf.ID_NIT_MOTORIZADO='".$motorizado."'";
}

$SQL.=" ORDER BY  mc.FECHA_DOC desc ";	 
 	 
$rs=mysql_query($SQL); 
$data=array();
$monto_total=0;
while($row=mysql_fetch_assoc($rs)){
	//$contrato=array("serie_contrato"=>$row['serie_contrato'],"no_contrato"=>$row['no_contrato']);
	$encriptID=System::getInstance()->Encrypt(json_encode($row)); 	
	$monto_total=$monto_total+$row['MONTO'];
?>
          <tr style="height:30px;cursor:pointer" >
            <td align="center"><input type="checkbox" class="individual_c" name="checkbox" id="checkbox"  value="<?php echo $encriptID;?>"/></td>
            <td align="center"><?php  echo strtoupper($row['responsable']);?></td>
            <td align="center"><?php  echo $row['SERIE']." ".$row['NO_DOCTO'];?></td>
            <td align="center"><?php  echo strtoupper($row['contrato']);?></td>
            <td align="center"><?php  echo $row['TMOV'];?></td>
            <td align="center"><?php  echo $row['cliente'];?></td>
            <td align="center"><?php  echo $row['creacion'];?></td>
            <td align="center"><?php  echo $row['fecha_cobro'];?></td>
            <td align="center"><?php  echo number_format($row['MONTO'],2);?></td>
            <td align="center"><?php  echo $row['moneda'];?></td>
            <td align="center"><?php if ($protect->getIfAccessPageById(160)){ ?>
	  <?php if ($row['SERIE']!='ND'){?><button type="button" class="recibo_remove orangeButton" value="<?php echo $encriptID;?>">	Eliminar</button><?php } ?><?php } ?></td>
            </tr>
 <?php   
}  
?>

        </tbody>
        <tfoot>
          <tr >
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td height="30" align="center"><strong><?php echo number_format($monto_total,2);?></strong></td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            </tr>         
        </tfoot>
    </table></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

 <div id="content_dialog" ></div>

<?php //SystemHtml::getInstance()->addModule("footer");?>