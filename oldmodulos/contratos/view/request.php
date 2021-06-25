<?php
if (!isset($protect)){
	exit;
}	



if (isset($_REQUEST['control']) && isset($_REQUEST['value'])){
	if ($_REQUEST['control']=="listEmpresa"){
		$type=$_REQUEST['value'];
        $SQL="SELECT * FROM empresa WHERE estatus=1 ";
		if ($type=="PRE"){
			$SQL.="  AND prenecesidad='Y' ";
		}
		if ($type=="NSD"){
			$SQL.="  AND necesidad='Y' ";
		}	 
		
		 $rs=mysql_query($SQL);
		?>
        <select name="empresa" id="empresa" class="form-control">
          <option value="">Seleccione</option>
          <?php 
       
        while($row=mysql_fetch_assoc($rs)){
            $encriptID=System::getInstance()->Encrypt(trim($row['EM_ID']));
        ?>
          <option value="<?php echo $encriptID?>" ><?php echo $row['EM_NOMBRE']?></option>
          <?php } ?>
        </select>
<?php
		exit;
	}

	
} 
?>