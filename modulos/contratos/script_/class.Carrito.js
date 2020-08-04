var Carrito = new Class({
	dialog_container : null,
	_rand : 0,
	_type : null,
	initialize : function(dialog_container){
		this.main_class="Carrito";
		this.dialog_container=dialog_container;
	},
	doCarView : function(rand,logic,title){
		var instance=this;	   
		this._rand=rand;
		this._type="create";
		instance.post("./?mod_contratos/listar",{
				"view_carrito_main":'1',
				"rand": rand
		},function(data){  	
			var dialog=instance.createDialog(instance.dialog_container,title,data,800);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 20]);  
			
			if (logic!=null){
				if (typeof logic.doLogic=="function"){
					logic.doLogic(instance);
				}
			}
			$("#bt_c_add_product").click(function(){
 				instance.post("./?mod_contratos/listar",{
					"view_carrito_main":'1',
					"validar_item":'1', 
					"token": instance._rand 
				},function(data){ 
					if (data.code!="valid"){
						alert('Error no se ha podido guardar la información, intente denuevo!');	
					}
					instance.fire("doRenderProduct",$("#cuadro_producto").html());
					instance.calcularMontoGeneral()
					instance.close(dialog)
				},"json");	
			});
			
			$("#bt_produc_cancel").click(function(){
				instance.close(dialog);
				instance.calcularMontoGeneral()
			});
		});
	},
	
	doCarEditView : function(rand,logic,title){
		var instance=this;	   
		this._rand=rand;
		this._type="edit";
		
		/*EDITAR UN PRODUCTO / SERVICIO*/
		$("#producto_"+rand).click(function(){
			instance.post("./?mod_contratos/listar",{
					"view_carrito_main":'1',
					"token": rand,
					"type":instance._type
			},function(data){  	
				var dialog=instance.createDialog(instance.dialog_container,title,data,800);
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/550), 20]);  
				
				if (logic!=null){
					if (typeof logic.doLogic=="function"){
						logic.doLogic(instance);
					}
				}
				$("#bt_produc_cancel").click(function(){
					instance.close(dialog);
					instance.calcularMontoGeneral();
				});
			});
		});
		/*ELIMINAR UN PRODUCTO*/
		$("#producto_remove_"+rand).click(function(){ 
				
			var token=$(this).attr("token");
			var ifdata='<br><center><strong><p>Esta seguro de eliminar este item? </p> </strong></center><br><center><button type="button" id="caputra_si" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">SI</span></button>&nbsp;&nbsp;<button id="captura_no" type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">NO</span></button></center>';
			
			var dialog=instance.createDialog(instance.dialog_container,"Eliminar item",ifdata,420);
		  
			$("#caputra_si").click(function(){ 
				//$('#'+dialog).showLoading({'addClass': 'loading-indicator-bars'});	
				instance.removeItemFromServer(token); 
				instance.close(dialog);
			});
			$("#captura_no").click(function(){
				instance.close(dialog);
				 
			});	 
 
		});

		
	},
	removeItemFromServer : function(token){
		var instance=this;
		instance.post("./?mod_contratos/listar",{
			"removeItemFromServer":'1',
			"token":token 
		},function(data){ 
			$("#bloque_producto_"+token).remove();	
			instance.calcularMontoGeneral();		 
		});
	},
	enableButtom: function(){
		$("#bt_c_add_product").show();
	},
	
	calcularMontoGeneral : function(){
		var instance=this;
		instance.post("./?mod_contratos/listar",{
			"view_carrito_main":'1',
			"calcular_monto_general":'1', 
			"token": instance._rand 
		},function(data){ 
			$("#gdt_precio_lista").html(instance.number_format(data.precio_lista)); 
			$("#gdt_monto_inicial_caja").html(instance.number_format(data.monto_enganche_caja));
			$("#gdt_monto_inicial").html(instance.number_format(data.monto_enganche));
			
			$("#gdt_detalle_monto_inicial").html('<strong>MONTO INCIAL ('+data.porciento_enganche+'%)</strong>');
			
			$("#dt_total_descuento").html(instance.number_format(data.total_descuentos));  
			$("#dt_capital_financiar_menos_descuentos").html(instance.number_format(data.capital_neto_a_pagar));    
			$("#dt_total_interes_a_pagar").html(instance.number_format(data.monto_total_interes_a_pagar)); 
			$("#dt_mensualidades").html(instance.number_format(data.mensualidades)); 
			$("#dt_total_a_pagar").html(instance.number_format(data.total_a_pagar)); 
			instance.fire("MontoGeneral",data);
		},"json");	
	}
	
});