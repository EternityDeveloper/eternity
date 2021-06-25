var Beneficiario= new Class({
	_dialog : null,
	_table_view_name:null,
	_title : "Datos personales",
	_tab_id : "tab",
	_type_view : "create",
	_person_id : null,
	_form_name : "client_form",
	_type_person : "MENOR", //SI ES MAYOR O MENOR DE EDADD
	_person_data : {primer_nombre:null,primer_apellido:null},
	_data : {},
	_serie_contrato: "",
	_no_contrato : "",
	_beneficiario : "",
	initialize : function(main_component){
		this.main_class="Beneficiario";
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
	setBeneficiario : function(bn){
		this._beneficiario=bn;
	},	
	setView : function(val,type_person){
		this._type_view=val;
		this._type_person=type_person;
	},
	setContrato : function(serie,no_contrato){
		this._serie_contrato=no_contrato;
		this._no_contrato=serie;
	},
	setPersonID : function(val){
		this._person_id=val;
	},
	setData : function(val){
		this._data=val;
	},	
	loadView : function(){
 		var instance = this;
 		 
		this.post("./?mod_personas/delegate",
			{
				"beneficiario_view":this._type_view,
				"id":this._person_id,
				"type_person":this._type_person,
				"data":this._data
			},function(data){
 				$("#"+instance.getTabID()).html(data);
				instance.validateForm();
				instance.submitForm();
				
				$("#procesar_cancel").click(function(){
					instance.fire("onViewCancel");
				});
 				 
				instance.fire("onViewCreate");
		 
				  
			},"text");	
	},
	
	submitForm : function(){
		var instance=this;
		$("#procesar").click(function(){
			if ($("#"+instance._form_name).valid()){
				 
				 var data={
							"id_documento":	$("#id_documento option:selected").val(),
							"tipo_documento":	$("#id_documento option:selected").text(),
					 		"numero_documento":$("#numero_documento").val(),
							"primer_nombre":$("#primer_nombre").val(),
							"segundo_nombre":$("#segundo_nombre").val(),
							"primer_apellido":$("#primer_apellido").val(),
							"segundo_apellido":$("#segundo_apellido").val(),
							"fecha_nacimiento":$("#fecha_nacimiento").val(),
							"lugar_nacimiento":$("#lugar_nacimiento").val(), 
							"parentesco" :$("#parentesco option:selected").text(),
							"parentesco_id" :$("#parentesco option:selected").val(),
							"beneficiario_view" : instance._type_view,
							'serie_contrato':instance._serie_contrato,
							'no_contrato':instance._no_contrato,
							'id_beneficiario':instance._beneficiario
						};	
				
				if (instance._type_person=="MAYOR"){
					instance.post("./?mod_client/client_add",$("#"+instance._form_name).serializeArray(),function(info){
							if (!info.error){ 
								instance.fire("onCreateBeneficiario",data);		
							}else{
								alert(info.mensaje + " error "+info.typeError);
							} 
						},"json");
				}else{
					if (instance._type_view=="edit"){

						data.update=1;
						instance.post("./?mod_personas/delegate",data,function(info){ 
							if (!info.error){ 
								alert(info.mensaje);
								instance.fire("onEditBeneficiario",data);		
							}else{
								alert(info.mensaje);
							} 
						},"json");
						
					}else{
						instance.fire("onCreateBeneficiario",data);	
					}
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