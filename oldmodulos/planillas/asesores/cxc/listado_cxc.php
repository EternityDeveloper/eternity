<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

if (isset($_REQUEST['aplicar_nota_cd'])){
	$rt=array("mensaje"=>"Error no se pudo procesar","error"=>true);
	if (validateField($_REQUEST,"tipo_m")&& validateField($_REQUEST,"monto")
		&& validateField($_REQUEST,"id")){  
		$tipo_m=json_decode(System::getInstance()->Decrypt($_REQUEST['tipo_m']));
		$codigo_asesor=System::getInstance()->Decrypt($_REQUEST['id']);
		$monto=$_REQUEST['monto'];
		 
		if (isset($tipo_m->tipo_accion) && ($monto>0) && ($codigo_asesor>0)){
			SystemHtml::getInstance()->includeClass("planillas","DescuentoComision");
			/*SI ES EL TIPO DE NOTA DE CREDITO LO CONVIERTO EN NEGATIVO*/
			if ($tipo_m->tipo_accion=="C"){
				$monto=-1*$monto;
			}  
			$comi= new DescuentoComision($protect->getDBLink());  
			$id=$comi->aplicarSaldo(0,
							$codigo_asesor,
							$_REQUEST['descripcion'],
							UserAccess::getInstance()->getIDNIT(),
							$tipo_m->codigo,
						    $monto);
			
			if ($id>0){
				$rt['mensaje']="Documento generado";
				$rt['error']=false;
			}
			//print_r($tipo_m);
		}
	}
	
	echo json_encode($rt);
	exit;
}

if (isset($_REQUEST['getDetallecxc'])){
	include("view/detalle_cxc.php");
	exit;
}

if (isset($_REQUEST['doViewCDAsesor'])){
	include("view/nota_cd_asesor.php");
	exit;
}
 

 
SystemHtml::getInstance()->addTagScriptByModule("jquery.dataTables.js","planillas"); 
SystemHtml::getInstance()->addTagScript("script/Class.js"); 
     
SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");
SystemHtml::getInstance()->addTagStyle("css/select2-bootstrap.css");
SystemHtml::getInstance()->addTagStyle("css/select2.css");
SystemHtml::getInstance()->addTagScriptByModule("class.cxcAsesores.js","planillas/asesores/cxc"); 
SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");

/*Cargo el Header*/
SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");
	
	 
?> 
<script> 

var cxc= new cxcAsesores("content_dialog");
 
$(function(){  
	cxc.doInitListado();
});

 
</script>
<br>
<div class="fsPage" style="width:990px;float:left">
  <h2 style="color:#FFF;margin-top:0px;">Cuenta x cobrar asesores</h2>
  <table width="100" border="0" cellspacing="0" cellpadding="0"  style="width:800px;">
    <tr>
      <td><button  type="button" class="bt bt-warning" id="do_aplicar_nota_cd" >Agregar movimiento</button></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>
    <table width="100%" id="dtTable" class="table table-hover" style="font-size:12px">
      <thead>
        <tr>
          <td><strong> NOMBRE ASESOR</strong></td>
          <td align="center"><strong>SALDO</strong></td>
          </tr>
      </thead>
      <tbody>
        <?php  

$SQL="SELECT a.codigo_asesor,
	 CONCAT(b.primer_nombre, ' ', b.segundo_nombre,' ',
		b.primer_apellido, ' ', b.segundo_apellido, ' ', b.apellido_conyuge) AS nombre_asesor,
	(SELECT SUM(cxc.monto) AS monto FROM `cxc_balance_asesor` AS cxc
	WHERE cxc.codigo_asesor=a.codigo_asesor and  cxc.estatus=1) AS monto
	FROM sys_asesor a
	INNER JOIN sys_personas b 
	ON a.id_nit = b.id_nit
	WHERE   a.codigo_asesor IS NOT NULL    
	HAVING monto IS NOT NULL ";
$rs=mysql_query($SQL);
$monto=0;
while($row=mysql_fetch_assoc($rs)){
	$monto=$monto+$row['monto'];
	$id=System::getInstance()->Encrypt(json_encode($row));  
?>
        <tr style="cursor:pointer" id="<?php echo $id?>" alt="<? echo $row['codigo_asesor']?>">
          <td class="listado_cxc"  ><?php echo utf8_encode($row['nombre_asesor']);?></td>
          <td  class="listado_cxc" align="center" ><?php echo number_format($row['monto'],2); ?></td>
          </tr>
      
        <?php } ?>
        
      </tbody>
      <tfoot>
        <tr   >
          <td  >&nbsp;</td>
          <td align="center" ><strong><?php echo number_format($monto,2);?></strong></td>
          </tr>
      </tfoot>
    </table></td>
    <td>&nbsp;</td>
  </tr>
</table>
 
</div>

<div id="content_dialog" ></div>