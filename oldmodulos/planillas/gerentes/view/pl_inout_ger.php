<?php

 session_start();
 
 /*INGRESO DE PARAMETROS DE CIERRE*/
 if (!isset($protect)){
	exit;
 }
 
 $anio            = isset($_REQUEST['anio'])  ? System::getInstance()->Decrypt($_REQUEST['anio'])   : 0; 
 $mes            = isset($_REQUEST['periodo'])  ? System::getInstance()->Decrypt($_REQUEST['periodo'])   : 0; 
 $tipo_cierre	 = isset($_REQUEST['type'])     ? System::getInstance()->Decrypt($_REQUEST['type'])      : 0;
 
 if(isset($_REQUEST['id'])){
     $numregistro = isset($_REQUEST['id']) ? System::getInstance()->Decrypt($_REQUEST['id']) : 0;
	 $sql = "select idconcepto, monto 
	           from cm_detplanilla_gerente_tbl 
			  where numregistro = " .$numregistro;
	 
	 $rsRegistro  = mysql_query($sql);
	 $rowRegistro = mysql_fetch_array($rsRegistro); 
 }
 
?>

<div>
 <form action="" method="post" id="frm_tbl_rubros" name="frm_tbl_rubros">
  <br/>
   <table width="349" align="center">
     <tr>
        <td width="89" align="right">Concepto:</td>
        <td width="248">
         <select name="idconcepto" id="idconcepto">
           <option value= "0" <?php echo $combo;?>> Seleccione ...</option>
             <?php  
			    $qrConcepto = "select idconcepto, descripcion from cm_concepto_tbl order by idconcepto";
				$rsConcepto = mysql_query($qrConcepto);
	            
				while($rowConcepto = mysql_fetch_array($rsConcepto)){			   
			 ?>	  
			     <option value="<?=$rowConcepto['idconcepto']?>" <?php if( $rowConcepto['idconcepto']==$rowRegistro['idconcepto']){ echo "selected='selected'" ;} echo $combo;?> ><?=$rowConcepto['descripcion']?></option>	  
			  <?php
                  }
			  ?>
         </select>       
        </td>
     </tr>
     <tr>
        <td align="right">Monto:</td>
        <td><input name="monto" type="text" id="monto" value="<?=$rowRegistro['monto']?>" size="20"/></td>
     </tr>
     <tr>
        <td colspan="2">&nbsp;
        </td>
     </tr>
     <tr>
        <td colspan="2" align="center">
            <button type="button" class="greenButton" id="procesar" name="procesar">&nbsp;Aceptar&nbsp;</button>
            <button type="button" class="redButton"   id="cancelar" name="cancelar">Cancelar</button>
            <input  type="hidden" name = "accion" id="accion" value="<?=$_REQUEST['accion']?>"/>
            <input  type="hidden" name = "idgerente" id="idgerente" value="<?=$_REQUEST['idgerente']?>"/>
            <input type="hidden" id="periodo" name="periodo" value="<?=$_REQUEST['periodo']?>" />
            <input type="hidden" id="type" name="type" value="<?=$_REQUEST['type']?>" />
             <input type="hidden" id="anio" name="anio" value="<?=$_REQUEST['anio']?>" />
            <input type="hidden" id="id" name="id" value="<?=$_REQUEST['id']?>" />
            
        </td>
     </tr>     
  </table>
 </form>
</div>