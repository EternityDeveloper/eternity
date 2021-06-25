<?php
 /*INGRESO DE PARAMETROS DE CIERRE*/
 if (!isset($protect)){
	exit;
 }
 
 
?>

<div>
 <form action="" method="post" id="frm_tbl_config" name="frm_tbl_config">
 <br/>
 
   <br/>
   <table width="349" align="center">
     
     <tr>
        <td colspan="2" align="center">¿Desea Procesar los Datos?
        </td>
     </tr>
     <tr>
        <td colspan="2" align="center">&nbsp;
        </td>
     </tr>
     <tr>
        <td colspan="2" align="center">
            <button type="button" class="greenButton" id="procesar" >&nbsp;Aceptar&nbsp;</button>
            <button type="button" class="redButton"   id="cancelar">Cancelar</button>
            <input  type="hidden" name = "periodo"    id="periodo" value="<?=$_REQUEST['periodo']?>"/>
            <input  type="hidden" name = "type"       id="type"    value="<?=$_REQUEST['type']?>"/>
            <input  type="hidden" name = "anio"       id="anio"    value="<?=$_REQUEST['anio']?>"/>
        </td>
     </tr>     
  </table>
 </form>
</div>