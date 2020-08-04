<?php
 /*INGRESO DE PARAMETROS DE CIERRE*/
 if (!isset($protect)){
	exit;
 }
 
 if (isset($_REQUEST["orden"])){
   $sql    = "select * from cm_bonoauxilio_tbl where orden = " .$_REQUEST['orden'];
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
       <td align="right">Mes:</td>
       <td><input type="text" name="mes" id="mes" value="<?=$row['mes']?>" <?php echo $cadena;?>></td>
     </tr>
      <tr>
       <td align="right">Cumplimiento:</td>
       <td><input type="text" name="cumplimiento" id="cumplimiento" value="<?=$row['cumplimiento']?>" <?php echo $cadena;?>></td>
     </tr>
     
     <tr>
       <td align="right">Bono Primario:</td>
       <td><input type="text" name="bonoprimario" id="bonoprimario" value="<?=$row['bonoprimario']?>" <?php echo $cadena;?>></td>
     </tr>
      <tr>
       <td align="right">Venta Contrato:</td>
       <td><input type="text" name="ventacontrato" id="ventacontrato" value="<?=$row['ventacontrato']?>" <?php echo $cadena;?>></td>
     </tr>
     <tr>
       <td align="right">Bono Adicional:</td>
       <td><input type="text" name="bonoadicional" id="bonoadicional" value="<?=$row['bonoadicional']?>" <?php echo $cadena;?>></td>
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
            <input  type="hidden" name="accion" id="accion" value="<?=$_REQUEST['accion']?>"/>
        </td>
     </tr>     
  </table>
 </form>
</div>