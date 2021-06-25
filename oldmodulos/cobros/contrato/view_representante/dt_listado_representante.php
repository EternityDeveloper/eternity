<?php 

if (!isset($protect)){
	exit;	
}

SystemHtml::getInstance()->includeClass("estructurac","Asesores"); 
SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
SystemHtml::getInstance()->includeClass("contratos","Carrito");  
SystemHtml::getInstance()->includeClass("caja","Caja"); 
//include("modulos/contratos/script/class.Contratos.js");
    #SystemHtml::getInstance()->addTagScriptByModule("Class.Contratos.js");

$_contratos=new Contratos($protect->getDBLink());
  $id = json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
	$result=  $_contratos->getRepresentantes($id->serie_contrato,
					$id->no_contrato);
        
$x = 1;
 //  echo "<pre>";
   // print_r($result);
    // echo "</pre>";
	//echo json_encode($rt);

?>

<style>
.myButton {
	background-color:#327E04;
	border:1px solid #18ab29;
	display:inline-block;
	cursor:pointer;
	color:#ffffff;
	font-family:Arial;
	font-size:17px;
	padding:9px 6px;
	text-decoration:none;
}
.myButton:hover {
	background-color:#5cbf2a;
        text-decoration:none;
}
.myButton:active {
	position:relative;
	top:1px;
}

</style>

<div class="fixed-table-container" style="width:1035px">
    	<div style="background-color:#CCC;height:30px;"> </div>
        <div class="fixed-table-container-inner">
          <table id="_detalle_contrato"   border="1" style="border-spacing:1px;font-size:12px" class="tb_detalle fsDivPage">
            <thead>
              <tr>
                  
                  <th align="center" width="240"><div class="th-inner_hb"><strong>Nombre Completo</strong></div></th>
                <th width="240"><div class="th-inner_hb"><strong>Documento</strong></div></th> 
                <th width="240"><div class="th-inner_hb"><strong>Parentesco</strong></div></th>
                <th width="240"><div class="th-inner_hb"><strong>Fecha Nacimiento</strong></div></th>
                <th width="240"><div class="th-inner_hb"><strong>Acciones</strong></div></th>
              </tr>
            </thead>
            <tbody>
<?php if(isset($result)&& count($result)> 0){ ?>
            
                
                    <?php foreach($result as $item): ?> 
                          
                    <tr style="height:30px;">
                       
                        <td align=center><?php echo $item['nombre_completo']; ?></td> 
                        <td align=center><?php echo $item['id_nit']; ?></td>
                        <td align=center><?php echo $item['parentesco']; ?></td> 
                        <td align=center><?php echo $item['fecha_nacimiento']; ?></td>
                        <td align=center><button type="button" class="myButton bcrepresentante" value="desactiva" id="<?php  echo $item['idnit']; ?>">Cambiar</button></td>
                        
                      </tr>
             <?php endforeach; ?>
              
               

<?php }if(isset($result)&& count($result)== 1){
        echo "<tr><td colspan=\"8\" align=center><span> Es posible adicionar un representante más a este contrato, haga click en el botón <strong>[AÑADIR]</strong> para realizar esta acción </span></td> <td align=\"center\"><button type=\"button\" class=\"myButton bcrepresentante\" value=\"no_desactiva\" id=".$item['idnit'].">Añadir</button></td></tr> "; 
        } if(isset($result)&& count($result)== 0){ 
    echo "<tr><td colspan=\"8\" align=center><span> No se encontraron representantes relacionados a este contrato, click en el botón <strong>[AGREGAR]</strong> si desea agregar uno</span></td> <td align=\"center\"><button type=\"button\" class=\"myButton bcrepresentante\" value=\"no_desactiva\" id=".$item['idnit'].">Agregar</button></td></tr> "; } 
    ?>

            </tbody>
         
          </table>

 
        </div>
     </div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
</table>
               