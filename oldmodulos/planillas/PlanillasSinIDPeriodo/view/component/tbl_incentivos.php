<?php
 if (!isset($protect)){
	exit;
 }
 

 
 if (isset($_REQUEST["idincentivo"])){
   $sql    = "select * from tbl_tipo_incentivo where tipo_incentivo = " .$_REQUEST['idincentivo'];
   $result = mysql_query($sql);
   $row    = mysql_fetch_array($result); 
   $accion = $_REQUEST['accion'];
    	 
 }
 
 if ( (isset( $_REQUEST["accion"] ) && $_REQUEST["accion"] === "DELETE") ){
	 $cadena  = "readonly='readonly'";
	 $combo   = "disabled='disabled'";
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
       <td align="right">Descripcion:</td>
       <td><input type="text" name="descripcion" id="descripcion" value="<?=$row['descripcion']?>" <?php echo $cadena;?>></td>
     </tr>

     <tr>
        <td colspan="2">&nbsp;
        </td>
     </tr>
     <tr>
        <td colspan="2" align="center">
            <button type="button" class="greenButton" id="procesar" >&nbsp;Aceptar&nbsp;</button>
            <button type="button" class="redButton"   id="cancelar">Cancelar</button>
            <input  type="hidden" name = "accion" id="accion" value="<?=$_REQUEST['accion']?>"/>
            <input  type="hidden" name = "tipo_incentivo" id="tipo_incentivo" value="<?=$_REQUEST['idincentivo']?>"/>
        </td>
     </tr>     
  </table>
 </form>
</div>