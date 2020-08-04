<?php
/*clase que maneja la conecion a la base de datos contable*/


class Siadcom 
{
	private static $_db = null;
	private static $instance;
	private $fecha="";
	public static function GI(){
		 if (!Siadcom::$instance instanceof self) {
             Siadcom::$instance = new Siadcom();
        }
        return Siadcom::$instance;
	}
	public function query($SQL){
		$dbh = ibase_pconnect("127.0.0.1:d:/infosis/database/mfsbase.ib", "sysdba", "masterkey",'ISO8859_1');
		$q = ibase_query($dbh,$SQL);
		$ret=array();
		while ($r = ibase_fetch_object($q)) {
			array_push($ret,$r);
		} 
		return $ret;
	}	
	
}

?>