var PagoCuota = new Class({
	dialog_container : null,
	_token: null,
	_id_nit : null, 
	_no_reserva : null,
	initialize : function(dialog_container){
		this.main_class="PagoCuota";
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
			title	: "Pago de cuota",
			reserva	: instance._no_reserva,
			contrato: instance._contrato,
			id_nit 	: instance._id_nit,
			/*Al cargar la pagina*/
			onload: function(){
				var inst=this;  
				$("#detalle_general").show();
			//	setTimeout(function(){$("#total_a_pagar_fp").hide(); },300);
				/*CARGO EL LISTADO DE SOLICITUDES PARA PODER REALIZAR EL ABONO A CAPITAL*/
				instance.post(
					"./?mod_caja/delegate",
					{
						view_listado_requerimientos:1,
						contrato :instance._contrato
					},
					function(data){ 
						 $("#detalle_general").html(data.html)
				/*		 $("#f_pago_total_a_pagar").html('<span class="badge alert-danger">'+
						 								instance.number_format(data.monto_a_pagar)+'</span>');
						inst.monto_a_pagar=data.monto_a_pagar;
						comp._forma_pago._data.monto_a_cobrar=data.monto_a_pagar;*/
						$(".listado_rc_").click(function(){
							alert('fdas');	
						});
						
					},"json");	
					
				comp.addListener("newFormaPago",function(){
					/*	$("#total_a_pagar").html('<span class="badge alert-danger">'+
						 								instance.number_format(inst.monto_a_pagar)+'</span>');		*/
				});	
			},
			/*cuando el formulario esta lleno*/
			onSubmit : function(){ 
				data={};
				data.forma_pago_token=comp._forma_pago.getToken();
 				data.type_transapcion='PAGOCUOTA'; 
				data.observacion=$("#observacion").val(); 
				data.doctype=doctype;			
				data.pago_cuota=1;	
				data.caja_submit=1;
				data.isNCF=comp._forma_pago._containNCF;
				data.rnc=comp._forma_pago._rnc_ced_factura;  
				data.contrato=id;
				
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
	} 
	
	
});