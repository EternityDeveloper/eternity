var CambioFormaPagoCT = new Class({
	dialog_container: null,
	initialize : function(containner){
		this.main_class="CambioFormaPagoCT"; 
		this.dialog_container=containner; 
		/*NECESARIO PARA VALIDAR LAS TARJETAS DE CREDITOS*/
		this.loadScript(["script/jquery.creditCardValidator.js"],function(){});
		
	},
	doChange : function(){
		var instance=this;
		$("#metodo_cobro").change(function(){
			var contrato=$("#metodo_cobro").attr("contrato");
			switch($(this).val()){
				case "DAU":
					instance.doViewChangeCreditCard(contrato,$(this).val()); 
				break;	
				default: 
				instance.doSave(contrato,$(this).val());
			}
		});	
	},	
	doSave : function(contrato,metodo_cobro){
		this.post("./?mod_cobros/delegate&doChangeMetodoCobro",
					{
						contrato:contrato,
						"fpago":metodo_cobro
					},function(data){  
					alert(data.mensaje);
			
		},"json");
	},
	doViewChangeCreditCard : function(ct,metodo_cobro){
		var instance=this; 
		instance.post("./?mod_cobros/delegate&view_metodo_cobro_credit_card",{},function(data){  
			instance.doDialog("myModal",instance.dialog_container,data);
			
			var tarjeta_valida=false;
			var tipo_tarjeta="";
			var numero_tarjeta="";
			var cvv_metodo="";
			var month_venc="";
			var year_venc="";
			var dias_debito="";
			$('#numero_tarjeta').validateCreditCard(function(result) {
				  
				if (result.valid){ 
					$("#tipo_tarjeta").html('Tipo de tarjeta: '+ (result.card_type == null ? '-' : result.card_type.name))				
					tipo_tarjeta=(result.card_type == null ? '-' : result.card_type.name);
					tarjeta_valida=true;
					numero_tarjeta=$(this).val();
				}								 
			});		 
			
			$("#month_venc").change(function(){
				if ($(this).val()!=""){
					month_venc=$(this).val();	
				}
			});
			$("#year_venc").change(function(){
				if ($(this).val()!=""){
					year_venc=$(this).val();	
				}
			});			
			$("#cvv_metodo").change(function(){
				if ($(this).val()!=""){
					cvv_metodo=$(this).val();	
				}
			});	
			$("#dias_debito").change(function(){
				if ($(this).val()!=""){
					dias_debito=$(this).val();	
				}
			});	
			 
			$("#guardar_cambio").click(function(){  
				if (!tarjeta_valida){
					alert('Debe de insertar un numero de tarjeta valida!');
					return false;	
				}
				if (!month_venc){
					alert('Debe de insertar un numero de tarjeta valida!');
					return false;	
				}				
				instance.post("./?mod_cobros/delegate&doSaveMetodoCobroTC",{ 
						'contrato':ct,
						'metodo_cobro':metodo_cobro,
						'tipo_tarjeta':tipo_tarjeta,
						'numero_tarjeta':numero_tarjeta,
						'cvv_metodo':cvv_metodo,
						'month_venc':month_venc,
						'year_venc':year_venc,
						'dias_debito':dias_debito,
						'comentario':$("#comentario").val(),
				},function(data){ 
					alert(data.mensaje);
					if (!data.valid){
						$("#myModal").modal('hide');
						window.location.reload();
					}
				},"json"); 
			});
		});	
		 
	},	
	
});