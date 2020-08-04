<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	/* cargo el modulo de top menu*/
	SystemHtml::getInstance()->addModule("main/topmenu");

?>
<style type="text/css" title="currentStyle">
.fsPage{
	width:350px;
	padding-left:0px;
	margin-left:3px;
}
</style>

<div class="fsPage">
  <div class="buttons ">
    <table width="100%" border="0">
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td valign="top">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3"><h2>Mantenimiento</h2> </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td valign="top">&nbsp;</td>
      </tr>
      <tr>
          <td width="50%"><table width="320" border="0" align="center">
            <tr>
              <td></td>
            </tr>
            <tr>
              <td><a href="./?mod_inventario/mante_jardines_fases" class="positive"  style="width:90%"><img src="images/apply2.png" alt=""/>Jardines / Fases</a></td>
            </tr>          
	        <tr>
              <td>&nbsp;</td>
            </tr>
        </table></td>
          <td width="50%">&nbsp;</td>
          <td width="50%" valign="top">&nbsp;</td>
      </tr>
    </table>
  </div> 
</div>


<?php SystemHtml::getInstance()->addModule("footer");?>