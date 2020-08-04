var CDetalle = new Class({
	dialog_container : null,
	_id_nit : null,
	_rand : 0,
	_producto : null,
	_filtro : null,
	initialize : function(dialog_container,financiamiento){
		this.main_class="CDetalle";
		this.dialog_container=dialog_container; 
		var instance=this;
		if (financiamiento!=null){
			financiamiento.addListener("OnPlanSelect",function(plan){
				instance.calcularMontos(); 
			});	
		}
	}, 
	setIDNit : function(id_nit){
		this._id_nit=id_nit;
	},
	doChargeModulo : function(template_view_id,filtro,rand,type){
		var instance=this;	   
		this._rand=rand;
		this._filtro=filtro;  
		this._type=type; //determina el comportamiento de la ventana si es editar o crear
		 
		if (type=="edit"){
			instance.doReloadData();
		}	
		
	},
	doReloadData : function(){
		this.calcularMontos();
		this.loadDescuento('MONTO','descuento_x_monto');
		this.loadDescuento('PORCIENTO','descuento_x_prociento');
	},

	
	doRemovePorciento : function(moneda){
		var instance=this;
		$(".bt_remove_monto").click(function(){ 
			instance.post("./?mod_contratos/listar",{
				"view_carrito_main":'1',
				"process_descuento_remove":'1',
				"index": $(this).attr("id"),
				"token": instance._rand ,
				'type':'PORCIENTO'
			},function(data){ 
				$("#descuento_x_prociento").html(data);
				instance.calcularMontos();
				instance.doRemovePorciento();
				instance.fire("doDetailChange");
				instance.doReloadData();
			});	
									
		});
	},	 
	
	/*Carga la vista de detalle de producto*/
	loadViewProducto : function(producto){
		var instance=this;
		instance.post("./?mod_contratos/listar",{
				"view_carrito_main":'1',
				"template_producto":'2',
				"producto": $.base64.encode(JSON.stringify(producto)),
				"token": this._rand
		},function(data){  	
			$("#display_product").html(data);
			$("#display_product").show();
		});
	},
	
	loadDescuento : function(type,div_name){
		var instance= this;
		instance.post("./?mod_contratos/listar",{
			"view_carrito_main":'1',
			"process_descuento_add":'1', 
			"token": instance._rand,
			'type':type,
			'type_form':'edit'
		},function(data){ 
			$("#"+div_name).html(data);
			instance.doRemovePorciento(); 
		});	
	},
	
	calcularMontos : function(){
		var instance=this;
		instance.post("./?mod_contratos/listar",{
			"view_carrito_main":'1',
			"calcular_monto":'1', 
			"token": instance._rand 
		},function(data){ 
			$("#pro_total_a_pagar").html(instance.number_format(data.precio_lista)); 
			$("#pro_total_descuento").html(instance.number_format(data.total_descuentos)); 
			$("#precio_lista_menos_TotalDescuentoMonto").html(instance.number_format(data.capital_a_financiar_menos_descuento)); 
			$("#txt_monto_incial").val(instance.number_format(data.monto_enganche)); 
			$("#monto_inicial_por").html(instance.number_format(data.porciento_enganche)); 
			$("#capital_neto_a_pagar").html(instance.number_format(data.capital_neto_a_pagar)); 
			$("#total_interes_a_pagar").html(instance.number_format(data.monto_total_interes_a_pagar)); 
			$("#sub_total_a_pagar").html(instance.number_format(data.sub_total_a_pagar)); 
			$("#mensualidades").html(instance.number_format(data.mensualidades)); 
			$("#total_a_pagar").html(instance.number_format(data.total_a_pagar)); 
			
			instance.fire("OnRenderDetalle");
		},"json");	
	},
	
	number_format : function(value){
		return parseFloat(value,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString()	
	}
	
});