<?php 

if (!isset($protect)){
	exit;	
}


SystemHtml::getInstance()->includeClass("estructurac","Asesores"); 
SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
SystemHtml::getInstance()->includeClass("contratos","Carrito");  
SystemHtml::getInstance()->includeClass("caja","Caja"); 

//include("modulos/contratos/script/class.Contratos.js");
    

$_contratos=new Contratos($protect->getDBLink());
	
	$id = json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
 
	$rt=  $_contratos->getBeneficiarios($id->serie_contrato,
					$id->no_contrato);
       // echo "<pre>";
        //print_r($rt);
       //echo "</pre>";
       
$x = 0;
/*
       echo "<pre>";
        print_r($rt);
        echo($rt[0]["id_beneficiario"]);    //id beneficiario
        echo(System::getInstance()->Decrypt($rt[0]["nit"])); //cedula
        echo(System::getInstance()->Decrypt($rt[0]["beneficiario"])); //beneficiario
        echo "</pre>";
 
 */
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

<div class="fixed-table-container" style="width:1200px">
    	<div style="background-color:#CCC;height:30px;"> </div>
        <div class="fixed-table-container-inner">
          <table id="_detalle_contrato"   border="1" style="border-spacing:1px;font-size:12px" class="tb_detalle fsDivPage benef">
            <thead>
              <tr>
                <th width="133"><div class="th-inner_hb"><strong>Primer Nombre</strong></div></th>
                <th width="133"><div class="th-inner_hb"><strong>Segundo Nombre</strong></div></th>
                <th width="133"><div class="th-inner_hb"><strong>Primer Apellido</strong></div></th>
                <th width="133"><div class="th-inner_hb"><strong>Segundo Apellido</strong></div></th>
                <th width="133"><div class="th-inner_hb"><strong>Documento</strong></div></th>
                <th width="133"><div class="th-inner_hb"><strong>Parentesco</strong></div></th>
                <th width="133"><div class="th-inner_hb"><strong>Fecha Nacimiento</strong></div></th>
                <th width="133"><div class="th-inner_hb"><strong>Lugar Nacimiento</strong></div></th>
                <th width="133"><div class="th-inner_hb"><strong>Acciones</strong></div></th>
              </tr>
            </thead>
            <tbody>
<?php if(isset($rt)&& count($rt)> 0){ ?>
           
               
          <?php foreach($rt as $item): $x++ ?> 
                     
                    <tr style="height:30px;">
                        <td align=center> <p  id="primer_nombre_<?php echo $x; ?>"><?php echo $item['nombre_1']; ?></td> 
                        <td align=center> <p  id="segundo_nombre_<?php echo $x; ?>"><?php echo $item['nombre_2']; ?></p></td>
                        <td align=center> <p  id="primer_apellido_<?php echo $x; ?>"><?php echo $item['apellido_1']; ?></p></td> 
                        <td align=center> <p  id="segundo_apellido_<?php echo $x; ?>"><?php echo $item['apelllido_2']; ?></p></td> 
                        <td align=center> <p  id="id_beneficiario_<?php echo $x; ?>"><?php if($item['id_nit'] == ""){ echo "-------------"; }else{ echo $item['id_nit']; } ?></p></td> 
                        <td align=center> <p  id="parentesco_<?php echo $x; ?>"><?php echo $item['parentesco']; ?></p></td> 
                        <td align=center> <p  id="fecha_nacimiento_<?php echo $x; ?>"> <?php echo $item['fecha_nacimiento']; ?></p></td>
                        <td align=center> <p  id="lugar_nacimiento_<?php echo $x; ?>"> <?php echo $item['lugar_nacimiento']; ?></p></td>
                       <!-- <td align=center><a href="./?mod_contratos/listar&view_search=1" target="new" type="button" class="myButton" id="bt_beneficiario">Cambiar</a></td> -->
                        <td align=center><button type="button" class="myButton bcbeneficiario" value="desactiva" id="<?php  echo System::getInstance()->Encrypt($item['id_beneficiario']); ?>">Cambiar</button></td>
                      </tr>
             <?php endforeach; ?>
             
               

<?php }if(isset($rt)&& count($rt)== 1){
        echo "<tr><td colspan=\"8\" align=center><span> Es posible adicionar un beneficiario más a este contrato, haga click en el botón <strong>[AÑADIR]</strong> para realizar esta acción </span></td> <td align=\"center\"><button type=\"button\" class=\"myButton bcbeneficiario\" value=\"no_desactiva\" id=".System::getInstance()->Encrypt($item['id_beneficiario']).">Añadir</button></td></tr> "; 
        } 
if(isset($rt)&& count($rt)== 0){ 
    echo "<tr><td colspan=\"8\" align=center><span> No se encontraron beneficiarios relacionados a este contrato, click en el botón <strong>[AGREGAR]</strong> si desea agregar uno</span></td> <td align=\"center\"><button type=\"button\" class=\"myButton bcbeneficiario\" value=\"no_desactiva\" id=".System::getInstance()->Encrypt($item['id_beneficiario']).">Agregar</button></td></tr> "; } 
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