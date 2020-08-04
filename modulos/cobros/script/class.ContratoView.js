/*  COBRO*/
var ContratoView = new Class({
	dialog_container : null, 
	_contrato: null,
	_rand : null,
	_doQuestion: false,
	initialize : function(dialog_container){
		this.main_class="DashBoard";
		this.dialog_container=dialog_container; 
	},	 
	doLoad : function(){
		var instance=this;
		$('#myTab a').click(function (e) {
		  var contrato=$(this).attr("id");
		  if (contrato==''){
			 return true;
	 	  }
			  $("#detalle_contrato").html('');
			  instance.Block();
			  instance.post("?mod_cobros/delegate&detalle_contrato_view",{id:contrato},function(data){
				  $("#detalle_contrato").html(data);
				  instance.doInit(contrato);
				  instance.unBlock();
				  
			  }); 
		})
	}, 
	doInit : function(id){ 
		var instance=this;
		this._contrato=id;  
		var _beneficiario= new DetalleBeneficiario(this.dialog_container);
 		var _representante= new DetalleRepresentante(this.dialog_container);
    	_beneficiario.doInit(id);
	   	_representante.doInit(id);	
		
		var cmFormaPago= new CambioFormaPagoCT(this.dialog_container);
		cmFormaPago.doChange();
				
		$("#labor_cobro").click(function(){ 
			instance.doCreateLaborCobro();
		});

		$("#lbr_cobro").click(function(){ 
			instance.doCreateLaborCobro();
		});
		
		$(".bt_req_cobro_").click(function(){ 
			instance.doCreateRequerimientoC($(this).attr("serie"),$(this).attr("no_c"));
		}); 
				
		$("#aviso_cobro").click(function(){ 
		//	instance.doCreateRequerimientoC();
		});		
		$("#anular").click(function(){  
			 instance.doAnular();
		});	
		$("#desistir").click(function(){ 
			instance.doDesistir();
		});	
						
		$("#solicitudGestion").click(function(){ 
			instance.doGenerateGestion();
		});	
		
		$("._change").click(function(){
			var inv= new Inventario('content_dialog');
			inv.viewCambioUbicacion($(this).attr("id"));  
			inv.addListener("onChange",function(data){
				alert(data.mensaje);	
				window.location.reload();
			})
		});	
		$("._remove_parcela").click(function(){
			var inv= new Inventario('content_dialog');
			inv.removerUbicacion($(this).attr("id"));  
			inv.addListener("onChange",function(data){
				alert(data.mensaje);	
				window.location.reload();
			})
		});			
			
		$("#add_parcela").click(function(){ 
			var inv= new Inventario('content_dialog');
			inv.viewAgregarUbicacion($(this).attr("contrato"),$(this).attr("id_nit"));  
			inv.addListener("onChange",function(data){
				alert(data.mensaje);	
				window.location.reload();
			})
		});				
								
		$("._facturar").click(function(){  
			instance.doFacturar($(this).attr("id"),$(this).attr("id_nit"));
		});		
		
		$("._facturar_nc").click(function(){  
			instance.doFacturarNC($(this).attr("id"),$(this).attr("id_nit"));
		});				
		
		
		$(".agregar_comentary").click(function(){  
			instance.doComentary($(this).attr("id"),$(this).attr("id_nit"));
		});	
						 	
		/*RETORNO A LA PAGINA PRINCIPAL*/	
		$("#lb_regresar").click(function(){
			window.location.href="./?mod_cobros/delegate&dashboard";
		}); 
		
		$(".gestion_").click(function(){
			var obj = new window[$(this).attr("object")]('content_dialog');   
			obj.doView($(this).attr("contranto")); 
		});
		
		$(".edit_person_view").click(function(){
			instance.doViewEditPerson($(this).attr("id")); 
		});		
		
		$("#gestiones_especiales").click(function(){
			var obj = new GGestion('content_dialog');   
			obj.doView($(this).attr('id_nit'),id); 
		});
		
		$("#change_oficial").click(function(){ 
			instance.doChangeOficial($(this).attr("alt"),$(this).attr("contrato"));
		});

		$(".direccion_on_map").click(function(){  
			instance.viewDireccionOnMap($(this).attr("id_direccion"));
		});		
		$(".fixed-table-container-inner").scrollTop(-1000);
		
		this._rand=this.getRand();
		this.doCuotas();
		this.enable_search(); 
	},   
	/*Ventana que accede a los anulados*/
	doChangeOficial : function(id_meta,contrato){
		var instance=this; 
		instance.post("./?mod_cobros/delegate&doViewChangeOficial",{ 
				'contrato':this._contrato,
				"meta": id_meta
		},function(data){  
			instance.doDialog("myModal",instance.dialog_container,data); 
			$("#aplicar_cambio").click(function(){
				if ($("#comentario").val()!=''){
					if ($("#comentario").val().length>7){
						if (!confirm("Esta seguro de realizar esta operacion?")){
							return false;
						}
						instance.post("./?mod_cobros/delegate&doChangeOficial",{ 
								'contrato':instance._contrato, 
								'oficial':$("#oficial_n").val(),
								'motorizado':$("#motorizado_n").val(),
								'comentario':$("#comentario").val(),
						},function(data){ 
							alert(data.mensaje);
							if (data.valid){
								$("#myModal").modal('hide');
								window.location.reload();
							}
						},"json");
				
					}else{
						alert('Debe de ingresar un motivo mas claro de por que lo esta pasando a este estatus!');		
					}
				}else{
					alert('Debe de llenar el campo motivo');	
				}
			});
		});	
	},	 
	enable_search : function(){
		var instance=this; 
		$("#search").keypress(function(e){
			var code = e.keyCode || e.which;
			if(code == 13) {  
				window.location.href="./?mod_caja/delegate&operacion&search="+$("#search").val(); 
			}	
			
		});
		$("#_buscar_bt").click(function(e){  
			window.location.href="./?mod_caja/delegate&operacion&search="+$("#search").val();
		});		
	},
	/*
		Carga la vista de direcciones
		en el mapa
	*/
	viewDireccionOnMap : function(id_direccion){
		var instance=this;
		instance.post("./?mod_cobros/delegate",{
						"doViewDireccionOnMap":'1',
						"id_direccion":id_direccion
				},
		function(data){   
				instance.doDialog("maps_view",instance.dialog_container,data.html); 
	  			setTimeout(function(){
						var map = new TMap('map_zona');
						map.addListener("onMapLoad",function(maps){  
							var lonLat = new OpenLayers.LonLat(data.longitud,data.latitud)
								.transform(
									new OpenLayers.Projection("EPSG:4326"), //transformando de WGS 1984
									maps._mapLayer.getProjectionObject() 
								);		
							maps._mapLayer.setCenter(lonLat, 15);	
  
							var markers= new OpenLayers.Layer.Markers("Direccion");
							 
							var size = new OpenLayers.Size(42,42);
							var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
							var icon = new OpenLayers.Icon(	
								'http://testarossa.memorial.com.do/images/map_location_pins.png', size, offset);
							markers.addMarker(new OpenLayers.Marker(
														new OpenLayers.LonLat(data.longitud,
																			  data.latitud ).transform(
									new OpenLayers.Projection("EPSG:4326"), //transformando de WGS 1984
									maps._mapLayer.getProjectionObject() 
								),icon)); 
 
							maps._mapLayer.addLayer(markers);  													
							 					
						});  				
						map.drawMapView(); 
				},1500);
			},"json");
				
			
		},
	doViewEditPerson : function(id_nit){ 
		var instance=this;
		this._person_component= new ModuloPersonas('Datos personales',this.dialog_container,this._dialog);
		var person= new Persona(this._person_component);
		person.addListener("onEditPerson",function(data){
			alert(data.mensaje);
		});
		/*PONGO EL MODULO DE PERSONA EN MODO EDITAR*/
		person.setView("edit");
		/**********************************/
		person.addListener("onViewCreate",function(){
			$("#tipo_clte").hide();			  
			$("#sys_clasificacion_persona").hide();
		});
				
		this._person_component.addModule(person);
		
		var direccion= new Direccion(this._person_component);
		this._person_component.addModule(direccion);
		
		direccion.addListener("doLoadViewComplete",function(obj){
		//	instance.insertIntoView(obj)
		});
		
		/**/
		var empresa= new personEmpresa(this._person_component);
		this._person_component.addModule(empresa);
		/***************************************************/
		 
		/**/
		var telefono= new Telefono(this._person_component);
		this._person_component.addModule(telefono);
		/***************************************************/
		 
		var email= new Email(this._person_component);
		this._person_component.addModule(email);	
		
		var reference= new Referencia(this._person_component);
		this._person_component.addModule(reference);
		
		var referidos = new Referidos(instance._person_component);
		instance._person_component.addModule(referidos);
		
		/*Y vuelvo a cargar la vista*/
		this._person_component.loadMainView();
		/**************************************/
  
		/*Le digo que cliente es el que sera editado*/
		this._person_component.setPersonID(id_nit);

	},
	
	getToken : function(){
		return this._rand;
	},
	doComentary : function(ct,id_nit){
		var instance=this; 
		instance.post("./?mod_cobros/delegate&view_comentario",{},function(data){  
			instance.doDialog("myModal",instance.dialog_container,data);
		   	
			$("#aplicar_cambio").click(function(){
				if ($("#comentario").val()!=''){
					if ($("#comentario").val().length>7){ 
						instance.post("./?mod_cobros/delegate&doGenerarComentario",{ 
								'id_nit':id_nit,
								'contrato':ct,
								'token':instance._rand, 
								'comentario':$("#comentario").val(),
						},function(data){ 
							alert(data.mensaje);
							if (!data.valid){
								$("#myModal").modal('hide');
								window.location.reload();
							}
						},"json");
				
					}else{
						alert('Debe de ingresar un comentario mas claro de por que lo esta pasando a este estatus!');		
					}
				}else{
					alert('Debe de llenar el campo comentario');	
				}
			});
		});	
		 
	},	
	doFacturar : function(id,id_nit){
		var instance=this; 
		var fac= new Facturar('content_dialog');
		if (instance._doQuestion){  
			if (confirm("Desea emitir un documento para cobrar estas cuotas?")){  
				instance.post("./?mod_cobros/delegate&doGenerateRequerimiento",{ 
						'contrato':id,
						'token':this._rand
				},function(data){ 
					
					fac.doView(id_nit,id,'',''); 
				});
			}
		}else{
			fac.doView(id_nit,id,'',''); 	
		}
	},
	doFacturarNC : function(id,id_nit){
		var instance=this; 
	 
		this.loadScript("./?mod_caja/delegate&includeFacturaNCScript",function(){ 
			var fac= new FacturarNC('content_dialog');
			if (instance._doQuestion){  
				if (confirm("Desea emitir un documento para cobrar estas cuotas?")){  
					instance.post("./?mod_cobros/delegate&doGenerateRequerimiento",{ 
							'contrato':id,
							'token':this._rand
					},function(data){ 
						
						fac.doView(id_nit,id,'',''); 
					});
				}
			}else{
				fac.doView(id_nit,id,'',''); 	
			}
			
		},function(){
			alert('Error al cargar el modulo');	
		});

	},	
	doCuotas: function(){
		var instance= this;
		$('.c_cuotas:first').prop("disabled", false ); 
		$(".c_cuotas").prop("checked",false);  
		
		$(".c_cuotas").click(function(){
			var sp=$(this).attr("name").split("_")
			var id=parseInt(sp[2])+1; 
			
			if ($(this).prop("checked")){
				$("#check_cuota_"+id).prop("disabled",false); 
				instance._doQuestion=true;
				instance.doCuotaPut($(this).val(),"add");
				$("#aviso_cobro").prop("disabled",false); 
			}else{
				instance.doCuotaPut($(this).val(),"remove");
				$("#aviso_cobro").prop("disabled",true); 
				instance._doQuestion=false;
 				$(".c_cuotas").each(function(index, element) {
					var sp2=$(this).attr("name").split("_") 
					if (parseInt(sp2[2])>parseInt(sp[2])){ 
						$(this).prop("disabled",true); 						
						$(this).prop("checked", false);
					}
                });
				$(".c_cuotas:checked").each(function(index, element) { 
					$("#aviso_cobro").prop("disabled",false); 
					instance._doQuestion=true;
				});
			}
		});
		
		/*AGREGAR TODAS LA CUOTAS*/
		$("#check_cuota_0").click(function(){
			if ($(this).prop("checked")){
				$(".c_cuotas").each(function(index, element) {  
					$(this).prop("disabled",false); 
					$(this).prop("checked", true); 
					instance.doCuotaPut($(this).val(),"add");
					instance._doQuestion=true;
				});	
			}else{
				 
				$(".c_cuotas").each(function(index, element) {  
					$(this).prop("disabled",true); 
					$(this).prop("checked", false); 
					instance._doQuestion=false;
					instance.doCuotaPut($(this).val(),"remove");
				});		
				$('.c_cuotas:first').prop("disabled", false );
			}
		}); 
	},
	/*Agrega una cuota del listado para ser pagada*/
	doCuotaPut : function(cuota,cmd){
		var instance=this; 
		instance.post("./?mod_cobros/delegate&processPutPago",{ 
			'cuota':cuota,
			"cmd": cmd,
			"token":instance._rand,
			'contrato':this._contrato
		},function(data){  
			if (data.valid){
				$("#monto_display").html(parseFloat(data.data.monto,10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString());
				$("#cuotas_display").html(data.data.no_cuota); 
			}else{
				alert(data.mensaje);	
			}
		},"json")
	},
	
	/*Ventana que accede a los anulados*/
	doAnular : function(){
		var instance=this; 
		instance.post("./?mod_cobros/delegate&view_anular",{ 
				'contrato':this._contrato 
		},function(data){  
			instance.doDialog("myModal",instance.dialog_container,data);
		   	
			$("#aplicar_cambio").click(function(){
				if ($("#comentario").val()!=''){
					if ($("#comentario").val().length>7){
						if (!confirm("Esta seguro de realizar esta operacion?")){
							return false;
						}
						instance.post("./?mod_cobros/delegate&anular_contrato",{ 
								'contrato':instance._contrato,
								'token':instance._rand,
								'motivo':$("#motivo").val(),
								'comentario':$("#comentario").val(),
						},function(data){ 
							alert(data.mensaje);
							if (!data.valid){
								$("#myModal").modal('hide');
								window.location.reload();
							}
						},"json");
				
					}else{
						alert('Debe de ingresar un motivo mas claro de por que lo esta pasando a este estatus!');		
					}
				}else{
					alert('Debe de llenar el campo motivo');	
				}
			});
		});	
	},

	/*Ventana que genera gestion*/
	doGenerateGestion : function(){
		var instance=this; 
		instance.post("./?mod_cobros/delegate&doGenerateGestion",{ 
				'contrato':this._contrato 
		},function(data){   
			$("#"+instance.dialog_container).html(data);
			var wizard = $('#gestion-wizard').wizard({
				keyboard : false,
				contentHeight : 400,
				contentWidth : 700,
				backdrop: 'static',
				showCancel:true,
				buttons: {
					cancelText: "Cancelar",
					nextText: "Siguiente",
					backText: "Ir atras",
					submitText: "Procesar",
					submittingText: "Procesando..",
				}
			});  
			wizard.show();	 
			wizard.on("submit", function(wizard) { 
			
				instance.post("./?mod_cobros/delegate&cierre_acta_listado&createActa",
				{
					descripcion:$("#doc_descripcion").val(),
					"id_acta":acta,
					'contratos':contratos
				},function(data){
					alert(data.mensaje) 
				},"json");
			 
				wizard.trigger("success");
				wizard.hideButtons();
				wizard._submitting = false;
				wizard.showSubmitCard("success");
				wizard.updateProgressBar(0);
				
				$("#close_w").click(function(){  
					wizard.cancelButton.click();
					window.location.reload();
				});
			 
			});
			var check_t_gestion=false;
			$('.tipo_gestion_cc').click(function(){
				if ($(this).is(':checked')){
					check_t_gestion=true;
				}
			});
			window["validate_gestion"]=function(){  
				var retValue = {}; 
				retValue.status = check_t_gestion; 
				if (!check_t_gestion){ alert('Debe de seleccionar un tipo de gestion!')}
				return retValue;	
			}			
		});	
	},	
	/*Ventana que accede a los desistidos*/
	doDesistir : function(){
		var instance=this; 
		instance.post("./?mod_cobros/delegate&view_desistir",{ 
				'contrato':this._contrato 
		},function(data){  
			instance.doDialog("myModal",instance.dialog_container,data);
		   	
			$("#aplicar_cambio").click(function(){
				if ($("#comentario").val()!=''){
					if ($("#comentario").val().length>7){
						if (!confirm("Esta seguro de realizar esta operacion?")){
							return false;
						}
						instance.post("./?mod_cobros/delegate&desitir_contrato",{ 
								'contrato':instance._contrato,
								'token':instance._rand
						},function(data){ 
							alert(data.mensaje);
							if (!data.valid){
								$("#myModal").modal('hide');
								window.location.reload();
							}
						},"json");
				
					}else{
						alert('Debe de ingresar un motivo mas claro de por que lo esta pasando a este estatus!');		
					}
				}else{
					alert('Debe de llenar el campo motivo');	
				}
			});
		});	
	},
	
	doCreateLaborCobro : function(){
		var instance=this;
		instance.post("./?mod_cobros/delegate&labor_cobro",{
				"view_add_caja":'1',
				'contrato':this._contrato 
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"Gestión",data,530);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 0]); 
			
			/*UN ARRAY QUE MANEJA LOS RESPONSABLES DE UNA ACTIVIDAD*/
			var actividad_responsable=[];
			
			$("#act_add").click(function(){
			//	if ($("#frm_actividad_").valid()){ 
					var info=$("#frm_actividad_").serializeArray();
					info.push({name: "actividad_responsable", value: JSON.stringify(actividad_responsable)}); 
 					
					instance.post("./?mod_cobros/delegate&processLaborCobro",info,function(data){
						alert(data.mensaje);	
						if (!data.valid){
							window.location.reload();
						}
					},"json");
			//	}
			});
			
			$("#accion").change(function(){ 
				$("#lb_gestion_tb").hide();
				$("#lb_gestion_tr").hide();
				
				$("#isTipoGestion").val($("#accion option:selected").attr('gestion'));
				if ($("#accion option:selected").attr('gestion')=="S"){
					$("#lb_gestion_tb").show(); 
					$("#lb_gestion_tr").show();
					//alert($("#accion option:selected").attr('gestion')); 
				}
			});
			
			/* CARGA LOS TIPOS DE GESTIONES */
			$("#tipo_gestion").change(function(){   
				instance.post("./?mod_cobros/delegate&charge_list_actividad",
					{
						"gestion":$("#tipo_gestion option:selected").val(),
						'contrato':instance._contrato,
						'token':instance._rand
					},function(items){
					 $("#load_view_actividad").html(items.html);
					 
					 for(i=0;i<items.field.length;i++){ 
						var id=items.field[i].nomen;
						$("#act_responsable_"+id).combogrid({
							url: './?mod_caja/delegate&caja&getListUsuario=1', 
							colModel: [ 
									 {'columnName':'nombre','width':'60','label':'Nombre'}
									],
							select: function( event, ui ) { 
								$(this).val( ui.item.nombre );
								var s=$(this).attr("id");
								var sp=s.split("_"); 
								$("#act_responsabe_id_"+sp[sp.length-1]).val( ui.item.value );  
								$("#act_dia_realizacion_"+sp[sp.length-1]).val('');
								var obj={
									actividad:items.field[sp[sp.length-1]],
									responsable: ui.item.value,
									fecha:""
								} 
								actividad_responsable[sp[sp.length-1]]=obj; 
								return false;
							},
							change : function(event,ui){
								if (ui.item==null){  ;
									var s=$(this).attr("id");
									var sp=s.split("_"); 
									$("#act_responsabe_id_"+sp[sp.length-1]).val('');  
									actividad_responsable[sp[sp.length-1]]="";									
								}
							}
						});	
						 
						 
						$("#act_dia_realizacion_"+id).datepicker({
								changeMonth: true,
								changeYear: true,
								yearRange: '1900:2050',
								monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'], 
								monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'], 
								dateFormat: 'dd-mm-yy',  
								dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'], 
								dayNamesMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'], 
								dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'], 
									
							}); 
						$("#act_dia_realizacion_"+id).change(function(){
							var s=$(this).attr("id");
							var sp=s.split("_"); 
							var obj=actividad_responsable[sp[sp.length-1]];							
							obj.fecha=$(this).val();
							actividad_responsable[sp[sp.length-1]]=obj;
						});
						
						$("#act_responsable_"+id).change(function(){
							if ($.trim($(this).val())==""){
								var s=$(this).attr("id");
								var sp=s.split("_"); 								
								actividad_responsable[sp[sp.length-1]]="";		
								$(this).val('');	
							}
						});
							
					 }
					 
					 
				},"json"); 
			});
			

			
			$("#act_cancel").click(function(){
				instance.close(dialog);	
			}); 
 
			$("#lb_fecha_contacto").datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange: '1900:2050',
			monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'], 
			monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'], 
			dateFormat: 'dd-mm-yy',  
			dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'], 
			dayNamesMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'], 
			dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'], 
				
		});				  
			 $('#lb_hora').timeEntry({showSeconds: true});	
		//	instance.validateFormGestion(); 
		});	
	},
	
	doCreateRequerimientoC : function(serie_contrato,no_contrato){
		var instance=this;
		instance.post("./?mod_cobros/delegate&crequerimiento",{
				"view_add_caja":'1',
				'contrato':this._contrato,
				'token':instance._rand
		},function(data){  
			instance.doDialog("modal_requerimiento",instance.dialog_container,data.html); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			}) 
			var old_compromiso=(data.compromiso*1);
			var compromiso=data.compromiso;
			var saldo_actual=data.saldo_actual;
			var abono=0;
			var cantidad=0;
			
			setTimeout(function(){
					$("#cantidad_cuotas").focus();
					$("#cantidad_cuotas").val('1');
					$("#cantidad_cuotas").change();
				},500);
				
			$("#fecha_requerimiento").datepicker({
				changeMonth: true,
				changeYear: true,
				yearRange: '1900:2050',
				monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'], 
				monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'], 
				dateFormat: 'dd-mm-yy',  
				dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'], 
				dayNamesMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'], 
				dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],  
			}); 
			
			$("#req_direccion").change(function(){
				if ($(this).val()=="add"){
					var direccion= new CDireccion(instance.dialog_container); 
					direccion._direccion_id=$("#bt_add_direccion").attr("id_address");
					direccion.loadView(serie_contrato,no_contrato);
					direccion.addListener("onAddAdress",function(){
						$("#req_direccion").val('')
					})
					direccion.addListener("doCancelAddress",function(){
						$("#req_direccion").val('')
					})					
				}
			});
				
			$("#compromiso").change(function(){
				var new_compromiso=$(this).val();
				
				if (new_compromiso>0){ 
					if (new_compromiso>=old_compromiso){ 
						compromiso=new_compromiso; 
						$("#procesar_requerimientoc").attr("disabled",false);
						$("#cantidad_cuotas").val((compromiso/old_compromiso)); 
						$("#cantidad_cuotas").change();
					}else{
						$("#procesar_requerimientoc").attr("disabled",true);	
						$("#compromiso").val(instance.number_format(old_compromiso));
						$("#cantidad_cuotas").val((old_compromiso/old_compromiso));	
						$("#cantidad_cuotas").change();				
						alert('El abono no puede ser menor que el compromiso minimo');	
					} 
				}
			});	
			$("#cantidad_cuotas").change(function(){
				cantidad=$(this).val();
				if (cantidad>0){
					abono=old_compromiso*cantidad; 
					if (saldo_actual>=abono){
						//$("#compromiso").val(instance.number_format(old_compromiso));
						$("#monto_abono").val(instance.number_format(abono));	
						$("#nuevo_saldo").val(instance.number_format(saldo_actual-abono));	
						$("#procesar_requerimientoc").attr("disabled",false);
					}else{
						$("#procesar_requerimientoc").attr("disabled",false);						
					//	$("#procesar_requerimientoc").attr("disabled",true);						
					//	alert('El abono no puede ser mayor que el saldo actual');	
					}
					
				}
			});
			
			
			
			$("#procesar_requerimientoc").click(function(){
			
				if ($("#frm_cobro").valid()){ 
					$("#procesar_requerimientoc").prop("disabled", true); 
					var data={
						"cantidad": cantidad,
						"abono":abono,
						"tmov":$("#tipo_movimiento_rmc").val(),
						"contrato":instance._contrato,
						"motorisado":$("#txt_motorisado").val(),
						"oficial": $("#txt_oficial").val(),
						"comentario":$("#cp_comentarios").val(),
						"fecha_requerimiento":$("#fecha_requerimiento").val(),
						"req_direccion":$("#req_direccion").val()
					}					
					instance.post("./?mod_cobros/delegate&procesarRequerimientoc",data,function(data){
						alert(data.mensaje);	
						$("#procesar_requerimientoc").prop("disabled",false); 
						if (data.valid){
							window.location.reload();
						}
					},"json");
				}
			});
			
			$("#frm_cobro").validate();	
			$.validator.messages.required = "Campo obligatorio."; 
			//$('#lb_hora').timeEntry({showSeconds: true});	
			
		},"json");	
	} ,
	
	validateFormAccion : function(){
		$("#frm_cobro").validate({});	
		$.validator.messages.required = "Campo obligatorio.";
	}
});