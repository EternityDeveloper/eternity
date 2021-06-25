<?php

	header('Content-type:application/xls');
	header('Content-Disposition: attachment; filename=todito.xls');  
	$query="SELECT * FROM todito";
	$result=mysql_query($query);
        if(!isset($result)) {
              echo "no hizo la consulta";
        }
?>

<table border="1">
	<tr style="background-color:red;">
		<th>Contrato</th>
		<th>Cliente</th>
		<th>Fecha Nacimiento</th>
		<th>Asesor</th>
		<th>Gerente</th>
		<th>Nombre Producto</th>
		<th>Fecha Venta</th>
		<th>Precio Lista</th>
		<th>Descuento</th>
		<th>Precio Vendido</th>
		<th>Precio Neto</th>
		<th>Enganche</th>
		<th>Porc. Enganche</th>
		<th>Cuotas</th>
		<th>Valor Cuota</th>
		<th>Tipo Cambio</th>
		<th>mes</th>
		<th>año</th>
		<th>Tipo Moneda</th>
		<th>dia Pago</th>
		<th>Fecha Primer Pago</th>
		<th>Estatus</th>
		<th>Direccion</th>
		<th>Departamento</th>
		<th>Municipio</th>
		<th>Pilar Inicial</th>
		<th>Pilar Final</th>
	</tr>
	<?php
		while ($row=mysql_fetch_assoc($result)) {

                 
			?>
				<tr>
					<td><?php echo $row['contrato']; ?></td>
					<td><?php echo $row['cliente']; ?></td>
					<td><?php echo $row['fecha_nacimiento']; ?></td>
					<td><?php echo $row['asesor']; ?></td>
					<td><?php echo $row['gerente']; ?></td>
					<td><?php echo $row['nombre_producto']; ?></td>
					<td><?php echo $row['fecha_venta']; ?></td>
					<td><?php echo $row['precio_lista']; ?></td>
					<td><?php echo $row['descuento']; ?></td>
					<td><?php echo $row['precio_vendido']; ?></td>
					<td><?php echo $row['precio_neto']; ?></td>
					<td><?php echo $row['enganche']; ?></td>
					<td><?php echo $row['porc_enganche']; ?></td>
					<td><?php echo $row['cuotas']; ?></td>
					<td><?php echo $row['valor_cuota']; ?></td>
					<td><?php echo $row['tipo_cambio']; ?></td>
					<td><?php echo $row['mes']; ?></td>
					<td><?php echo $row['año']; ?></td>
					<td><?php echo $row['tipo_moneda']; ?></td>
					<td><?php echo $row['dia_pago']; ?></td>
					<td><?php echo $row['fecha_primer_pago']; ?></td>
					<td><?php echo $row['estatus']; ?></td>
					<td><?php echo $row['direccion']; ?></td>
					<td><?php echo $row['depto']; ?></td>
					<td><?php echo $row['municipio']; ?></td>
					<td><?php echo $row['pilar_inicial']; ?></td>
					<td><?php echo $row['pilar_final']; ?></td>
				</tr>	

			<?php
		}
	?>
</table>
