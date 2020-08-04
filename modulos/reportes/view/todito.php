<?php


header('Content-type:application/xls');
header('Content-Disposition: attachment; filename=todito.xls');
$query="SELECT DISTINCT a.em_id AS empresa, CONCAT(a.serie_contrato,' ',a.no_contrato) AS contrato,CONCAT(i.primer_nombre,' ',i.segundo_nombre,' ',i.primer_apellido,' ',i.segundo_apellido) AS cliente, i.fecha_nacimiento, CONCAT(d.primer_nombre,' ',d.segundo_nombre,' ',d.primer_apellido,' ',d.segundo_apellido) AS asesor, CONCAT(e.primer_nombre,' ',e.segundo_nombre,' ',e.primer_apellido,' ',e.segundo_apellido) AS gerente, IF(o.serv_descripcion IS NULL ,CONCAT(g.jardin,' ', h.fase),o.serv_descripcion) AS nombre_producto, a.fecha_venta, a.fecha_primer_pago, k.fecha_ultimo_pago, a.dia_pago, TO_DAYS(UTC_DATE()) - IFNULL(TO_DAYS(k.fecha_ultimo_pago),0) AS dias_no_pago, a.precio_lista, a.descuento, a.precio_neto, a.enganche, a.porc_enganche, a.cuotas, a.valor_cuota, k.cuota_pagada, a.tipo_cambio, a.mes, a.ano AS anio, a.tipo_moneda, j.descripcion AS estatus, CONCAT('Avenida ',l.avenida,' Calle ',l.calle,' No. ',l.numero,' Manzana ',l.manzana,' Zona ',l.zona) AS direc_cobro, l.departamento AS apartamento, l.residencia_colonia_condominio, l.referencia, m.descripcion AS municipio, n.descripcion AS departamento, tel1.numero AS celular, tel2.numero AS laboral, p.cmc_descripcion AS forma_pago, IF(a.tipo_moneda='DOLARES',ROUND(k.valor_cuota*a.tipo_cambio,2),ROUND(k.valor_cuota,2)) AS saldo_0_30, IF(a.tipo_moneda='DOLARES',ROUND(IF((k.a_pagar >1),k.valor_cuota * a.tipo_cambio,0),2),ROUND(IF((k.a_pagar >1),k.valor_cuota,0),2)) AS saldo_31_60, IF(a.tipo_moneda='DOLARES',ROUND(IF((k.a_pagar >2),k.valor_cuota * a.tipo_cambio,0),2),ROUND(IF((k.a_pagar >2),k.valor_cuota,0),2)) AS saldo_61_90, IF(a.tipo_moneda='DOLARES',ROUND(IF((k.a_pagar >3),k.valor_cuota * a.tipo_cambio,0),2),ROUND(IF((k.a_pagar >3),k.valor_cuota,0),2)) AS saldo_91_120, IF(a.tipo_moneda='DOLARES',ROUND(IF((k.a_pagar >=5),(k.a_pagar - 5)* k.valor_cuota * a.tipo_cambio,0),2),ROUND(IF((k.a_pagar >=5),(k.a_pagar - 5)* k.valor_cuota,0),2)) AS saldo_mas_120, q.pilar_inicial  AS  pilar_inicial, r.pilar_final AS  pilar_final
FROM contratos a 
LEFT JOIN sys_asesor b ON a.codigo_asesor = b.codigo_asesor 
LEFT JOIN sys_gerentes_grupos c ON a.codigo_gerente = c.codigo_gerente_grupo
LEFT JOIN sys_personas d ON b.id_nit = d.id_nit
LEFT JOIN sys_personas e ON c.id_nit = e.id_nit 
LEFT JOIN producto_contrato f ON f.serie_contrato = a.serie_contrato AND f.no_contrato = a.no_contrato
LEFT JOIN jardines g ON f.id_jardin = g.id_jardin
LEFT JOIN fases h ON f.id_fases = h.id_fases
LEFT JOIN sys_personas i ON a.id_nit_cliente = i.id_nit
LEFT JOIN sys_status j ON a.estatus = j.id_status
LEFT JOIN  a_cobrar k ON a.serie_contrato = k.serie_contrato AND a.no_contrato = k.no_contrato
LEFT JOIN sys_direcciones l ON a.id_nit_cliente = l.id_nit AND l.tipo_direccion = 7
LEFT JOIN sys_municipio m ON l.idmunicipio = m.idmunicipio
LEFT JOIN sys_provincia n ON l.idprovincia = n.idprovincia
LEFT JOIN (SELECT  id_nit, tipo_telefono, MIN(numero) AS numero  FROM sys_telefonos  GROUP BY id_nit, tipo_telefono) AS tel1 ON a.id_nit_cliente = tel1.id_nit AND tel1.tipo_telefono = 6 
LEFT JOIN (SELECT  id_nit, tipo_telefono, MIN(numero) AS numero  FROM sys_telefonos  GROUP BY id_nit, tipo_telefono) AS tel2 ON a.id_nit_cliente = tel2.id_nit AND tel2.tipo_telefono = 2 
LEFT JOIN servicios o ON o.serv_codigo = f.serv_codigo 
LEFT JOIN contratos_metodo_cobro p ON p.cmc_codigo = a.forpago
LEFT JOIN  prospecto_comercial   q  ON  q.id_nit  =  a.id_nit_cliente  AND  q.estatus  = 4
LEFT JOIN  prospectos   r ON  r.serie_contrato  =  a.serie_contrato  AND  r.no_contrato  =  a.no_contrato AND  r.estatus  = 4";
	
	
	
	$result=mysql_query($query);
        
?>

<table border="1">
	<tr style="background-color:red;">
	    <th>Empresa</th>
		<th>Contrato</th>
		<th>Cliente</th>
		<th>Fecha Nacimiento</th>
		<th>Asesor</th>
		<th>Gerente</th>
		<th>Nombre Producto</th>
		<th>Fecha Venta</th>
		<th>Fecha Primer Pago</th>
		<th>Fecha Ultimo Pago</th>
		<th>Dia de  Pago</th>
		<th>Dias de  no Pago</th>
		<th>Precio Lista</th>
		<th>Descuento</th>
		<th>Precio Neto</th>
		<th>Enganche</th>
		<th>Porc. Enganche</th>
		<th>Cuotas</th>
		<th>Valor Cuota</th>
		<th>Tipo Cambio</th>
		<th>mes</th>
		<th>anio</th>
		<th>Tipo Moneda</th>
		<th>estatus</th>
		<th>Direccion Cobro</th>
		<th>Apartamento</th>
		<th>Colonia / Condominio</th>
		<th>Referencia</th>
		<th>Municipio</th>
		<th>Departamento</th>
		<th>Avenida</th>
		<th>Calle</th>
		<th>Numero</th>
		<th>Manzana</th>
		<th>Zona</th>
		<th>Celular</th>
		<th>Laboral</th>
		<th>Forma de Pago</th>
		<th>Saldo 0 a 30</th>
		<th>Saldo 31 a 60</th>
		<th>Saldo 61 a 90</th>
		<th>Saldo 91 a 120</th>
		<th>Mas de 120</th>
		<th>Pilar Inicial</th>
		<th>Pilar Final</th>
	</tr>
	<?php
		while ($row=mysql_fetch_assoc($result)) {
			?>
				<tr>
				    <td><?php echo $row['empresa']; ?></td>
					<td><?php echo $row['contrato']; ?></td>
					<td><?php echo $row['cliente']; ?></td>
					<td><?php echo $row['fecha_nacimiento']; ?></td>
					<td><?php echo $row['asesor']; ?></td>
					<td><?php echo $row['gerente']; ?></td>
					<td><?php echo $row['nombre_producto']; ?></td>
					<td><?php echo $row['fecha_venta']; ?></td>
					<td><?php echo $row['fecha_primer_pago']; ?></td>
					<td><?php echo $row['fecha_ultimo_pago']; ?></td>
					<td><?php echo $row['dia_pago']; ?></td>
					<td><?php echo $row['dias_no_pago']; ?></td>
					<td><?php echo $row['precio_lista']; ?></td>
					<td><?php echo $row['descuento']; ?></td>
					<td><?php echo $row['precio_neto']; ?></td>
					<td><?php echo $row['enganche']; ?></td>
					<td><?php echo $row['porc_enganche']; ?></td>
					<td><?php echo $row['cuotas']; ?></td>
					<td><?php echo $row['valor_cuota']; ?></td>
					<td><?php echo $row['cuota_pagada']; ?></td>
					<td><?php echo $row['tipo_cambio']; ?></td>
					<td><?php echo $row['mes']; ?></td>
					<td><?php echo $row['anio']; ?></td>
					<td><?php echo $row['tipo_moneda']; ?></td>
					<td><?php echo $row['estatus']; ?></td>
					<td><?php echo $row['direc_cobro']; ?></td>
					<td><?php echo $row['apartamento']; ?></td>
					<td><?php echo $row['residencia_colinia_condominio']; ?></td>
					<td><?php echo $row['referencia']; ?></td>
					<td><?php echo $row['municipio']; ?></td>
					<td><?php echo $row['departamento']; ?></td>
					<td><?php echo $row['avenida']; ?></td>
					<td><?php echo $row['calle']; ?></td>
					<td><?php echo $row['numero']; ?></td>
					<td><?php echo $row['manzana']; ?></td>
					<td><?php echo $row['zona']; ?></td>
					<td><?php echo $row['celular']; ?></td>
					<td><?php echo $row['laboral']; ?></td>
					<td><?php echo $row['forma_pago']; ?></td>
					<td><?php echo $row['saldo_0_30']; ?></td>
					<td><?php echo $row['saldo_31_60']; ?></td>
					<td><?php echo $row['saldo_61_90']; ?></td>
					<td><?php echo $row['saldo_91_120']; ?></td>
					<td><?php echo $row['saldo_mas_120']; ?></td>
					<td><?php echo $row['pilar_inicial']; ?></td>
					<td><?php echo $row['pilar_final']; ?></td>
				</tr>	

			<?php
		}
	?>
</table>
