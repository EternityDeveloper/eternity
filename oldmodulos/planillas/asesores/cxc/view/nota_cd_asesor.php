<?php 
if (!isset($protect)){
	exit;	
}
  
 	
?>
<div class="modal fade" id="aplicar_nota_c_d" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">APLICAR NOTA DEBITO/CREDITO</h4>
      </div>
      <div class="modal-body">
        <table width="100%" border="0"  style="margin:10px;">
          <tr>
            <td style="width:150px;"><strong>ASESOR</strong></td>
            <td><select name="_asesor" id="_asesor" style="width:300px">
              <?php 
			$SQL="SELECT  
					ase.`codigo_asesor` AS codigo_asesor,
					CONCAT(LTRIM(oficial.`primer_nombre`),' ',LTRIM(oficial.`segundo_nombre`),
' ',LTRIM(oficial.`primer_apellido`),' ',LTRIM(oficial.segundo_apellido)) AS nombre_asesor
					FROM `sys_asesor` AS ase
					INNER JOIN `sys_personas` AS oficial ON (`oficial`.id_nit=ase.id_nit)
					GROUP BY oficial.id_nit ";  
			$rs=mysql_query($SQL); 
			while($row=mysql_fetch_assoc($rs)){	
					?>
              <option  value="<?php echo System::getInstance()->Encrypt($row['codigo_asesor']); ?>"><?php echo $row['nombre_asesor'];?></option>
              <?php } ?>
            </select></td>
          </tr>
          <tr>
            <td style="width:150px;"><strong>TIPO MOVIMIENTO:</strong></td>
            <td><select name="t_movimiento" id="t_movimiento" class="form-control"  style="width:250px;">
              <option value="">Seleccionar..</option>
              <?php  
	$SQL="SELECT * FROM `cxc_tipo_movimiento` where estatus='1' ";
	$rs=mysql_query($SQL);
	while($row=mysql_fetch_assoc($rs)){
		$id=System::getInstance()->Encrypt(json_encode($row));
?>
              <option value="<?php echo $id?>"><?php echo $row['descripcion']?></option>
              <?php } ?>
            </select></td>
          </tr>
          <tr>
            <td><strong>MONTO:</strong></td>
            <td> 
            <input type="text" name="notacd_monto" id="notacd_monto" class="form-control" style="width:150px;"></td>
          </tr>
          <tr>
            <td valign="top"><strong>DESCRIPCION:</strong></td>
            <td> 
            <textarea name="nota_descripcion" id="nota_descripcion" class="form-control" cols="45" rows="5"></textarea></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="close_view" data-dismiss="modal">Cerrar</button>
        <button type="button" id="aplicar_nota_cd" class="btn btn-primary">Aplicar</button>
      </div>
    </div>
  </div>
</div>
 