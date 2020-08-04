var Persona = new Class({
	_dialog : null,
	_table_view_name:null,
	_title : "Datos personales",
	_tab_id : "tab",
	_type_view : "create",
	_person_id : null,
	_form_name : "client_form",
	_person_data : {primer_nombre:null,primer_apellido:null},
	_submit : {
		valid:false,
		doSubmit : function(){}
	},
	initialize : function(main_component){
		this.main_class="Persona";
		this.main_component=main_component;
		var instance = this;
		this.main_component.addListener("onloadView",function(){
			instance.main_component.putInTheView(instance);
			instance.loadView();
			//instance.main_component.removeListener("onloadView");
		});
	},
	setChangeSubmit : function(sub_mit){
		this._submit=sub_mit;
	},
	setView : function(val){
		this._type_view=val;
	},
	setPersonID : function(val){
		this._person_id=val;
	},
	showParentesco : function(){
		$(".dt_parentesco").show();
	},
	loadView : function(){
 		var instance = this;
		//$('#'+this.getTabID()).showLoading({'addClass': 'loading-indicator-bars'});	
		 
		this.post("./?mod_personas/delegate",
			{
				"view_person":this._type_view,
				"id":this._person_id
			},function(data){
			//	$('#'+instance.getTabID()).hideLoading();	
				$("#"+instance.getTabID()).html(data);
				instance.validateForm();
				instance.submitForm();
 				
				$("#cancel_creation").click(function(){
					instance.fire("cancel_creation");	
				});
				
				instance.fire("onViewCreate");
				
				instance.post("./?mod_personas/delegate",{
						"getPersonData":"1",
						"id":instance._person_id
					},
				  function(rs){ 
					  if (!rs.error){
						instance._person_data={
							"direccion":rs.data.primer_nombre,
							"telefono":rs.data.primer_nombre,
							'celular':rs.data.primer_nombre,
							"primer_nombre":rs.data.primer_nombre,
							"primer_apellido":rs.data.primer_apellido,
							"person_id" : instance._person_id
						};
						
					  }
				},"json");		
				  
			},"text");	
	},
	
	submitForm : function(){
		var instance=this;
		var documento=null;
 		$("#id_documento").change(function(){
			if ($(this).val()!=''){ 
				tipo_documento=$(this).val();
			}else{ 
				tipo_documento=null;
			}
		});		
		
		$("#factura_fiscal").click(function(){ 
			instance.post("./?mod_client/client_edit",{ 
					'doFiscal':true,
					'id':$("#id").val(), 
					'factura_fiscal':$("#factura_fiscal").is(':checked')				 
			},function(data){ 
				alert(data.mensaje); 
			},"json");			
		});
		
		$("#procesar").click(function(){
			
			if (!instance._submit.valid){
				
				if ($("#client_form").valid()){
					var url=null;
					if (instance._type_view=="create"){
						url="./?mod_client/client_add";
					}else if (instance._type_view=="edit"){
						url="./?mod_client/client_edit";
					} 
				
					var type_document=$('#id_documento option:selected').text();
					var valid_field=false;
					
					if (type_document.trim()=="CEDULA"){	 	
						valid_field=valida_cedula($("#numero_documento").val()); 
							
					}else{
						valid_field=true;	
					}				
					
					if (valid_field){ 
						$.post(url,$("#"+instance._form_name).serializeArray(),function(data){ 
							if (!data.error){
								if (instance._type_view=="create"){
									instance.fire("onCreatePerson",data);
								}else if (instance._type_view=="edit"){
									instance.fire("onEditPerson",data);	
								}
							}else{
								alert(data.mensaje + " error "+data.typeError);
							} 
						},"json");
					}
				}	
			}else{
				/*Cambiar el comportamiento del submit*/
				if (typeof instance._submit.doSubmit == 'function'){
					instance._submit.doSubmit();
				}
			}
		});
	},
	
	getTitle : function(){
		return this._title;	
	},
	getTabID : function(){
		return this._tab_id+"-1";	
	},
	getFormData : function(){
		return this._person_data;
	},
	validateForm : function(){
		$("#client_form").validate({
			rules: {
				numero_documento: {
					required: true,
					minlength: 7
				}
			},
			messages : {
				numero_documento : {
					required: "Este campo es obligatorio",
					minlength: "Debes de digitar un minimo de 7 caracteres"	
				}	
				
			}
		
		});	
		$.validator.messages.required = "Campo obligatorio.";

		$("#fecha_nacimiento").datepicker({
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
		/*VALIDO EL NUMERO DE INDENTIFICACION*/
		$("#numero_documento").change(function(){
			var type_document=$('#id_documento option:selected').text();
			var valid_field=true;
			if (type_document.trim()=="CEDULA"){
				valid_field=valida_cedula($("#numero_documento").val());
			}
			if (($("#numero_documento").val().length>=7) && (valid_field)){
				$.post("./?mod_client/client_add",{form_identifaction:"1","numero_documento":$("#numero_documento").val(),"id_documento":$("#id_documento").val()},function(data){	
				 
					if (data.existe==1){
						alert(data.mensaje);	
						$("#numero_documento").val("");
						$("#numero_documento").removeClass('validdate');
						$("#numero_documento").addClass('invalidate');
					}else{
						$("#numero_documento").removeClass('invalidate');
						$("#numero_documento").addClass('validdate');
					}
				},"json");	
			}else{
				$("#numero_documento").val('');
				alert('Digite un numero de identificacion valido!');	
			}
			
		});
	}
	
});