    var DetalleBeneficiario = new Class({
	dialog_container : null,
	_contrato : null,  
        initialize : function(dialog_container){
		this.main_class="DashBoard";
		this.dialog_container=dialog_container; 
	}, 
        //INSERTED HERE BY ROBERTO 10/06/15
	doInit : function(id){ 
	   var instance=this;
	   this._contrato=id; 
	   
	   instance.post("./?mod_cobros/delegate&view_beneficiario",{
			"id":id
		}, function(response){
			//$("#lbeneficiario").remove();
			$("#lbeneficiario").html(response);
			
			$(".bcbeneficiario").click(function(){
				var id = $(this).attr("id");
				var accion = $(this).val(); 
				instance.editBeneficiario(id, accion);
			});
 
		});  		   
		 
	},
	editBeneficiario : function(id, accion){
            var instance=this;	    
            this.doViewBeneficiario(id);
            //var url      = window.location.href;
            this.removeListener("onCreateBeneficiario");     
            this.addListener("onCreateBeneficiario",function(data){
				 
                 instance.post("./?mod_cobros/delegate&procesar_beneficiario",{ 
                                "contrato":instance._contrato,
                                'beneficiario':id,
                                'new_beneficiario':(data),
                                'accion':accion
                },
                function(data){ 
                        alert(data.mensaje);
                        if (!data.valid){
                                $("#myModal").modal('hide');
                                
                        }
                        location.reload();
                      
                },"json");   
        
            });    
	},
        /*CREA LA VISTA PARA AGREGAR UN BENEFICIARIO*/
	doViewBeneficiario: function(id){
		var instance=this; 
		var data='<br><br><center><button type="button" id="mayor_edad" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">MAYOR DE EDAD</span></button>&nbsp;&nbsp;<button id="menor_edad" type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">MENOR DE EDAD</span></button></center>';
		
		var dialog=this.createDialog(this.dialog_container,"Beneficiario",data,400);
		
		$("#menor_edad").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();

			var person_component= new ModuloPersonas('Beneficiario',instance.dialog_container,instance._dialog);
			person_component.loadMainView();
			
			var person= new Beneficiario(person_component);
			//SELECCIONO LA VISTA QUE QUIERO QUE APAREZCA
			person.setView("create","MENOR");
			//********************************************//
			person.addListener("onViewCancel",function(){
				person_component.closeView();
			});
			//Agrego el modulo al main content/
			person_component.addModule(person);
	 
			///Evento que captura cuando un cliente ha sido creado //
			person.addListener("onCreateBeneficiario",function(data){
				//Cierro la vista para refrescarla con los datos del cliente agregado //
				person_component.closeView();
				//********************************/
				instance.fire("onCreateBeneficiario",{"tipo":"menor","data":data});
			});

			
		}); 
				
		$("#mayor_edad").click(function(){
			$("#"+dialog).dialog("destroy");
			$("#"+dialog).remove();
			
			var action={
				//Quiero agregarlo!
				doAdd : function(data){   
					instance.processCreateBeneficiario(data,id);
				},
				//No deseo agregar el cliente
				doNotAdd : function(data){
					
				},
				//Si el cliente existe
				onClientExist : function(data){ 
					if ((true)){ 
						instance.processEditBeneficiario(data.personal.id_nit,id,"");
					}else{
						alert('El contratante no puede ser Beneficiario!');	
					}
				}
			};
			
			instance.finderView('Buscar',action);
                        
                        
				//********************************/
			  	
			//********************//

		}); 				
 	 
	},
    finderView : function(title,oAction){
		var instance=this;
 		instance.post("./?mod_contratos/listar",
		{
			"view_search":"1" 
		},function(data){
			//$('#'+instance.dialog_container).hideLoading();
			var dialog=instance.createDialog(instance.dialog_container,title,data,420);
   
			$("#_cancel").click(function(){      
				instance.close(dialog);
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
							var ifdata='<br><center><strong><p>Este numero de identificacion no existe en nuestra base de datos.</p> <p> Desea Agregarlo?</p> </strong></center><br><center><button type="button" id="caputra_si" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">SI</span></button>&nbsp;&nbsp;<button id="captura_no" type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">NO</span></button></center>';
							
							dialog=instance.createDialog(instance.dialog_container,title,ifdata,420);
						  
							$("#caputra_si").click(function(){ 
								instance.close(dialog);
								if (oAction!=null){
									if (typeof oAction.doAdd=="function"){
										oAction.doAdd(info);
									}
								} 
							});
							$("#captura_no").click(function(){
								instance.close(dialog);
								//EVENTO SE DISPARA CUANDO SELECCIONAN QUE NO EN LA RESPUESTA
								if (oAction!=null){
									if (typeof oAction.doNotAdd=="function"){
										oAction.doNotAdd(data);
									}
								}
							});	 
								
						}else{  
							instance.close(dialog);
							if (oAction!=null){
								if (typeof oAction.onClientExist=="function"){
									oAction.onClientExist(data);
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
	setContrato : function(serie_contrato,no_contrato){
		this._serie_contrato=serie_contrato;
		this._no_contrato=no_contrato;
	},
        processEditPersona: function(idnit,obj){
		var instance=this;  
		var person_component= new ModuloPersonas('Datos Personales',this.dialog_container);	
		var person= new Persona(person_component);
		//PONGO EL MODULO DE PERSONA EN MODO EDITAR
		person.setView("edit"); 
		person.addListener("cancel_creation",function(){
			person_component.closeView();
		}); 		
		person_component.addModule(person);

		person.addListener("onEditPerson",function(data){
			alert(data.mensaje);
		}); 
		person.addListener("onViewCreate",function(){
			$(".dt_parentesco").show();
			if (obj!=null){ 
				if (obj.hideParentesco){
					$(".dt_parentesco").hide();
				} 
			}
		});	
		

 		var direccion= new Direccion(person_component);
		person_component.addModule(direccion);
	 
		direccion.addListener("doLoadViewComplete",function(rsobj){
			$(".dt_parentesco").show();
			if (obj!=null){
				if (obj.hideParentesco){
					$(".dt_parentesco").hide();
				} 
			}			
			var data='<br><center><button type="button" class="greenButton" id="bt_pros_select">Finalizar</button>';
			data=data+'<button type="button" class="redButton" id="bt_pros_cancelar">Cancelar</button></center><br>';
			$("#main_module").append(data);
			
			$("#bt_pros_cancelar").click(function(){
				person_component.closeView();
			});
			
			$("#bt_pros_select").click(function(){
				 
				var data={
                                            "id_documento":	$("#id_documento option:selected").val(),
                                            "tipo_documento": $("#id_documento option:selected").text(),
                                            "numero_documento":$("#numero_documento").val(),
                                            "primer_nombre": $("#primer_nombre").val(),
                                            "segundo_nombre":$("#segundo_nombre").val(),
                                            "primer_apellido":$("#primer_apellido").val(),
                                            "segundo_apellido":$("#segundo_apellido").val(),
                                            "fecha_nacimiento":$("#fecha_nacimiento").val(),
                                            "lugar_nacimiento":$("#lugar_nacimiento").val(), 
                                            "parentesco" : $("#parentesco option:selected").text(),
                                            "parentesco_id" :$("#parentesco option:selected").val(),
                                            'idnit':idnit

                                    };	
				 
				if (($("#parentesco option:selected").val()!="")){		
					person_component.closeView();	
					//instance.fire("onCreateBeneficiario",{"tipo":"mayor","data":data}); 	
					 
				}else{
					alert('Debe de seleccionar el parentesco!');	
				} 
                                
				
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
	 	person_component.loadMainView();
                
                
	},
	  
	processEditBeneficiario: function(idnit,obj,id_beneficiario){
		var instance=this;  
		var person_component= new ModuloPersonas('Datos Personales',this.dialog_container);	
		var person= new Persona(person_component);
		//PONGO EL MODULO DE PERSONA EN MODO EDITAR
		person.setView("edit"); 
		person_component.addModule(person);
		person.addListener("cancel_creation",function(){
			person_component.closeView();
		}); 
		
		person.addListener("onEditPerson",function(data){
			alert(data.mensaje);
		});
		person.addListener("onViewCreate",function(){
			$(".dt_parentesco").show();
			if (obj!=null){
				if (obj.hideParentesco){
					$(".dt_parentesco").hide();
				} 
			}
			var data='<br><center><button type="button" class="greenButton" id="bt_pros_select">Finalizar</button>';
			data=data+'<button type="button" class="redButton" id="bt_pros_cancelar">Cancelar</button></center><br>';
			$("#main_module").append(data);
			
			$("#bt_pros_cancelar").click(function(){
				person_component.closeView();
			});
			
			$("#bt_pros_select").click(function(){
				 
                                   
				var data={
                                        "id_nit":idnit,
                                        "id_documento":	$("#id_documento option:selected").val(),
                                        "tipo_documento": $("#id_documento option:selected").text(),
                                        "numero_documento":$("#numero_documento").val(),
                                        "primer_nombre": $("#primer_nombre").val(),
                                        "segundo_nombre":$("#segundo_nombre").val(),
                                        "primer_apellido":$("#primer_apellido").val(),
                                        "segundo_apellido":$("#segundo_apellido").val(),
                                        "fecha_nacimiento":$("#fecha_nacimiento").val(),
                                        "lugar_nacimiento":$("#lugar_nacimiento").val(), 
                                        "parentesco" : $("#parentesco option:selected").text(),
                                        "parentesco_id" :$("#parentesco option:selected").val(),
                                        "serie_contrato":instance._serie_contrato,
                                        "no_contrato":instance._no_contrato,
                                        "id_beneficiario":id_beneficiario
                                    };	
                                   
				if (($("#parentesco option:selected").val()!="")){		
					person_component.closeView();
                                      instance.fire("onCreateBeneficiario",{"tipo":"mayor","data":data});
					//obj.draw(data,data); 
				}else{
					alert('Debe de seleccionar el parentesco!');	
				} 
                                
			});
			

			/*VERIFICO SI EL PROSPECTO ES EL MISMO BENEFICIARIO Y ACTUALIZO EL PARENTESCO*/
			//if (instance._dt_prospecto.valid){
				//alert(instance._dt_prospecto.idnit+ " "+ idnit)
				/*if (instance._dt_prospecto.idnit==idnit){
					$("#parentesco").val($("#prospect_parentesco option:selected").val());
				}*/
			//}
			
			
		});		
		 
 		var direccion= new Direccion(person_component);
		person_component.addModule(direccion);
 		 
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
	 	person_component.loadMainView();
            
	},	
	processCreateBeneficiario : function(data,obj){
		var instance=this;
		var person_component= new ModuloPersonas('Datos Personales',this.dialog_container);
		
		var person= new Beneficiario(person_component);
		/*SELECCIONO LA VISTA QUE QUIERO QUE APAREZCA*/
		person.setView("create","MAYOR");
		/*********************************************/
		
		person.addListener("onViewCancel",function(){
			person_component.closeView();
		});
		
		/*Capturar cuando la vista ha sido creada*/
		person.addListener("onViewCreate",function(){
			$("#numero_documento").val(data.numero_documento);			  
			$('#id_documento option[value="' + data.tipo_documento + '"]').prop('selected',true);
		});
		
		/*Evento que captura cuando un cliente ha sido creado */
		person.addListener("onCreateBeneficiario",function(data){
			/*Cierro la vista para refrescarla con los datos del cliente agregado */
			person_component.closeView();
			/*********************************/
			obj.draw(data,data); 
		});
		
		person_component.addModule(person);
	  
	 	person_component.loadMainView();
	},
	
	draw_beneficiario : function(patern,bt_name,data){
		///alert(data.personal.primer_nombre)
		$("#"+patern+"_primer_nombre").html(data.primer_nombre);
		$("#"+patern+"_segundo_nombre").html(data.segundo_nombre);
		//$("#"+patern+"_tercer_nombre").html(data.tercer_nombre);
		$("#"+patern+"_primer_apellido").html(data.primer_apellido);
		$("#"+patern+"_segundo_apellido").html(data.segundo_apellido); 
		//$("#"+patern+"_apellido_casado").html(data.apellido_conyuge); 
		
		$("#"+patern+"_cedula").html(data.tipo_documento+" ("+data.numero_documento+")");	

		$("#"+patern+"_fecha_nacimiento").html(data.fecha_nacimiento);
		$("#"+patern+"_nacionalidad").html(data.lugar_nacimiento);
		$("#"+patern+"_parentesco").html(data.parentesco); 
  		$("#"+bt_name).html("Cambiar");
		//$("#"+bt_name).hide();
	},removeHandlers : function () {
                document.getElementById("lbeneficiario").removeEventListener("click", editBeneficiario);
        }  
        
});