/*PROCESO DE CAPTURA DE DATOS DE CLIENTES Y PROSPECTOS*/
var Captura = new Class({
	dialog_container : null,
	_form_name : "Captura",
	_person : { 
		valid:false,
		data : null,
		idnit:null
	 },
	_prospecto : {
		valid:false,
		data : null,
		idnit : null
	 },	
	initialize : function(dialog_container){
		this.main_class="Captura";
		this.dialog_container=dialog_container; 
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
										//CARGO LA DATA DE LA PERSONA SELECCIONADA
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
	
	editContrat : function(dialog,id_nit_contratante,idnit_prospecto){
		var instance=this; 
 	
		var obj={
			onload:function(data){ 
				instance.fillPersonData(true,data,id_nit_contratante);  
				instance.upDataContratante(id_nit_contratante,data.personal.id_documento); 
		 		instance.fire("onEndCapture",instance);				
			}	
		}; 
		instance.getPersonData(id_nit_contratante,obj);	
		
		var obj={
			onload:function(data){ 
				//alert(data.personal.fecha_nacimiento);
				instance.fillProspectoData(true,data,idnit_prospecto);			
		 		instance.fire("onEndCapture",instance);
				
				var action={
					onload : function(rs){
						//alert(rs.parentesco) //alert(rs.id_parentesco)
						$("#prospect_parentesco").val(rs.id_parentesco);	
						$("#prospect_parentesco").click();
					}	
				} 
				instance.getParentesco(id_nit_contratante,idnit_prospecto,action);
				$("#bt_find_person").hide();				
			}	
		};  
		if (idnit_prospecto!=""){
			instance.getPersonData(idnit_prospecto,obj);
		}
			
		 
	},
	
	fillPersonData : function(valid,data,idnit){
		this._person.valid=valid;
		this._person.data=data; 
		this._person.idnit=idnit;	
	},
	
	fillProspectoData : function(valid,data,idnit){
		this._prospecto.valid=valid;
		this._prospecto.data=data; 
		this._prospecto.idnit=idnit;	
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
				instance.upDataProspecto(instance._person.idnit,instance._person.data.personal.id_documento); 
//				instance.fire("onEndCapture",instance);				
			//	person_component.closeView();
				var obj={
					onload:function(res){ 
						instance.fillPersonData(true,res,instance._person.idnit);  					
						instance.fillProspectoData(true,instance._person.data,instance._person.idnit); 					
						instance.fire("onEndCapture",instance);	
					}	
				};	
				instance.getPersonData(instance._person.idnit,obj);				 

				
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
			/*$("#"+person_component._dialog).parent().children().children('.ui-dialog-titlebar-close').hide();	
			$("#"+person_component._dialog).dialog({ 
				closeOnEscape: false, 
				close: function (ev, ui) {
					instance.fire("doNotCreatePerson");
				}
			});*/
		});
 
	 	person_component.loadMainView();
	},
	
	processCreatePeron : function(data){
		var instance=this;
		var person_component= new ModuloPersonas('Datos Personales',this.dialog_container);
 
		var person= new Persona(person_component);
		/*SELECCIONO LA VISTA QUE QUIERO QUE APAREZCA*/
		person.setView("create");
		person.addListener("cancel_creation",function(){
			person_component.closeView();
			instance.fire("doNotCreatePerson");
		}); 
		/*********************************************/
		/*Capturar cuando la vista ha sido creada*/
		person.addListener("onViewCreate",function(){
			$("#numero_documento").val(data.numero_documento);			  
			$('#id_documento option[value="' + data.tipo_documento + '"]').prop('selected',true);
		});
		
		/*Evento que captura cuando un cliente ha sido creado */
		person.addListener("onCreatePerson",function(persons){
			/*Cierro la vista para refrescarla con los datos del cliente agregado */
			person_component.closeView();
			instance.processEditPeron(persons['nit']);			
		});
		
		person_component.addModule(person);
	 
		person_component.addListener("onloadView",function(){
		/*	$("#"+person_component._dialog).parent().children().children('.ui-dialog-titlebar-close').hide();	
			$("#"+person_component._dialog).dialog({ 
				closeOnEscape: false, 
				close: function (ev, ui) {
					instance.fire("doNotCreatePerson");
				}
			});*/
		});
	 	person_component.loadMainView();
	},
	
	processEditPeron : function(idnit,doQuestion){
	
		var instance=this; 
		var person_component= new ModuloPersonas('Datos Personales',this.dialog_container);	
		var person= new Persona(person_component);
	 
		/*PONGO EL MODULO DE PERSONA EN MODO EDITAR*/
		person.setView("edit"); 
		person.addListener("onEditPerson",function(data){
			alert(data.mensaje);	
		});
		person_component.addModule(person);
		
 		var direccion= new Direccion(person_component);
		person_component.addModule(direccion);
	 
		direccion.addListener("doLoadViewComplete",function(obj){
			 
			var data='<br><center><button type="button" class="greenButton" id="bt_pros_select">Finalizar</button>';
			data=data+'<button type="button" class="redButton" id="bt_pros_cancelar">Cancelar</button></center><br>';
			$("#main_module").append(data);
			
			$("#bt_pros_cancelar").click(function(){
				person_component.closeView();
				instance.fire("doNotCreatePerson");
			});
			
			$("#bt_pros_select").click(function(){
				person_component.closeView();
		 
				var obj={
					onload:function(data){ 
						instance.fillPersonData(true,data,idnit);
						/*CARGO LA DATA DE LA PERSONA SELECCIONADA*/
						instance.upDataContratante(idnit,data.personal.id_documento);	
						if (doQuestion==null){
							instance.doQuestionAsociarProspecto();	
						}else if (doQuestion){
							instance.doQuestionAsociarProspecto();	
						}else{
							instance.fire("onEndCapture",instance);	
						}
					}	
				};
				instance.getPersonData(idnit,obj);
 			});
			
			
		});
 		 
		var empresa= new personEmpresa(person_component);
		person_component.addModule(empresa);
  
		var telefono= new Telefono(person_component);
		person_component.addModule(telefono);
 		 
		var email= new Email(person_component);
		person_component.addModule(email);	
		
		var reference= new Referencia(person_component);
		person_component.addModule(reference);
		
		var referidos = new Referidos(person_component);
		person_component.addModule(referidos);
		
		///Le digo que cliente es el que sera editado/
		person_component.setPersonID(idnit);
 		///SETTEO LA VISTA PARA QUE CARGE DE PRIMERO LA VISTA DE DIRECCION/
		//person_component.selected(direccion.getTabID());
 
		person_component.addListener("onloadView",function(){
		/*	$("#"+person_component._dialog).parent().children().children('.ui-dialog-titlebar-close').hide();	
			$("#"+person_component._dialog).dialog({ 
				closeOnEscape: false, 
				close: function (ev, ui) {
					instance.fire("doNotCreatePerson");
				}
			});*/
		});
		
	 	person_component.loadMainView();
		
	}, 
	
	getPersonData : function(idnit,obj){
		var instance=this;
		var persona= new PersonalData(instance.dialog_container,"test",idnit);
		/*CARGO LOS DATOS PERSONALES DEL CLIENTE*/
		persona.getPersonData();
		persona.addListener("personal_data_load",function(data){
			if (data.valid){ 
				if (obj!=null){
					if (typeof obj.onload=="function"){
						obj.onload(data);
					}
				} 
			}else{
				//alert('Error al tratar de seleccionar el cliente!');	
			}
		});	
	},
	getParentesco : function(idnit,idnitparentesco,eaction){
		var instance=this;
		instance.post("./?mod_contratos/listar",
		{
			"getParentesco":"1",
			"idnit": idnit,
			"idnit_parentesco":idnitparentesco
		},function(inf){
			if (eaction!=null){
				if (typeof eaction.onload=="function"){
					eaction.onload(inf);
				}
			} 			
		},"json");	
	},
	/*CUADRO DE DIALOGO PARA ASOCIAR CON UN PROSPECTO*/
	doQuestionAsociarProspecto : function(){
		var instance=this;
		/*SI EL # DE IDENTIFICACION NO EXISTE*/
		var data='<br><center><p><strong>Desea asociar con un prospecto?.</strong></p></center><center><br><button type="button" id="captura_p_si" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">SI</span></button>&nbsp;&nbsp;<button id="captura_p_no" type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">NO</span></button></center>';
		
		var dialog=instance.createDialog(instance.dialog_container,"Asociar prospecto",data,420);
		$("#"+dialog).dialog({ 
			closeOnEscape: false,
			open: function(event, ui) { 
				$(".ui-dialog-titlebar-close").hide(); 
			} ,
			close: function (ev, ui) {
				instance.fire("doNotCreatePerson");
			}
		});
		 
		$("#captura_p_si").click(function(){ 
			instance.close(dialog);
			instance.find_prospecto(); 
		});
		$("#captura_p_no").click(function(){
			instance.close(dialog);
			instance.fillProspectoData(false,[],0)
			instance.fire("onEndCapture",instance);
		});
		
	},
	
	upDataContratante : function(idnit,id_documento){ 
		var instance=this;
		instance.post("./?mod_contratos/listar",
		{
			"contrato_data":"contratante",
			"id_nit":idnit,
			"id_documento":id_documento
		},function(inf){
			
		});
	},
	
	upDataProspecto : function(idnit,id_documento){ 
		var instance=this;
		instance.post("./?mod_contratos/listar",
		{
			"contrato_data":"prospecto",
			"id_nit":idnit,
			"id_documento":id_documento
		},function(inf){
			
		});
	},	
	/*Ventana de seleccionar prospecto*/
	find_prospecto : function(){
		var instance=this;
		var dialog=instance.dialog_container;
		var _prospectos= new Prospectos(dialog,'');
		_prospectos.viewSimpleList();
		_prospectos.addListener("prospecto_selected",function(prospect){
			var person= new PersonalData(dialog,"test",prospect.idnit);
			/*CARGO LOS DATOS PERSONALES DEL CLIENTE*/
			person.getPersonData();
			person.addListener("personal_data_load",function(data){
				if (data.valid){
					instance._prospecto.idnit=prospect.idnit;
					instance._prospecto.data=data;
					instance._prospecto.valid=true;
					
					//LLENO LOS DATOS DEL PROSPECTO	 				 			
					instance.fillProspectoData(true,data,instance._prospecto.idnit);
					
					////////////////////////////////////////
				//	$('#'+dialog).showLoading({'addClass': 'loading-indicator-bars'});	
			 	
					instance.post("./?mod_contratos/listar",
					{
						"contrato_data":"prospecto",
						"id_nit": prospect.idnit,
						"id_documento":data.personal.id_documento
					},function(inf){
						//$('#'+dialog).hideLoading(); 
						instance.fire("onEndCapture",instance);
					});
					 
					
				}else{
					alert('Error al tratar de seleccionar el prospecto!');	
				}
			})
			
		});
		
	}
	
 
});