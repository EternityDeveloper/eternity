var ContratoDescuento = new Class({
	dialog_container : null,
	_contrato_id : null,
	_form_name : "frm_new_actividad",
	_descuentos : {
		valid:false,
		data: null	
	},
	initialize : function(dialog_container){
		this.main_class="ContratoDescuento";
		this.dialog_container=dialog_container; 
	},
	/*GENEREA LA VISTA
		type es el tipo de descuento por MONTO O PORCENTAJE
		situacion si es necesidad o prenecesidad */
	createView : function(moneda,situacion,type){
		var instance=this;	 
 		instance.post("./?mod_contratos/listar",{
			"add_descuento":'1' ,
			'moneda':moneda,
			'type':type,
			"situacion":situacion 
		},function(data){ 
 			var dialog=instance.createDialog(instance.dialog_container,"Agregar Descuento",data,450);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 20]); 
			var descuento_id='';
			var obj={};
			
			$("#tipo_descuento").change(function(){				
				if ($(this).val()!=""){
					descuento_id=$(this).val();
					obj= jQuery.parseJSON($(this).find(':selected').attr("alt"));
					
					$("#monto").val(obj.monto);
					$("#porcentaje").val(obj.porcentaje);
					$("#monto").prop('disabled', true);
					$("#porcentaje").prop('disabled', true);
					
					$(".cl_monto").show();
					$(".cl_porcentaje").show();
					$("#desc_apply").prop('disabled', false);
					
					
					if (obj.monto_ingresado=="S"){
						$("#monto").prop('disabled', false);
					}
					if (obj.ingresado=="S"){
						$("#porcentaje").prop('disabled', false);
					}
					
				}else{
					descuento_id=''
					$(".cl_monto").hide();
					$(".cl_porcentaje").hide();
					$("#desc_apply").prop('disabled', true);
				}
			});
			
			$("#desc_apply").click(function(){
				$("#desc_apply").prop('disabled', false);
				if (descuento_id!=""){

					var des_producto=$("#desc_producto").val();
					
					var descuento={
						"monto":$("#monto").val(),
						"porcentaje":$("#porcentaje").val(),
						"descuento_id":descuento_id,
						"codigo":obj.codigo,
						"descripcion":obj.descripcion,
						"des_producto":des_producto,
						"type": type
					};
					var valid=true;
					var message="";
					
					if (des_producto==""){
						alert('Debe de seleccionar un producto!');
						return false;
					}
					
					if (type=="MONTO"){
						if (!($("#monto").val()>0)){
							valid=false;
							message="Debe de ingresar un monto mayor que 0";
						}
					}
					if (type=="PORCIENTO"){
						if (!($("#porcentaje").val()>0)){
							valid=false;
							message="Debe de ingresar un porcentaje mayor que 0";
						}
					}
					if (valid){
						instance.close(dialog);
						instance.fire("onSelectDiscount",descuento);
					}else{
						alert(message);	
					}
				}else{
					alert('Debe de seleccionar el tipo de descuento!');	
				}
			});
			
			$("#cancel_decuento").click(function(){
				instance.close(dialog);	
			});
			
		});
		
	}
});