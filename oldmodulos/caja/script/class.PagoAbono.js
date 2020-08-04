var PagoAbono = new Class({
	dialog_container : null,
	_token: null,
	_id_nit : null, 
	_no_reserva : null,
	initialize : function(dialog_container){
		this.main_class="PagoAbono";
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
		this._id_nit=id;  
		var comp = new PagoComponent(this.dialog_container); 
		
		var accion={
			title	: "Abono",
			reserva	: instance._no_reserva,
			contrato: null,
			id_nit 	: instance._id_nit,
			/*Al cargar la pagina*/
			onload: function(){
				
			},
			/*cuando el formulario esta lleno*/
			onSubmit : function(){ 
				data={};
				data.forma_pago_token=comp._forma_pago.getToken();
 				data.type_transapcion='PAGOABONO'; 
				data.observacion=$("#observacion").val(); 
				data.doctype=doctype;			
				data.pago_abono=1;	
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
	} 
	
	
});