<?php 
/*
Se encarga de manejar los descuentos x dessesitimiento 
y anulacion de una asesor y gerente
*/

class DescuentoComision{
	private static $db_link;
	public function __contruct($db_link){
		if ($db_link!=""){
			self::$db_link=$db_link; 
		}	
	}
	
	public function debitarActaAsesor($id_acta,$creador_por){
 
		$SQL="SELECT COUNT(*) AS total FROM `cxc_balance_asesor` WHERE idacta='".$id_acta."'";
		$rsx    = mysql_query($SQL);	
		$row=mysql_fetch_assoc($rsx);
		if ($row['total']>0){
			return array("mensaje"=>"Error, esta acta ya ha sido procesada!","valid"=>false);	
		} 
 
	 /*ACTUALIZO LOS CONTRATOS QUE NO SE LES REALIZO EL PAGO A TRAVEZ DEL SISTEMA DE NOMINA DE TESTAROSSA*/
		$SQL="UPDATE `actas_desistidos_anulados` SET 
				actas_desistidos_anulados.`comision_figs`=1 WHERE 
			CONCAT(actas_desistidos_anulados.`serie_contrato`,' ',actas_desistidos_anulados.`no_contrato`)
			IN (
			SELECT contrato FROM (
			SELECT 
				CONCAT(cc.serie_contrato,' ',cc.no_contrato) AS contrato 
			FROM  `actas_desistidos_anulados` AS aa
			INNER JOIN contratos AS cc ON (cc.serie_contrato=aa.serie_contrato AND cc.no_contrato = aa.no_contrato) 
			WHERE  aa.idacta='".$id_acta."' and aa.secuencia='".$secuencia."'  AND
			(SELECT    
				(SUM(movimiento_factura.MONTO)/contratos.valor_cuota) AS TOTAL_CUOTA 	
				FROM 
					`movimiento_caja`	
				INNER JOIN `movimiento_factura` ON (`movimiento_factura`.`CAJA_SERIE`=movimiento_caja.SERIE AND 
			movimiento_factura.`CAJA_NO_DOCTO`=movimiento_caja.NO_DOCTO)
				INNER JOIN contratos ON (contratos.serie_contrato=movimiento_caja.serie_contrato AND
			contratos.no_contrato=movimiento_caja.no_contrato) 
			WHERE  
				movimiento_caja.TIPO_DOC='RBC' AND 	
			movimiento_caja.ANULADO='N' 
			AND movimiento_factura.TIPO_MOV IN ('CUOTA') 
			 AND 
				movimiento_factura.serie_contrato=cc.serie_contrato AND  
				movimiento_factura.no_contrato=cc.no_contrato)<=2.98 
				AND ((SELECT COUNT(*) AS total FROM `pl_planillas_asesores` AS pl 
				WHERE  codigo_asesor=aa.codigo_asesor AND 
				pl.serie=cc.serie_contrato AND pl.contrato=cc.no_contrato) )<=0
				) AS cl ) ";
		mysql_query($SQL);
	 /*---------------------------------------------------------------------------*/
	   
   
		/*AGREGO LOS DESISTIDOS*/
		$SQL="SELECT 
				cc.serie_contrato,
				cc.no_contrato,
				CONCAT(ppa.`anio`,'-',ppa.`mes`,'-',cc.`codigo_asesor`) AS indice,
				SUM(ppa.comision) AS comision_total,
				ppa.*,
				aa.tipo,
				aa.capital_pendiente,
				aa.precio_neto
			FROM  `actas_desistidos_anulados` AS aa
			INNER JOIN `actas` ON (`actas`.id=aa.acta_id)		
			INNER JOIN contratos AS cc ON (cc.serie_contrato=aa.serie_contrato AND cc.no_contrato = aa.no_contrato)
			LEFT JOIN `pl_planillas_asesores` AS ppa ON (cc.serie_contrato=ppa.serie AND cc.no_contrato = ppa.contrato)
			WHERE  actas.id='".$id_acta."' AND `actas`.estatus=1 AND aa.tipo='D'  
			 
 			 AND    
			(SELECT    
				IF ((SUM(movimiento_factura.MONTO)/contratos.valor_cuota) IS NULL,0,(SUM(movimiento_factura.MONTO)/contratos.valor_cuota)) AS TOTAL_CUOTA 	
				FROM 
					`movimiento_caja`	
				INNER JOIN `movimiento_factura` ON (`movimiento_factura`.`CAJA_SERIE`=movimiento_caja.SERIE AND 
			movimiento_factura.`CAJA_NO_DOCTO`=movimiento_caja.NO_DOCTO)
				INNER JOIN contratos ON (contratos.serie_contrato=movimiento_caja.serie_contrato AND
			contratos.no_contrato=movimiento_caja.no_contrato) 
			WHERE  
				movimiento_caja.TIPO_DOC='RBC' AND 	
			movimiento_caja.ANULADO='N' 
			AND movimiento_factura.TIPO_MOV IN ('CUOTA') 
			 AND 
				movimiento_factura.serie_contrato=cc.serie_contrato AND  
				movimiento_factura.no_contrato=cc.no_contrato)<=2.98 
			GROUP BY `anio`,`mes`,`codigo_asesor`,`serie`,`contrato` ";
			 
		 $rsx    = mysql_query($SQL);
		 $datos_p=array(); 
		 
		 while($rowCM = mysql_fetch_assoc($rsx)){  
			 if (!isset($datos_p[$rowCM['indice']])){
				 $datos_p[$rowCM['indice']]=array(); 
			 }
			 array_push($datos_p[$rowCM['indice']],$rowCM);
		 }    
		  
		 /*AGREGO LOS ANULADOS*/ 
		$SQL="SELECT 
				cc.serie_contrato,
				cc.no_contrato,
				CONCAT(ppa.`anio`,'-',ppa.`mes`,'-',cc.`codigo_asesor`) AS indice,
				CONCAT(aa.`anio`,'-',aa.`mes`,'-',cc.`codigo_asesor`) AS indice2,
				SUM(ppa.comision) AS comision_total,
				ppa.*,
				aa.tipo,
				aa.capital_pendiente,
				aa.precio_neto
			FROM  `actas_desistidos_anulados` AS aa
			INNER JOIN `actas` ON (`actas`.id=aa.acta_id)	
			INNER JOIN contratos AS cc ON (cc.serie_contrato=aa.serie_contrato AND cc.no_contrato = aa.no_contrato)
			LEFT JOIN `pl_planillas_asesores` AS ppa ON (cc.serie_contrato=ppa.serie AND cc.no_contrato = ppa.contrato)
			WHERE  actas.id='".$id_acta."'  AND `actas`.estatus=1 AND aa.tipo='A'   
		 
 			GROUP BY `anio`,`mes`,`codigo_asesor`,`serie`,`contrato`  ";
		 
		 
		 $rsx    = mysql_query($SQL); 
		 while($rowCM = mysql_fetch_assoc($rsx)){   
			if (trim($rowCM['indice'])==""){
				$rowCM['indice']=$rowCM['indice2'];
			} 
			 if (!isset($datos_p[$rowCM['indice']])){
				 $datos_p[$rowCM['indice']]=array(); 
			 }
			 array_push($datos_p[$rowCM['indice']],$rowCM);
		 } 
		  
		 foreach($datos_p as $indice =>$datosX){
			 $sp=explode("-",$indice);
			 $mes         = $sp[1];
			 $tipo_cierre = "T";
			 $anio        = $sp[0];
			 $codigo_asesor_x=$sp[2];

			   $contratos_anulados="";	 
			   $c_anulados=array();
			   $capital_pendiente=0;
			   foreach($datosX as $xkey=> $xrow){
				   $contratos_anulados.="'".$xrow['serie_contrato']." ".$xrow['no_contrato']."',"; 
				   /*EN CASO DE QUE NO SEA ANULADO ENTONCES TOMAR EL CAPITAL PENDIENTE*/
				   if ($xrow['tipo']!="A"){
					   $capital_pendiente= $capital_pendiente+$xrow['capital_pendiente']; 
				   }else{
					   $capital_pendiente= $capital_pendiente+$xrow['precio_neto'];    
				   } 
				   array_push($c_anulados,$xrow);		   
			   } 
			   
			   $contratos_anulados=substr($contratos_anulados,0,strlen($contratos_anulados)-1);
				
				 $sql = "select fecha_inicio_ventas as fechaini, "
							.(mysql_real_escape_string($tipo_cierre)=="P"?"precierre_ventas":"fecha_fin_ventas"). " as fechafin
							from cierres
						   where mes = " .(int)$mes. "
							 and ano = " .(int)$anio;
			 
				  $result    = mysql_query($sql);
				  $rsPeriodo = mysql_fetch_array($result);
			 
				  /* Equipo de Asesores */
				$sql  = "select a.codigo_asesor,
								 concat(b.primer_nombre, ' ', b.segundo_nombre) as nombre,
								 concat(b.primer_apellido, ' ', b.segundo_apellido, ' ', b.apellido_conyuge) as apellidos,
								 a.idgrupos
							from sys_asesor a
					  inner join sys_personas b 
							  on a.id_nit = b.id_nit
						   where   a.codigo_asesor is not null   
							 and a.codigo_asesor='".$codigo_asesor_x."'
						   order by a.idgrupos, cast(a.codigo_asesor as unsigned)";
			 
				$rsData  = mysql_query($sql);
			 
			   while($row = mysql_fetch_assoc($rsData)){ 
				   /* contabilizacion de contratos activos */
				   $query    = "select 
										SUM(contratos.`no_productos`) AS conteo
								  from contratos_ventas as contratos
								 where codigo_asesor = ".(int)$row['codigo_asesor']."  and
							fecha_venta between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' 
							and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'  ";
								   
					 if($tipo_cierre=="T"){
						$query.=" AND (SELECT (CASE 
			 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
			 WHEN pc.id_jardin!='' THEN (SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin) END ) AS producto 
			 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
			  pc.serie_contrato=contratos.serie_contrato AND pc.no_contrato=contratos.no_contrato LIMIT 1)!='OSARIOS' "; 
					 }		
					if ($contratos_anulados!=""){
						$query.=" and CONCAT(contratos.serie_contrato,' ',contratos.no_contrato) not in (".$contratos_anulados.") ";
					}
					$query.=" and CONCAT(contratos.serie_contrato,' ',contratos.no_contrato) in (SELECT  CONCAT(pl.serie,' ',pl.contrato) AS ct FROM `pl_planillas_asesores` AS pl WHERE  codigo_asesor='".$codigo_asesor_x."' 
							AND anio='".$anio."' AND mes='".$mes."')";	
 				 
				   $result    = mysql_query($query);
				   $contratos = mysql_fetch_array($result);  
				
				   /* porcentaje de comisiones */
				   $qrPorcentaje = "select orden, porcentaje, rangoini, rangofin
									  from cm_comisiones_tbl
									 where ".(int)$contratos['conteo']." between rangoini and rangofin
									   and tipo = 1
									   and estatus = 1
									   AND tipo_cierre='". mysql_real_escape_string($tipo_cierre)."' ";
			 	 	   
				   $rsPorcentaje = mysql_query($qrPorcentaje);
				   $porcentaje   = mysql_fetch_assoc($rsPorcentaje); 
				  
 				   /* negocios con su comision */
				   $monto = 0;
				   $nuevo_porcentaje=$porcentaje['porcentaje'];
		 	 
				   if( $contratos['conteo'] > 0){ 
			 
					 $info=$this->opternerCalculoComision($anio,
														$mes,
														$tipo_cierre,
														$porcentaje,
														$rsPeriodo,
														$contratos_anulados,
														$codigo_asesor_x,
														$datosX);
											
					  $monto=$info['monto'];		 				  
				   }else{
					  $nuevo_porcentaje=$this->getPorcientoPagado($codigo_asesor_x,$anio,$mes);   
					 
				   	  $query    = "select 
										SUM(contratos.`no_productos`) AS conteo
								  from contratos_ventas as contratos
								 where codigo_asesor = ".(int)$row['codigo_asesor']."  and
							fecha_venta between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' 
							and '".mysql_real_escape_string($rsPeriodo['fechafin'])."'  ";
								   
					 if($tipo_cierre=="T"){
						$query.=" AND (SELECT (CASE 
			 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
			 WHEN pc.id_jardin!='' THEN (SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin) END ) AS producto 
			 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
			  pc.serie_contrato=contratos.serie_contrato AND pc.no_contrato=contratos.no_contrato LIMIT 1)!='OSARIOS' "; 
					 }		 
					$query.=" and CONCAT(contratos.serie_contrato,' ',contratos.no_contrato) in (SELECT  CONCAT(pl.serie,' ',pl.contrato) AS ct FROM `pl_planillas_asesores` AS pl WHERE  codigo_asesor='".$codigo_asesor_x."' 
							AND anio='".$anio."' AND mes='".$mes."')";	
						 
				 
				   $result    = mysql_query($query);
				   $contratos = mysql_fetch_array($result);  
				
				   /* porcentaje de comisiones */
				   $qrPorcentaje = "select orden, porcentaje, rangoini, rangofin
									  from cm_comisiones_tbl
									 where ".(int)$contratos['conteo']." between rangoini and rangofin
									   and tipo = 1
									   and estatus = 1
									   AND tipo_cierre='". mysql_real_escape_string($tipo_cierre)."' ";
			 	 	   
					   $rsPorcentaje = mysql_query($qrPorcentaje);
					   $porcentaje   = mysql_fetch_assoc($rsPorcentaje);  
	 
					   $info=$this->opternerCalculoComision($anio,
															$mes,
															$tipo_cierre,
															$porcentaje,
															$rsPeriodo,
															$contratos_anulados,
															$codigo_asesor_x,
															$datosX);					 					    
					   				
					   $monto=$info['monto']; 	
					  		 	 
				   } 
					 $nuevo_porcentaje=$info['escala']; 
					 
					 	
				  	 if (trim($contratos_anulados)!=""){
						 $squery="(SELECT SUM(comision) AS comisionp FROM `pl_planillas_asesores` AS pl WHERE
									pl.codigo_asesor=a.codigo_asesor AND pl.anio=a.anio AND pl.mes=a.mes AND tipo_cierre='P'
									AND CONCAT(pl.serie,' ',pl.contrato) NOT IN (".$contratos_anulados.")) AS precierre, ";
					 }else{
						 $squery=" 0 AS precierre, ";	  	 
					 }
					  $SQL="SELECT 
								SUM(a.monto) AS total_comi,
								(SELECT MAX(porcentaje) AS porcentaje FROM `pl_planillas_asesores` AS pl WHERE
								pl.codigo_asesor=a.codigo_asesor AND pl.anio=a.anio AND pl.mes=a.mes) AS porcentaje, ";
					  $SQL.=$squery;			
					  $SQL.="			
								SUM(IF(a.idconcepto = 2, a.monto,0)) AS bono_x_plan
							FROM cm_detplanilla_asesor_tbl a,
								sys_asesor b 
							WHERE a.codigo_asesor = b.codigo_asesor
							AND a.codigo_asesor='".$codigo_asesor_x."'  
							AND a.anio = '".$anio."'
							AND a.mes = '".$mes."'
							AND a.tipo_cierre IN  ('P','T') AND a.idconcepto IN (1,2)";	
				 
					  $rxpl = mysql_query($SQL);
					  $comi   = mysql_fetch_assoc($rxpl);	
					   
					 
					  $monto_desc=0;   
					  $monto_desc=$comi['total_comi'];  
					  /*
					  if ((round($info['monto'],2)==0) &&
					  	   (round($info['escala'],2)==0) &&
						   (round($info['bono_x_plan'],2)==0) &&
						   ($comi['precierre']==0)
						){
						  //$monto=$monto+$info['bono_x_plan']+$comi['precierre'];  
					  }else{
							$monto=$monto+$info['bono_x_plan']+$comi['precierre'];  
							$monto=$monto_desc-$monto;
					   }
					   */
						$monto=$monto+$info['bono_x_plan']+$comi['precierre'];  
						$monto=$monto_desc-$monto;
				 		   
					  $descripcion="";
					  if ($nuevo_porcentaje!=$comi['porcentaje']){
						  $descripcion="CE ".$comi['porcentaje'] ." A ".$nuevo_porcentaje." Cierre mes ".$mes." año ".$anio;
					  }  
				 
					  $no_docto=$this->getCorrelativo('ADC'); 
					  $cxc= new ObjectSQL();
					  $cxc->idacta=$id_acta;
					 // $cxc->secuencia_acta=$secuencia;
					  $cxc->codigo_asesor=$codigo_asesor_x; 
					  $cxc->no_docto=$no_docto['CORRELATIVO'];
					  $cxc->descripcion=$descripcion." ".str_replace("'","",$contratos_anulados);
					  $cxc->creado_por=$creador_por;
					  $cxc->fecha_registro="concat(curdate(),' ',CURTIME())";
					  $cxc->tipo_movimiento="ND";	
					  $cxc->monto=$monto; 		 
					  $cxc->setTable("cxc_balance_asesor");
					  $SQL=$cxc->toSQL("insert");    
				      mysql_query($SQL);
					   
					
				}
		   }
		
		  return array("mensaje"=>"acta procesada!","valid"=>true);	
		
	}
	/*APLICA LOS SALDOS ASESORES*/
	public function aplicarSaldo($idacta,$codigo_asesor,$descripcion,$creado_por,$tipo_movimiento,$monto){
		  $no_docto=$this->getCorrelativo('ADC'); 
		  $cxc= new ObjectSQL();
		  $cxc->idacta=$id_acta; 
		  $cxc->codigo_asesor=$codigo_asesor; 
		  $cxc->no_docto=$no_docto['CORRELATIVO'];
		  $cxc->descripcion=$descripcion;
		  $cxc->creado_por=$creador_por;
		  $cxc->fecha_registro="concat(curdate(),' ',CURTIME())";
		  $cxc->tipo_movimiento=$tipo_movimiento;	
		  $cxc->monto=$monto; 		 
		  $cxc->setTable("cxc_balance_asesor");
		  $SQL=$cxc->toSQL("insert");   
		  mysql_query($SQL);
		  return mysql_insert_id();
	}
	/*APLICA LOS SALDOS GERENTES*/
	public function aplicarSaldoGerentes($idacta,$codigo_gerente,$descripcion,$creado_por,$tipo_movimiento,$monto){
		  $no_docto=$this->getCorrelativo('ADC'); 
		  $cxc= new ObjectSQL();
		  $cxc->idacta=$id_acta; 
		  $cxc->codigo_gerente=$codigo_gerente; 
		  $cxc->no_docto=$no_docto['CORRELATIVO'];
		  $cxc->descripcion=$descripcion;
		  $cxc->creado_por=$creador_por;
		  $cxc->fecha_registro="concat(curdate(),' ',CURTIME())";
		  $cxc->tipo_movimiento=$tipo_movimiento;	
		  $cxc->monto=$monto; 		 
		  $cxc->setTable("cxc_balance_gerentes");
		  $SQL=$cxc->toSQL("insert");   
 		  mysql_query($SQL); 
		  
		  return mysql_insert_id();
	}	
	public function opternerCalculoComision($anio,
											$mes,
											$tipo_cierre,
											$porcentaje,
											$rsPeriodo,
											$contratos_anulados,
											$codigo_asesor_x,
											$datosX){
			 					 
			    
		  $qrComisiones = "select ".(int)$anio." as anio,
								  ".(int)$mes. " as mes,'".mysql_real_escape_string($tipo_cierre)."' as tipo_cierre,
								  codigo_asesor,
								  serie_contrato as serie,
								  no_contrato as contrato,
								  enganche,
								  (SELECT (CASE 
					 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
					 WHEN pc.id_jardin!='' THEN (SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin) END ) AS producto 
					 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
					  pc.serie_contrato=contratos.serie_contrato AND pc.no_contrato=contratos.no_contrato LIMIT 1) as producto,
								  
								  fecha_venta as fecha_ingreso,
								 round((((enganche-monto_capitalizado)/((precio_lista-monto_capitalizado)-descuento))*100),2) as por_enganche,
								 (((precio_lista-monto_capitalizado)-descuento)*tipo_cambio) AS precio_lista, 
								  cuotas,
								  ".(float)$porcentaje['porcentaje']." as porcentaje,
								  round( ( (((precio_lista-monto_capitalizado)-descuento)*tipo_cambio) * ".(float)$porcentaje['porcentaje']."/100),2) as comision,

							(SELECT COUNT(*) AS total 
								FROM 
							`pl_planillas_asesores` WHERE serie=serie_contrato 
							AND contrato=no_contrato AND `tipo_cierre`='P'
							AND anio='".(int)$anio."' AND mes='".(int)$mes."') as TOTAL_PRE,
							
							(SELECT pl_planillas_asesores.porcentaje
								FROM 
							`pl_planillas_asesores` WHERE serie=serie_contrato 
							AND contrato= no_contrato AND `tipo_cierre`='P'
							AND anio='".(int)$anio."' AND mes='".(int)$mes."' limit 1 ) as comision_precierre 						
						 
							 from 
								contratos_ventas as contratos
							where 
								
							codigo_asesor = '".$codigo_asesor_x."'  AND
								fecha_venta between '" .mysql_real_escape_string($rsPeriodo['fechaini'])."' and '".mysql_real_escape_string($rsPeriodo['fechafin'])."' ";
						
		  if ($contratos_anulados!=""){	  
		//	$qrComisiones.=" and CONCAT(contratos.serie_contrato,' ',contratos.no_contrato) not in (".$contratos_anulados.") "; 
		  }
		  $qrComisiones.=" and CONCAT(contratos.serie_contrato,' ',contratos.no_contrato) in (SELECT  CONCAT(pl.serie,' ',pl.contrato) AS ct FROM `pl_planillas_asesores` AS pl WHERE  codigo_asesor='".$codigo_asesor_x."' 
								AND anio='".$anio."' AND mes='".$mes."')";	
	 
	 
	 /*(contratos.cuotas >6 or ((SELECT (CASE 
					 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
					 WHEN pc.id_jardin!='' THEN (SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin) END ) AS producto 
					 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
					  pc.serie_contrato=contratos.serie_contrato AND pc.no_contrato=contratos.no_contrato LIMIT 1)='OSARIOS'))  
								 
								and*/
 
 		  $rsComisiones = mysql_query($qrComisiones);
		  $monto = 0;
		  $gtDiferido1 = 0;
		  $bono_x_plan=0; 
	 	  $escala=0;	
		  $total=mysql_num_rows($rsComisiones); //TOTAL DE CONTRATOS
		  while($rwComisiones = mysql_fetch_assoc($rsComisiones)){
			   
		  			/*VERIFICO SI EL CONTRATO ES UN DESISTIDO ENTONCES SOLO 
					CALCULO LA COMISION DEL CAPITAL PENDIENTE*/ 
				$next_item=false;	
			    foreach($datosX as $xkey=> $xrow){  
				   if (($xrow['serie_contrato']==$rwComisiones['serie']) && 
						($xrow['no_contrato']==$rwComisiones['contrato'])){
					   /*EN CASO DE QUE NO SEA ANULADO ENTONCES TOMAR EL CAPITAL PENDIENTE*/
					   if ($xrow['tipo']!="A"){ 
						   $rwComisiones['precio_lista']=$rwComisiones['precio_lista']-$xrow['capital_pendiente'];
						   $rwComisiones['comision']=($rwComisiones['precio_lista']*$rwComisiones['porcentaje'])/100;
						 
					   }else{
 							$rwComisiones['comision']=0; 
					    } 
					   $next_item=true;		   
					} 	   
				} 			  
				
				if ($next_item){ 
					$monto = $monto + $rwComisiones['comision'];
					continue;	 
				}  
			  
				if (($tipo_cierre=="T") && ($rwComisiones['producto']!='OSARIOS')){ 
			
					if ($rwComisiones['cuotas']<=6){		
						   $qrPorcentaje = "select orden, porcentaje, rangoini, rangofin
											  from cm_comisiones_tbl
											 where ".$rwComisiones['por_enganche']." between rangoini and rangofin
											   and tipo = 3  and estatus = 1
											   AND tipo_cierre='". mysql_real_escape_string($tipo_cierre)."' "; 
						   $rsPorcentaje = mysql_query($qrPorcentaje);
						   $porcentanje_int   = mysql_fetch_assoc($rsPorcentaje);	
							
						   /*SI E PORCENTAJE DE ENGANCHE PAGADO  ES MAYOR QUE 15% O es un 
						   contrato de contado aplico el bono por plan*/
						   if (mysql_num_rows($rsPorcentaje)>0){
						   //if (count($porcentanje_int)>0){ 		 
							   $qrPorcentaje = "select orden, porcentaje, rangoini, rangofin
												  from cm_comisiones_tbl
												 where ".$rwComisiones['cuotas']." between rangoini and rangofin
												   and tipo = 2
												   and estatus = 1
												   AND tipo_cierre='". mysql_real_escape_string($tipo_cierre)."' "; 
							   $rsPorcentaje = mysql_query($qrPorcentaje);
							   $comi_bono_por_plan   = mysql_fetch_assoc($rsPorcentaje);
							   
						
							 //  if (count($comi_bono_por_plan)>0){
							   if (mysql_num_rows($rsPorcentaje)>0){   
//								   $rwComisiones['porcentaje']=0;

								   $procentaje=$comi_bono_por_plan['porcentaje']-
																			$rwComisiones['comision_precierre'];			
								   $bono_x_plan=$bono_x_plan+round((($rwComisiones['precio_lista']*$procentaje)/100),2);	
								   
									/* Calculo si existe este contrato se a comisionado en el precierre */			
								   $rwComisiones['porcentaje']=0;//$procentaje;
								   $rwComisiones['comision']=0;//$bono_x_plan; 
							   }
						   }else{
							   /*SI EL PORCENTAJE DE ENGANCHE ES MENOR DEL 15% ENTOCNES NO COMISIONA DE ESTA VENTA*/ 								
								 
								$rwComisiones['porcentaje']= $rwComisiones['porcentaje']-$rwComisiones['comision_precierre'];
								
								
								if ($rwComisiones['porcentaje']>0){
									$rwComisiones['comision']=round((($rwComisiones['precio_lista']* $rwComisiones['porcentaje'])/100),2);
								}else{
									$rwComisiones['porcentaje']=0;
									$rwComisiones['comision']=0;
								}
						   }
									
					 //  print_r($rwComisiones);
			 
				   }else{
						 
					   $comision_x=0;
						/*EN CASO DE LOS CONTRATOS A CREDITO*/
						if ($rwComisiones['TOTAL_PRE']>0){ 
						 // print_r($rwComisiones);
						  /* Calculo si existe este contrato se a comisionado en el precierre */	  
						  $comision_x=$rwComisiones['comision']; 	 
						  $rwComisiones['porcentaje']=$rwComisiones['porcentaje']-$rwComisiones['comision_precierre'];
						  $rwComisiones['comision']=round((($rwComisiones['precio_lista']*$rwComisiones['porcentaje'])/100),2);  
					//	  print_r($rwComisiones);
						 
						  if (($rwComisiones['porcentaje']<=0)  && ($total>1)){
						  //	$rwComisiones['comision']=$rwComisiones['monto_precierre'];  
						  }   					   
					   }					   
					   
				   }
				
				} 
				/*EN CASO DE SER UN PRODUCTO OSARIO Y SEA EL CIERRE TOTAL*/
				if (($tipo_cierre=="T") && ($rwComisiones['producto']=='OSARIOS')){ 
					   $procentaje=$rwComisiones['porcentaje']-$rwComisiones['comision_precierre']; 	
					   /*Esta condicion ajusta un error en los contratos osarios que se estaban tomando
					   en cuenta como parte del bono por plan*/
					   if (($rwComisiones['anio']=='2015') && ($rwComisiones['mes']>9)){		 
	   					   $rwComisiones['porcentaje']=$procentaje;
						   $rwComisiones['comision']=round((($rwComisiones['precio_lista']*$procentaje)/100),2);//$comision; 	 
					   }else{
						  
						   if (($rwComisiones['anio']=='2015') && ($rwComisiones['mes']!=5)){
							   $bono_x_plan=$bono_x_plan+round((($rwComisiones['precio_lista']*$procentaje)/100),2);
							   $rwComisiones['porcentaje']=0;
							   $rwComisiones['comision']=0; 	
						   }
					   }   
				   //	   
					  // $rwComisiones['porcentaje']=$procentaje;
					  // $rwComisiones['comision']=round((($rwComisiones['precio_lista']*$procentaje)/100),2);//$comision; 	 
				}	     
				$monto = $monto + $rwComisiones['comision']; 	
				
				if ($rwComisiones['porcentaje']>$escala){
					$escala=$rwComisiones['porcentaje'];	
				}  
				
		  } 	 
 		 $data=array("monto"=>$monto,"bono_x_plan"=>$bono_x_plan,"escala"=>$escala);
		  
		 return $data; 
		  
	}
	/*Optiene el porcentaje pagado de una comision antigua*/
	public function getPorcientoPagado($codigo_asesor,$anio,$mes){
		   $query    = "select 
								MAX(ppa.porcentaje) AS porcentaje
						  from contratos_ventas as contratos
						  INNER JOIN `pl_planillas_asesores` AS ppa ON 
						  	(contratos.serie_contrato=ppa.serie AND contratos.no_contrato = ppa.contrato)
						 where contratos.codigo_asesor = '".$codigo_asesor."'   ";
			 
				$query.=" AND (SELECT (CASE 
	 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
	 WHEN pc.id_jardin!='' THEN (SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin) END ) AS producto 
	 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
	  pc.serie_contrato=contratos.serie_contrato AND pc.no_contrato=contratos.no_contrato LIMIT 1)!='OSARIOS' ";  
			$query.=" and  ppa.codigo_asesor='".$codigo_asesor."' AND ppa.anio='".$anio."' AND ppa.mes='".$mes."' ";	
			 
		   $result    = mysql_query($query);
		   $contratos = mysql_fetch_assoc($result);
		   $porcent=0;   
		   if ($contratos['porcentaje']>0){ 
			   $porcent=$contratos['porcentaje'];		   
		   } 
		   return $porcent;		
	}
	public function getCorrelativo($tipo_doc){ 
		$SQL="LOCK TABLES correlativo_doc WRITE;";
		mysql_query($SQL);	 
		$cor = new ObjectSQL(); 
		$cor->CORRELATIVO="(CORRELATIVO+1)"; 
		$cor->setTable('correlativo_doc'); 
		$SQL=$cor->toSQL('update',"where TIPO_DOC='".$tipo_doc."'");
		mysql_query($SQL);		
		$SQL="SELECT (`CORRELATIVO`) AS CORRELATIVO 
			FROM `correlativo_doc` WHERE `TIPO_DOC`='".$tipo_doc."' "; 
		$rs=mysql_query($SQL);
		$row=mysql_fetch_assoc($rs); 
		$SQL="UNLOCK TABLES;"; 
		mysql_query($SQL);		  
		return $row;
	}  
 
}

?>