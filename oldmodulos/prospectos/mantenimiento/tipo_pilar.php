<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
if (!$protect->getIfAccessPageById(90)){ 
	exit;
}
 
if (isset($_REQUEST['submit_tipo_reserva'])){
	
	$retur=array("mensaje"=>"Tipo reserva agregada","error"=>false);
 	
	if ($_REQUEST['edit']=="0"){
		$obj = new ObjectSQL();
		$obj->id_reserva=$_REQUEST['id_reserva'];
		$obj->reserva_descrip=$_REQUEST['reserva_descrip'];
		$obj->abono=$_REQUEST['abono']=="1"?"1":"0"; 
		$obj->horas=$_REQUEST['horas'];
		$obj->gerencia=$_REQUEST['gerencia']=="1"?"1":"0"; 
		$SQL=$obj->getSQL("insert","tipos_reservas");
		mysql_query($SQL);
		
		//echo json_encode($retur);
	}else if ($_REQUEST['edit']=="1"){
		$retur['mensaje']="Tipo reserva actualizada";
		
		$data=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id'],$protect->getSessionID());
		$data=json_decode($data);	
		$obj = new ObjectSQL();
	//	$obj->id_reserva=$_REQUEST['id_reserva'];
		$obj->reserva_descrip=$_REQUEST['reserva_descrip'];
		$obj->abono=$_REQUEST['abono']=="1"?"1":"0"; 
		$obj->horas=$_REQUEST['horas'];
		$obj->gerencia=$_REQUEST['gerencia']=="1"?"1":"0"; 
		$obj->estatus=$_REQUEST['estatus'];
 
		$SQL=$obj->getSQL("update","tipos_reservas"," where id_reserva='".$data->id_reserva."' ");
		mysql_query($SQL);
		
	 	sleep(5);
		
	}
	echo json_encode($retur);
	
	exit;	
 
} 


	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	
	SystemHtml::getInstance()->addTagScript("script/Class.js");
	
	SystemHtml::getInstance()->addTagScript("script/Class.js");
	
	SystemHtml::getInstance()->addTagScriptByModule("Class.MantenimientoProspectos.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");
	
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.mouse.js");
	SystemHtml::getInstance()->addTagScript("script/ui//jquery.ui.draggable.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.position.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.resizable.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.button.js");
	SystemHtml::getInstance()->addTagScript("script/ui/jquery.ui.dialog.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");

	
	SystemHtml::getInstance()->addTagStyle("css/base/jquery.ui.all.css");
	
	SystemHtml::getInstance()->addTagStyle("css/showLoading.css");


	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");

?>
<script>
var mantenimiento;
$(function(){
	
	mantenimiento= new MantenimientoProspectos('content_dialog','role_list');
	mantenimiento.createTable();
	mantenimiento.activateButtom("r_button");
	
	
});

</script>
 

<div class="fsPage" style="width:98%">
  <table width="100%" border="0">
    <tr>
      <td width="150%"><h2>Tipos de Pilares </h2></td>
    </tr>
    <tr>
      <td><button type="button" class="orangeButton" name="r_button"  id="r_button">Agregar</button></td>
    </tr>
    <tr>
      <td><table width="100%" border="0" class="display" id="role_list" style="font-size:13px">
        <thead>
          <tr>
            <th>Pilar</th>
            <th>Descripcion</th>
            <th>Dias</th>
            <th>Estatus</th>
            <th>&nbsp;</th>
          </tr>
        </thead>
        <tbody>
          <?php
$SQL="SELECT * FROM `tipos_pilares` 
INNER JOIN sys_status ON (tipos_pilares.estatus=sys_status.id_status)
WHERE tipos_pilares.estatus=1 ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
          <tr>
            <td height="25"><?php echo $row['idtipo_pilar']?></td>
            <td align="center" ><?php echo $row['dscrip_tipopilar']?></td>
            <td align="center" ><?php echo $row['dias_proteccion']?></td>
            <td align="center" ><?php echo $row['descripcion']?></td>
            <td align="center" ><a href="#" id="<?php echo $encriptID;?>" class="edit"><img src="images/clipboard_edit.png"  /></a></td>
          </tr>
          <?php 
}
 ?>
        </tbody>
      </table></td>
    </tr>
  </table>
</div>

<div id="content_dialog" ></div>

<?php SystemHtml::getInstance()->addModule("footer");?>