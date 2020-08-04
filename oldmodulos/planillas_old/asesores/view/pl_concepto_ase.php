<?php

 if (!isset($protect)){
	exit;
 }
 
 $codigo_asesor  = isset($_REQUEST['idasesor'])? System::getInstance()->Decrypt($_REQUEST['idasesor']) : 0;
 $anio           = isset($_REQUEST['anio'])     ? System::getInstance()->Decrypt($_REQUEST['anio'])      : 0;
 $mes            = isset($_REQUEST['periodo'])  ? System::getInstance()->Decrypt($_REQUEST['periodo'])   : 0; 
 $tipo_cierre	 = isset($_REQUEST['type'])     ? System::getInstance()->Decrypt($_REQUEST['type'])      : 0;
 
 SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js");
  
 SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
 SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
  
 SystemHtml::getInstance()->addTagScript("script/jquery.showLoading.min.js");
 SystemHtml::getInstance()->addTagScript("script/jquery.blockUI.js");
 SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");
 
 SystemHtml::getInstance()->addTagScript("script/Class.js");
 SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
 SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");
 
?> 

<script>
  var opTable;

 $(document).ready(function(){
   
	opTable = $("#detconcepto").dataTable({
							"bFilter": false,
							"bInfo": false,
							"bPaginate": true,
							  "oLanguage": {
									"sLengthMenu": "regs x pag_MENU_",
									"sZeroRecords": "No se ha encontrado - lo siento",
									"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
									"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
									"sInfoFiltered": "(filtrado de _MAX_ total registros)",
									"sSearch":"Buscar"
								}
							});							
							
 });
 
 
 
</script> 

<div id="principal">
   <button type="button" class="greenButton" name="pbutton"  id="pbutton" style="float:right">Agregar</button>
   <br/>
   <br/>
   
   <div id="canvas" class="fsPage" style="width:98%">
      <table border="0" class="display" id="detconcepto" width="100%" style="font-size:12px;width:100%">
        <thead>
        <tr>
          <th>Id</th>
          <th>Concepto</th>
          <th>Ingreso</th>
          <th>Descuento</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
         <?php 
		    $query = "select a.numregistro,
						     a.idconcepto,
						    if(b.tipo=1,a.monto,0) as ingreso,
							if(b.tipo=0,a.monto,0) as descuento,
						    b.descripcion
					   from cm_detplanilla_asesor_tbl a,
						    cm_concepto_tbl b
					  where a.idconcepto = b.idconcepto
					    and a.codigo_asesor = ".$codigo_asesor." 
					    and a.anio = ".$anio."
					    and a.mes = ".$mes."
					    and a.tipo_cierre = '".$tipo_cierre."' order by a.idconcepto";
						
		  $qrResult = mysql_query($query);
		  while($qry = mysql_fetch_array($qrResult)){
			  $encryptReg = System::getInstance()->Encrypt($qry['numregistro']);  
		 ?> 	
		    <tr>
               <td align="right"><?=$qry['idconcepto']?></td>
               <td><?=$qry['descripcion']?></td>
               <td align="right"><?=number_format($qry['ingreso'],2,".",",")?></td>
               <td align="right"><?=number_format($qry['descuento'],2,".",",")?></td>
              <td align="center">
                    <a href="#" onClick="editConcepto('<?=$encryptReg?>')"><img src="images/clipboard_edit.png"/></a>
              </td>

            </tr>	  
		 <?php 
          }
		 ?>
      </tbody>
      </table>
     
   </div>
   
</div>

