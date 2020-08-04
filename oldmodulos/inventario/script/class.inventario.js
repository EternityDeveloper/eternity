var Inventario = new Class({
	dialog_container : null,
	_contrato_id : null,
	_form_name : "frm_new_actividad",
	_tb_inventario : "tb_inventario",
	_producto : null,
	_status_filter : [],
	_rand : 0,
	_token : 0,
	_id_nit : "",
	initialize : function(dialog_container){
		this.main_class="PlanProductos";
		this.dialog_container=dialog_container; 
		this._rand=this.getRand();
		this._status_filter[this._rand]=[];
	},
	
	setToken : function(value){
		this._token=value;
	},
	setStatusFilter : function(estatus){
		this._status_filter[this._rand].push(estatus);
	},
	setFilterByIDNit : function(id_nit){
		this._id_nit=id_nit;
	},
	chargeView : function(productos){
		var instance=this; 
		this.addListener("onLoadPlanView",function(data){ 
			var dialog=instance.createDialog(instance.dialog_container,"Listar Inventario",data,900);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 20]); 
			
			var nproduct={
					moneda:productos.moneda,
					plazo:productos.plazo,
					enganche:productos.enganche,
					situacion:productos.situacion
				}; 
			createTable(instance._tb_inventario,{
						"bSort": false,
						"bInfo": false,
						"bPaginate": true,
						"bLengthChange": false,
						"bFilter": true, 
						"bPaginate": true,
						"bProcessing": true,
						"bServerSide": true,
						"sAjaxSource": "./?mod_inventario/inventario_list&dt_list=1&filter_estatus="+ JSON.stringify(instance._status_filter[instance._rand])+"&product_list_not_show="+ JSON.stringify(nproduct)+"&filter_by_nit="+instance._id_nit,
						"sServerMethod": "POST",
						"aoColumns": [
								{ "mData": "nombre_jardin" },
								{ "mData": "nombre_fase" },
								{ "mData": "bloque" },
								{ "mData": "lote" },
								{ "mData": "descripcion" },
								{ "mData": "cavidades" },
								{ "mData": "osarios" },
								{ "mData": "serie_recibo_no" },									
								{ "mData": "bt_select" }  
							],
					  "oLanguage": {
							"sLengthMenu": "Mostrar _MENU_ registros por pagina",
							"sZeroRecords": "No se ha encontrado - lo siento",
							"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
							"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
							"sInfoFiltered": "(filtrado de _MAX_ total registros)",
							"sSearch":"Buscar"
						},
					  "fnServerData": function ( sSource, aoData, fnCallback ) {
							$.getJSON( sSource, aoData, function (json) { 
								fnCallback(json)
							  
								/*AL DAR CLICK EN EL TR*/
								var tr=$('td', $('.inventario_edit').parent().parent()).parent();
								tr.css( 'cursor', 'pointer' );
								tr.click(function(){
									var nTds=$(this).children();
								    
									var  producto= { 
										"jardin":$(nTds[0]).text(),
										"fase":$(nTds[1]).text(),
										"bloque":$(nTds[2]).text(),
										"lote":$(nTds[3]).text(),
										"estatus":$(nTds[4]).text(),
										"cavidades":$(nTds[5]).text(),
										"osarios":$(nTds[6]).text(), 
										"product_id":$(nTds).find("a").attr("id")
									}
									 
									instance.close(dialog);
									instance._producto=producto;
									instance.fire("producto_select",producto);
									
									//alert($($(this).children().find("a")).attr("id"));	
								});
								/*AGREGANDO HIGTHLIGHT*/
								tr.hover(function(){ 
									$(this).addClass('hover_tr');  
								},function(){ 
									$(this).removeClass('hover_tr'); 
								});
								
							} );
						},
					  "fnDrawCallback": function( oSettings ) {
							
							if (!instance._isCharge){
								 /*
								 $('<button id="client_add"  class="greenButton">Agregar</button>').appendTo('#listado_cliente div.dataTables_filter'); 	
								 $("#client_add").click(function(){ 
									instance.close(dialog);
									instance.doViewCreatePerson(); 	
									
								  });*/
								 
								
								instance._isCharge=true;
							}
						}
						
					});
			 
		});
		
		this.chargePlainView({"view_simple_inventario":'1' });
	},
	
	viewCambioUbicacion : function(id){
		var instance=this; 	
		instance.post("./?mod_inventario/inventario_list&view_cambio_producto",{ 
				'producto':id 
		},function(data){   
			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){ 
			})
			setTimeout(function(){
				$("#buscar_producto").click(function(){
					instance.viewListadoParcela();			
				});
				
				instance.addListener("onSelectParcela",function(id){
					instance.post("./?mod_inventario/inventario_list",{ 
							'producto':id,
							'doSelect':'true'
					},function(prod){  
						var nombre=prod.id_jardin+'-'+prod.fase+'-'+prod.bloque+'-'+prod.lote;
						if (prod.osario!=''){
							nombre=nombre+'-'+prod.osario;
						}
						$("#producto_name").html(nombre); 			
						instance.fire("onSelectProduct",prod);
						$("#prod_realizar_cambio").prop("disabled",false);
					},"json");
				});
				
				$("#prod_realizar_cambio").click(function(){
					instance.post("./?mod_inventario/inventario_list",{ 
							'doRealizarCambio':true,
							'producto':id,
							'comentario':$("#cu_comentario").val()
					},function(prod){  
						if (!prod.error){
							instance.fire("onChange",prod);
						}else{
							alert(prod.mensaje);	
						}
					},"json");
				});
			},500);
			
		});
	},
	/*VISTA PARA REMOVER PRODUCTO*/
	removerUbicacion : function(id){
		var instance=this; 	
		instance.post("./?mod_inventario/inventario_list&view_remover_producto",{ 
				'producto':id 
		},function(data){   
			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){ 
			})
			setTimeout(function(){
				  
				$("#prod_realizar_cambio").click(function(){
					instance.post("./?mod_inventario/inventario_list",{ 
							'doRemoverProducto':true,
							'producto':id,
							'comentario':$("#cu_comentario").val()
					},function(prod){  
						if (!prod.error){
							instance.fire("onChange",prod);
						}else{
							alert(prod.mensaje);	
						}
					},"json");
				});
				
			},500);
			
		});
	},	
	viewAgregarUbicacion : function(contrato,id_nit){
		var instance=this; 	 
		instance.post("./?mod_inventario/inventario_list&view_agregar_producto",{ 
				'contrato':contrato,
				'id_nit':id_nit
		},function(data){   
			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			})
			setTimeout(function(){
				$("#buscar_producto").click(function(){
					instance.viewListadoParcela();			
				});
				var producto_id="";
				instance.addListener("onSelectParcela",function(id){
					instance.post("./?mod_inventario/inventario_list",{ 
							'producto':id,
							'doSelect':'true'
					},function(prod){  
						var nombre=prod.id_jardin+'-'+prod.fase+'-'+prod.bloque+'-'+prod.lote;
						if (prod.osario!=''){
							nombre=nombre+'-'+prod.osario;
						}
						$("#producto_name").html(nombre); 			
						instance.fire("onSelectProduct",prod);
						$("#prod_realizar_cambio").prop("disabled",false);
						producto_id=id;
					},"json");
				});
				
				$("#prod_agregar_cambio").click(function(){
					if (producto_id==""){
						alert('Falta seleccionar el producto!')
						return false;
					}
					instance.post("./?mod_inventario/inventario_list",{ 
							'doAgregarProducto':true,
							'producto':producto_id,
							'contrato':contrato,
							'comentario':$("#cu_comentario").val()
					},function(prod){  
						if (!prod.error){
							alert(prod.mensaje);	
							window.location.reload();
						}else{
							alert(prod.mensaje);	
						}
					},"json");
				});
			},500);
			
		});
	},
	viewLiberarUbicacion : function(id){
		var instance=this; 	 
		instance.post("./?mod_inventario/inventario_list&view_liberar_parcela",{  
				'id':id
		},function(data){   
			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){ 
			})
			$("#prod_realizar_cambio").click(function(){
				instance.post("./?mod_inventario/inventario_list",{ 
						'doLiberarParcela':true,
						'producto':id,
						'comentario':$("#cu_comentario").val()
				},function(prod){  
					if (prod.valid){
						alert('Proceso realizado');
						instance.CloseDialog("myModal");
					}else{
						alert(prod.mensaje);	
					}
				},"json");
			}); 
		});
	},	
	viewListadoParcela : function(){
		var instance=this; 	
		instance.post("./?mod_inventario/inventario_list&view_list_parcela",{ 
				'_a':''
		},function(data){   
			instance.doDialog("viewListadoParcela",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
 			})			
			$("#tb_listado_inv").dataTable({
							"bFilter": true,
							"bInfo": false,
							"bLengthChange": false,
							"bPaginate": true,
							"bProcessing": true,
							"bServerSide": true,
							"sAjaxSource": "./?mod_inventario/inventario_list&dt_list=1",
							"sServerMethod": "POST",
							"aoColumns": [
									{ "mData": "nombre_jardin" },
									{ "mData": "nombre_fase" },
									{ "mData": "bloque" },
									{ "mData": "lotes" },
									{ "mData": "descripcion" },
									{ "mData": "contrato" },
									{ "mData": "cavidades" },
									{ "mData": "osarios" },
									{ "mData": "_seleccionar" }
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
							
			window["_seleccionar"]=function(value){  
				instance.fire("onSelectParcela",value);
				instance.CloseDialog("viewListadoParcela");
				return true;	
			}										 

		});			
	},
	/*VISTA REALIZADA PARA EL MODULO DE SOLICITUDES*/
	chargeCustomView : function(productos){
		var instance=this; 
		this.addListener("onLoadPlanView",function(data){ 
			var dialog=instance.createDialog(instance.dialog_container,"Producto Reservado",data,900);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 20]);
			 
			$("#bt_invt_cerrar").click(function(){ 
				instance.close(dialog);	
				instance.fire("producto_select");
			}); 
			
			var nproduct={
				moneda:productos.moneda,
				plazo:productos.plazo,
				enganche:productos.enganche,
				situacion:productos.situacion
			}; 
			 
			var DT=createTable(instance._tb_inventario,{
						"bSort": false,
						"bInfo": false,
						"bPaginate": false,
						"bLengthChange": false,
						"bFilter": true, 
						"bPaginate": true,
						"bProcessing": true,
						"bServerSide": true,
						"sAjaxSource": "./?mod_inventario/inventario_list&dt_list_custom=1&filter_estatus="+ JSON.stringify(instance._status_filter[instance._rand])+"&product_list_not_show="+ JSON.stringify(nproduct)+"&filter_by_nit="+instance._id_nit+"&token="+instance._token,
						"sServerMethod": "POST",
						"aoColumns": [
								{ "mData": "total" },
								{ "mData": "nombre_jardin" },
								{ "mData": "nombre_fase" },
								{ "mData": "bloque" }, 
								{ "mData": "lote_codigo" }, 
								{ "mData": "descripcion" }, 
								{ "mData": "serie_recibo_no" },									
								{ "mData": "bt_select" }  
							],
					  "oLanguage": {
							"sLengthMenu": "Mostrar _MENU_ registros por pagina",
							"sZeroRecords": "No se ha encontrado - lo siento",
							"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
							"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
							"sInfoFiltered": "(filtrado de _MAX_ total registros)",
							"sSearch":"Buscar"
						},
					  "fnServerData": function ( sSource, aoData, fnCallback ) {
							$.getJSON( sSource, aoData, function (json) { 
								fnCallback(json)
							    
								/*AL DAR CLICK EN EL TR*/ 
								var tr=$('td', $('.inventario_edit').parent().parent()).parent();
								tr.css('cursor','pointer');
								 
								tr.click(function(){
									var nTds=$(this).children(); 
									  																	
									var data=$.parseJSON($.base64.decode($(nTds).find("a").attr("item"))); 
 									var action={};
									
									if ($(nTds).find("input").prop("checked")){ 
										$(nTds).find("input").prop("checked",false);   
										action={
											onFinish: function(info){  	 
												$("div.dataTables_filter").find('input').val('');
												$("div.dataTables_filter").find('input').trigger("keyup.DT"); 
											}	
										};  
										instance.doPutItem($(nTds).find("a").attr("id"),"remove",action);	  
									}else{
										
										$(nTds).find("input").prop("checked",true);
										action={
											onFinish: function(info){ 
											//alert(info.total_reserva)
												if (info.total_reserva==1){
													/*DISPARO LA VISTA PARA REALIZAR EL RENDER EN PANTALLA*/
													instance.close(dialog);	
													instance.fire("producto_select");
												}
												setTimeout(function(){ 
													$("div.dataTables_filter").find('input').val(data.id_jardin);
													$("div.dataTables_filter").find('input').trigger("keyup.DT");	
													
													$("div.dataTables_filter").find('input').val(data.id_jardin);
													$("div.dataTables_filter").find('input').trigger("keyup.DT");														
												},500);
											}	
										};
										instance.doPutItem($(nTds).find("a").attr("id"),"add",action);										
									} 
									$("#title_"+instance._token).html(data.nombre_jardin);		 
									var  producto= {  
										"jardin":data.nombre_jardin,
										"fase":data.id_fases,
										"bloque":data.bloque,
										"estatus":data.descripcion,
										"cavidades":0,
										"osarios":0, 
										"product_id":data.product_id
									}											
									 
								//	instance.close(dialog);
									//instance._producto=producto;
							//		instance.fire("producto_select",producto);
									
									//alert($($(this).children().find("a")).attr("id"));	
								}); 
								
								/*AGREGANDO HIGTHLIGHT*/
								tr.hover(function(){ 
									$(this).addClass('hover_tr');  
								},function(){ 
									$(this).removeClass('hover_tr'); 
								});
								
							} );
						},
					  "fnDrawCallback": function( oSettings ) {
							
							if (!instance._isCharge){
								 /*
								 $('<button id="client_add"  class="greenButton">Agregar</button>').appendTo('#listado_cliente div.dataTables_filter'); 	
								 $("#client_add").click(function(){ 
									instance.close(dialog);
									instance.doViewCreatePerson(); 	
									
								  });*/ 
								instance._isCharge=true;
							}
						}
						
					});
				/*OCULTO EL BUSCADOR*/	
				$($("div.dataTables_filter"),'#DT').hide();
			 
		});
		
		this.chargePlainView({"view_custom_inventario":'1'});
	},
	/*Este metodo agrega al carrito el producto seleccionado del producto reservado*/
	doPutItem : function(items,cmd,obj){
		var instance=this; 
		instance.post("./?mod_inventario/inventario_list",{ 
			'items':items,
			"cmd": cmd,
			"token":instance._token,
			'action':"doAddToCar"
		},function(data){  
		  	obj.onFinish(data);
		},"json")
	},	
	chargePlainView : function(view){
		var instance=this;	 
		instance.post("./?mod_inventario/inventario_list",view,function(data){  
			instance.fire("onLoadPlanView",data); 
		});
	}
	
});