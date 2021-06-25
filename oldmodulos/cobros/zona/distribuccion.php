<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
 
	
	if (validateField($_REQUEST,"optenerDistribucion") ){
		$SQL="SELECT SECTOR,LongLact,COUNT(*) AS TOTAL FROM (SELECT    
	sys_status.descripcion AS estatus,
	contratos.no_productos AS PRODUCTOS,
	contratos.fecha_venta  ,
	((contratos.precio_lista-contratos.descuento)*contratos.tipo_cambio) AS MONTO ,
	contratos.descuento,
	contratos.enganche,
	CONCAT(cli.primer_nombre,' ',cli.segundo_nombre,
	' ',cli.`primer_apellido`,' ',cli.`segundo_apellido`) AS nombre_cliente,
	CONCAT(sys_personas.primer_nombre,' ',sys_personas.segundo_nombre,
	' ',sys_personas.`primer_apellido`,' ',sys_personas.`segundo_apellido`) AS asesor,
	CONCAT(gerente.primer_nombre,' ',gerente.segundo_nombre,
	' ',gerente.`primer_apellido`,' ',gerente.`segundo_apellido`) AS gerente, 
	CONCAT(serie_contrato,' ',no_contrato) AS contrato,
	(SELECT GROUP_CONCAT(CONCAT('(',IFNULL(sys_telefonos.`area`,''),') ',IFNULL(sys_telefonos.numero,''),' Ext:',IFNULL(sys_telefonos.extencion,''))) AS PHONE 
	FROM `sys_telefonos` WHERE id_nit=cli.id_nit ) AS telefono,
	(SELECT (CASE 
			 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
			 WHEN pc.id_jardin!='' THEN 
			 CONCAT((SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin),', ',id_jardin,'-',id_fases,'-',lote
			) END ) AS producto 
			 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
			  pc.serie_contrato=contratos.serie_contrato AND pc.no_contrato=contratos.no_contrato LIMIT 1) AS producto,
			(SELECT
			sys_sector.`descripcion` sector
			  FROM 
			`sys_direcciones` 
			INNER JOIN `sys_ciudad` ON (sys_ciudad.`idciudad`=sys_direcciones.idciudad)
			INNER JOIN `sys_provincia` ON (sys_provincia.`idprovincia`=sys_direcciones.idprovincia)
			INNER JOIN `sys_sector` ON (sys_sector.`idsector`=sys_direcciones.idsector)
			WHERE id_nit=cli.id_nit  LIMIT 1) AS SECTOR,
			(SELECT
			CONCAT (sys_sector.longitud, ',' , sys_sector.latitud)
			  FROM 
			`sys_direcciones` 
			INNER JOIN `sys_ciudad` ON (sys_ciudad.`idciudad`=sys_direcciones.idciudad)
			INNER JOIN `sys_provincia` ON (sys_provincia.`idprovincia`=sys_direcciones.idprovincia)
			INNER JOIN `sys_sector` ON (sys_sector.`idsector`=sys_direcciones.idsector)
			WHERE id_nit=cli.id_nit LIMIT 1 )AS LongLact
		       
							
FROM contratos 
INNER JOIN `sys_status` ON (sys_status.id_status=contratos.estatus) 
INNER JOIN `sys_asesor` ON (`sys_asesor`.codigo_asesor=contratos.codigo_asesor)
INNER JOIN `sys_personas` ON (sys_personas.id_nit=sys_asesor.id_nit)              
INNER JOIN `sys_personas`  AS cli ON (cli.id_nit=contratos.id_nit_cliente)            
INNER JOIN `sys_gerentes_grupos` ON (`sys_gerentes_grupos`.codigo_gerente_grupo=sys_asesor.codigo_gerente_grupo)
INNER JOIN `sys_personas` AS gerente ON (gerente.id_nit=sys_gerentes_grupos.id_nit)            
WHERE  
	1=1 ";

//contratos.estatus IN (1,13,20,54) 
		if (validateField($_REQUEST,"gerente") ){
			$SQLi="";
			foreach($_REQUEST['gerente'] as $key =>$val){
				$valor=System::getInstance()->Decrypt($val);
				$oficial[$valor]=$valor;
				$SQLi.=" contratos.codigo_gerente='".$valor."' or";
			}	
			$SQLi=" and ( ".substr($SQLi,0,strlen($SQLi)-2).") ";
			$SQL.=$SQLi;	
		}
		if (validateField($_REQUEST,"productos") ){
			 
			$prod="";
			if ($_REQUEST['productos']=="SV"){
				$SQL.=" AND (SELECT (CASE 
				 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
				 WHEN pc.id_jardin!='' THEN 
				 CONCAT((SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin),', ',id_jardin,'-',id_fases,'-',lote
				) END ) AS producto 
				 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
				  pc.serie_contrato=contratos.serie_contrato AND pc.no_contrato=contratos.no_contrato LIMIT 1) LIKE '%SERVICIO%'";
			}else{
				$SQL.=" AND ((SELECT (CASE 
				 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
				 WHEN pc.id_jardin!='' THEN 
				 CONCAT((SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin),', ',id_jardin,'-',id_fases,'-',lote
				) END ) AS producto 
				 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
				  pc.serie_contrato=contratos.serie_contrato AND pc.no_contrato=contratos.no_contrato LIMIT 1) NOT LIKE '%SERVICIO%'	
					OR 
					
					  (SELECT (CASE 
								 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
								 WHEN pc.id_jardin!='' THEN 
								 CONCAT((SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin),', ',id_jardin,'-',id_fases,'-',lote
								) END ) AS producto 
								 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
								  pc.serie_contrato=contratos.serie_contrato AND pc.no_contrato=contratos.no_contrato LIMIT 1) IS NULL
								  ) ";				
			}
			   
		}		
		 
			  
		$SQL.=") AS ct GROUP BY SECTOR";
		 
		 
			$rs=mysql_query($SQL);
			$data=array();
			while($row=mysql_fetch_object($rs)){
				array_push($data,$row);
			}
		echo json_encode($data);
		exit;
	}
	


	SystemHtml::getInstance()->addTagScript("script/jquery.dataTables.js"); 
	SystemHtml::getInstance()->addTagScript("script/jquery.form.js");
	SystemHtml::getInstance()->addTagScript("script/jquery.validate.js"); 
	SystemHtml::getInstance()->addTagScript("script/jquery.jqplot.min.js");  
	SystemHtml::getInstance()->addTagScript("script/Class.js"); 
	SystemHtml::getInstance()->addTagScript("script/select2.min.js");  
	SystemHtml::getInstance()->addTagStyle("css/smoothness/jquery.ui.combogrid.css");
	SystemHtml::getInstance()->addTagScript("script/jquery.ui.combogrid-1.6.3.js"); 
	
	SystemHtml::getInstance()->addTagScriptByModule("class.DistribuccionMapa.js"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.TMap.js"); 
	
 	SystemHtml::getInstance()->addTagScript("script/jquery.timeentry.min.js");
	   
	SystemHtml::getInstance()->addTagScriptByModule("class.COperacion.js","caja"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.AbonoPersona.js","caja"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.PagoReserva.js","caja"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.PagoContrato.js","caja"); 
	SystemHtml::getInstance()->addTagScriptByModule("class.FormaPago.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.TDocSerieRVenta.js","caja");
	SystemHtml::getInstance()->addTagScriptByModule("class.CFactura.js","caja");
	
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Empresa.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Persona.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Direccion.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Telefono.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Email.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Referencia.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Contactos.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.Referidos.js");
	SystemHtml::getInstance()->addTagScript("script/persona/Class.ModuloPersona.js");
	
	SystemHtml::getInstance()->addTagScript("script/Class.direcciones.js");
	SystemHtml::getInstance()->addTagScript("script/Class.phone.js");
	SystemHtml::getInstance()->addTagScript("script/Class.empresa.js");
	SystemHtml::getInstance()->addTagScript("script/Class.email.js");
	SystemHtml::getInstance()->addTagScript("script/Class.reference.js");
	SystemHtml::getInstance()->addTagScript("script/Class.contactos.js");
	SystemHtml::getInstance()->addTagScript("script/Class.AsesoresTree.js");
	SystemHtml::getInstance()->addTagScript("script/Class.Referidos.js"); 
	SystemHtml::getInstance()->addTagScript("script/jquery.formatCurrency-1.4.0.js");	  
	
	/*BOOSTRAP SCRIPT*/
	SystemHtml::getInstance()->addTagScript("script/bootstrap/js/bootstrap.min.js");
	
	SystemHtml::getInstance()->addTagStyle("css/jquery.ptTimeSelect.css"); 
	SystemHtml::getInstance()->addTagStyle("css/demo_page.css");
	SystemHtml::getInstance()->addTagStyle("css/demo_table.css");
	SystemHtml::getInstance()->addTagStyle("script/jquery.jqplot.min.css");
	
	SystemHtml::getInstance()->addTagStyle("css/bootstrap/css/bootstrap.min.css");
	SystemHtml::getInstance()->addTagStyle("css/select2-bootstrap.css");
	SystemHtml::getInstance()->addTagStyle("css/select2.css");
	
	
	/*Cargo el Header*/
	SystemHtml::getInstance()->addModule("header");
	SystemHtml::getInstance()->addModule("header_logo");
	
	
	$zonas= new Zonas($protect->getDBLink()); 
?> 
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
<script type="text/javascript" src="resource/OpenLayers/OpenLayers.js"></script>
<style>
.dataTables_wrapper{
	font-size:12px;	
}
</style>
<script> 

var _dashboard= new DistribuccionMapa("content_dialog");
 
$(document).ready(function(){ 
	_dashboard.viewOnMap('main_map_zona'); 
});

 
</script>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:0px;padding:0px">
 
  <tr>
    <td align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="450"><span style="font-size: 20px">DISTRIBUCCION</span></td>
        </tr>
      <tr>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><table width="100" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="50"><strong>PRODUCTO</strong></td>
                <td><select name="productos" id="productos" style="width:300px;">
                  <option value="" selected="selected">Todos</option>
                  <option value="SV">SERVICIO DE VELACION</option>
                  <option value="PP">PRODUCTOS PARCELAS</option> 
                </select></td>
                <td rowspan="2">&nbsp;</td>
                <td rowspan="2"><input type="submit" name="bt_distribuccion" id="bt_distribuccion" value="Filtrar" class="btn btn-default btn-sm"  /></td>
              </tr>
              <tr>
                <td><strong>GERENTE</strong></td>
                <td><select name="gerentes" size="1" multiple="multiple" id="gerentes" style="width:300px;">
                  <option value="">Todos</option>
                  <?php 
                
                $SQL="SELECT 
					`codigo_gerente_grupo` as codigo_gerente,
					CONCAT(gerente.primer_nombre,' ',gerente.segundo_nombre,
					' ',gerente.`primer_apellido`,' ',gerente.`segundo_apellido`) AS gerente
					 FROM `sys_gerentes_grupos`
					 INNER JOIN `sys_personas` AS gerente ON (gerente.id_nit=sys_gerentes_grupos.id_nit)            
					WHERE sys_gerentes_grupos.`status`=1";
                $rs=mysql_query($SQL);
                while($row=mysql_fetch_assoc($rs)){
                $encriptID=System::getInstance()->Encrypt($row['codigo_gerente']);
                ?>
                  <option value="<?php echo $encriptID?>"><?php echo $row['gerente']?></option>
                  <?php } ?>
                </select></td>
              </tr>
            </table></td>
            <td width="200" align="center"><table width="100" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><strong>CANTIDAD:</strong></td>
                <td width="100" id="cantidad_">&nbsp;</td>
                <td width="1020" id="cantidad_">&nbsp;</td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>
         <div id="main_map_zona" style="height:500px;width:100%"></div>
          </td>
      </tr>
    </table></td>
  </tr>
 
          <tr>
            <td valign="top">&nbsp;</td>
          </tr>
  </table>
 <div id="content_dialog" ></div>