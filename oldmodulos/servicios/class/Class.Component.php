<?php


class Component{
	private $db_link;
	public function __construct($db_link){ 
		$this->db_link=$db_link;
	}
	
	public function uploadImage($fileElementName,$CompID){
		$error = "";
		$msg = "";
		$name="";
 
		if(!empty($_FILES[$fileElementName]['error']))
		{
			switch($_FILES[$fileElementName]['error'])
			{
	
				case '1':
					$msg = 'El archivo subido excede la directiva upload_max_filesize en php.ini';
					$error="true";	
					break;
				case '2':
					$msg = 'El archivo subido excede la directiva MAX_FILE_SIZE que se especificó en el formulario HTML';
					$error="true";	
					break;
				case '3':
					$msg = 'El archivo subido fue sólo parcialmente cargado';
					$error="true";	
					break;
				case '4':
					$msg = 'Ningún archivo fue subido.';
					$error="true";	
					break;
				case '6':
					$msg = 'Falta una carpeta temporal';
					$error="true";	
					break;
				case '7':
					$msg = 'No se pudo escribir el archivo en el disco';
					$error="true";	
					break;
				case '8':
					$msg = 'La carga de archivos se detuvo por extensión';
					$error="true";	
					break;
				case '999':
				default:
					$msg = 'No código de error disponibles';
			}
		}elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none')
		{
			$msg = 'No file x was uploaded..';
		}else 
		{
				$type=array("jpg","png","jpeg","gif");
				/*Verifico el tipo de archivo que es */
				if (in_array(end(explode(".",$_FILES[$fileElementName]['name'])),$type)){
					$msg .= " Nombre arhivo: " . $_FILES[$fileElementName]['name'] . ", ";
					$msg .= " Tamaño: " . @filesize($_FILES[$fileElementName]['tmp_name']);
					
					$tmp_name=$_FILES[$fileElementName]['tmp_name'];
	 
					$name=$CompID."_".rand(1000,9999).".".end(explode(".",$_FILES[$fileElementName]['name']));
					
					if (_PATH_DEV!=""){
						$uploads_dir=$_SERVER['DOCUMENT_ROOT']."/"._PATH_DEV."/images/servicios/".$name;
					}else{
						$uploads_dir=$_SERVER['DOCUMENT_ROOT']."/images/servicios/".$name;	
					}
					//print_r($_SERVER['DOCUMENT_ROOT']."/"._PATH_DEV);
					move_uploaded_file($tmp_name,$uploads_dir);
			 
					@unlink($_FILES[$fileElementName]);		
					$error="false";	
					
					$obj= new ObjectSQL();
					$obj->imagen=$name;
					$SQL=$obj->getSQL("update","componentes","where id_componente='". mysql_escape_string($CompID) ."'");
					mysql_query($SQL);
					
					
				}else{
					$msg="El tipo de archivo no es valido.";
					$error="true";	
				}
		}
				
		$data=array("message"=>$msg,"error"=>$error,"file_name"=>$name);
		return $data;
	}
}

?>