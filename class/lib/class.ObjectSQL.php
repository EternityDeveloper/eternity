<?php
/*Create by
Jose Gregorio Ramos*/
class ObjectSQL{
	private $table_name="";
	public function charge_obj($db_link,$table,$where){
		$SQL=$this->getSQL("select",$table,$where);
		$rs=mysql_query($SQL,$db_link);
		while($row=mysql_fetch_assoc($rs)){
			$this->phush_obj($row);
		}
	}

	public function clearvar($var){
		unset($this->{$var});
	}
	
	public function phush_obj($table){
		foreach($table as $keys=>$value){
			$this->{$keys}=$value;
		}
	}	
	public function push($table){
		foreach($table as $keys=>$value){
			$this->{$keys}=$value;
		}
	}
	public function setTable($tablename){
		$this->table_name=$tablename;
		return $this;
	}
	public function toSQL($sentence,$condition=""){
		if ($this->table_name!=""){
			$table_name=$this->table_name;
			unset($this->table_name);
			return $this->getSQL($sentence,$table_name,$condition);
		}else{
			return 'Error no ha seleccionado el nombre de la tabla';	
		}
	}
	public function toArray(){ 
		$data=array();
		foreach($this as $keys=>$value){
			$data[$keys]=$value; 
		}
		return $data;		
	}
	public function getSQL($Sentence,$Table,$Condition=""){
	//	print_r( get_declared_classes ());
		unset($this->table_name);
		$result="";
		$Campo="";
		$Campos="";
		foreach($this as $keys=>$value){
				$Campo=$keys;
				///$value=mysql_escape_string($value);
				$restar=0; 
 				if (strlen($value)>0){
					$restar=-1;	
				}				
				 switch($Sentence){
					case "update":	
					   if (substr($value,strlen($value)-1,strlen($value))==")"){
					  	 $result.=$Campo."=".$value.",";
					   }else{
						$result.=$Campo."='".mysql_escape_string($value)."',";
					   }	
					break;
					case "insert":

						$result.=substr($value,strlen($value)-1,strlen($value))==")"?$value .",":"'".mysql_escape_string($value) ."',";
						$Campos.= $Campo.',';							
					break;
					case "select":
						$Campos.=$Campo.',';
					break;
				}									
		}		
			switch($Sentence){
				case "update":	
					$result=substr($result,0,strlen($result)-1);
					$SQL=" UPDATE  `".$Table."` SET ".$result . " ".$Condition;
					return $SQL;
					break;
				case "insert":
					$result=substr($result,0,strlen($result)-1);
					$Campos=substr($Campos,0,strlen($Campos)-1);
					$SQL="INSERT INTO ".$Table." (".$Campos.") VALUES (".$result.") ".$Condition;
					return $SQL;
					break;
				case "select":
					$Campos=substr($Campos,0,strlen($Campos)-1);
					if ($Campos==""){
						$Campos=" * ";
					}
					$SQL="SELECT ".$Campos." FROM ".$Table."  ".$Condition;
					return $SQL;
					break;
			}	
			 
	}	
}


?>
