<?php

 /*INGRESO DE PARAMETROS DE CIERRE*/
 if (!isset($protect)){
	exit;
 }
 
 
?>

<div>
 <form action="" method="post" id="frm_tbl_rubros" name="frm_tbl_rubros">
   <table width="349" align="center">
     <tr>
        <td align="left">COMENTARIO</td>
      </tr>
     <tr>
       <td align="left"  > 
        <textarea name="comentario_pl" id="comentario_pl" cols="45" rows="5"></textarea></td>
     </tr>
     <tr>
        <td align="center">&nbsp;
        </td>
     </tr>
     <tr>
        <td align="center">
            <button type="button" class="greenButton" id="procesar" name="procesar">&nbsp;Aceptar&nbsp;</button>
            <button type="button" class="redButton"   id="cancelar" name="cancelar">Cancelar</button></td>
     </tr>     
  </table>
 </form>
</div>