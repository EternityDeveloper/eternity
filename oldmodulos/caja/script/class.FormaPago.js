var FormaPago = new Class({
	dialog_container : null,
	_data :{ 
		monto_a_cobrar : 0,
 		monto_a_pagar :0,
		forma_pago : 0,
 		banco : null,
		autorizacion :null,
		observacion :null,
 		total_reserva:0,
		monto_minimo_a_pagar:0,
		monto_a_pagar_acumulado:0
	}, 
	fields : {
		tipo_movimiento :1	
	}, 
	_f_pago_data : [],	
	_f_pago_descuento : [],	
	_view_forma_pago : null,
	total_abonado_pagos :0,
	token : 0,
	_descuento_token : null,
	_id_nit_cliente : null,
	_factura : null,
	_rnc_ced_factura : null,
	_containNCF : false,
	_containCF: false,
	_contrato : '',
	initialize : function(dialog_container){
		this.main_class="FormaPago";
		this.dialog_container=dialog_container; 
		this._id_rnd=this.getRand();
		this._f_pago_data[this._id_rnd]=[];
		this._f_pago_descuento[this._id_rnd]=[];
		this.token=this.getRand();
		//this._descuento_token=this.getRand();
		
	},	
	setNIT : function(id_nit){
		this._id_nit_cliente=id_nit;
	},
	setContrato : function(contrato){
		this._contrato=contrato;
	},	
	getFormaPago : function(){
		return this._f_pago_data[this._id_rnd];
	},
	getToken : function(){
		return this.token;
	},
	setToken  : function(token){
		this.token=token;
	},
	getDescuentos : function(){
		return this._f_pago_descuento[this._id_rnd];
	},	 
	setFormaPagoContentView : function(content){
		this._view_forma_pago=content;
	}, 
	
	loadModule : function(){
		var instance=this;  
		
		this._factura = new CFactura(instance.dialog_container,instance._id_nit_cliente);  
		
		instance.post("./?mod_caja/delegate&forma_pago",{"payment_view":"payment_list",
														"token":this.token,
														'contrato':this._contrato},function(data){
			$("#"+instance._view_forma_pago).show();
			$("#"+instance._view_forma_pago).html('');
			$("#"+instance._view_forma_pago).html(data);
			
  
			$("#bt_agrega_fp").click(function(){
				instance.createPaymentDialog();
			});
			
			var descuentos= new CDescuento(instance.dialog_container);
			descuentos.addListener("onSelectDiscount",function(desc){ 
				/*AGREGAR UN DESCUENTO*/
				desc.token=instance.token 
				
				instance.post("./?mod_caja/delegate&forma_pago_add_descuento",desc,function(data){ 									
					instance.renderDetalleFormaPago(data);
					//instance.fire("removePago",data)
				},"json");
			});	
			$("#bt_agregar_descuento").click(function(){
				descuentos.createView('MONTO','LOCAL');
				
			});
			 
			instance._factura.addListener("factura_rnc",function(id_nit){
				instance._rnc_ced_factura=id_nit;
			}); 
			if ($("#IneedFF").is(':checked')){
				instance._containNCF=true;
			//	instance._factura.loadView('factura_view_');	
			}
			
			$("#IneedFF").click(function(){ 
				if ($(this).is(':checked')){
				//	instance._factura.loadView('factura_view_');	
					instance._containNCF=true;
				}else{
					$("#factura_view_").html('');
					instance._containNCF=false;
				}
			});
			$("#IneedCF").click(function(){ 
				if ($(this).is(':checked')){ 
					instance._containCF=true;
				}else{
					$("#factura_view_").html('');
					instance._containCF=false;
				}
			});			
			
			instance.addListener("renderDetalleFormaPago",function(info){   
				instance._data.monto_a_cobrar=info.detalle.monto_a_pagar;
 			});	
			instance.addListener("doGenerateFactura",function(isFact){   
				if (isFact){
					if (!instance._containNCF){
						//instance._factura.loadView('factura_view_');	
					}
				}else{
					if (!instance._containNCF){
						$("#factura_view_").html('');
					}
				}
			});				
		 	 
			instance.fire("onFormaPagoLoad");
		});	

	},
	
	/*LO UTILIZO PARA QUE NO ME ACEPTE MENOS DE UN MONTO X DETERMINADO*/
	setMontoMinimo : function(monto){
		this._data.monto_minimo_a_pagar	=monto; 
		this._data.monto_a_cobrar=this._data.monto_minimo_a_pagar;
	},
	
	createPaymentDialog : function(){ 
		var instance=this;
		this.post("./?mod_caja/delegate&forma_pago",{"payment_view":"payment","token":this.token,
													'contrato':this._contrato},function(data){
			//var dialog=instance.createDialog(instance.dialog_container,"Forma de pago",data,430);
			var dialog=instance.doDialog("modal_forma_pago",instance.dialog_container,data);
 			
			setTimeout(function(){;
				$("#forma_pago").focus();
			},500);
			$("#forma_pago").change(function(){
				var forma_id=$(this).val();
				instance._data.label_forma_pago=$("#forma_pago option:selected").text(); 
				
				$(".tipo_trans_tarjeta").hide();
				instance._data.forma_pago=forma_id;
				switch(forma_id){ 	
					case "TC": //FORMA DE PAGO Tarjeta de credito
						$(".tipo_trans_tarjeta").show();
					break;	
					case "TB": //FORMA DE PAGO Transferencia Bancaria
						$(".tipo_trans_tarjeta").show();
					break; 
					case "CK": //FORMA DE PAGO CHEQUE
						$(".tipo_trans_tarjeta").show();
					break; 
					case "DP": //FORMA DE PAGO CHEQUE
						$(".tipo_trans_tarjeta").show();
					break; 								
												
				}  
			 
			});	
			
			instance.forma_pago_validate();
			
			$("#bt_fp_save").click(function(){
				if ($("#form_forma_pago").valid()){ 
					
					if (parseFloat($('#monto').val()) >= instance._data.monto_minimo_a_pagar){ 
						instance._data.banco=$("#banco").val();
						instance._data.tipo_cambio=$("#tipo_cambio").val();
						
						var label_banco=$("#banco option:selected").text();  
						var autorizacion=$("#autorizacion").val();
						if (instance._data.forma_pago=="EF"){
							label_banco="N/A";
							autorizacion="N/A";  
						}
						instance._data.label_banco=label_banco;
						instance._data.autorizacion=autorizacion; 
						var nenganche=$('#monto').val()
						nenganche= nenganche.replace(",",'');
						nenganche= nenganche.replace(",",'');
						nenganche= nenganche.replace(",",'');	 
						instance._data.monto_a_pagar=parseFloat(nenganche); 
						instance._data.token=instance.token;
				  		 
						/*OCULTO EL MENSAJE DE ALERTA*/
						$("#alert_payment").hide();
					
						instance.post("./?mod_caja/delegate&forma_pago_put",instance._data,function(data){ 
 							instance.renderDetalleFormaPago(data);
							instance.fire("savePago",data.detalle);
							instance.fire("doGenerateFactura",data.doGenerateFactura); 
						},"json");
						
						instance.CloseDialog(dialog); 
					}else{
						alert('El monto debe de ser mayor de '+instance._data.monto_minimo_a_pagar+'!');	
					}
				}				
			}); 		
			
			$("#bt_fp_cancel").click(function(){
				instance.CloseDialog(dialog);
			}); 
			 
			$('#monto').val(instance._data.monto_a_cobrar);
			//$('#monto').formatCurrency();
			
			$('#monto').click(function(){
				$(this).val('');	
			}); 
			
			$('#monto').focusout(function(){
				var nenganche=$(this).val();
				nenganche= nenganche.replace(",",''); 
				if (nenganche<0){
					$(this).val(instance._data.monto_a_cobrar);
					$(this).formatCurrency();
				}	
			}); 
			
			$('#monto').change(function(){ 
				var nenganche=$(this).val();
				if (nenganche>=0.01){
					nenganche=parseFloat(nenganche);  
					instance._data.monto_minimo_a_pagar=0.01;
					if (nenganche >= instance._data.monto_minimo_a_pagar){ 
						instance._data.monto_a_pagar=nenganche;
						$('#monto').val(instance._data.monto_a_pagar);
						$('#monto').formatCurrency();
					}else{
						$('#monto').val(instance._data.monto_minimo_a_pagar);
						$('#monto').formatCurrency();
						alert('El monto debe de ser mayor de '+instance._data.monto_minimo_a_pagar+'!');	
					}
				}else{
					$('#monto').val(instance._data.monto_minimo_a_pagar);
					$('#monto').formatCurrency();
					alert('El monto debe de ser numerico!');	
				} 
			});	
			 
			 
		});		
		
	},
	renderDetalleFormaPago : function(data){
		var instance=this;
		$("#detalle_pagos").html(data.html);
		instance._data.monto_a_pagar_acumulado=data.detalle.monto_acumulado;  
		instance._data.monto_completo=data.detalle.monto_completo;  
		
		$(".fp_remove_dt").click(function(){
			var thi_=this; 
			instance.post("./?mod_caja/delegate&forma_pago_remove",
				{
					id:$(this).attr('id'),
					"token":instance.token 
			},function(data){ 									
				$(thi_).parent().parent().remove(); 
			//	$("#total_a_pagar").html(instance.number_format(data.detalle.monto_a_pagar));
				instance._data.monto_a_pagar_acumulado=data.detalle.monto_acumulado;
				instance.fire("removePago",data)
				instance.fire("doGenerateFactura",data.doGenerateFactura); 
			},"json");
		});
		$(".fp_remove_desc").click(function(){
			var thi_=this; 
			instance.post("./?mod_caja/delegate&forma_pago_descuento_remove",
				{
					id:$(this).attr('id'),
					"token":instance.token
			},function(data){ 									
				$(thi_).parent().parent().remove(); 
		//		$("#total_a_pagar").html(instance.number_format(data.detalle.monto_a_pagar));
				instance._data.monto_a_pagar_acumulado=data.detalle.monto_acumulado;
				instance.fire("removePago",data)
			},"json");
		});
		this.fire("renderDetalleFormaPago",data);
	},
	forma_pago_validate: function(){
		$("#form_forma_pago").validate({
			rules: {
				forma_pago : {
					required: true
				},
				serie_recibo: {
					required: true
				},
				no_recibo: {
					required: true
				},
				monto: {
					required: true,
					number:true
				},
				tipo_cambio: {
					required: true,
					number:true 
				},
				no_documento: {
					required: true 
				} ,
				aprobacion: {
					required: true 
				},
				banco : {
					required: true
				}
			},
			messages : {
				forma_pago : {
					required: "Este campo es obligatorio" 
				},
				serie_recibo : {
					required: "Este campo es obligatorio" 
				},
				no_recibo : {
					required: "Este campo es obligatorio" 
				},
				monto : {
					required: "Este campo es obligatorio",
					number: "Este campo es numerico"
				},
				tipo_cambio : {
					required: "Este campo es obligatorio",
					number: "Este campo es numerico"
				},
				no_documento : {
					required: "Este campo es obligatorio" 
				},
				aprobacion : {
					required: "Este campo es obligatorio" 
				},
				banco : {
					required: "Este campo es obligatorio" 
				}
			}
		});
	},
	
	drawDescuento: function(){
		var instance=this;
		var total_descuento=0;
  
		$("#descuento_field_for_pay").html('');
		
		for(i=0;i<instance._f_pago_descuento[instance._id_rnd].length;i++){ 
			
			var desc=instance._f_pago_descuento[instance._id_rnd][i];
			var tb_='<tr id="d_tr_'+i+'">' +
					' <td width="300" ali>'+desc.descripcion+'</td>'+
					'	<td width="100">'+desc.monto+'</td>' +
					'	<td>'+desc.monto+'</td>'+
					'	<td><span class="ui-button-icon-primary ui-icon ui-icon-closethick" id="dremove_'+i+'" style="cursor:pointer;">X</span></td>' +
					'  </tr>';  
			$("#descuento_field_for_pay").append(tb_);
			
			$("#dremove_"+i).click(function(){ 
				var sp=$(this).attr("id").split("_"); 
				var field=instance._f_pago_descuento[instance._id_rnd][sp[1]];
				$("#d_tr_"+sp[1]).remove();	 				
			//	instance.total_abonado_pagos=parseFloat(instance.total_abonado_pagos) - parseFloat(field.monto_a_pagar);
				 
				var info=instance.removeFromArray(instance._f_pago_descuento[instance._id_rnd],sp[1]);
				instance._f_pago_descuento[instance._id_rnd]=info; 
				 
				total_a_apagar=0; 
				for(i=0;i<info.length;i++){
					var field2=info[i];   
			//		total_a_apagar=total_a_apagar+parseFloat(field2.tipo_cambio*field2.monto_a_pagar,10) 
				} 
				instance.updateTotalaPagar();
				//instance.fire("removeMountDiscount",field);
				
			});
			
			instance.updateTotalaPagar();
		}
	},
	
	updateTotalaPagar : function(){
		var instance=this;
		var total_a_apagar=0;
		for(i=0;i<instance._f_pago_data[instance._id_rnd].length;i++){
			var field=instance._f_pago_data[instance._id_rnd][i];  
			var monto_a_pagar=field.tipo_cambio*field.monto_a_pagar;		 
			total_a_apagar=total_a_apagar+parseFloat(field.tipo_cambio*field.monto_a_pagar,10) 
		}	

		/*var descuento=0;
		for(i=0;i<instance._f_pago_descuento[instance._id_rnd].length;i++){ 
			 var desc=instance._f_pago_descuento[instance._id_rnd][i];	
		//	 descuento=	descuento+parseFloat(desc.monto); 
		}*/
		
		//total_a_apagar=total_a_apagar-descuento;
 		
		$("#f_pago_total_a_pagar").html('<strong>'+parseFloat(total_a_apagar,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString()+'</strong>');		
	},
	
	reloadTable: function(){
		var instance=this;
		$("#list_formas_pagos #t_body_fpago").html('');
		
		var total_a_apagar=0;
		for(i=0;i<instance._f_pago_data[instance._id_rnd].length;i++){
			var field=instance._f_pago_data[instance._id_rnd][i];  
			 
			var monto_a_pagar=field.tipo_cambio*field.monto_a_pagar;
			
			var tb_='<tr id="r_tr_'+i+'"> ' +
					'	<td>'+field.label_forma_pago+'</td>' +
					'	<td>'+field.label_banco+'</td>' +
					'	<td>'+field.autorizacion+'</td>' +
					'	<td>'+field.tipo_cambio+'</td>' +
 					'	<td>'+parseFloat(field.monto_a_pagar,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString()+'</td>' + '	<td>'+parseFloat(monto_a_pagar,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString()+'</td>' + 
					'	<td><span class="ui-button-icon-primary ui-icon ui-icon-closethick" id="remove_'+i+'" style="cursor:pointer;">X</span></td>' +
					'  </tr>';
			//tfoot
			$("#list_formas_pagos #t_body_fpago").append(tb_);
			
			$("#remove_"+i).click(function(){
				var sp=$(this).attr("id").split("_");
				var field=instance._f_pago_data[instance._id_rnd][sp[1]];
				$("#r_tr_"+sp[1]).remove();				
				instance.total_abonado_pagos=parseFloat(instance.total_abonado_pagos) - parseFloat(field.monto_a_pagar);
				
				instance.fire("removeMount",field);
				var info=instance.removeFromArray(instance._f_pago_data[instance._id_rnd],sp[1]);
				instance._f_pago_data[instance._id_rnd]=info; 
				 
				total_a_apagar=0; 
				for(i=0;i<info.length;i++){
					var field2=info[i];   
					total_a_apagar=total_a_apagar+parseFloat(field2.tipo_cambio*field2.monto_a_pagar,10) 
				}
				
				$("#f_pago_total_a_pagar").html('<strong>'+parseFloat(total_a_apagar,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString()+'</strong>');
				
			});
			
			total_a_apagar=total_a_apagar+parseFloat(field.tipo_cambio*field.monto_a_pagar,10) 
		}
		
		//$("#f_pago_total_a_pagar").html('<strong>'+parseFloat(total_a_apagar,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString()+'</strong>');
		
		this.updateTotalaPagar();
		
		
	},
 	
	removeFromArray : function(arrays,rm_index){
		var instance=this;
		var data=[];
		//instance._f_pago_data[instance._id_rnd]
		for(i=0;i<arrays.length;i++){
			if (i!=rm_index){
				//data.push(instance._f_pago_data[instance._id_rnd][i]);	
				data.push(arrays[instance._id_rnd][i]);	
			}
		}
		return data;
	}
	
	
});

var CDescuento = new Class({
	dialog_container : null,
	_contrato_id : null,
	_form_name : "frm_new_actividad",
	_descuentos : {
		valid:false,
		data: null	
	}, 
	initialize : function(dialog_container){
		this.main_class="CDescuento";
		this.dialog_container=dialog_container; 
	},
	/*GENEREA LA VISTA
		type es el tipo de descuento por MONTO O PORCENTAJE*/
	createView : function(type,moneda){
		var instance=this;	  
		instance.post("./?mod_caja/delegate&forma_pago_descuento",{
			"add_descuento":'1' , 
			'type':type,
			'moneda':moneda
		},function(data){  	
			//var dialog=instance.createDialog(instance.dialog_container,"Agregar Descuento",data,450);
			var dialog= instance.doDialog("modal_forma_pago_descuento",instance.dialog_container,data); 
		//	instance._dialog=dialog;
		//	var n = $('#'+dialog);
		//	n.dialog('option', 'position', [(document.scrollLeft/550), 20]); 
			var descuento_id='';
			var obj={};
			 
			$("#tipo_descuento").change(function(){				
				if ($(this).val()!=""){
					descuento_id=$(this).val();
					obj= jQuery.parseJSON($(this).find(':selected').attr("alt"));
					
					$("#autorizacion_tr").hide();
					$("#autorizado_por").val('');
					$("#autorizacion_id").val('');
					
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
					if (obj.autorizacion=="S"){
						$("#autorizacion_tr").show();
						$("#autorizado_por").combogrid({
							url: './?mod_estructurac/list_view2&empleados_list=1', 
							colModel: [ {'columnName':'nombre','width':'60','label':'Nombre'} ],
							select: function( event, ui ) {
								//$("#nombre_empleado").val( ui.item.nombre );
								$("#autorizado_por").val(ui.item.nombre); 
								$("#autorizado_por_id").val(ui.item.value);
  
								return false;
							}
						});
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
					var descuento={
						"monto":$("#monto").val(),
						"porcentaje":$("#porcentaje").val(),
						"autorizacion_id":$("#autorizado_por_id").val(),
						"isAutorizadoNeed":obj.autorizacion,
						"descuento_id":descuento_id,
						"codigo":obj.codigo,
						"descripcion":obj.descripcion,
					};
					 
					var valid=true;
					var message="";
					
					var autorizacion=$("#autorizado_por").val();
					
					if (obj.autorizacion=="S"){
						if (autorizacion==""){
							valid=false;
							message="Debe de especificar la persona que ha autorizado dicha transapción";	
						}	
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
						instance.CloseDialog(dialog);
						instance.fire("onSelectDiscount",descuento);
					}else{
						alert(message);	
					}
				}else{
					alert('Debe de seleccionar el tipo de descuento!');	
				}
			});
			
			$("#cancel_decuento").click(function(){
				instance.CloseDialog(dialog);	
			});
			
		});
		
	}
});