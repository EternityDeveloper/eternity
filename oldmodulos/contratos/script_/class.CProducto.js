var CProducto = new Class({
	dialog_container : null,
	_id_nit : null,
	_rand : 0,
	_producto : null,
	_type_product : "producto",
	initialize : function(dialog_container){
		this.main_class="CProducto";
		this.dialog_container=dialog_container;
	}, 
	setIDNit : function(id_nit){
		this._id_nit=id_nit;
	},
	doChargeModulo : function(template_view_id,filtro,rand,type){
		var instance=this;	   
		this._rand=rand;
		this._type=type; //determina el comportamiento de la ventana si es editar o crear
		instance.post("./?mod_contratos/listar",{
				"view_carrito_main":'1',
				"template_producto":'1',
				"type":type,
				"rand":rand
		},function(data){  	
			$("#"+template_view_id).html(data); 
			if (instance._type=="edit"){
				instance.loadViewProducto();
			}
			
			$("#bt_c_add_producto").click(function(){
				var producto= new Inventario(instance.dialog_container); 		 
				producto.setFilterByIDNit(instance._id_nit);
				producto.setStatusFilter(3); 
				producto.chargeCustomView(filtro);
				producto.setToken(rand);
				producto.addListener("producto_select",function(producto){ 
					/*CARGA LA VISTA DE PRODUCTO Y LA ASIGNA A UN CARRITO*/
					instance.loadViewProductoAndAdd();
				});	 
			});
		});
	},
	/*Carga la vista de detalle de producto y lo agrega al carrito*/
	loadViewProductoAndAdd : function(){
		var instance=this;
		//this._producto=producto; 
		instance.post("./?mod_contratos/listar",{
				"view_carrito_main":'1',
				"template_producto":'2',
				"product_add" : 1,
				"producto":'',
				"token": this._rand
		},function(data){  	
			$("#display_product").html(data);
			$("#display_product").show();
			//$("#title_"+instance._rand).html(producto.jardin); 
			/*Evento que se dispara cuando seleccionan un producto*/ 
			instance.fire("OnProductoSelect");
		});
	},
	
	/*Carga la vista de detalle de producto*/
	loadViewProducto : function(){
		var instance=this;  
		instance.post("./?mod_contratos/listar",{
				"view_carrito_main":'1',
				"template_producto":'2', 
				"product_get" : 1,
				"token": instance._rand
		},function(data){  	
			$("#display_product").html(data);
			$("#display_product").show();
			//$("#title_"+instance._rand).html(producto.jardin); 
		});
	}	
	
});