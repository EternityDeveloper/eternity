var CFinanciamiento = new Class({
	dialog_container : null,
	_id_nit : null,
	_rand : 0, 
	_filtro : null,
	_event_add : false,
	_producto :null,
	_plan_selected: null,
	initialize : function(dialog_container,producto){
		this.main_class="CFinanciamiento";
		this.dialog_container=dialog_container;
 		var instance=this;
		if (producto!=null){
			this._producto=producto; 
			producto.addListener("OnProductoSelect",function(){
				$("#bt_c_financiamiento_find").show();
				instance.doListPlan();
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
		instance.post("./?mod_contratos/listar",{
				"view_carrito_main":'1',
				"template_financiamiento":'1',
				"type":instance._type,
				"token":rand
		},function(data){  	
			$("#"+template_view_id).html(data);
			if (instance._type=="edit"){
				$("#bt_c_financiamiento_find").show();	
				instance._event_add=false;
				instance.doListPlan();
			}
			
			$("#p_precio_lista").change(function(){ 
				if (instance._plan_selected!=null){
					instance.post("./?mod_contratos/listar",{
							"view_carrito_main":'1',
							"process_update_financiamiento":'1',
							"financiamiento": $.base64.encode(JSON.stringify(instance._plan_selected)),
							"monto_precio_lista":$(this).val(),
							"token": instance._rand,
							"type_product":instance._producto._type_product /*Define si es un producto o servicio*/
						},function(info){ 
							if (info.code=="invalid"){
								alert("Error al tratar de asignar el plan de financiamiento!");
							}else{ 
								instance.fire("OnPlanSelect",data);	
								instance.fire("OnPlanChange");	
							}
					},"json");	
				}
			});
		});
	},
	
	cleanView : function(){
		$("#p_plan").html('');
		$("#p_iteneres").html('');
		$("#p_precio_lista").val(0);
		$("#p_enganche").html('');
		$("#p_plazo").html('');
		$("#p_mondeda").html(''); 	
	},
	doListPlan : function(){
		var instance=this; 
		if (this._event_add){ return false;} 
		this._event_add=true;
 
		$("#bt_c_financiamiento_find").click(function(){
			var finan= new PlanesFinanciamiento(instance.dialog_container);
			//instance._producto[instance._randViewID].data.product_id
			instance._filtro.token=instance._rand;
			/*Define el tipo de producto que sera afectado*/
			instance._filtro.type_product=instance._producto._type_product
			
			finan.viewPlanListProduct(instance._filtro);
			finan.addListener("plan_select",function(data){
				$("#product_content").show();
				$('#product_content').html(''); 
				
				instance._plan_selected=data; 
				
				instance.post("./?mod_contratos/listar",{
						"view_carrito_main":'1',
						"process_financiamiento":'1',
						"financiamiento": $.base64.encode(JSON.stringify(data)),
						"token": instance._rand,
						"type_product":instance._producto._type_product /*Define si es un producto o servicio*/
					},function(info){ 
						if (info.code=="invalid"){
							alert("Error al tratar de asignar el plan de financiamiento!");
						}else{ 
							instance.fire("OnPlanSelect",data);	
							instance.fire("OnPlanChange");	
						}
				},"json");		
				
				$("#p_plan").html(data.codigo);
				$("#p_iteneres").html(data.por_interes);
				$("#p_precio_lista").val(parseFloat(data.precio,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
				$("#p_enganche").html(data.por_enganche);
				$("#p_plazo").html(data.plazo);
				$("#p_mondeda").html(data.moneda); 
				$("#bt_c_financiamiento_find").html("Cambiar");
				
				/*VISTA EXTERNA*/
				$("#detalle_plan_"+instance._rand).html(data.codigo);
				$("#detalle_plazo_"+instance._rand).html(data.plazo);
				//$("#detalle_cantidad_"+instance._rand).html(data.plazo);
			});		
			
		});
	}  
	
});