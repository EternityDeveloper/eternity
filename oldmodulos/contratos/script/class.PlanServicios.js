var PlanServicios = new Class({
	dialog_container : null,
	_contrato_id : null,
	_form_name : "frm_new_actividad",
	_producto : [],
	_financiamiento : [],
	_descuento : [],
	monto_enganche: 0,
	_totales_plan : null,
	_randViewID : 0,
	initialize : function(dialog_container){
		this.main_class="PlanServicios";
		this.dialog_container=dialog_container; 
		this._randViewID=this.getRand();
		
		this._descuento[this._randViewID]={
												valid:false,
												monto: [],
												porciento: []
											};
		
		this._financiamiento[this._randViewID]={
													valid:false,
													data: null	
												}
		
		
		this._producto[this._randViewID]={
							valid:false,
							data: null	
						};

	},
	
	captureEdit : function(filtro){
		var instance=this;
		$("#edit_"+this._randViewID).click(function(){ 	
			instance.editProduct(filtro);
		});
	},
	
	fillProduct : function(){
		var producto=this._producto[this._randViewID].data;
		$("#display_product").show();
		$('#display_product').html('');
		$('#display_product').html('<table class="display" id="inv_simple_list"></table>' );
		createTable("inv_simple_list",{
				"bSort": false,
				"bInfo": false, 
				"bLengthChange": false,
				"bFilter": false, 
				"bPaginate": false,
				"aaData": [ 
						[ 
						  producto.jardin,
						  producto.fase,
						  producto.bloque,
						  producto.lote,
						  producto.estatus,
						  producto.cavidades,
						  producto.osarios
						] 
					],
				"aoColumns": [
						{ "sTitle": "Jardin" },
						{ "sTitle": "Fase" },
						{ "sTitle": "Bloque" },
						{ "sTitle": "Lote" },
						{ "sTitle": "Estatus" },
						{ "sTitle": "Cavidades" },
						{ "sTitle": "Osarios" } 
						
					],
				  "oLanguage": {
						"sLengthMenu": "Mostrar _MENU_ registros por pagina",
						"sZeroRecords": "No se ha encontrado - lo siento",
						"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
						"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
						"sInfoFiltered": "(filtrado de _MAX_ total registros)",
						"sSearch":"Buscar"
					} 
		});
		
	},
	fillFinanciamiento : function(){
		var instance=this;
		var totales=instance._totales_plan;
		totales.fire("onChangeData"); 					
	},
	fillDescuentosMontos : function(){
		var instance =this;
		var totales=instance._totales_plan;
		
		var data=[];
		$("#descuento_x_monto").show();
		$('#descuento_x_monto').html('');
		$('#descuento_x_monto').html('<table class="display" id="descuento_monto_simple_list"></table>' );
		var data=[];
		$("#descuento_x_monto").show();
		$('#descuento_x_monto').html('');
		$('#descuento_x_monto').html('<table class="display" id="descuento_monto_simple_list"></table>' );
		var sum=0;
		for(i=0;i<instance._descuento[instance._randViewID].monto.length;i++){
			var dt=instance._descuento[instance._randViewID].monto[i]; 
			var tb_='<tr><td style="padding-left:8px;width:70%;">'+dt.descripcion+'</td><td style="width:35px;">'+parseFloat(dt.monto,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString()+'<a href="#down" id="down" alt="'+dt.porcentaje+'" counter="'+i+'" class="bt_remove_monto"><img src="images/cross.png" width="16" height="16"></a></td></tr>';
			$("#descuento_monto_simple_list").append(tb_);
			sum=sum+ parseFloat(dt.monto);
		} 
		
		$(".bt_remove_monto").click(function(){
			var alt=$(this).attr("alt");
			var counter=$(this).attr("counter");
			if (instance._descuento[instance._randViewID].monto.length==1){
				$('td',$("#tts_descuento_x_monto").parent()).remove();  
			}
			
			$('td',$(this).parent().parent()).parent().remove();
			delete instance._descuento[instance._randViewID].monto[counter];
			instance._descuento[instance._randViewID].monto=instance._descuento[instance._randViewID].monto.filter(function(a){return typeof a !== 'undefined';}) 
			instance.fillDescuentosMontos();
		});
		
		if (instance._descuento[instance._randViewID].monto.length>0){
			var tb_='<tr><td id="tts_descuento_x_monto" style="padding-left:8px;width:70%;text-align:right;"><strong>Total descuento x monto:</strong></td><td style="width:35px;border-top:#333 solid 1px;">'+parseFloat(sum,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString()+'</td></tr>';
		$("#descuento_monto_simple_list").append(tb_);
		}
		
		/*AGREGANDO SUBTOTALES*/
		totales.DescuentoMonto=sum;//parseFloat(totales.precio_lista)-parseFloat(sum);
		totales.fire("onChangeData",null);
	},
	fillDescuentosProciento: function(){
		var instance=this;	
		var totales=instance._totales_plan;
		var data=[];
		$("#descuento_x_prociento").show();
		$('#descuento_x_prociento').html('');
		$('#descuento_x_prociento').html('<table class="display" id="descuento_porciento_simple_list"></table>' );
		var sum=0;
		for(i=0;i<instance._descuento[instance._randViewID].porciento.length;i++){
			var dt=instance._descuento[instance._randViewID].porciento[i];
			if (dt!=null){
				var tb_='<tr><td style="padding-left:8px;width:70%;">'+dt.descripcion+'</td><td style="width:35px;">'+dt.porcentaje+'%<a href="#down" id="down" alt="'+dt.porcentaje+'" counter="'+i+'" class="bt_remove_descuento"><img src="images/cross.png" width="16" height="16"></a></td> </tr>';
				$("#descuento_porciento_simple_list").append(tb_);
				sum=sum+ parseFloat(dt.porcentaje);
			}
		}
		 
		$(".bt_remove_descuento").click(function(){
			var alt=$(this).attr("alt");
			var counter=$(this).attr("counter");
			if (instance._descuento[instance._randViewID].porciento.length==1){
				$('td',$("#tts_descuento_porciento").parent()).remove(); 
				$('td',$("#tts_descuento_porciento_monto").parent()).remove();  
			}
			
			$('td',$(this).parent().parent()).parent().remove();
			delete instance._descuento[instance._randViewID].porciento[counter];
			instance._descuento[instance._randViewID].porciento=instance._descuento[instance._randViewID].porciento.filter(function(a){return typeof a !== 'undefined';});
			instance.fillDescuentosProciento();
		});
		 
		/*AGREGANDO SUBTOTALES*/
		var por_to_monto=(parseFloat(totales.precio_lista)*sum)/100;
		totales.TotalDescuentoPorc=sum;			
		totales.TotalDescuentoPorcMonto=por_to_monto;		
			 		
		if (instance._descuento[instance._randViewID].porciento.length>0){
			var tb_='<tr><td id="tts_descuento_porciento" style="padding-left:8px;width:70%;text-align:right;"><strong>Total descuento x porciento:</strong></td><td style="width:35px;border-top:#333 solid 1px;" >'+sum+'%</td>';
			$("#descuento_porciento_simple_list").append(tb_);
		
			tb_='<tr><td id="tts_descuento_porciento_monto"   style="padding-left:8px;width:70%;text-align:right;"><strong>Total descuento x porciento en monto:</strong></td><td style="width:35px;" >'+parseFloat(totales.TotalDescuentoPorcMonto,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString()+'</td></tr>';
			$("#descuento_porciento_simple_list").append(tb_);
		}
		
		totales.fire("onChangeData");
	},
	
	changeViewProduct : function(obj,serie_contrato,no_contrato){
		var instance=this; 
		var rand=this._randViewID; 
		
		//$('#'+instance.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	
		instance.post("./?mod_contratos/listar",{"edit_producto":'1',"product":obj},function(data){ 
			//$('#'+instance.dialog_container).hideLoading();	
			var dialog=instance.createDialog(instance.dialog_container,"Editar Producto",data,900);
			instance._dialog=dialog;
 			
			createTable("inv_simple_list",{
								"bSort": false,
								"bInfo": false, 
								"bLengthChange": false,
								"bFilter": false, 
								"bPaginate": false, 
								"oLanguage": {
									"sLengthMenu": "Mostrar _MENU_ registros por pagina",
									"sZeroRecords": "No se ha encontrado - lo siento",
									"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
									"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
									"sInfoFiltered": "(filtrado de _MAX_ total registros)",
									"sSearch":"Buscar"
								} 
						});
			
			$("#bt_c_add_producto").click(function(){
				var producto= new Inventario(dialog); 
				producto.setStatusFilter(3); 
				producto.chargeView();
				producto.addListener("producto_select",function(producto){ 
					instance._producto[instance._randViewID].valid=true;
					instance._producto[instance._randViewID].data=producto; 
					$("#product_content").hide();
					/////////////////////////////////////////////// 
					$("#display_product").show();
					$('#display_product').html('');
					$('#display_product').html('<table class="display" id="inv_simple_list"></table>' );
					createTable("inv_simple_list",{
							"bSort": false,
							"bInfo": false, 
							"bLengthChange": false,
							"bFilter": false, 
							"bPaginate": false,
							"aaData": [ 
									[ 
									  producto.jardin,
									  producto.fase,
									  producto.bloque,
									  producto.lote,
									  producto.estatus,
									  producto.cavidades,
									  producto.osarios
									] 
								],
							"aoColumns": [
									{ "sTitle": "Jardin" },
									{ "sTitle": "Fase" },
									{ "sTitle": "Bloque" },
									{ "sTitle": "Lote" },
									{ "sTitle": "Estatus" },
									{ "sTitle": "Cavidades" },
									{ "sTitle": "Osarios" } 
									
								],
							  "oLanguage": {
									"sLengthMenu": "Mostrar _MENU_ registros por pagina",
									"sZeroRecords": "No se ha encontrado - lo siento",
									"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
									"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
									"sInfoFiltered": "(filtrado de _MAX_ total registros)",
									"sSearch":"Buscar"
								} 
					});
					
					$("#bt_c_add_producto").html("Cambiar");
					$("#bt_c_save_product").prop("disabled",false);
					
				});
			});

			$("#bt_c_save_product").click(function(){
				//alert(instance._producto[instance._randViewID].data.servicio_id)
				var ifdata='<br><center><strong><p>Desea realizar el cambio de producto? </p> </strong></center><br><center><button type="button" id="caputra_si" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">SI</span></button>&nbsp;&nbsp;<button id="captura_no" type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">NO</span></button></center>';
					
					var dialog=instance.createDialog(instance._dialog,"Aplicar cambio de producto",ifdata,420);
				  
					$("#caputra_si").click(function(){ 
						//$('#'+dialog).showLoading({'addClass': 'loading-indicator-bars'});	
						instance.post("./?mod_contratos/listar",{
							"contrato_data":'changeProduct',
							"product":obj,
							"newproduct":instance._producto[instance._randViewID].data.servicio_id,
							"serie_contrato":serie_contrato,
							"no_contrato":no_contrato
						},function(data){ 
						//	$('#'+dialog).hideLoading(); 
							alert(data.mensaje);
							instance.close(dialog); 
							instance.close(instance._dialog); 
							window.location.reload();
						},"json");
						
					});
					$("#captura_no").click(function(){
						instance.close(dialog);
						 
					});	 
				 
			});
			$("#bt_produc_cancel").click(function(){ 
			   instance.close(dialog);
			}); 				
			
		});
	},
	
	editProduct : function(filtro){
		var instance=this;	  
		var rand=this._randViewID;  
		$("#producto_"+this._randViewID).remove();
		//$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	
		instance.post("./?mod_contratos/listar",{
				"add_producto":'1' ,
				"rand": rand
			},function(data){ 
			//	$('#'+instance.dialog_container).hideLoading();	
				var dialog=instance.createDialog(instance.dialog_container,"Agregar Producto",data,900);
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/550), 20]);  
				
				var totales=instance._totales_plan;
				 
				$("#bt_produc_cancel").click(function(){
					var data={
						"html":$("#cuadro_producto").html(),
						"data":instance,
						'descuento_totales' : totales
					} 
					instance.fire("onRefreshView",data);
					instance.close(dialog);
				}); 
	  			
				$('#txt_monto_incial').formatCurrency();
				$('#txt_monto_incial').click(function(){
					$('#txt_monto_incial').val('');	
				});
				$('#txt_monto_incial').focusout(function(){
					if (!$.isNumeric($('#txt_monto_incial').val())){
						$('#txt_monto_incial').val(totales.monto_enganche);
						$('#txt_monto_incial').formatCurrency();
					}	
				}); 
				$('#txt_monto_incial').change(function(){ 
					 
					if ($.isNumeric($('#txt_monto_incial').val())){
						instance.monto_enganche=$('#txt_monto_incial').val();
						tt_enganche=((parseFloat(totales.precio_lista_menos_TotalDescuentoMonto)*parseFloat(totales.enganche))/100);		 
						if (instance.monto_enganche<tt_enganche){
							instance.monto_enganche=0;
							alert('El monto introducido no puede ser menor a '+ tt_enganche);	
						}else if (instance.monto_enganche>totales.precio_lista){
							/*VERIFICO QUE EL total enganche no sea mayor que el precio de lista */
							instance.monto_enganche=0;
							alert('El monto introducido no puede ser mayor que el precio de lista!');	
						}
						totales.fire("onChangeData");
					}else{
						$('#txt_monto_incial').val(totales.monto_enganche);
						$('#txt_monto_incial').formatCurrency();
						alert('El monto debe de ser numerico!');	
					} 
				});
				
				$("#bt_c_add_producto").html("Cambiar");		
				/*AGREGAR UN PRODUCTO*/
				$("#bt_c_add_producto").click(function(){
					var producto= new Inventario(dialog); 
					producto.setStatusFilter(3); 
					producto.chargeView(filtro);
					producto.addListener("producto_select",function(producto){
						instance._producto[instance._randViewID].valid=true;
						instance._producto[instance._randViewID].data=producto;
						
						/*CAMBIO EL ESTATUS DEL PLAN DE FINANCIAMIENTO*/
						instance._financiamiento[instance._randViewID].valid=false;
						instance._financiamiento[instance._randViewID].data=null;
						$("#product_content").hide();
						///////////////////////////////////////////////
						instance.fillProduct();
						
						$("#bt_c_add_producto").html("Cambiar");
						
					});
				});
				instance.fillProduct();
				 
				/*AGREGAR EL PLAN DE FINANCIAMIENTO*/
				$("#bt_c_financiamiento_find").html("Cambiar");
				$("#bt_c_financiamiento_find").click(function(){ 
					var finan= new PlanesFinanciamiento(dialog);
					if (instance._producto[instance._randViewID].data!=null){
						finan.viewPlanListFromProduct(instance._producto[instance._randViewID].data.servicio_id,filtro);
						finan.addListener("plan_select",function(data){
							$("#product_content").show();
							$('#product_content').html(''); 
							instance._financiamiento[instance._randViewID].valid=true;
							instance._financiamiento[instance._randViewID].data=data;
							 
							totales.precio_lista=data.precio;
							totales.interes = data.por_interes;
							totales.enganche =data.por_enganche;
							totales.plazo= data.plazo
							totales.plan =data.codigo; 
							totales.moneda=data.moneda;
							
							instance.monto_enganche=0;
							totales.fire("onChangeData"); 
							$("#bt_c_financiamiento_find").html("Cambiar");
							
						});
					}else{
						alert('Debe de seleccionar un producto');	
					}
					
				});
				instance.fillFinanciamiento();
				
				/*AGREGAR DESCUENTOS POR MONTO*/
				$("#bt_c_descuento_x_monto").click(function(){
					if (instance._financiamiento[instance._randViewID].data!=null){
						var descuentos= new ContratoDescuento(dialog);
						descuentos.createView(instance._financiamiento[instance._randViewID].data.plan_id,'MONTO');
						descuentos.addListener("onSelectDiscount",function(desc){
						
							if (desc!=null){
								instance._descuento[instance._randViewID].valid=true 
								instance._descuento[instance._randViewID].monto.push(desc); 
								
							} 
							instance.fillDescuentosMontos();  
						});	
					}else{
						alert('Debe de seleccionar un Plan de Financiamiento!');	
					}	
				});
				instance.fillDescuentosMontos();
				
				/*AGREGAR DESCUENTOS POR PORCIENTO*/
				$("#bt_c_descuento_x_porciento").click(function(){
					if (instance._financiamiento[instance._randViewID].data!=null){
						var descuentos= new ContratoDescuento(dialog);
						descuentos.createView(instance._financiamiento[instance._randViewID].data.plan_id,'PORCIENTO');
						descuentos.addListener("onSelectDiscount",function(desc){
							if (desc!=null){
								instance._descuento[instance._randViewID].valid=true 
								instance._descuento[instance._randViewID].porciento.push(desc); 
							}
							instance.fillDescuentosProciento();
						});	
					}else{
						alert('Debe de seleccionar un Plan de Financiamiento!');	
					}
					
				});
				instance.fillDescuentosProciento();

				$("#bt_c_add_product").html("Guardar");
				$("#bt_c_add_product").click(function(){
					var valid=true;
					var message="";
 
					if (!instance._financiamiento[instance._randViewID].valid){
						 message="Debe de seleccionar un Plan de Financiamiento!";
						 valid=false;
					}
					if (!instance._producto[instance._randViewID].valid){
						 message="Debe de seleccionar un Producto!";
						 valid=false;
					}					
					if (valid){
						
						instance._financiamiento[instance._randViewID].data.monto_inicial=totales.monto_enganche;
						/*Envio el codigo html para que sea dibujado*/
						var data={
							"html":$("#cuadro_producto").html(),
							"data":instance,
							'descuento_totales' : totales
						} 
						instance.close(dialog);
						instance.fire("onPlanServicioSelect",data);
						
					}else{
						alert(message);	
					}
				
				
				});

				instance.bind_totales();
				
				totales.addListener("onChangeNumers",function(){
					instance.createCustomTableView(instance._randViewID,totales);
				});
				instance.createCustomTableView(instance._randViewID,totales);
 
			}); 
			
	},
	
	loadView : function(filtro){
		var instance=this;	 
		this._descuento[this._randViewID].data=[];
		this._descuento[this._randViewID].valid=false;
		var rand=this._randViewID; 
		
		//$('#'+this.dialog_container).showLoading({'addClass': 'loading-indicator-bars'});	
		instance.post("./?mod_contratos/listar",{
				"add_producto":'1' ,
				"rand": rand
			},function(data){ 
				//$('#'+instance.dialog_container).hideLoading();	
				var dialog=instance.createDialog(instance.dialog_container,"Agregar Producto",data,900);
				instance._dialog=dialog;
				var n = $('#'+dialog);
				n.dialog('option', 'position', [(document.scrollLeft/550), 20]);  
				
				var totales= new PlanTotalDescuentos(); 
				instance._totales_plan=totales;
				 
				$("#bt_produc_cancel").click(function(){
					instance.close(dialog);
				}); 
	  			
				instance.monto_enganche=0;
				$('#txt_monto_incial').formatCurrency();
				$('#txt_monto_incial').click(function(){
					$('#txt_monto_incial').val('');	
				});
				$('#txt_monto_incial').focusout(function(){
					if (!$.isNumeric($('#txt_monto_incial').val())){
						$('#txt_monto_incial').val(totales.monto_enganche);
						$('#txt_monto_incial').formatCurrency();
					}	
				});
				
				$('#txt_monto_incial').change(function(){ 
					if ($.isNumeric($('#txt_monto_incial').val())){
						instance.monto_enganche=$('#txt_monto_incial').val();
						tt_enganche=((parseFloat(totales.precio_lista_menos_TotalDescuentoMonto)*parseFloat(totales.enganche))/100);
						if (instance.monto_enganche<tt_enganche){
							instance.monto_enganche=0;
							alert('El monto introducido no puede ser menor a '+ tt_enganche);	
						}else if (instance.monto_enganche>totales.precio_lista){
							/*VERIFICO QUE EL total enganche no sea mayor que el precio de lista */
							instance.monto_enganche=0;
							alert('El monto introducido no puede ser mayor que el precio de lista!');	
						}	
						 				
						totales.fire("onChangeData");
					}else{
						$('#txt_monto_incial').val(totales.monto_enganche);
						$('#txt_monto_incial').formatCurrency();
						alert('El monto debe de ser numerico!');	
					} 
				});
						
				/*AGREGAR UN PRODUCTO*/
				$("#bt_c_add_producto").click(function(){
					var producto= new Servicios(dialog);
					producto.chargeView(filtro);
					producto.addListener("servicios_select",function(servicios){ 
						instance._producto[instance._randViewID].valid=true;
						instance._producto[instance._randViewID].data=servicios; 
						
						/*CAMBIO EL ESTATUS DEL PLAN DE FINANCIAMIENTO*/
					//	instance._financiamiento.valid=false;
					//	instance._financiamiento.data=null;
						$("#product_content").hide();
						///////////////////////////////////////////////
						
						$("#display_product").show();
						$('#display_product').html('');
						$('#display_product').html('<table class="display" id="serv_simple_list"></table>' );
						createTable("serv_simple_list",{
								"bSort": false,
								"bInfo": false, 
								"bLengthChange": false,
								"bFilter": false, 
								"bPaginate": false,
								"aaData": [ 
										[ 
										  servicios.codigo,
										  servicios.descripcion
										] 
									],
								"aoColumns": [
										{ "sTitle": "Codigo" },
										{ "sTitle": "Descripcion" } 
									],
								  "oLanguage": {
										"sLengthMenu": "Mostrar _MENU_ registros por pagina",
										"sZeroRecords": "No se ha encontrado - lo siento",
										"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
										"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
										"sInfoFiltered": "(filtrado de _MAX_ total registros)",
										"sSearch":"Buscar"
									} 
						});
						
						$("#bt_c_add_producto").html("Cambiar");
						
					});
				});
				
				/*AGREGAR EL PLAN DE FINANCIAMIENTO*/
				$("#bt_c_financiamiento_find").click(function(){ 
					var finan= new PlanesFinanciamiento(dialog);
					if (instance._producto[instance._randViewID].data!=null){
						finan.viewPlanListFromProduct(instance._producto[instance._randViewID].data.servicio_id,filtro);
						finan.addListener("plan_select",function(data){
							$("#product_content").show();
							$('#product_content').html('');
						//	$('#product_content').html('<table class="display" id="product_simple_list"></table>' );
							
							instance._financiamiento[instance._randViewID].valid=true;
							instance._financiamiento[instance._randViewID].data=data;
							
							$("#p_mondeda").html(data.moneda); 
							
							totales.precio_lista=data.precio;
							totales.interes = data.por_interes;
							totales.enganche =data.por_enganche;
							totales.plazo= data.plazo
							totales.plan =data.codigo; 
							totales.moneda=data.moneda;
							
							totales.fire("onChangeData",null);
							 
							$("#p_plazo").html(data.plazo);
	
							$("#bt_c_financiamiento_find").html("Cambiar");
							
						});
					}else{
						alert('Debe de seleccionar un producto');	
					}
					
				});
				
				/*AGREGAR DESCUENTOS POR MONTO*/
				$("#bt_c_descuento_x_monto").click(function(){
					if (instance._financiamiento[instance._randViewID].data!=null){
						var descuentos= new ContratoDescuento(dialog);
						descuentos.createView(instance._financiamiento[instance._randViewID].data.plan_id,'MONTO');
						descuentos.addListener("onSelectDiscount",function(desc){
							 
							if (desc!=null){
								instance._descuento[instance._randViewID].valid=true 
								instance._descuento[instance._randViewID].monto.push(desc);  	
							} 
							
							var data=[];
							$("#descuento_x_monto").show();
							$('#descuento_x_monto').html('');
							$('#descuento_x_monto').html('<table class="display" id="descuento_monto_simple_list"></table>' );
							var sum=0;
							for(i=0;i<instance._descuento[instance._randViewID].monto.length;i++){
								var dt=instance._descuento[instance._randViewID].monto[i]; 
								var tb_='<tr><td style="padding-left:8px;width:70%;">'+dt.descripcion+'</td><td style="width:35px;">'+parseFloat(dt.monto,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString()+'<a href="#down" id="down" alt="'+dt.porcentaje+'" counter="'+i+'" class="bt_remove_monto"><img src="images/cross.png" width="16" height="16"></a></td></tr>';
								$("#descuento_monto_simple_list").append(tb_);
								sum=sum+ parseFloat(dt.monto);
							} 
							
							$(".bt_remove_monto").click(function(){
								var alt=$(this).attr("alt");
								var counter=$(this).attr("counter");
								if (instance._descuento[instance._randViewID].monto.length==1){
									$('td',$("#tts_descuento_x_monto").parent()).remove(); 
									//$('td',$("#tts_descuento_porciento_monto").parent()).remove();  
								}
								
								$('td',$(this).parent().parent()).parent().remove();
								delete instance._descuento[instance._randViewID].monto[counter];
								instance._descuento[instance._randViewID].monto=instance._descuento[instance._randViewID].monto.filter(function(a){return typeof a !== 'undefined';}) 
								descuentos.fire("onSelectDiscount");
							});
							
							if (instance._descuento[instance._randViewID].monto.length>0){
								var tb_='<tr><td id="tts_descuento_x_monto" style="padding-left:8px;width:70%;text-align:right;"><strong>Total descuento x monto:</strong></td><td style="width:35px;border-top:#333 solid 1px;">'+parseFloat(sum,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString()+'</td></tr>';
							$("#descuento_monto_simple_list").append(tb_);
							}
							
							/*AGREGANDO SUBTOTALES*/
							totales.DescuentoMonto=sum;//parseFloat(totales.precio_lista)-parseFloat(sum);
							totales.fire("onChangeData",null);
							 
						});	
					}else{
						alert('Debe de seleccionar un Plan de Financiamiento!');	
					}
					
				});
				
				/*AGREGAR DESCUENTOS POR PORCIENTO*/
				$("#bt_c_descuento_x_porciento").click(function(){
					if (instance._financiamiento[instance._randViewID].data!=null){
						var descuentos= new ContratoDescuento(dialog);
						descuentos.createView(instance._financiamiento[instance._randViewID].data.plan_id,'PORCIENTO');
						descuentos.addListener("onSelectDiscount",function(desc){
							//alert(desc.codigo);
							if (desc!=null){
								instance._descuento[instance._randViewID].valid=true 
								instance._descuento[instance._randViewID].porciento.push(desc); 
							}
							
							var data=[];
							$("#descuento_x_prociento").show();
							$('#descuento_x_prociento').html('');
							$('#descuento_x_prociento').html('<table class="display" id="descuento_porciento_simple_list"></table>' );
							var sum=0;
							for(i=0;i<instance._descuento[instance._randViewID].porciento.length;i++){
								var dt=instance._descuento[instance._randViewID].porciento[i];
								if (dt!=null){
									var tb_='<tr><td style="padding-left:8px;width:70%;">'+dt.descripcion+'</td><td style="width:35px;">'+dt.porcentaje+'%<a href="#down" id="down" alt="'+dt.porcentaje+'" counter="'+i+'" class="bt_remove_descuento"><img src="images/cross.png" width="16" height="16"></a></td> </tr>';
									$("#descuento_porciento_simple_list").append(tb_);
									sum=sum+ parseFloat(dt.porcentaje);
								}
							}
							 
							$(".bt_remove_descuento").click(function(){
								var alt=$(this).attr("alt");
								var counter=$(this).attr("counter");
								if (instance._descuento[instance._randViewID].porciento.length==1){
									$('td',$("#tts_descuento_porciento").parent()).remove(); 
									$('td',$("#tts_descuento_porciento_monto").parent()).remove();  
								}
								
								$('td',$(this).parent().parent()).parent().remove();
								delete instance._descuento[instance._randViewID].porciento[counter];
								instance._descuento[instance._randViewID].porciento=instance._descuento[instance._randViewID].porciento.filter(function(a){return typeof a !== 'undefined';}) 
								descuentos.fire("onSelectDiscount");
							});
							

							/*AGREGANDO SUBTOTALES*/
							var por_to_monto=(parseFloat(totales.precio_lista)*sum)/100;
							totales.TotalDescuentoPorc=sum;			
							totales.TotalDescuentoPorcMonto=por_to_monto;		
								
															
							if (instance._descuento[instance._randViewID].porciento.length>0){
								var tb_='<tr><td id="tts_descuento_porciento" style="padding-left:8px;width:70%;text-align:right;"><strong>Total descuento x porciento:</strong></td><td style="width:35px;border-top:#333 solid 1px;" >'+sum+'%</td>';
								$("#descuento_porciento_simple_list").append(tb_);
							
								tb_='<tr><td id="tts_descuento_porciento_monto"   style="padding-left:8px;width:70%;text-align:right;"><strong>Total descuento x porciento en monto:</strong></td><td style="width:35px;" >'+parseFloat(totales.TotalDescuentoPorcMonto,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString()+'</td></tr>';
								$("#descuento_porciento_simple_list").append(tb_);
							}
							
							totales.fire("onChangeData");
							
						});	
					}else{
						alert('Debe de seleccionar un Plan de Financiamiento!');	
					}
					
				});
				 
				//instance.createCustomTableView(rand);
				$("#bt_c_add_product").click(function(){
					var valid=true;
					var message="";
 
					if (!instance._financiamiento[instance._randViewID].valid){
						 message="Debe de seleccionar un Plan de Financiamiento!";
						 valid=false;
					}
					if (!instance._producto[instance._randViewID].valid){
						 message="Debe de seleccionar un Producto!";
						 valid=false;
					}					
					if (valid){
						
						instance._financiamiento[instance._randViewID].data.monto_inicial=totales.monto_enganche;
						/*Envio el codigo html para que sea dibujado*/
						var data={
							"html":$("#cuadro_producto").html(),
							"data":instance,
							'descuento_totales' : totales
						} 
						instance.close(dialog);
						instance.fire("onPlanServicioSelect",data);
						
					}else{
						alert(message);	
					}
				
				
				});
				 
				instance.bind_totales();
				
				totales.addListener("onChangeNumers",function(){
					instance.createCustomTableView(instance._randViewID,totales);
				});
 
			}); 
			
	},
	
	setProductData : function(data){
		var instance=this;
		instance._producto[instance._randViewID].valid=true;
		instance._producto[instance._randViewID].data=data.producto;

	//	instance._financiamiento[instance._randViewID].valid=true;
	//	instance._financiamiento[instance._randViewID].data=data;
		
		
		/*
		$("#p_mondeda").html(data.moneda); 
		
		totales.precio_lista=data.precio;
		totales.interes = data.por_interes;
		totales.enganche =data.por_enganche;
		totales.plazo= data.plazo
		totales.plan =data.codigo; 
		totales.moneda=data.moneda;		 */
		 
		
	},
	bind_totales : function(){
		var instance= this;
		var totales=instance._totales_plan;
		instance._totales_plan.addListener("onChangeData",function(){
								
			var total_descuento=parseFloat(totales.TotalDescuentoPorcMonto)+parseFloat(totales.DescuentoMonto);
			totales.TotalDescuentoMonto=total_descuento;
						
			totales.precio_lista_menos_TotalDescuentoMonto=parseFloat(totales.precio_lista)-parseFloat(totales.TotalDescuentoMonto);
			
			if (instance.monto_enganche<=0){
				totales.monto_enganche=(parseFloat(totales.precio_lista_menos_TotalDescuentoMonto)*parseFloat(totales.enganche))/100;
			}else{
				totales.monto_enganche=parseFloat(instance.monto_enganche);
			}				
			
			totales.capital_neto_a_pagar=parseFloat(totales.precio_lista_menos_TotalDescuentoMonto)-totales.monto_enganche;
			
			
			totales.total_interes_anual=(parseFloat(totales.interes));	
			totales.total_interes_monto_anual=(parseFloat(totales.total_interes_anual,2)*parseFloat(totales.capital_neto_a_pagar,2))/100;
			
			totales.total_interes_a_pagar=(parseFloat(totales.total_interes_monto_anual,2)*(parseFloat(totales.plazo,10)/ 12));

		 
			totales.capital_cuota=parseFloat(totales.capital_neto_a_pagar,10)/parseFloat(totales.plazo,10);
			
			
			totales.total_interes_cuota=parseFloat(totales.total_interes_a_pagar,10)/parseFloat(totales.plazo,10);
						
			totales.mensualidades=parseFloat(totales.total_interes_cuota,10)+ parseFloat(totales.capital_cuota,10);
			
			totales.sub_total_a_pagar=parseFloat(totales.total_interes_a_pagar)+parseFloat(totales.capital_neto_a_pagar);
	
			totales.total_a_pagar=parseFloat(totales.sub_total_a_pagar)+parseFloat(totales.monto_enganche);
 
 			
 
			 
			var porcient_inicial=((totales.monto_enganche*100)/totales.precio_lista_menos_TotalDescuentoMonto);
			$("#monto_inicial_por").html(parseFloat(porcient_inicial,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
			 
			$("#pro_total_descuento").html(parseFloat(totales.TotalDescuentoMonto,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
			$("#pro_total_a_pagar").html(parseFloat(totales.precio_lista,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
			
			$("#p_precio_lista").html(parseFloat(totales.precio_lista,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString()); 
			

			$("#precio_lista_menos_TotalDescuentoMonto").html(parseFloat(totales.precio_lista_menos_TotalDescuentoMonto,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
			 
  
			$("#capital_neto_a_pagar").html(parseFloat(totales.capital_neto_a_pagar,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());		
			
			$("#p_plazo").html(totales.plazo); 
			$("#p_mondeda").html(totales.moneda); 			
			$("#p_plan").html($.trim(totales.plan));
			$("#p_iteneres").html(totales.interes+"%");  
			$("#p_enganche").html(totales.enganche+"%"); 			
			
			$("#total_descuento_x_porciento").html(totales.TotalDescuentoPorc+"%");
			
			$("#total_porciento_monto").html(parseFloat(totales.TotalDescuentoPorcMonto,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());			
			
			$('#txt_monto_incial').val(totales.monto_enganche);
			$('#txt_monto_incial').formatCurrency();
			
			
			$("#total_interes_a_pagar").html(parseFloat(totales.total_interes_a_pagar,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
			 
			$("#mensualidades").html(parseFloat(totales.mensualidades,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
			
			$("#sub_total_a_pagar").html(parseFloat(totales.sub_total_a_pagar,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
			
			$("#total_a_pagar").html(parseFloat(totales.total_a_pagar,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
						
		 
			
			totales.fire("onChangeNumers");
					
		});
	},
	createCustomTableView: function(rand,totales){
		var instance=this;
		var producto=instance._producto[instance._randViewID].data;
 
		/*AGREGANDO EL TITULO*/
		$("#title_"+rand).html(producto.descripcion);
		$("#detalle_plan_"+rand).html(totales.plan);
		$("#detalle_moneda_"+rand).html(totales.moneda);
		$("#detalle_enganche_"+rand).html(totales.enganche+"%");
		$("#detalle_plazo_"+rand).html(totales.plazo);
		$("#detalle_descuento_"+rand).html(parseFloat(totales.TotalDescuentoMonto,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
		$("#detalle_mensualidades_"+rand).html(parseFloat(totales.mensualidades,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
		 
	}
	
});