<?php
include("class/lib/excel/class_excel_ms.php");

$ms_db = mssql_connect("DESARROLLO-PC","memorial","A123456a");
mssql_select_db("SERVICIOSM",$ms_db);

$fecha_inicio=date('Y/m/01');
$fecha_fin=date('Y/m/d');

/*TODITO COBROS */
$SQL="SELECT 
  (dbo.VIEW_AUDITORES.CTT_COD_TD + ' ' + dbo.VIEW_AUDITORES.CTT_CODIGO) AS CONTRATO,
  MAE_CC_CLIENTES.CLI_DESCRIPCION AS NOMBRE_CLIENTE,
  MAE_TRABAJADORES.TRA_DESCRIPCION AS OFICIAL_COBROS,
  VIEW_AUDITORES.COBRADOR,
  VIEW_AUDITORES.PRODUCTO,
  dbo.VIEW_AUDITORES.FECHA_VENTA,
   PAGOS_CUOTAS.VTA_FECHA AS FECHA_PAGO_CUOTA,
  ((
  SELECT 
  MAX (PRO_CONTRATOS_DEUDAS.DEU_MONTO + PRO_CONTRATOS_DEUDAS.DEU_INTERES_FINANCIAMIENTO + PRO_CONTRATOS_DEUDAS.DEU_INTERES + PRO_CONTRATOS_DEUDAS.DEU_ALICUOTA)
FROM
  PRO_CONTRATOS_DEUDAS
WHERE
  PRO_CONTRATOS_DEUDAS.DEU_COD_TD = VIEW_AUDITORES.CTT_COD_TD AND 
  PRO_CONTRATOS_DEUDAS.DEU_COD_CONTRATO = VIEW_AUDITORES.CTT_CODIGO AND
  PRO_CONTRATOS_DEUDAS.DEU_COD_TIPO_DOCUMENTO=PAGOS_CUOTAS.VTA_TIPO_DOC_APLICA AND
  PRO_CONTRATOS_DEUDAS.DEU_NUMERO=PAGOS_CUOTAS.VTA_NRO_DOC_APLICA
  )* VIEW_AUDITORES.TASA) AS MONTO_CUOTA,  
  dbo.PAGOS_CUOTAS.MONTO_NETO as MONTO_COBRADO,
  'CUOTAS' AS TIPO
FROM
  MAE_CC_CLIENTES
  INNER JOIN dbo.VIEW_AUDITORES ON (MAE_CC_CLIENTES.CLI_CODIGO = dbo.VIEW_AUDITORES.CLI_CODIGO)
  INNER JOIN MAE_TRABAJADORES ON (MAE_CC_CLIENTES.CLI_PROMOTOR = MAE_TRABAJADORES.TRA_LOGIN)
  INNER JOIN dbo.PAGOS_CUOTAS ON (dbo.VIEW_AUDITORES.CTT_COD_TD = dbo.PAGOS_CUOTAS.VTA_COD_TD)
  AND (dbo.VIEW_AUDITORES.CTT_CODIGO = dbo.PAGOS_CUOTAS.VTA_COD_PROYECTO)
WHERE
  CTT_COD_TD IN ('CM','CP','CT','CO') AND 
  (PAGOS_CUOTAS.VTA_FECHA >= '".$fecha_inicio."' AND 
  PAGOS_CUOTAS.VTA_FECHA <= '".$fecha_fin." 11:59 PM' OR 
  PAGOS_CUOTAS.VTA_FECHAL_ULT_MODIF >= '".$fecha_inicio."' AND 
  PAGOS_CUOTAS.VTA_FECHAL_ULT_MODIF <= '".$fecha_fin." 11:59 PM' AND 
  PAGOS_CUOTAS.VTA_FECHA < '".$fecha_fin."' AND 
  PAGOS_CUOTAS.VTA_IVA_RETENIDO <> 0) AND 
  PAGOS_CUOTAS.VTA_TIPO = 3  
  
  UNION

    SELECT 
  (dbo.VIEW_AUDITORES.CTT_COD_TD + ' ' + dbo.VIEW_AUDITORES.CTT_CODIGO) AS CONTRATO,
  MAE_CC_CLIENTES.CLI_DESCRIPCION AS NOMBRE_CLIENTE,
  MAE_TRABAJADORES.TRA_DESCRIPCION AS OFICIAL_COBROS,
  VIEW_AUDITORES.COBRADOR,
  VIEW_AUDITORES.PRODUCTO,
  dbo.VIEW_AUDITORES.FECHA_VENTA,
   PAGOS_CUOTAS.VTA_FECHA AS FECHA_PAGO_CUOTA,
  ((
  SELECT 
  MAX (PRO_CONTRATOS_DEUDAS.DEU_MONTO + PRO_CONTRATOS_DEUDAS.DEU_INTERES_FINANCIAMIENTO + PRO_CONTRATOS_DEUDAS.DEU_INTERES + PRO_CONTRATOS_DEUDAS.DEU_ALICUOTA)
FROM
  PRO_CONTRATOS_DEUDAS
WHERE
  PRO_CONTRATOS_DEUDAS.DEU_COD_TD = VIEW_AUDITORES.CTT_COD_TD AND 
  PRO_CONTRATOS_DEUDAS.DEU_COD_CONTRATO = VIEW_AUDITORES.CTT_CODIGO AND
  PRO_CONTRATOS_DEUDAS.DEU_COD_TIPO_DOCUMENTO=PAGOS_CUOTAS.VTA_TIPO_DOC_APLICA AND
  PRO_CONTRATOS_DEUDAS.DEU_NUMERO=PAGOS_CUOTAS.VTA_NRO_DOC_APLICA
  )* VIEW_AUDITORES.TASA) AS MONTO_CUOTA,  
   PAGOS_CUOTAS.MONTO_NETO as MONTO_COBRADO,
  'ABONO CAPITAL' AS TIPO
FROM
  MAE_CC_CLIENTES
  INNER JOIN dbo.VIEW_AUDITORES ON (MAE_CC_CLIENTES.CLI_CODIGO = dbo.VIEW_AUDITORES.CLI_CODIGO)
  INNER JOIN MAE_TRABAJADORES ON (MAE_CC_CLIENTES.CLI_PROMOTOR = MAE_TRABAJADORES.TRA_LOGIN)
  INNER JOIN dbo.PAGOS_ABONO_CAPITAL as PAGOS_CUOTAS ON (dbo.VIEW_AUDITORES.CTT_COD_TD = PAGOS_CUOTAS.VTA_COD_TD)
  AND (dbo.VIEW_AUDITORES.CTT_CODIGO = PAGOS_CUOTAS.VTA_COD_PROYECTO)
WHERE
  CTT_COD_TD IN ('CM','CP','CT','CO') AND 
  (PAGOS_CUOTAS.VTA_FECHA >= '2014/05/01' AND 
  PAGOS_CUOTAS.VTA_FECHA <= '".$fecha_fin." 11:59 PM' OR 
  PAGOS_CUOTAS.VTA_FECHAL_ULT_MODIF >= '".$fecha_inicio."' AND 
  PAGOS_CUOTAS.VTA_FECHAL_ULT_MODIF <= '".$fecha_fin." 11:59 PM' AND 
  PAGOS_CUOTAS.VTA_FECHA < '".$fecha_fin."' AND 
  PAGOS_CUOTAS.VTA_IVA_RETENIDO <> 0)  ";
  
$file_name="TODITORCOBROS_".str_replace("/","-",$fecha_inicio)." AL ".str_replace("/","-",$fecha_fin).'.csv';
//writeCSVFromDB($ms_db,$SQL,$file_name);

$address=array();
array_push($address,array("email"=>"jose.ramos@gpmemorial.com","name"=>"Jose Gregorio Ramos"));
array_push($address,array("email"=>"manuel.encarnacion@gpmemorial.com","name"=>"Manuel Encarnacion"));

//sendMailer("noreplay@gpmemorial.com",'GPMEMORIAL :: TODITO COBROS ('.date('Y-m-d').')',$address,$file_name,"Reporte Todito Cobros","Reporte Todito Cobros");

/////////////////FIN TODITO COBROS////////////////////////////



/* META DE COBRO Y LO NO COBRADO POR DIA*/
$SQL="SELECT 
   (SELECT  SUM(PAGOS_CUOTAS_1.[MONTO_NETO]) AS Expr1
    FROM            dbo.MAE_CC_CLIENTES AS MAE_CC_CLIENTES_1 INNER JOIN
                                dbo.VIEW_AUDITORES AS VIEW_AUDITORES_1 ON MAE_CC_CLIENTES_1.CLI_CODIGO = VIEW_AUDITORES_1.CLI_CODIGO INNER JOIN
                                dbo.MAE_TRABAJADORES AS MAE_TRABAJADORES_1 ON MAE_CC_CLIENTES_1.CLI_PROMOTOR = MAE_TRABAJADORES_1.TRA_LOGIN INNER JOIN
                                dbo.PAGOS_CUOTAS AS PAGOS_CUOTAS_1 ON VIEW_AUDITORES_1.CTT_COD_TD = PAGOS_CUOTAS_1.VTA_COD_TD AND 
                                VIEW_AUDITORES_1.CTT_CODIGO = PAGOS_CUOTAS_1.VTA_COD_PROYECTO
    WHERE        (VIEW_AUDITORES_1.CTT_COD_TD IN ('CM', 'CP', 'CT', 'CO')) AND (PAGOS_CUOTAS_1.VTA_FECHA >= '".$fecha_inicio."') AND 
                                (PAGOS_CUOTAS_1.VTA_FECHA <= '".date("Y/m/t", strtotime($fecha_fin))." 11:59 PM') AND (PAGOS_CUOTAS_1.VTA_TIPO = 3) AND 
                                (VIEW_AUDITORES_1.CTT_COD_TD + ' ' + VIEW_AUDITORES_1.CTT_CODIGO = dbo.Y_METAS_DE_COBRO.CONTRATO) OR
                                (VIEW_AUDITORES_1.CTT_COD_TD IN ('CM', 'CP', 'CT', 'CO')) AND (PAGOS_CUOTAS_1.VTA_FECHA < '2014/05/31') AND 
                                (PAGOS_CUOTAS_1.VTA_TIPO = 3) AND 
                                (VIEW_AUDITORES_1.CTT_COD_TD + ' ' + VIEW_AUDITORES_1.CTT_CODIGO = dbo.Y_METAS_DE_COBRO.CONTRATO) AND 
                                (PAGOS_CUOTAS_1.VTA_FECHAL_ULT_MODIF >= '".$fecha_inicio."') AND (PAGOS_CUOTAS_1.VTA_FECHAL_ULT_MODIF <= '".date("Y/m/t", strtotime($fecha_fin))." 11:59 PM') AND 
                                (PAGOS_CUOTAS_1.VTA_IVA_RETENIDO <> 0)) AS MONTO_COBRADO, 
  
	  [TIPO_CUOTA]
      ,[CONTRATO]
      ,[MONTO_PENDIENTE]
      ,[MONTO_CUOTA]
      ,[MOTORIZADO]
      ,[NOMBRE_CLIENTE]
      ,[PRODUCTO]
      ,[OFICIAL]
      ,[FECHA_CUOTA]
      ,[MONTO TOTAL]
  FROM [SERVICIOSM].[dbo].[Y_METAS_DE_COBRO]
  ORDER BY FECHA_CUOTA";
 
 
 echo $SQL;
 
 exit;
//echo date("Y/m/t", strtotime($fecha_fin)); 
  
$file_name="METAS_POR_DIA_".str_replace("/","-",$fecha_inicio)." AL ".str_replace("/","-",$fecha_fin).'.csv';
writeCSVFromDB($ms_db,$SQL,$file_name);

$address=array();
array_push($address,array("email"=>"jose.ramos@gpmemorial.com","name"=>"Jose Gregorio Ramos"));
array_push($address,array("email"=>"manuel.encarnacion@gpmemorial.com","name"=>"Manuel Encarnacion"));

//sendMailer("noreplay@gpmemorial.com",'GPMEMORIAL :: METAS POR DIA COBRO ('.date('Y-m-d').')',$address,$file_name,"Metas por dia Cobros","Metas por dia Cobros");




function writeCSVFromDB($db_link,$SQL,$file_name){

	$rs=mssql_query($SQL,$db_link);
	$fp = fopen($file_name, 'w');
	$i=0;
	while($row=mssql_fetch_assoc($rs)){
	
		if ($i==0){
			$fields=array();
			foreach($row as $key=>$val){
				array_push($fields,$key);
			}
			fputcsv($fp, $fields);		
			$i++;	
			echo "[-]::Importando datos\n";
		}
	
		$fields=array();
		foreach($row as $key=>$val){
			array_push($fields,$val);
		}
		fputcsv($fp, $fields);
	}	
	fclose($fp);
	echo "[-]::Proceso Terminado!\n";

}

function sendMailer($from,$name,$address,$file,$body,$mensaje){
	echo "[-]::Procediendo a enviar e-mail\n";
	require_once 'class/lib/phpMail/PHPMailerAutoload.php';	
	$mail = new PHPMailer(); 
	$mail->IsSMTP();
	$mail->Host       = "mail.yourdomain.com"; 
	//$mail->SMTPDebug  = 1;   
	$mail->SMTPAuth   = true;                  
	$mail->SMTPSecure = "tls";               
	$mail->Host       = "mirgpmemorial.ipower.com";    
	$mail->Port       = 587;                  
	$mail->Username   = "noreply@gpmemorial.com";
	$mail->Password   = "H31pd3sk";           
	 
	$mail->setFrom($from,$name); 
	$mail->addReplyTo('noreply@gpmemorial.com', 'GPMEMORIAL :: AUTO');
 
	foreach($address as $value){
		$mail->addAddress($value['email'], $value['name']);
	}
	 
	$mail->Subject = $body;
	$mail->msgHTML($mensaje); 
	$mail->AltBody = $body; 
	$mail->addAttachment($file);

	if (!$mail->send()) {
		echo "[-]::Mailer Error: " . $mail->ErrorInfo;
	} else {
		echo "[-]::Message sent!";
	}	
}

?>