<?php
if (!isset($protect)){
	echo "Security error!";
	exit;
}

SystemHtml::getInstance()->includeClass("financiamiento","PlanFinanciamiento");


?>
<style>
 .fsPage2{
	width:98%; 
	}
	.dataTables_wrapper{
		min-height:80px;	
	}
	.fp_transferencia{
		display:none;	
	}
	.fp_efectivo{
		display:none;
	 }
	.fp_tipo_reserva{
		display:none;		
	}

#h_ span{
	float:right;
	margin:0;
	margin-right:10px;
	color:#FFF;
	border-radius:10px;
	font-size:20px;
	height:21px;
	width:21px;
	font-weight:bold;
	text-align:center;
	cursor:pointer;
}	
#h_ span:hover{
	background-color:#FFF;
	color:#000;
}
	
</style>
<form name="form_financiamiento" id="form_financiamiento" method="post">
 <div class="fsPage fsPage2" id="fp_financiamiento"  >
 
   <table width="100%" border="1">
    <tr>
      <td valign="top"><table  width="100%" border="1">
        <tr>
          <td><h2 id="h_">FINANCIAMIENTO</h2></td>
        </tr>
        <tr>
          <td><table id="tb_financiamiento" width="100%" border="1" class="display" style="border-spacing:2px;">
            <thead>
              <tr>
                <td align="center"><strong>Moneda</strong></td>
                <td align="center"><strong>Plazo</strong></td>
                <td align="center"><strong>% Enganche</strong></td>
                <td>&nbsp;</td>
              </tr>
            
            </thead>
            <tbody>
<?php

	$plan_fin= new PlanFinanciamiento($protect->getDBLink(),$_REQUEST);
	$data=$plan_fin->getListPlanesFinanciamientoGroup();
	foreach($data as $key =>$plan){
		$EncryptID=System::getInstance()->Encrypt(json_encode($plan));
		$info=base64_encode(json_encode($plan))
		
?> 
              <tr class="enganche_<?php echo $plan['moneda']?>"  id="<?php echo $info; ?>">
                <td align="center">&nbsp;<?php echo $plan['moneda']?></td>
                <td align="center"><?php echo $plan['plazo']?></td>
                <td align="center"><?php echo $plan['enganche']?></td>
                <td><a href="#" class="select_plan_fin" id="<?php echo $info ?>"><img src="images/plus.png"  /></a></td>
              </tr>
<?php
}
?>  
            </tbody>
          </table>
          </td>
        </tr>
      </table></td>
      </tr>
    <tr>
      <td align="center" valign="top">&nbsp;<button type="button" class="redButton" id="bt_close">Cerrar</button></td>
      </tr>
  
  </table>
 
  
</div>
</form>