<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}


?>
<div id="listado_cliente" class="fsPage" style="width:100%">
<h2>Listado de prospectos</h2>
	<table border="0" class="display" id="tb_clientes_<?php echo $_REQUEST['rand']?>" style="font-size:13px;" >
      <thead>
        <tr>
          <th>Tipo<br>Documento</th>
          <th>Numero <br>documento </th>
          <th>Nombres</th>
          <th>Apellidos</th>
          <th>Fecha nacimento </th> 
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
 	
      </tbody>
  </table>
</div>