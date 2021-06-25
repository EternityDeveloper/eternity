<?php
/*
	Se encarga de imprimir en cualquier documento
	html las cabeceras script,css style y includes. 

*/

class SystemHtml{
	private $_ARRAY_SCRIPT=array();
	private $_ARRAY_STYLE=array();
	private static $instance;
	private static $protect;
	
	public static function getInstance(){
		 if (!SystemHtml::$instance instanceof self) {
             SystemHtml::$instance = new SystemHtml();
        }
        return SystemHtml::$instance;
	}
	/* paso la clase protect para que el sistema reconozca 
	   los permisos del usuario */
	public function setSystemProtect($protect){
		$this->protect=$protect;
	}
	public function getSystemProtect(){
		return $this->protect;
	}
	public function addTagScript($val){
		if (!in_array($val,$this->_ARRAY_SCRIPT)){
			array_push($this->_ARRAY_SCRIPT,$val);
		}
	} 
	public function getTagScript(){
		return $this->_ARRAY_SCRIPT;
	}	
	public function removeTagScript($index){
		//unset($this->_ARRAY_SCRIPT[$index]);
		foreach($this->_ARRAY_SCRIPT as $k =>$val){
			if ($index==$val){
				unset($this->_ARRAY_SCRIPT[$k]);
			}
		}
		
	}	
	public function addTagScriptByModule($val,$modulo=""){
		$module=$modulo;
		if ($modulo==""){
			$sp=explode("/",System::getInstance()->getCurrentModulo());
			unset($sp[count($sp)-1]);
			$module= implode("/",$sp);
		}
		if (!in_array($val,$this->_ARRAY_SCRIPT)){
			array_push($this->_ARRAY_SCRIPT,"modulos/".$module."/script/".$val);
		}
	}
	public function addTagStyle($val){
		if (!in_array($val,$this->_ARRAY_STYLE)){
			array_push($this->_ARRAY_STYLE,$val);
		}
	}

	public function removeTagStyle($index){
		//unset($this->_ARRAY_SCRIPT[$index]);
		foreach($this->_ARRAY_STYLE as $k =>$val){
			if ($index==$val){
				unset($this->_ARRAY_STYLE[$k]);
			}
		}
		
	}			
	public function addModule($val){
		$protect=$this->protect;
		/* Verificar si tiene permisos para acceder a este modulo*/
		if (isset($this->protect)){
			
			$data=$protect->getPermisosByPage($val);
			 
			if ($data['return']=="1"){
				if ($data['acceso']=="1"){
					$this->loadModule($val);
				}else{
					$this->loadModule("not_have_permissions",$val);
				}
			}else{
				$permiso=$protect->getIDPermiso($val);

				$perm="";
				if (count($permiso)>0){
					$perm=$permiso['id_pantalla'];
				}
				echo 'No tiene permisos para acceder a esta pagina. Codigo ('.$perm.') 
				<br>Contacte con el administrador del sistema. <a href="./?logoff">Volver atras</a>';
				//$this->loadModule("not_have_permissions",$val);
			}
		}else{
			$this->loadModule($val);
		}
	}
	private function loadModule($val,$_url=""){
		$protect=$this->protect;
		$mod="modulos/".$val.".php";
		if (file_exists($mod)){
			include($mod);
		}
	}
	public function includeFile($modulo,$name){
		$protect=$this->protect;
		$mod="modulos/".$modulo."/".$name;
		echo $mod;
		if (file_exists($mod)){ 
			require_once($mod);
		}
	}
	public function includeClass($modulo,$class){
		$protect=$this->protect;
		$mod="modulos/".$modulo."/class/class.".$class.".php";
		if (file_exists($mod)){ 
			require_once($mod);
		}
	}
	public function printParserTagScript(){
		echo $this->parserTags("script",$this->_ARRAY_SCRIPT);
	}
	public function printParserTagStyle(){
		echo $this->parserTags("style",$this->_ARRAY_STYLE);
	}
	public function parserTags($tag="script",$array){
		$str="";
		/* $tag='<script type="text/javascript" src="%s?nocache='.mt_rand().'"></script>'."\n";*/
		//$tag='<link type="text/css" href="%s?nocache='.mt_rand().'" rel="stylesheet"/>'."\n";
		
		if ($tag=="script"){
		 	$tag='<script type="text/javascript" src="%s?nocache='.mt_rand().'"></script>'."\n"; 
		/* 	$tag='<script type="text/javascript" src="%s"></script>'."\n";*/
		}
		if ($tag=="style"){
			$tag='<link type="text/css" href="%s?nocache='.mt_rand().'" rel="stylesheet"/>'."\n";
		/*	$tag='<link type="text/css" href="%s" rel="stylesheet"/>'."\n";*/
		}
		
		if (isset($array)){
			foreach($array as $key => $val){
				$str.= sprintf($tag,$val) ;
			}	
		}
		
		return $str;
	}

}




?>