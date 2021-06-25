<?php

/*

	Create By: Jose Gregorio Ramos Castro

	#UserAccess V1.0

	Clase encargada de la seguridad del sistema, maneja roles y accesos



*/

class UserAccess {

	private static $_db = null;
	public  $id= null;
	private $ua_version="UAC_LOGIN_V0";	
	private static $instance;
	

	public static function getInstance(){

		 if (!UserAccess::$instance instanceof self) {
             UserAccess::$instance = new UserAccess();
        }

        return UserAccess::$instance;
	}	

	public function __construct($db_class){

		self::$_db=$db_class;
		UserAccess::$instance=$this;

	}	

	public function getDBLink(){

		return self::$_db;

	}

	public function login($user,$pwd) {

	    if (!isset($_SESSION[$this->ua_version])){
           
           
           $nwUser = mysql_escape_string($user);
           $nwPwd  = md5($pwd);
           

          $sql    = "select a.*, b.id_role
                        from usuarios a, usu_role b 
                       where a.id_usuario = b.id_usuario
                         and a.status = '1'
                         and a.email  = '".$nwUser."'
                         and a.contrasena = '" .$nwPwd."'"; 



			/*$sql = sprintf( "SELECT * 
				               FROM usuarios
			             INNER JOIN usu_role ON usu_role.id_usuario = usuarios.id_usuario
			             where email='%s' 
			               and contrasena='%s' 
			               and usuarios.status = 1 ", mysql_escape_string($user), md5($pwd)); */

					   //TIMESTAMPDIFF(MINUTE,last_login,CONCAT(CURDATE(),' ', CURTIME())) //


		 	unset($_SESSION[$this->ua_version]['time_to_update']);
         
            
			$rs = self::$_db->query($sql);
            
            if ( mysql_num_rows($rs) > 0 ){

				$row   = mysql_fetch_assoc($rs);
				$token = md5(time()).md5($user.time().$pwd); 

				$obj   = new ObjectSQL();
				$obj->token = $token;
				$obj->setTable("usuarios");
				$SQL=$obj->toSQL("update"," where id_usuario='".$row['id_usuario']."'");

				mysql_query($SQL);


				$_SESSION[$this->ua_version]['user_data'] = $row;
                

                
				$_SESSION[$this->ua_version]['isLogin']   = true;	
				$this->charge_permisos();

                setcookie("token", $token);

				SysLog::getInstance()->Log('', 

										 '',

										 '',

										 '',

										 '',

										 "LOGIN DE USUARIO",

										 json_encode($row),

										 'LOGIN');				

			}

		}

	}

	/*API PARA DISPOSITIVOS EXTERNOS*/

	public function doLoginAPI($user,$pwd){

		//echo $user." - ".$pwd; 

		$sql = sprintf("SELECT * FROM usuarios

		INNER JOIN `usu_role` ON (usu_role.`id_usuario`=usuarios.`id_usuario`)

		 where email='%s' and contrasena='%s' and usuarios.`status`=1 ", 

						mysql_escape_string($user), md5($pwd)); 

		 

		$rs = self::$_db->query($sql);

		//print_r($rs);

		if (mysql_num_rows($rs)>0){		

			$row=mysql_fetch_assoc($rs);

			$token=md5(time()).md5($user.time().$pwd); 

			$obj=new ObjectSQL();

			$obj->token=$token;

			$obj->setTable("usuarios");

			$SQL=$obj->toSQL("update"," where id_usuario='".$row['id_usuario']."'");

			mysql_query($SQL);			 

			SysLog::getInstance()->Log('', 

									 '',

									 '',

									 '',

									 '',

									 "LOGIN DE USUARIO",

									 json_encode($row),

									 'LOGIN');		 

			return array("valid"=>1,"mensaje"=>"Login correcto!","token"=>$token);									 	

		}

		

		return array("valid"=>0,"mensaje"=>"Usuario y/o contraseña invalida");

		 

	}	

	/*API MODULO DE PRINT CLOUD*/

	public function doLoginToken($token){

	//	echo $user." - ".$pwd; 

		$sql = "SELECT * FROM 

						system_group_printers 

				where token='". mysql_real_escape_string($token) ."' 

				and estatus=1 "; 

		 

		$rs = self::$_db->query($sql);

		//print_r($rs);

		if (mysql_num_rows($rs)>0){		

			$row=mysql_fetch_assoc($rs); 

			SysLog::getInstance()->Log('', 

									 '',

									 '',

									 '',

									 '',

									 "LOGIN DE IMPRESORA ".$row['nombre'],

									 json_encode($row),

									 'LOGIN');		 

			return array("valid"=>1,"mensaje"=>"Login correcto!","token"=>$token);									 	

		}

		

		return array("valid"=>0,"mensaje"=>"Token invalido");

		 

	}	

	public function checkToken($token){

	//	echo $user." - ".$pwd; 

		$sql = sprintf("SELECT * FROM usuarios

		INNER JOIN `usu_role` ON (usu_role.`id_usuario`=usuarios.`id_usuario`)

		 where token='%s' and usuarios.`status`=1 ", 

						mysql_escape_string($token) ); 

			 

		$rs = self::$_db->query($sql);

		//print_r($rs);

		if (mysql_num_rows($rs)>0){		

			$row=mysql_fetch_assoc($rs); 

			$_SESSION[$this->ua_version]['user_data']=$row;

			$_SESSION[$this->ua_version]['isLogin']=true;				

			return array("valid"=>1,"mensaje"=>"Login correcto!","data"=>$row);	 

		}

		

		return array("valid"=>0,"mensaje"=>"Token invalido");

		 

	}		

	public static function getSessionID(){

		return session_id();

	}

	public function getUserData(){

		return $_SESSION[$this->ua_version]['user_data'];

	}

	public function getID(){

		if ($this->isLogin()){

			return $_SESSION[$this->ua_version]['user_data']['id_usuario'];

		}	

		return 0;		

	}	

	public function isLogin(){

		if (isset($_SESSION[$this->ua_version]['isLogin'])){
               
			if ($_SESSION[$this->ua_version]['isLogin']){
                

				$this->fillTipoDoctoCaja($_SESSION[$this->ua_version]['user_data']['id_usuario']);

				return true;

			}

		}	

		return false;

	}

	

	public function getRoleId(){

		if ($this->isLogin()){
          
            return $_SESSION[$this->ua_version]['user_data']['id_role'];

		}	

		return 0;

	}

	/*Metodo que carga los permisos de usuario*/

	private function charge_permisos(){

		$SQL="select *
		        from seguridad
		       inner join roles on (seguridad.id_role = roles.id_role)
               inner join pantallas on (pantallas.id_pantalla = seguridad.id_pantalla)
			   where roles.id_role ='".$this->getRoleId()."'";

	  



        /*$SQL="select *
		        from seguridad
		      inner join roles on (seguridad.id_role = roles.id_role)
              inner join pantallas on (pantallas.id_pantalla = seguridad.id_pantalla)
			  where roles.id_role ='1'";*/

		if (!isset($_SESSION[$this->ua_version]['time_to_update'])){

			$_SESSION[$this->ua_version]['time_to_update']=time();

			$this->execute_perm($SQL);  

			SysLog::getInstance()->Log('', 

									 '',

									 '',

									 '',

									 '',

									 "ACTUALIZANDO SESSION",

									 '',

									 'LOGIN');				

		} 

		if (((time()-$_SESSION[$this->ua_version]['time_to_update'])/60)>5){

			$_SESSION[$this->ua_version]['time_to_update']=time();

			$this->execute_perm($SQL);	  	

			SysLog::getInstance()->Log('', 

									 '',

									 '',

									 '',

									 '',

									 "ACTUALIZANDO SESSION",

									 '',

									 'LOGIN');							

		}  

	 

	}

	private function execute_perm($SQL){

		$rs = self::$_db->query($SQL); 
        $lineas = mysql_num_rows($rs);

		if ($lineas > 0){		 

			$_SESSION[$this->ua_version]['permisos']=array();

			while($row=mysql_fetch_assoc($rs)){

				$_SESSION[$this->ua_version]['permisos'][$row['id_pantalla']]=$row;

			}

		}	

	}

	/*Obterer los permisso dado un determinado id */

	public function getPermisosById($id){

		return  isset($_SESSION[$this->ua_version]['permisos'][$id])?$_SESSION[$this->ua_version]['permisos'][$id]:array();

	}

	/*Obterer los permisso dado un determinado id */

	public function getIfAccessPageById($id){

		$dt= isset($_SESSION[$this->ua_version]['permisos'][$id])?$_SESSION[$this->ua_version]['permisos'][$id]:array();

		if (count($dt)>0){

			if ($dt['acceso']=="1"){

				return true;

			}

		}

		return false;

	}

	/*Obterer los permisso dada una pagina determinada*/

	public function getPermisosByPage($page){
        
        $this->charge_permisos();

		$rt=array("retrun"=>false);

		$permisos=$_SESSION[$this->ua_version]['permisos'];

 	

		if (count($permisos)>0){

			foreach($permisos as $key => $val){

				if (str_replace("mod_","",$val['URL'])==str_replace("mod_","",$page)){

					$val["return"]=true;

					$rt=$val;

					break;

				}

			}

		} 

	 

		return $rt;

	}	

	public function getAllPermisos(){

		return $_SESSION[$this->ua_version]['permisos'];	

	}

	public function containAccess(){

	

	}

	public function logoff(){

		$_SESSION[$this->ua_version]['isLogin']=false;

		unset($_SESSION[$this->ua_version]);
		

	}

 

	public function getComercialID(){

		if (!isset($_SESSION[$this->ua_version]['time_to_update_comercial_id'])){

			$_SESSION[$this->ua_version]['time_to_update_comercial_id']=time();

		    $update=true;

		} 

		if (((time()-$_SESSION[$this->ua_version]['time_to_update_comercial_id'])/60)>10){

			$_SESSION[$this->ua_version]['time_to_update_comercial_id']=time();

			$update=true; 

		} 	 

		$update=true;

		if ($update){	

			$SQL="SELECT * FROM view_estructura_comercial as `asesores_g_d_gg_view` WHERE id_nit='".$_SESSION[$this->ua_version]['user_data']['id_nit']."'";

			$rs = self::$_db->query($SQL);

			if (mysql_num_rows($rs)>0){			  

				$row=mysql_fetch_assoc($rs);	

				$_SESSION[$this->ua_version]['id_comercial']=$row['id_comercial'];		

				return $row['id_comercial'];

			}

		}else{

			return $_SESSION[$this->ua_version]['id_comercial'];

		}

		return "------------";

	}

	public function getComercialData(){

		$SQL="SELECT asesores_g_d_gg_view.*,concat(sys_personas.primer_nombre,' ',sys_personas.segundo_nombre,' ',concat(sys_personas.primer_apellido,' ',sys_personas.segundo_apellido)) as nombre_completo

		 FROM view_estructura_comercial as `asesores_g_d_gg_view` 

		INNER JOIN `sys_personas` ON (`sys_personas`.id_nit=asesores_g_d_gg_view.id_nit)

 			WHERE sys_personas.id_nit='".$_SESSION[$this->ua_version]['user_data']['id_nit']."' ";

	 

		$rs = self::$_db->query($SQL);

		if (mysql_num_rows($rs)>0){			  

			$row=mysql_fetch_assoc($rs);				

			return $row;

		}

		return array();

	}	

	public function getNITComercial(){

		$SQL="SELECT * FROM view_estructura_comercial as `asesores_g_d_gg_view` WHERE id_nit='".$_SESSION[$this->ua_version]['user_data']['id_nit']."' AND  asesores_g_d_gg_view.estatus=1";

		$rs = self::$_db->query($SQL);

		if (mysql_num_rows($rs)>0){			  

			$row=mysql_fetch_assoc($rs);				

			return $row['id_nit'];

		}

		return "------------";

	}

	/*GET ID PANTALLA FROM PERMISOS*/

	public function getIDPermiso($id){

		$SQL ="select * 
		         from seguridad 
		        inner join pantallas on (pantallas.id_pantalla = seguridad.id_pantalla) 
		        where url like '%".$id."%' limit 1";


		$rs = self::$_db->query($SQL);

		if (mysql_num_rows($rs)>0){		 

			$row=mysql_fetch_assoc($rs);

			return ($row);

		}	

	 	return array();

	} 

	public function fillTipoDoctoCaja($id){

		$SQL="SELECT tipo_documentos_caja.*,tipo_documento.* FROM 

			`tipo_documentos_caja` 

			INNER JOIN caja ON (caja.`ID_CAJA`=tipo_documentos_caja.`CAJA_ID_CAJA`)

			INNER JOIN `tipo_documento` ON (tipo_documento.`TIPO_DOC`=tipo_documentos_caja.`CAJA_TIPO_DOC`)

			WHERE caja.id_usuario='".$id."' and tipo_documentos_caja.estatus=1 ";  


		$rs = self::$_db->query($SQL); 

	//	echo RECIBO_CAJA."\n";

		while($row=mysql_fetch_assoc($rs)){  

			define($row['VARIABLE'],$row['TIPO_DOC']);		 

		}

	//	 echo RECIBO_CAJA; 	

	// 	 exit;

	}

	public function getCaja(){


		$SQL="SELECT caja.`ID_CAJA`,caja.`id_usuario`,caja.`DESCRIPCION_CAJA`,caja.`IP_CAJA`,caja.`INICIAL_CAJA`,usuarios.id_nit FROM `caja`

INNER JOIN `usuarios` as usuarios ON (usuarios.id_usuario=caja.id_usuario) 

WHERE caja.id_usuario='".$this->getID()."' and caja.ESTATUS=1"; 



		$rs = self::$_db->query($SQL);

		if (mysql_num_rows($rs)>0){		 

			$row=mysql_fetch_assoc($rs);

			return $row;

		}	

	 	return array();

	}

	/*RETORNA EL id_nit*/

	public function getIDNIT(){

		return $_SESSION[$this->ua_version]['user_data']['id_nit'];	

	}



}





?>