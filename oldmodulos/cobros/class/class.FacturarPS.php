<?php
/*
	Facturar productos y servicios 
*/
class FacturarPS{
	private static $db_link;
	private $_data;
	private $token;
	private static $instance;
	
	public function __construct($db_link=""){
		if ($db_link!=""){
			self::$db_link=$db_link;
			FacturarPS::$instance = $this;
		}
	}  
	public static function getInstance(){
		 if (!FacturarPS::$instance instanceof self) {
             FacturarPS::$instance = new FacturarPS();
        }
        return FacturarPS::$instance;
	} 
	public function getProductos(){
		$data=array();
		$SQL="SELECT sp_productos.* FROM `sp_productos`
			LEFT JOIN`sp_inventario` ON (`sp_inventario`.id_producto=sp_productos.id_producto)
			WHERE (sp_inventario.existencia>0 or sp_productos.is_servicio=1) ";	  

		$rs=mysql_query($SQL); 
		while($row=mysql_fetch_assoc($rs)){	 
			array_push($data,$row);
		}
		return $data;
	}	
	/*LIMPIA LA SESSION DEL CARRITO*/
	public function clearCarSession(){
		$_SESSION['CAR_PRODUCTO']=array();
	}
	public function doRemoveCarProduct($producto){
		unset($_SESSION['CAR_PRODUCTO'][$producto->id_producto]);
		$return=array("valid"=>true,"mensaje"=>"Producto removido");
		return $return;				
	}
	/**/
	public function doPutCarProducto($producto,$costo,$cantidad){
		if (!isset($_SESSION['CAR_PRODUCTO'])){
			$_SESSION['CAR_PRODUCTO']=array();
		}
 		$prd=array("producto"=>$producto,
					"cantidad"=>$cantidad,
					"base"=>$costo*$cantidad,
					"neto"=>$costo*$cantidad,);

		$_SESSION['CAR_PRODUCTO'][$producto->id_producto]=$prd;
		$return=array("valid"=>true,"mensaje"=>"Producto agregado"); 
		return $return;		
	}
	public function getCarProductoList(){
		if (!isset($_SESSION['CAR_PRODUCTO'])){
			$_SESSION['CAR_PRODUCTO']=array();
		} 
		return $_SESSION['CAR_PRODUCTO'];		
	}	
}
?>