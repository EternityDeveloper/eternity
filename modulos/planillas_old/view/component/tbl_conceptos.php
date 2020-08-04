<?php
 /*INGRESO DE PARAMETROS DE CIERRE*/
 if (!isset($protect)){
	exit;
 }
 
 $tipoconcepto = array("1" => "Pago", "0" => "Descuento");
 
 if (isset($_REQUEST["idconcepto"])){
   $sql    = "select * from cm_concepto_tbl where idconcepto = " .$_REQUEST['idconcepto'];
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
        <td align="right">Tipo:</td>
        <td>
         <select name="tipo" id="tipo">
           <option value= "" <?php echo $combo; ?>> Seleccione ...</option>
             <?php  
		      foreach( $tipoconcepto as $linea => $descripcion ){
			 ?>	  
			    <option value="<?=$linea?>" <?php if( $row['tipo']==$linea ){ echo "selected='selected'" ;} echo $combo; ?> ><?=$descripcion?></option>	  
			  <?php
                  }
			  ?>
         </select>       
        </td>
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
            <input  type="hidden" name = "idconcepto" id="idconcepto" value="<?=$_REQUEST['idconcepto']?>"/>
        </td>
     </tr>     
  </table>
 </form>
</div>