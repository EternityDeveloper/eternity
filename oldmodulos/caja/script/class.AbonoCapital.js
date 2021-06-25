var AbonoCapital = new Class({
	dialog_container : null,
	_token: null,
	_id_nit : null, 
	_no_reserva : null,
	_contrato : null,
	initialize : function(dialog_container){
		this.main_class="AbonoCapital";
		this.dialog_container=dialog_container; 
		this._token=this.getRand();
	},
	setToken : function(token){
		this._token=token;
	},
	getToken : function(){
		return this._token;
	},
	/*
		Este metodo es el principal 
		el cual se programa el comportamiento del a ventana emergente
	*/
	doView : function(doctype,id){
		var instance=this; 
		this._contrato=id;  
		var comp = new PagoComponent(this.dialog_container); 
		
	 
		var accion={
			title	: "Abono a Capital",
			reserva	: 0,
			contrato: instance._contrato,
			id_nit 	: instance._id_nit,
			monto_a_pagar: 0,
			/*Al cargar la pagina*/
			onload: function(){  
				var inst=this;
				$("#detalle_general").show();
			//	setTimeout(function(){$("#total_a_pagar_fp").hide(); },300);
				/*CARGO EL LISTADO DE SOLICITUDES PARA PODER REALIZAR EL ABONO A CAPITAL*/
				instance.post(
					"./?mod_caja/delegate",
					{
						view_solicitud_gestion_abono:1,
						contrato :instance._contrato
					},
					function(data){ 
						 $("#detalle_general").html(data.html)
						 $("#f_pago_total_a_pagar").html('<span class="badge alert-danger">'+
						 								instance.number_format(data.monto_a_pagar)+'</span>');
						inst.monto_a_pagar=data.monto_a_pagar;
						comp._forma_pago._data.monto_a_cobrar=data.monto_a_pagar;
						
					},"json");	
					
				comp.addListener("newFormaPago",function(){
						$("#total_a_pagar").html('<span class="badge alert-danger">'+
						 								instance.number_format(inst.monto_a_pagar)+'</span>');		
				});			
			},
			/*cuando el formulario esta lleno*/
			onSubmit : function(){ 
				data={};
				data.forma_pago_token=comp._forma_pago.getToken();
 				data.type_transapcion='ABONOCAPITAL'; 
				data.observacion=$("#observacion").val(); 
				data.doctype=doctype;			
				data.pago_abono_capital=1;	
				data.caja_submit=1;
				data.isNCF=comp._forma_pago._containNCF;
				data.rnc=comp._forma_pago._rnc_ced_factura;
				data.id_nit=this.id_nit;
				data.no_reserva=this.reserva;
				data.contrato=this.contrato; 
				comp.doDisableSubmit(); 
				instance.post("./?mod_caja/delegate",data,
					function(data){ 
						alert(data.mensaje); 
						comp.doEnableSubmit();
						if (!data.error){
							 comp.Cerrar();	 
							 window.location.reload();
						}
						
					},"json");
					
					
			} 	
		}  
		comp.doView(accion); 
		
	}, 
	doLoadAbonos : function(id_nit){
		var instance=this;  	
		this.post("./?mod_caja/delegate",{
									"view_person_abono_resumen":1,
									"id_nit":id_nit,
									"token":instance._token
								},function(data){ 
		 
			$("#detalle_general").show();
			$("#detalle_general").html(data); 
			
			var tr_abono=$(".abono_persona"); 
			tr_abono.css('cursor', 'pointer' );
			tr_abono.click(function(){
				var nTds=$(this).children();
				//$(nTds).find("input").prop("checked",true);  
				if ($(nTds).find("input").prop("checked")){
					$(nTds).find("input").prop("checked",false);   
					instance.doPutAbonoToInicial($(nTds).find("input").val(),"remove");
				}else{
					$(nTds).find("input").prop("checked",true); 
					instance.doPutAbonoToInicial($(nTds).find("input").val(),"add");	
				} 	
			}); 
			
			tr_abono.hover(function(){ 
				$(this).addClass('hover_tr');  
			},function(){ 
				$(this).removeClass('hover_tr'); 
			});
			 
		});		
	},
	/*Agrega un abono*/
	doPutAbonoToInicial : function(items,cmd){
		var instance=this; 
		instance.post("./?mod_caja/delegate&processSelectAbono",{ 
			'items':items,
			"cmd": cmd,
			"token":instance._token,
			'id_nit':this._id_nit
		},function(data){  
			 
		})
	},
	
	/*VISTA QUE MUESTRA EL LISTADO QUE HA ABONADO UN CLIENTE*/
	viewAbonoPerson : function(id){
		var instance=this; 
		this._id_nit=id; 		
		this.post("./?mod_caja/delegate",{"view_person_abono":1,"id_nit":id,"token":instance._token},function(data){ 
			var dialog=instance.createDialog(instance.dialog_container,"Iniciales",data,1000);
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/450), 0]);
			
			var obj={
				monto:0	
			}
			
			$("#bt_abni_cerrar").click(function(){
				instance.close(dialog);	  
			});
			
			var tr_abono=$(".abono_persona"); 
			tr_abono.css('cursor', 'pointer' );
			tr_abono.click(function(){
				var nTds=$(this).children();
				//$(nTds).find("input").prop("checked",true);  
				if ($(nTds).find("input").prop("checked")){
					$(nTds).find("input").prop("checked",false);   
					instance.doPutAbono($(nTds).find("input").val(),"remove",obj);
				}else{
					$(nTds).find("input").prop("checked",true); 
					instance.doPutAbono($(nTds).find("input").val(),"add",obj);	
				} 	
			}); 
			
			tr_abono.hover(function(){ 
				$(this).addClass('hover_tr');  
			},function(){ 
				$(this).removeClass('hover_tr'); 
			});
			 
		});
	},
	/*Agrega un abono*/
	doPutAbono : function(items,cmd,obj){
		var instance=this; 
		instance.post("./?mod_caja/delegate&processPutAbono",{ 
			'items':items,
			"cmd": cmd,
			"token":instance._token,
			'id_nit':this._id_nit
		},function(data){  
			$("#abi_monto_total").html('<strong>'+instance.number_format(data.monto)+'</strong>');
			obj.monto=data.monto;
			/**/
			instance.fire("onSelectMontoCaja",data.html);
		},"json")
	}
	
});