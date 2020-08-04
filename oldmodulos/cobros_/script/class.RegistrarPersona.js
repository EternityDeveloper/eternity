/* REGISTRAR PERSONA */
var RegistrarPersona = new Class({
	dialog_container : null,  
	_rand : null, 
	initialize : function(dialog_container){
		this.main_class="GOrdenInhumacion";
		this.dialog_container=dialog_container; 
	}, 
	doView: function(){
		var instance=this;   
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
			
			$("#consultar_doc").click(function(){
				var type_document=$('#id_documento option:selected').text();
				if (type_document.trim()=="CEDULA"){					
					valid_field=valida_cedula($("#numero_documento_question").val());
				}
				if (($("#numero_documento_question").val().length>=7) && (valid_field)){ 
					instance.post("./?mod_contratos/listar",
					{
						validarPersona:"1",
						"numero_documento":$("#numero_documento_question").val(),
						"tipo_documento":$("#id_documento").val()
					},function(data){	 
						if (data.addnew){
							if (confirm("Este numero de identificacion no existe en nuestra base de datos. Desea Agregarlo?")){
								instance.CloseDialog("doViewQuestion");
								instance.doCreatePeron(data.personal.id_nit);
							}
						}else{
							
						}
					},"json");
				}else{
					alert('Debe de ingresar un numero de documento valido');	
				}
			});
		  		
		});			
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