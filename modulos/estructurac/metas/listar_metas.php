<?php

SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");

SystemHtml::getInstance()->addTagScript("script/Class.js"); 
SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
SystemHtml::getInstance()->addTagStyle("css/style.css");

SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");
SystemHtml::getInstance()->addTagScriptByModule("class.Metas.js"); 
/*Cargo el Header*/
SystemHtml::getInstance()->addModule("header");
SystemHtml::getInstance()->addModule("header_logo");
/* cargo el modulo de top menu*/
SystemHtml::getInstance()->addModule("main/topmenu");
?>
<script>
var metas;
$(function(){
	metas=new Metas("content_dialog");
	metas.listOfGerentes("metas_list");
		
})

</script>
<div  class="fsPage" style="width:98%">
  <h2>Listado de Metas</h2>
 	<table border="0" class="display" id="metas_list" style="font-size:13px">
      <thead>
        <tr>
          <th>Code</th>
          <th>Nombre</th>
          <th>Apellido</th>
          <th>Cargo</th>   
        </tr>
      </thead>
      <tbody>
<?php
//WHERE tabla='Gerente de ventas' 
	$SQL="SELECT asesores_g_d_gg_view.*,sys_personas.primer_nombre,sys_personas.primer_apellido FROM `asesores_g_d_gg_view`
			INNER JOIN `sys_personas` ON (`sys_personas`.id_nit=asesores_g_d_gg_view.id_nit)
			WHERE asesores_g_d_gg_view.estatus=1
			 ";
  
	$rs=mysql_query($SQL);
 
	while($row=mysql_fetch_assoc($rs)){
		$id=System::getInstance()->Encrypt(json_encode($row));
 
 ?>
    <tr id="<?php echo $id;?>">
        <td height="30" ><?php echo $row['id_comercial']?></td>
        <td ><?php echo $row['primer_nombre']?></td>
        <td ><?php echo $row['primer_apellido']?></td>
        <td ><?php echo $row['tabla']?></td>
    </tr>
<?php } ?>    
      </tbody>
  </table>
</div>
<div id="content_dialog" ></div>
