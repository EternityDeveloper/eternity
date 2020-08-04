<?php 
if (!isset($protect)){
	exit;	
}

if (!isset($_REQUEST['id'])){
	exit;
}
$recibo=json_decode(System::getinstance()->Decrypt($_REQUEST['id']));
 
SystemHtml::getInstance()->includeClass("caja","Recibos");  
$recibos= new Recibos($protect->getDBLINK());   
$filter=array("action"=>'getRecibo',"SERIE"=>$recibo->SERIE,"NO_DOCTO"=>$recibo->NO_DOCTO);
$rc_detalle=$recibos->getListadoRecibo($filter); 
?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:800px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">DETALLE RECIBO</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="120"><table width="600" border="0" cellspacing="0" cellpadding="0" class="table table-bordered table-striped"  >
                <tr>
                  <td width="120" align="left"><strong>NO RECIBO:</strong></td>
                  <td width="324"><?php echo $recibo->SERIE."-".$recibo->NO_DOCTO;?><?php echo strtoupper($row['TMOVIMIENTO']);?></td>
                </tr>
                <tr>
                  <td align="left"><strong>MONTO:</strong></td>
                  <td><?php echo number_format($rc_detalle[0]['MONTO'],2)?>&nbsp;</td>
                </tr>
                <tr>
                  <td align="left"><strong>CONTRATO:</strong></td>
                  <td><?php echo $rc_detalle[0]['SERIE_CONTRATO']." ".$rc_detalle[0]['NO_CONTRATO'];?></td>
                </tr>
                <tr>
                  <td align="left"><strong>RECIBO CAJA:</strong></td>
                  <td><input type="text" name="REPORTE_VENTA" id="REPORTE_VENTA" value="<?php echo $rc_detalle[0]['REPORTE_VENTA']?>" class="form-control"></td>
                </tr>
                <tr >
                  <td align="left" ><strong>MOTORIZADO:</strong></td>
                  <td><p>
                    <select name="motorizado" id="motorizado">
                    <option value="0">Seleccionar</option>
                      <?php 
					
		$SQL="SELECT sys_personas.id_nit,
			CONCAT(sys_personas.primer_nombre,' ',sys_personas.segundo_nombre,' ',sys_personas.primer_apellido,' ',sys_personas.segundo_apellido) AS nombre_motorizado 
		 
			 FROM `cobros_zona` 
		LEFT JOIN `sys_personas` ON (`sys_personas`.id_nit=cobros_zona.`motorizado`) 
		WHERE cobros_zona.estatus=1 ";
		  
		$SQL.=$QUERY;
		$rs=mysql_query($SQL);
		$result=array("results"=>array());  
		
		while($row=mysql_fetch_assoc($rs)){	 ?>
                      <option value="<?php echo System::getInstance()->Encrypt($row['id_nit'])?>"><?php echo utf8_encode($row['nombre_motorizado'])?></option>
                      <?php } ?>
                    </select>
                  </p></td>
                </tr>
                <tr >
                  <td align="left" ><strong>OFICIAL:</strong></td>
                  <td><select name="oficial" id="oficial">
                    <option value="0">Seleccionar</option>                  
                    <?php 
					
		$SQL="SELECT oficial.id_nit, 
			CONCAT(oficial.primer_nombre,' ',oficial.segundo_nombre,' ',oficial.primer_apellido,' ',oficial.segundo_apellido) AS nombre_oficial
			 FROM `cobros_zona`  
		LEFT JOIN `sys_personas` AS  oficial ON (`oficial`.id_nit=cobros_zona.`oficial_nit`)
		WHERE cobros_zona.estatus=1 ";
		  
		$SQL.=$QUERY;
		$rs=mysql_query($SQL);
		$result=array("results"=>array());  
		
		while($row=mysql_fetch_assoc($rs)){	 ?>
                    <option value="<?php echo System::getInstance()->Encrypt($row['id_nit'])?>"><?php echo utf8_encode($row['nombre_oficial'])?></option>
                    <?php } ?>
                  </select></td>
                </tr>
                <tr>
                  <td align="left">&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan="2" align="center"><button type="button" class="orangeButton" id="doSaveAsignarRecibo">Guardar</button></td>
                </tr>
            </table></td>
          </tr>
           
          </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button> 
      </div>
    </div>
  </div>
</div>