<?php
 /*INGRESO DE PARAMETROS DE CIERRE*/
 if (!isset($protect)){
	exit;
 }
 
 if (isset($_REQUEST["orden"])){
   $sql    = "select * from cm_comisiones_tbl where orden = " .$_REQUEST['orden'];
   $result = mysql_query($sql);
   $row    = mysql_fetch_array($result); 
   $accion = $_REQUEST['accion'];
    	 
 }
 
 if ( (isset( $_REQUEST["accion"] ) && $_REQUEST["accion"] === "DELETE") ){
	 $cadena  = "readonly='readonly'";
	 $msg     = "Eliminación de Registro."; 
 }
 
 if ( (isset( $_REQUEST["accion"] ) && $_REQUEST["accion"] === 'EDIT') ){
	 $msg     = "Modificación de Registro."; 
 }

 if ( (isset( $_REQUEST["accion"] ) && $_REQUEST["accion"] === 'INSERT') ){
	 $msg     = "Ingresando un nuevo Registro."; 
 }

?>

<div>
 <form action="" method="post" id="frm_tbl_config" name="frm_tbl_config">
 <br/>
   <?php echo $msg; ?>
   <br/>
   <table width="349" align="center">
     <tr>
       <td align="right">Rango Inicial:</td>
       <td><input type="text" name="rangoini" id="rangoini" value="<?=$row['rangoini']?>" <?php echo $cadena;?>></td>
     </tr>
     <tr>
        <td align="right">Rango Final:</td>
        <td><input type="text" name="rangofin" id="rangofin" value="<?=$row['rangofin']?>" <?php echo $cadena;?>></td>
     </tr>
     <tr>
        <td align="right">Porcentaje:</td>
        <td><input type="text" name="porcentaje" id="porcentaje" value="<?=$row['porcentaje']?>" <?php echo $cadena;?>></td>
     </tr>
     <tr>
        <td colspan="2">&nbsp;
        </td>
     </tr>
     <tr>
        <td colspan="2" align="center">
            <button type="button" class="greenButton" id="procesar" >&nbsp;Aceptar&nbsp;</button>
            <button type="button" class="redButton"   id="cancelar">Cancelar</button>
            <input  type="hidden" name="orden" id="orden"  value="<?=$_REQUEST['orden']?>" />
            <input  type="hidden" name="tipo" id="tipo"   value="<?=$row['tipo']?>" />
            <input  type="hidden" name="accion" id="accion" value="<?=$_REQUEST['accion']?>"/>
        </td>
     </tr>     
  </table>
 </form>
</div>