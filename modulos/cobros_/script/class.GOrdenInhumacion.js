/* ORDEN DE INHUMACION*/
var GOrdenInhumacion = new Class({
	dialog_container : null,  
	_rand : null, 
	initialize : function(dialog_container){
		this.main_class="GOrdenInhumacion";
		this.dialog_container=dialog_container; 
	}, 
	doView: function(contrato){
		var instance=this; 
		this._contrato=contrato;
		
		instance.post("./?mod_cobros/delegate&view_gestion_inhumacion",{ 
				'contrato':this._contrato 
		},function(data){   
			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){ 
			}) ;
			
			var atendido_por=null;
		  	var ciudad=null;
		  	var preparado_por=null;
 		  	var cementerio=null;
		  	var funeraria=null;	
			var causa_fallecimiento=null;	
			var lugar_defuncion=null;	
			var fecha_defuncion=null;	
			var medico=null;	
			var inicio_servicio=null;	
			var fin_servicio=null;	
			var nombre_lapida=null;	
			var esquela=null;	
			var id_nit = null;
			var no_acta_defuncion=null;
														
 			
			$('#cuidad_id').select2();	
			$("#cuidad_id").on("change", 
				function(e) { 
					ciudad=e.val; 
			});
			
			$(".date_pick").datepicker({
				changeMonth: false,
				changeYear: false,
				yearRange: '1900:2050',
				monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'], 
				monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'], 
				dateFormat: 'dd-mm-yy',  
				dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'], 
				dayNamesMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'], 
				dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'], 
					
			});	
			 
 			$('#txt_preparado_por').select2({
			  multiple: false,
			  minimumInputLength: 4,
			  query: function (query){ 
				  $.post("./?mod_cobros/delegate&zonas",{"motorizados":'1',"sSearch":query.term},function(data){ 
					 query.callback(data);
				   },"json");   
			  }
			});
			$("#txt_preparado_por").on("change", 
				function(e) { 
					preparado_por=e.val; 
			});
			
		 		
 			$('#txt_cementerio').select2({
			  multiple: false, 
			  query: function (query){ 
				  $.post("./?mod_cobros/delegate&inhumado",{"getCementerio":'1',"sSearch":query.term},function(data){ 
					 query.callback(data);
				   },"json");   
			  }
			});
			$("#txt_cementerio").on("change", 
				function(e) { 
					cementerio=e.val; 
			});				
				
 			$('#txt_funeraria').select2({
			  multiple: false, 
			  query: function (query){ 
				  $.post("./?mod_cobros/delegate&inhumado",{"getFuneraria":'1',"sSearch":query.term},function(data){ 
					 query.callback(data);
				   },"json");   
			  }
			});	
			$("#txt_funeraria").on("change", 
				function(e) { 
					funeraria=e.val; 
			});	
			
			$('#inhu_tipo_cofre').select2();
						
			
			$("#agregar_inhumado").click(function(){ 
				var registrar = new RegistrarInhumado(instance.dialog_container,"detalle_inhumado_view");
				registrar.doView(instance._contrato);
				registrar.addListener("onSaveDetalle",function(data){  
					$("#inh_nombre_completo").val(data.nombre_completo);
					$("#inh_no_documento").val(data.id_nit); 
					id_nit=data.id_nit_enc
				});
			});
			
			$("#inh_bt_facturar").click(function(){
				var fact = new FacturarProductos(instance.dialog_container);
				fact.doView(instance._contrato);
				fact.addListener("onSaveDetalle",function(data){  
					$("#inh_nombre_completo").val(data.nombre_completo);
					$("#inh_no_documento").val(data.id_nit); 
				});
				
			});
			
			$("#procesar_solicitud").click(function(){
				
				if (!$("#detalle_inhumado_form").valid()){
					alert('Debe de llenar la informacion de cada item del menu');
					return false;
				}
				if (atendido_por==""){
					alert('No se puede procesar  sin antes completar todos los datos correctamente!');
					return false;
				} 
				
				instance.post("./?mod_cobros/delegate&inhumado",{
					"doGenerateServices":1,
					"atendido_por":atendido_por,
					"contrato":contrato,
					"solicitante_nombre_contacto":$("#solicitante_nombre_contacto").val(),
					"solicitante_telefono":$("#solicitante_telefono").val(),
					"solicitante_parentesco":$("#solicitante_parentesco").val(),
					"difunto_parentesco":$("#difunto_parentesco").val(),
					"hora_servicio":$("#hora_servicio").val(),
					"inhu_tipo_cofre":$("#inhu_tipo_cofre").val(),
					"inhu_religion":$("#inhu_religion").val(),
					"nombre_lapida":$("#nombre_lapida").val(),
					"esquela":$("#esquela").val(),
					"servicio_descripcion":$("#servicio_descripcion").val(),
					"servicio_parcela":$("#servicio_parcela").val(), 
					"id_nit":id_nit,
					"contrato":contrato,
					"ciudad":ciudad,
					"preparado_por":preparado_por,
					"cementerio":cementerio,
					"funeraria":funeraria,
					"causa_fallecimiento":$("#causa_fallecimiento").val(),
					"lugar_defuncion":$("#lugar_defuncion").val(),
					"fecha_defuncion":$("#fecha_defuncion").val(),
					"no_acta_defuncion":$("#no_acta_defuncion").val(), 
					"serv_fecha_inicio":$("#serv_fecha_inicio").val(), 
					"serv_fecha_fin":$("#serv_fecha_fin").val(), 
					"medico":$("#medico").val(),
					"inicio_servicio":$("#inicio_servicio").val() ,
					"fin_servicio":$("#fin_servicio").val(),
					"inhu_tipo_cofre":$("#inhu_tipo_cofre").val(),
					"inhu_religion":$("#inhu_religion").val()				
				},function(data){					  
					alert(data.mensaje)	
					if (data.valid){ 
						window.location.reload(); 
					} 
				},"json");		
			});
			
			
			$('#txt_atendido_por').select2({
			  multiple: false,
			  minimumInputLength: 4,
			  query: function (query){ 
				  $.post("./?mod_cobros/delegate&inhumado&getAtendidoPor",{"sSearch":query.term},function(data){ 
					 query.callback(data);
				   },"json");   
			  }
			});
			$("#txt_atendido_por").on("change", 
				function(e) { 
					atendido_por=e.val; 
			});	
			
			
			$("#servicio_parcela").change(function(){
 				instance.post("./?mod_cobros/delegate&inhumado",{
					"getInfoParcela":1,
					"parcela":$(this).val(),
					"contrato":contrato 
				},function(data){  
					if (data.valid){ 
						$("#dt_plan").html(data.plan);
						$("#dt_boveda").html(data.boveda);						
					}else{
						alert(data.mensaje)	
					}
				
				},"json");
			});			
			
			$.validator.messages.required = "Campo obligatorio.";	
						
		});			
	},
	doquestionView : function(){
		var instance=this;  
		instance.post("./?mod_contratos/listar",
		{
			"view_search":"1" 
		},function(data){ 
			var dialog=instance.createDialog(instance.dialog_container,"Agregar solicitud",data,420);
			
			$("#"+dialog).dialog({ 
				closeOnEscape: false, 
				close: function (ev, ui) {
					instance.fire("doNotCreatePerson");
				}
			});
			$("#"+dialog).parent().children().children('.ui-dialog-titlebar-close').hide();
			
			$("#_cancel").click(function(){
				instance.fire("doNotCreatePerson");
			});
			
			
			$("#id_documento").change(function(){
				var type_document=$('#id_documento option:selected').text();
				if ($("#id_documento").val().trim()!=""){
					$("#numero_documento").prop('disabled', false);
				}else{
					$("#numero_documento").prop('disabled', true);
				}
			});
			 
			$("#_buscar").click(function(){
				
				var valid_field=true;
				var type_document=$('#id_documento option:selected').text();
				if (type_document.trim()=="CEDULA"){					
					valid_field=valida_cedula($("#numero_documento").val());
				}
				if (type_document==""){valid_field=false;}
				
				if (($("#numero_documento").val().length>=7) && (valid_field)){
					//$('#'+dialog).showLoading({'addClass': 'loading-indicator-bars'});
					
					instance.post("./?mod_contratos/listar",{validarPersona:"1","numero_documento":$("#numero_documento").val(),"tipo_documento":$("#id_documento").val()},function(data){	
						//$('#'+dialog).hideLoading();
		
						if (data.addnew){
							var info={"numero_documento":$("#numero_documento").val(),"tipo_documento":$("#id_documento").val()};
							instance.close(dialog);
							
							/*SI EL # DE IDENTIFICACION NO EXISTE*/
							var data='<br><center><strong><p>Este numero de identificacion no existe en nuestra base de datos.</p> <p> Desea Agregarlo?</p> </strong></center><br><center><button type="button" id="caputra_si" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">SI</span></button>&nbsp;&nbsp;<button id="captura_no" type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">NO</span></button></center>';
							
							dialog=instance.createDialog(instance.dialog_container,"Agregar solicitud",data,420);
							$("#"+dialog).dialog({ 
								closeOnEscape: false, 
								close: function (ev, ui) {
									instance.fire("doNotCreatePerson");
								}
							});
							/*OCULTO EL BOTON DE CLOSE DE LA VENTANA DE DIALOGO*/
							$("#"+dialog).parent().children().children('.ui-dialog-titlebar-close').hide();
					 
							$("#caputra_si").click(function(){ 
								instance.close(dialog);
								instance.processCreatePeron(info);
							});
							$("#captura_no").click(function(){
								/*EVENTO SE DISPARA CUANDO SELECCIONAN QUE NO EN LA RESPUESTA*/
								instance.fire("doNotCreatePerson");
							});	 
								
						}else{  
							var prosp=data.personal;
							/*VERIFICO EL ESTATUS DEL PROSPECTO PARA VER SI ESTA EN CIERRE O RESERVA*/
							if ((prosp.pros_estatus==6) || (prosp.pros_estatus==7)){
								instance.close(dialog); 
								var obj={
									onload:function(res){ 
										instance.fillPersonData(true,res,prosp.id_nit);  
										/*CARGO LA DATA DE LA PERSONA SELECCIONADA*/
										instance.upDataContratante(prosp.id_nit,data.personal.id_documento);	
										 
									}	
								};	
								instance.getPersonData(prosp.id_nit,obj);	
								instance.processPeronOrProspectoExist(data); 	
								
							}else{
								instance.close(dialog); 
								
								if (prosp.pros_estatus==null){
									alert('La persona '+
										prosp.primer_nombre+ " "+
										prosp.segundo_nombre+ " "+
										prosp.primer_apellido+ " "+
										prosp.segundo_apellido + " esta registrado como persona, pero"
										+" no ha sido prospectado!.");
								}
								
								if ((prosp.pros_estatus==null) || (prosp.pros_estatus==4)){
 
									var obj={
										onload:function(res){ 
											instance.fillPersonData(true,res,prosp.id_nit);  
											/*CARGO LA DATA DE LA PERSONA SELECCIONADA*/
											instance.upDataContratante(prosp.id_nit,data.personal.id_documento);	
											 
										}	
									};	
									//CARGO LOS DATOS PERSONALES DE X PERSONA
									instance.getPersonData(prosp.id_nit,obj);	
				 					//CARGAR VIEW DE EDITAR PERSONA 
									instance.processEditPeron(prosp.id_nit,prosp.pros_estatus==4?false:true);		
								}else{
									alert('El prospecto se encuentra protegido, para poder proceder deben de cerrar la prospectacion!');
								}
							}
						} 
					},"json");	
				}else{
					$("#numero_documento").val('');
					$("#numero_documento").focus();
					alert('Digite un numero de identificacion valido!');	
				}
				
			});   
			
		},"text");		
		 	 
	},
	processPeronOrProspectoExist : function(obj){
		var instance=this; 
		//pros_estatus
		var person_component= new ModuloPersonas('Datos Personales',this.dialog_container);
		var person= new Persona(person_component);
		/*PONGO EL MODULO DE PERSONA EN MODO EDITAR*/
		person.setView("edit");
		person.addListener("onEditPerson",function(data){
			alert(data.mensaje);	
		});
		
		/**********************************/
 		person_component.addModule(person);
		
		var direccion= new Direccion(person_component);
		person_component.addModule(direccion);
		
		direccion.addListener("doLoadViewComplete",function(nobj){
			
			var data='<br><center><button type="button" class="greenButton" id="bt_pros_select">Finalizar</button>';
			data=data+'<button type="button" class="redButton" id="bt_pros_cancelar">Cancelar</button></center><br>';
			$("#main_module").append(data);
			
			$("#bt_pros_cancelar").click(function(){
				person_component.closeView();
			});
			
			/* BOTTON FINALIZAR */
			$("#bt_pros_select").click(function(){
				person_component.closeView(); 
				instance.fillProspectoData(true,instance._person.data,instance._person.idnit); 
				instance.upDataProspecto(instance._person.idnit,instance._person.data.personal.id_documento); 
				instance.fire("onEndCapture",instance);				
				
			});
		});
		
		/**/
		var empresa= new personEmpresa(person_component);
		person_component.addModule(empresa);
		/***************************************************/
		 
		/**/
		var telefono= new Telefono(person_component);
		person_component.addModule(telefono);
		/***************************************************/
		 
		var email= new Email(person_component);
		person_component.addModule(email);	
		
		var reference= new Referencia(person_component);
		person_component.addModule(reference);
		
		var referidos = new Referidos(person_component);
		person_component.addModule(referidos);
		
		
		/*Le digo que cliente es el que sera editado*/
		person_component.setPersonID(obj.personal.id_nit);
		/************************************************/
 	 
		person_component.addListener("onloadView",function(){
			/*REMUEVO LA X DEL DIALOG*/
			$("#"+person_component._dialog).parent().children().children('.ui-dialog-titlebar-close').hide();	
			$("#"+person_component._dialog).dialog({ 
				closeOnEscape: false, 
				close: function (ev, ui) {
					instance.fire("doNotCreatePerson");
				}
			});
		});
		
	 	person_component.loadMainView();
	}	
	
});