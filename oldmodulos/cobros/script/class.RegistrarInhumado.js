/* REGISTRAR PERSONA */
var RegistrarInhumado = new Class({
	dialog_container : null,  
	_rand : null, 
	_contrato : null,
	_dinh : null,
	_render_view : null,
	initialize : function(dialog_container,render_view){
		this.main_class="RegistrarInhumado";
		this.dialog_container=dialog_container; 
		this._render_view=render_view;
	}, 
	doView: function(contrato){
		var instance=this;   
		this._contrato=contrato;
		instance.post("./?mod_cobros/delegate&view_question_data",{},function(data){   
			instance.doDialog("doViewQuestion",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			}); 
			var documento=null;
			var tipo_documento=null;
			$("#id_documento").change(function(){
				if ($(this).val()!=''){
					$("#numero_documento_question").attr('disabled' , false);
					$("#consultar_doc").attr('disabled' , false);
					tipo_documento=$(this).val();
				}else{
					$("#numero_documento_question").attr('disabled' , true);
					$("#consultar_doc").attr('disabled' , true);
					tipo_documento=null;
				}
			});
			
			$("#id_documento").change(function(){
				documento=$("#numero_documento_question").val();
			});
			var valid_field=true;
			$("#consultar_doc").click(function(){
				var type_document=$('#id_documento option:selected').text();
				if (type_document.trim()=="CEDULA"){					
					valid_field=valida_cedula($("#numero_documento_question").val());
				}
				if (($("#numero_documento_question").val().length>=7) && (valid_field)){ 
					instance.post("./?mod_client/client_add&form_identifaction=1",
					{
						validarPersona:"1",
						"numero_documento":$("#numero_documento_question").val(),
						"tipo_documento":$("#id_documento").val()
					},function(data){	 
						if (data.existe==0){
							if (confirm("Este numero de identificacion no existe en nuestra base de datos. Desea Agregarlo?")){
								instance.CloseDialog("doViewQuestion");
								instance.doCreatePeron(data.id_nit);
							}
						}else{
							instance.CloseDialog("doViewQuestion");
							instance.doFillDetalle(data.id_nit);
						}
					},"json");
				}else{
					alert('Debe de ingresar un numero de documento valido');	
				}
			});
		  		
		});			
	},
	/*LLENA LOS DETALLES DE LA INHUMACION*/ 
	doFillDetalle : function(id_nit){
		var instance=this;  
		/*this._dinh= new DetalleInhumado(this._render_view);
		this._dinh.doView(id_nit,this._contrato);
		this._dinh.addListener("onSaveDetalle",function(data){ 
			instance.fire("onSaveDetalle",data);
		});*/
		instance.post("./?mod_cobros/delegate&view_detalle_inhumado",{
			"id_nit":id_nit,
			"contrato":this._contrato
		},function(data){ 
			if (data.valid){
				instance.fire("onSaveDetalle",data.data);
			}else{
				alert('Error datos invalidos!');
			}
		},"json");
	},
	doCreatePeron : function(id_nit){
		var instance=this;  
	 
		var person_component= new ModuloPersonas('Datos Personales',this.dialog_container);
		var person= new Persona(person_component);
		/*PONGO EL MODULO DE PERSONA EN MODO EDITAR*/
		person.setView("create");
		person.addListener("onCreatePerson",function(data){
			alert(data.mensaje);	
			person_component.closeView();
		}); 
		person.addListener("onViewCreate",function(data){
			 //$(".dt_parentesco").show();
			 $(".dt_telefono").show(); 
		}); 
		person.addListener("cancel_creation",function(data){ 
			person_component.closeView();
		}); 
				
		/*Le digo que cliente es el que sera editado*/
		person_component.setPersonID(id_nit);
		
	 	person_component.loadMainView();
	},
	doEditPeron : function(id_nit){
		var instance=this;  
		//pros_estatus
		var person_component= new ModuloPersonas('Datos Personales',this.dialog_container);
		var person= new Persona(person_component);
		/*PONGO EL MODULO DE PERSONA EN MODO EDITAR*/
		person.setView("create");
		person.addListener("onEditPerson",function(data){
			alert(data.mensaje);	
		});
		
		/**********************************/
 		person_component.addModule(person);
		
		var direccion= new Direccion(person_component);
		person_component.addModule(direccion);
  
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
		person_component.setPersonID(id_nit);
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