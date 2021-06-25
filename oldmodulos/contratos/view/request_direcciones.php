<?php

if (!isset($protect)){
	exit;
}




if (!isset($_REQUEST['action'])){
	exit;
}


switch($_REQUEST['action']){
	case "loadmunicipio":
		if (!isset($_REQUEST['number'])){
			exit;
		}
		if (!isset($_REQUEST['id_field'])){
			exit;
		}
		$number=$_REQUEST['number'];
		$id_provincia=System::getInstance()->getEncrypt()->decrypt($_REQUEST['id_field'],$protect->getSessionID());
		
		?>
		<select name="municipio_id[]" id="municipio_id[]" class="required" onchange="loadAddressField(this.value,'loadciudad','<?php echo $number?>','ciudad_charge','<?php echo $_REQUEST['component'] ?>');">
				  <option value="">Seleccione</option>
				  <?php 
		
		$SQL="SELECT * FROM `sys_municipio` WHERE status_2='A' and idprovincia='". mysql_escape_string($id_provincia) ."'";
		//echo $SQL;
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$encriptID=System::getInstance()->getEncrypt()->encrypt($row['idmunicipio'],$protect->getSessionID());
		?>
				  <option value="<?php echo $encriptID?>"><?php echo $row['descripcion']?></option>
				  <?php } ?>
				</select>		
		<?php
		
	break;
	exit;
	case "loadciudad":
		if (!isset($_REQUEST['provicina'])){
			exit;
		}
	  
		$provincia=System::getInstance()->Decrypt($_REQUEST['provicina']); 
		?>
		<select name="cuidad_id" id="cuidad_id" class="required" >
				  <option value="">Seleccione</option>
				  <?php 
		
		$SQL="SELECT * FROM `sys_ciudad` WHERE status_2='A' and idprovincia='". mysql_escape_string($provincia) ."'";
 
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$encriptID=System::getInstance()->Encrypt($row['idciudad']); 
		?>
			<option value="<?php echo $encriptID?>"><?php echo $row['Descripcion']?></option>
				  <?php } ?>
		</select>		
		<?php
		
	break;	
	case "loadsector":
 		if (!isset($_REQUEST['idciudad'])){
			exit;
		}	
		$idciudad=System::getInstance()->Decrypt($_REQUEST['idciudad']);  
		?>
		<select name="sector_id" id="sector_id" class="required">
				  <option value="">Seleccione</option>
				  <option value="-2">Agregar nuevo</option>
				  <?php 
		
		$SQL="SELECT * FROM `sys_sector` WHERE status='A' and idciudad='". mysql_escape_string($idciudad) ."'";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$encriptID=System::getInstance()->getEncrypt()->encrypt($row['idsector'],$protect->getSessionID());
		?>
				  <option value="<?php echo $encriptID?>"><?php echo $row['descripcion']?></option>
				  <?php } ?>
				</select>		
		<?php
		
	break;	
		
}	
?>