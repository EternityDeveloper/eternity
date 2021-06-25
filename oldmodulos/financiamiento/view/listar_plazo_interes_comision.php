<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
 	
	SystemHtml::getInstance()->includeClass("financiamiento","PlanFinanciamiento");
  
	if (isset($_REQUEST['add_PIC'])){  
		include("add_plazo_interes_c.php"); 
		exit;
	}  
	 
	if (isset($_REQUEST['edit_PIC'])){  
		include("edit_plazo_interes_c.php"); 
		exit;
	} 
	 	
	if (validateField($_REQUEST,"savePLC")){  
		$data=array("mensaje"=>"Debe de completar todos los datos","valid"=>false);
		if (validateField($_REQUEST,"necesidad_pre") &&  validateField($_REQUEST,"EM_ID") && 
			validateField($_REQUEST,"plazo_desde")&&  validateField($_REQUEST,"plazo_hasta")&& 
			validateField($_REQUEST,"interes_local") &&  validateField($_REQUEST,"interes_dolares") && 
			validateField($_REQUEST,"Comision")  ){
			
			$plan_fin= new PlanFinanciamiento($protect->getDBLink(),''); 
			$data=$plan_fin->addPagoInteresComision($_REQUEST); 
		}
		 
		echo json_encode($data);  
		exit;
	} 
	
	if (validateField($_REQUEST,"editPLC")){  
		$data=array("mensaje"=>"Debe de completar todos los datos","valid"=>false);
		if (validateField($_REQUEST,"necesidad_pre") &&  validateField($_REQUEST,"EM_ID") && 
			validateField($_REQUEST,"plazo_desde")&&  validateField($_REQUEST,"plazo_hasta")&& 
			validateField($_REQUEST,"interes_local") &&  validateField($_REQUEST,"interes_dolares") && 
			validateField($_REQUEST,"Comision")  ){
			
			$plan_fin= new PlanFinanciamiento($protect->getDBLink(),''); 
			$data=$plan_fin->updatePagoInteresComision($_REQUEST); 
		}
		 
		echo json_encode($data);  
		exit;
	} 	 
	 
	 
	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js"); 
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js");  
	SystemHtml::getInstance()->addTagScript("script/Class.js"); 
	SystemHtml::getInstance()->addTagScript("script/select2.min.js");  
  	SystemHtml::getInstance()->addTagScript("script/jquery.timeentry.min.js");
	
	SystemHtml::getInstance()->addTagScriptByModule("Class.PlazoIC.js"); 

	SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");	 
	
	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css"); 
	
	SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");
	SystemHtml::getInstance()->addTagStyle("css/select2-bootstrap.css");
	SystemHtml::getInstance()->addTagStyle("css/select2.css");
	
	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	
 
 
 $plan_fin= new PlanFinanciamiento($protect->getDBLink(),'');
?> 
 
<style>
.dataTables_wrapper{
	font-size:12px;	
}
</style>
<script> 

var _plazoic= new PlazoIC("content_dialog");

$(document).ready(function(){ 
	_plazoic.doInit(); 
});

 
</script>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:0px;padding:0px">
 
  <tr>
    <td align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="450"><span style="font-size: 20px">PLAZO INTERES &amp; COMISION</span></td>
        </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
 
        <td>
           
                           <table width="100%" border="0" cellspacing="0" cellpadding="0">
                
                      <tr>
                        <td><input type="submit" name="crear_pic" id="crear_pic" value="Crear"  class="btn btn-primary bt-sm"  /></td>
                      </tr>
                      <tr>
                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td valign="top">&nbsp;</td>
                          </tr>
                        </table></td>
                      </tr>
                    </table>
       
           </td>
      </tr>
    </table></td>
  </tr>
 
          <tr>
            <td valign="top"><table id="list_cartera" width="100%" border="0" cellpadding="0" cellspacing="0" class="fsDivPage table"  style="font-size:12px;border-spacing:1px;">
              <thead>
                <tr style="background-color:#CCC;height:30px;">
                  <td width="208" align="center"><strong>SITUACION</strong></td>
                  <td width="276" align="center"><strong>EMPRESA</strong></td>
                  <td width="276" align="center"><strong>PLAZO</strong></td>
                  <td width="276" align="center"><strong>INTERES LOCAL</strong></td>
                  <td width="276" align="center"><strong>INTERES DOLAR</strong></td>
                  <td width="276" align="center"><strong>COMISION</strong></td>
                </tr>
              </thead>
              <tbody>
                <?php



$data=$plan_fin->getPlazoInteresComision();

foreach($data as $key =>$row){ 
	$id=System::getInstance()->Encrypt(json_encode($row));
?>
            <tr class="list_plc" id="<?php echo $id;?>" style="cursor:pointer">
              <td align="center"><?php  echo $row->necesidad_pre?></td>
              <td align="center"><?php  echo $row->EM_NOMBRE;?></td>
              <td align="center"><?php  echo $row->plazo_desde." - ".$row->plazo_hasta;?></td>
              <td align="center"><?php  echo $row->interes_local;?> %</td>
              <td align="center"><?php  echo $row->interes_dolares;?> %</td>
              <td align="center"><?php  echo $row->Comision;?> %</td>
            </tr>
<?php } ?>
              </tbody>
            </table></td>
          </tr>
  </table>
 <div id="content_dialog" ></div>