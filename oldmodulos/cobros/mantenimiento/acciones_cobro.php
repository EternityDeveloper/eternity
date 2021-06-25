<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	


if (!$protect->getIfAccessPageById(101)){exit;}
 

	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
	
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 

	SystemHtml::getInstance()->addTagScript("script/Class.js");  
	SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
	SystemHtml::getInstance()->addTagScript("script/jquery.ui.combogrid-1.6.3.js");
	
	SystemHtml::getInstance()->addTagScriptByModule("class.ManteCobro.js"); 


	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");

?>
<script> 

var _mante= new ManteCobro("content_dialog");
$(document).ready(function(){
	_mante.tableViewAccion("listar_accion","gbutton_add",'gestiones_class');
 
});
 
</script>
 

<div class="fsPage" style="width:98%" id="jardines_page">
  <table width="100%" border="1">
    <tr>
      <td width="50%" valign="top"><table width="100%" border="0">
        <tr>
          <td width="150%"><h2>Acciones de cobro</h2></td>
        </tr>
        <tr>
          <td><button type="button" class="positive" name="gbutton_add"  id="gbutton_add" > <img src="images/apply2.png" alt=""/>Agregar </button></td>
        </tr>
        <tr>
          <td><table width="100%" border="0" class="display" id="listar_accion" style="font-size:13px">
            <thead>
              <tr>
                <th>Descripción</th>
                <th>Genera Actividad</th>
                <th>&nbsp;</th>
              </tr>
            </thead>
            <tbody>
<?php

SystemHtml::getInstance()->includeClass("client","PersonalData");
$person= new PersonalData($protect->getDBLink());

$escalamiento1="";
$escalamiento2="";
$SQL="SELECT * FROM acciones_cobros ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row)); 	
	
?>
              <tr>
                <td height="25"><?php echo $row['accion']?></td>
                <td align="center" > <?php echo $row['gen_gestion']=="S"?'SI':'NO';?></td>
                <td align="center" ><a href="#" class="gestiones_class" id="<?php echo $encriptID;?>"><img src="images/clipboard_edit.png"  /></a></td>
              </tr>
              <?php 
}
 ?>
            </tbody>
          </table></td>
        </tr>
      </table></td>
     
    </tr>
  </table>
</div>

<div id="content_dialog" ></div>

<?php SystemHtml::getInstance()->addModule("footer");?>