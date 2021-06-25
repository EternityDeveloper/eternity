/* CAMBIO DE PLAN*/
var CCambioPlan = new Class({
	dialog_container : null,  
	_rand : null,
	_polygon :null,
	initialize : function(dialog_container){
		this.main_class="CCambioPlan";
		this.dialog_container=dialog_container; 
	}, 
	doView: function(contrato){
		var instance=this; 
		this._contrato=contrato;
		
		instance.post("./?mod_cobros/delegate&view_cambio_plan_sn_ab",{ 
				'contrato':this._contrato 
		},function(data){   
			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			})  
			setTimeout(function(){ $("#monto_a_abonar").focus() },1000);
 
			var procesar_abono=false;
			var plazo=0;
			var int_financiamiento=0;
			var cambio_plazo=""
/*			$("#monto_a_abonar").change(function(){
				monto_abono=$(this).val();
				instance.post("./?mod_cobros/delegate&calc_abono_capital",{ 
					'contrato':instance._contrato,
					'monto_a_abonar':$(this).val(),
					'plazo':plazo
				},function(data){
					$("#nuevo_saldo").val(instance.number_format(data.precio_neto));
					$("#nuevo_plazo").val(data.plazo);
					$("#nueva_cuota").val(instance.number_format(data.monto_cuota));						
					if (data.error){
						alert(data.mensaje);						
					}else{
						procesar_abono=true;	
					}				
				},"json")
			});	*/	
			 
			$("#int_financiamiento").change(function(){
			//	if ($(this).val()>=0){ 
					int_financiamiento=$(this).val(); 
					$("#nuevo_plazo").change();   
			//	}
			});
			$("#nuevo_plazo").change(function(){
				plazo=$(this).val();     
				instance.post("./?mod_cobros/delegate&calc_cambio_plan",{ 
					'contrato':instance._contrato, 
					'int_financiamiento':int_financiamiento,
					'plazo':plazo
				},function(data){
					$("#int_financiamiento").val(data.interes_finaciamiento);
					$("#nuevo_saldo").val(instance.number_format(data.precio_neto));
					$("#nuevo_plazo").val(data.plazo);
					$("#nueva_cuota").val(instance.number_format(data.monto_cuota));						
					if (data.error){
						alert(data.mensaje);						
					}else{
						procesar_abono=true;	
					}				
				},"json")
				
			});		
			$(".plazo_l").change(function(){ 
				cambio_plazo=$(this).val();
				$("#nuevo_plazo").prop('disabled',false);
			});	
			
			
			$("#procesar_abono").click(function(){
				if (!procesar_abono){
					alert('No se puede procesar el abono sin antes completar todos los datos correctamente!');
					return false;
				}  
				instance.post("./?mod_cobros/delegate&CGCambioPlan",{ 
						"plazo":plazo, 
						'contrato':instance._contrato,
						'int_financiamiento':int_financiamiento,
						"comentario":$("#cp_comentarios").val()
					},function(data){
						alert(data.mensaje)	
						if (!data.error){
							if (confirm('Desea imprimir la solicitud')){
								$("#close_view").click();
								window.open("./?mod_cobros/delegate&solicitud_gestion_abono&id="+data.id_solicitud); 
							}else{
								window.location.reload();
							}
						} 
						
					},"json");		
			});
						
		});			
	} 
	
});