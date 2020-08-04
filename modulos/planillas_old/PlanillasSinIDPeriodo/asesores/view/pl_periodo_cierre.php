<?php
 /*INGRESO DE PARAMETROS DE CIERRE*/
 if (!isset($protect)){
	exit;
 }
 
 /* Verificamos que hayan factores en la tabla de CM_COMISIONES <COMISIONES>*/
 if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 1) ){
	 
	  $sql         = "select count(1) as conteo from cm_comisiones_tbl where tipo = 1 and estatus = 1";
	  $rsFactores  = mysql_query($sql);
	  $rowFactores = mysql_fetch_array($rsFactores); 
	  
	  if ($rowFactores['conteo'] == 0){
         $mensaje = "No Existen Rangos en la Tabla de Porcentajes.";
	  }	      
  }
  
 /* Verificamos que hayan factores en la tabla de CM_COMISIONES <BONO POR PLAN>*/
  if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 2) ){
	 
	  $sql         = "select count(1) as conteo from cm_comisiones_tbl where tipo = 2 and estatus = 1";
	  $rsFactores  = mysql_query($sql);
	  $rowFactores = mysql_fetch_array($rsFactores); 
	  
	  if ($rowFactores['conteo'] == 0){
         $mensaje = "No Existen Rangos en la Tabla de Porcentajes.";
	  }	      
  }
  
  
  /* Verificamos que hayan factores en la tabla de CM_BONOAUXILIO_TBL <BONO POR AUXILIO> */
  if( isset($_REQUEST['choice']) && ( System::getInstance()->Decrypt($_REQUEST['choice']) == 3) ){
	 
	  $sql         = "select count(1) as conteo from cm_bonoauxilio_tbl where estatus = 1";
	  $rsFactores  = mysql_query($sql);
	  $rowFactores = mysql_fetch_array($rsFactores); 
	  
	  if ($rowFactores['conteo'] == 0){
         $mensaje = "No Existen Rangos en la Tabla de Porcentajes.";
	  }	      
  }
 
 $tipoP  = System::getInstance()->Encrypt('P');
 $tipoT  = System::getInstance()->Encrypt('T');
 $meses  = array (
     "1" => 'ENERO',
	 "2" => 'FEBRERO',
	 "3" => 'MARZO',
	 "4" => 'ABRIL',
	 "5" => 'MAYO',
	 "6" => 'JUNIO',
	 "7" => 'JULIO',
	 "8" => 'AGOSTO',
	 "9" => 'SEPTIEMBRE',
	 "10" => 'OCTUBRE',
	 "11" => 'NOVIEMBRE',
	 "12" => 'DICIEMBRE'
 );
 
 $sqlAnio = "select distinct ano from cierres order by ano";
 $rsAnio  = mysql_query($sqlAnio);
 
 
?>

<div>
 <form action="" method="post" id="frm_datos_cierre" name="frm_datos_cierre">
   <?php 
      if(strlen($mensaje) > 0 ){
	?>
       <table width="349" align="center">
           <tr><td align="center"><?=$mensaje?></td></tr>
       </table>
   <?php
	 }else {
   ?>
   
       <table width="349" align="center">
        <tr>
           <td align="right">A&ntilde;o:</td>
           <td>
             <select name="anio" id="anio">
               <option value= ""> Seleccione ...</option>
                  <?php 
				     while($row = mysql_fetch_array($rsAnio)){
				   ?>	 
					   <option value= "<?=System::getInstance()->Encrypt($row['ano'])?>"> <?=$row['ano']?></option>
                   <?php 
					 }
				  ?>
             </select>
           </td>
         </tr>
         <tr>
           <td align="right">Mes :</td>
           <td>
             <select name="periodo" id="periodo">
               <option value= ""> Seleccione ...</option>
                 <?php  
                  foreach( $meses as $periodo => $descripcion ){
                 ?>	  
                    <option value="<?=System::getInstance()->Encrypt($periodo)?>"><?=$descripcion?></option>	  
                  <?php
                      }
                  ?>
             </select>
           </td>
         </tr>
         <tr>
            <td align="right">Tipo de Cierre :</td>
            <td><input type="radio" name="type" id="type" value="<?=$tipoP?>">Parcial</td>
         </tr>
         <tr>
            <td>&nbsp;</td>
            <td><input type="radio" name="type" id="type" value="<?=$tipoT?>">Total
         </tr>
         <tr>
            <td colspan="2">&nbsp;
            </td>
         </tr>
         <tr>
            <td colspan="2" align="center">
                <button type="button" class="greenButton" id="procesar" disabled="disabled">&nbsp;Proceder&nbsp;</button>            
                <button type="button" class="redButton"   id="cancelar">Cancelar</button>
    
            </td>
         </tr>     
      </table>
     </form>
    </div>
 <?php
	 }
  ?>	 