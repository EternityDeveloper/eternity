<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	


SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
 
SystemHtml::getInstance()->addTagScript("script/Class.js");
SystemHtml::getInstance()->addTagScriptByModule("Class.prospectos.js");
SystemHtml::getInstance()->addTagScriptByModule("Class.ActividadProspectos.js"); 

SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
SystemHtml::getInstance()->addTagScript("script/jquery.timeentry.min.js"); 
SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.datepicker.js");

	
SystemHtml::getInstance()->addTagScript("script/jquery/jquery.cookie.js");
SystemHtml::getInstance()->addTagScript("script/jquery/jquery.hotkeys.js");

SystemHtml::getInstance()->addTagScript("script/persona/Class.Empresa.js");
SystemHtml::getInstance()->addTagScript("script/persona/Class.Persona.js");
SystemHtml::getInstance()->addTagScript("script/persona/Class.Direccion.js");
SystemHtml::getInstance()->addTagScript("script/persona/Class.Telefono.js");
SystemHtml::getInstance()->addTagScript("script/persona/Class.Email.js");
SystemHtml::getInstance()->addTagScript("script/persona/Class.Referencia.js");
SystemHtml::getInstance()->addTagScript("script/persona/Class.Contactos.js");
SystemHtml::getInstance()->addTagScript("script/persona/Class.Referidos.js");
SystemHtml::getInstance()->addTagScript("script/persona/Class.ModuloPersona.js");


SystemHtml::getInstance()->addTagScript("script/Class.direcciones.js");
SystemHtml::getInstance()->addTagScript("script/Class.phone.js");
SystemHtml::getInstance()->addTagScript("script/Class.empresa.js");
SystemHtml::getInstance()->addTagScript("script/Class.email.js");
SystemHtml::getInstance()->addTagScript("script/Class.reference.js");
SystemHtml::getInstance()->addTagScript("script/Class.contactos.js");
SystemHtml::getInstance()->addTagScript("script/Class.AsesoresTree.js");
SystemHtml::getInstance()->addTagScript("script/Class.Referidos.js");

SystemHtml::getInstance()->addTagScript("script/jquery.base64.min.js");
 
SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
SystemHtml::getInstance()->addTagStyle("css/showLoading.css");

SystemHtml::getInstance()->addTagStyle("css/jquery.ptTimeSelect.css");

SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");
SystemHtml::getInstance()->addTagStyle("css/select2-bootstrap.css");
SystemHtml::getInstance()->addTagStyle("css/select2.css");	


/*Cargo el Header*/
SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");
/* cargo el modulo de top menu*/
SystemHtml::getInstance()->addModule("main/topmenu");

$SQL="SELECT actividades.actividad AS act_descripcion,
	tracking_prospecto.`fecha_inicio_cliente`,
	tracking_prospecto.`id_actividad`,
	tracking_prospecto.`hora`,
	tracking_prospecto.`lugar`,
	tracking_prospecto.`apoyo`,
	tracking_prospecto.detalle_actividad,
	tracking_prospecto.actividad_proxima,
	DATE_FORMAT(tracking_prospecto.fecha_proxima, '%d-%m-%Y') AS fecha_proxima,
	DATEDIFF(CURDATE(),fecha_proxima) AS TIME_DIFERENCE,
	CONCAT(asesor.`primer_nombre`,' ',asesor.`segundo_nombre`,' ',
	asesor.`primer_apellido`,' ',asesor.segundo_apellido) AS nombre_asesor,
	CONCAT(cli.`primer_nombre`,' ',cli.`segundo_nombre`,' ',
	cli.`primer_apellido`,' ',cli.segundo_apellido) AS nombre_cliente
FROM `tracking_prospecto`
INNER JOIN `actividades`  ON (`actividades`.`id_actividad`=tracking_prospecto.`id_actividad`)
INNER JOIN prospecto_comercial ON (prospecto_comercial.`last_tracking_prospecto_id`=tracking_prospecto.id)
INNER JOIN `sys_asesor` ON (`sys_asesor`.`codigo_asesor`=prospecto_comercial.`codigo_asesor`)
INNER JOIN `sys_personas` AS asesor ON (`asesor`.id_nit=sys_asesor.id_nit)
INNER JOIN `sys_personas` AS cli ON (`cli`.id_nit=prospecto_comercial.id_nit)
WHERE sys_asesor.`codigo_gerente_grupo`='".$protect->getComercialID()."'
	 AND apoyo=1 AND tracking_prospecto.fecha_proxima>CURDATE()
ORDER BY tracking_prospecto.correlativo,tracking_prospecto.id DESC ";

$rs=mysql_query($SQL); 
$data=array();
$fecha="";
while($row=mysql_fetch_assoc($rs)){ 
	array_push($data,$row);
	$fecha=$fecha.'"'.$row['fecha_proxima'].'",';
}
$fecha=substr($fecha,0,strlen($fecha)-1);
 
//echo $protect->getComercialID(); 
?>
<style>
.dataTables_filter{
	width:80%;	
	margin-top:0px;
	margin-left:10px;
}
.fsPage{
	width:99%;
}
.fields_hidden{
	display:none;	
}
 
.ui-highlight .ui-state-default{
background: red !important;
border-color: red !important;
color: white !important;
}
</style>
<script>
 
var _prospectos;

var agenda = [<?php echo $fecha;?>];

function agenda_alert(date) {
    dmy = date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
    if ($.inArray(dmy, agenda) == -1) {
        return [true, ""];
    } else {
        return [true,"ui-highlight"];
    }
}

$(function(){
 						
  //	_prospectos= new Prospectos('content_dialog','prospecto_list');
//	_prospectos.createListTable('prospecto_list'); 

    $( "#datepicker" ).datepicker({ 
						beforeShowDay: agenda_alert
					});
	
});

 
</script>
 
<div id="inventario_page" class="fsPage">
  <h2 style="margin:0;color:#FFF">Mi agenda</h2>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="250"><div id="datepicker"></div></td>
      <td valign="top"><table width="700" border="0" cellspacing="0" cellpadding="0" class="table table-hover">
        <thead>
          <tr>
            <td><strong>Asesor</strong></td>
            <td align="center"><strong>Fecha</strong></td>
            <td align="center"><strong>Actividad</strong></td>
            <td align="center"><strong>Detalle</strong></td>
            <td align="center"><strong>Hora</strong></td>
            <td align="center"><strong>Lugar</strong></td>
            <td align="center"><strong>Prospecto</strong></td>
            </tr>
        </thead>
        <tbody>
<?php 
foreach($data as $key =>$row){
?>
    <tr >
        <td ><?php echo $row['nombre_asesor'];?></td>
        <td align="center"><?php echo $row['fecha_proxima'];?></td>
        <td align="center"><?php echo $row['act_descripcion']; ?></td>
        <td align="center" ><?php echo $row['detalle_actividad'];?></td>
        <td align="center" ><?php echo $row['hora'];?></td>
        <td align="center"><?php   echo $row['lugar'];?></td>
        <td align="center"><?php   echo $row['nombre_cliente'];?></td>
    </tr>
<?php } ?>
        </tbody>
        <tfoot>
        </tfoot>
      </table></td>
    </tr>
  </table>
</div>
<div id="content_dialog" ></div>
<?php SystemHtml::getInstance()->addModule("footer");?>