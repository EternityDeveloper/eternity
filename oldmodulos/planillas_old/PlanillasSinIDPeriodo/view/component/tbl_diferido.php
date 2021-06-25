<?php
 /*INGRESO DE PARAMETROS DE CIERRE*/
 if (!isset($protect)){
	exit;
 }
 
 if (isset($_REQUEST["id"])){
   $sql    = "select * from cm_diferidos_tbl where id = " .$_REQUEST['id'];
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
       <td align="right">Desde:</td>
       <td><input type="text" name="rangoini" id="rangoini" value="<?=$row['rangoini']?>" <?php echo $cadena;?>></td>
     </tr>
     <tr>
        <td align="right">Hasta:</td>
        <td><input type="text" name="rangofin" id="rangofin" value="<?=$row['rangofin']?>" <?php echo $cadena;?>></td>
     </tr>
     <tr>
        <td align="right">Diferido 1:</td>
        <td><input type="text" name="diferido_1" id="diferido_1" value="<?=$row['diferido_1']?>" <?php echo $cadena;?>></td>
     </tr>     
     <tr>
        <td align="right">Diferido 2:</td>
        <td><input type="text" name="diferido_2" id="diferido_2" value="<?=$row['diferido_2']?>" <?php echo $cadena;?>></td>
     </tr>     
     <tr>
        <td align="right">Diferido 3:</td>
        <td><input type="text" name="diferido_3" id="diferido_3" value="<?=$row['diferido_3']?>" <?php echo $cadena;?>></td>
     </tr>     
     <tr>
        <td colspan="2">&nbsp;
        </td>
     </tr>
     <tr>
        <td colspan="2" align="center">
            <button type="button" class="greenButton" id="procesar" >&nbsp;Aceptar&nbsp;</button>
            <button type="button" class="redButton"   id="cancelar">Cancelar</button>
            <input  type="hidden" name="id" id="id"  value="<?=$_REQUEST['id']?>" />
            <input  type="hidden" name="accion" id="accion" value="<?=$_REQUEST['accion']?>"/>
        </td>
     </tr>     
  </table>
 </form>
</div>