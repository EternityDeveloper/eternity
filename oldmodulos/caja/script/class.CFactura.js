var CFactura = new Class({
	dialog_container : null,
	config: null,
	enc_id_nit :0,
	_view : null,
	initialize : function(dialog_container,enc_id_nit){
		this.main_class="CFactura";
		this.dialog_container=dialog_container;  
		this.enc_id_nit=enc_id_nit;

		var instance=this;
		this.addListener("onEndUpdateData",function(){
			instance.close(instance._dialog);	
			instance.viewAddDataFacturacion();
		});
		this.addListener("onCreateData",function(){
			instance.loadView(instance._view);
			instance.close(instance._dialog);	
			
		});

	},	
	loadView : function(view){
		var instance=this;  
		this._view=view;
		this.post("./?mod_caja/delegate",{"viewFactura":1,"type":'form',id:this.enc_id_nit},function(data){ 
			$("#"+view).html(data); 
			$("#factura_cliente").change(function(){
				var factura =$("#factura_cliente option:selected").attr('serialize');
				if (factura!=""){
					var obj=$.parseJSON($.base64.decode(factura));
					$("#fac_direccion").val(obj.direccion);
					$("#fac_nit_rnc").val(obj.NIT_FAC_CLI); 
					instance.fire("factura_rnc",obj.NIT_FAC_CLI);
				}
			});
			
			$("#agregar_fact").click(function(){
				instance.viewAddDataFacturacion();	
			});
		});
		
	},
	viewAddDataFacturacion : function(){
		var instance=this;  
		this.post("./?mod_caja/delegate&operacion",{"viewFactura":1,"type":'add_frm',id:this.enc_id_nit},function(data){ 
			//var dialog=instance.createDialog(instance.dialog_container,"Agregar dirección de facturación",data,500);
			var dialog=instance.doDialog("modal_view_facturacion_cliente",instance.dialog_container,data);
		  
			$("#bt_fact_cancel").click(function(){
				instance.CloseDialog("modal_view_facturacion_cliente");
			});
			
			$("#fact_v_id_documento").change(function(){
				var type_document=$('#fact_v_id_documento option:selected').text();
				if ($("#fact_v_id_documento").val().trim()!=""){
					$("#fact_v_numero_documento").prop('disabled', false);
				}else{
					$("#fact_v_numero_documento").prop('disabled', true);
				}
			});
			
			$("#fact_v_numero_documento").change(function(){
				var valid_field=true;
				var type_document=$('#fact_v_id_documento option:selected').text();
				if (type_document.trim()=="CEDULA"){					
					valid_field=valida_cedula($("#fact_v_numero_documento").val());
				}
				if (type_document==""){valid_field=false;}
 				
				if (($("#fact_v_numero_documento").val().length>=7) && (valid_field)){	
					instance.post("./?mod_contratos/listar",{validarPersona:"1","numero_documento":$("#fact_v_numero_documento").val(),"tipo_documento":$("#fact_v_id_documento").val()},function(data){	
						 
						if (!data.addnew){
							$("#fact_a_nombre_de").val(
													data.personal.primer_nombre+" "
													+data.personal.segundo_nombre+" "
													+data.personal.primer_apellido+" "
													+data.personal.segundo_apellido);
						}
					},"json");
				}else{
					alert('Numero de documento invalido');
					$("#fact_v_numero_documento").val('');
				}
			});
			
			$("#fac_agregar_direccion").click(function(){
				instance.doViewEditPerson(instance.enc_id_nit);
			});

			
			$("#bt_fac_add_process").click(function(){
				if ($("#dt_fact_form").valid()){ 
					instance.post("./?mod_caja/delegate&operacion&viewFactura=1",$("#dt_fact_form").serializeArray(),function(data){
						alert(data.mensaje);	
						if (!data.error){
							instance.fire("onCreateData");
							instance.CloseDialog("modal_view_facturacion_cliente");
						}
					},"json");
				}
			});
			
			instance.validateForm();
		});
	},
	doViewEditPerson : function(id_nit){ 
		var instance=this; 
		this._person_component= new ModuloPersonas('DATOS PERSONALES',this.dialog_container,this._dialog);
  
		var person= new Persona(this._person_component);
		person.addListener("onEditPerson",function(data){
			alert(data.mensaje);
		});
		/*PONGO EL MODULO DE PERSONA EN MODO EDITAR*/
		person.setView("edit");
		/**********************************/
 	
		this._person_component.addModule(person);
		
		var direccion= new Direccion(this._person_component);
		this._person_component.addModule(direccion);
		
		direccion.addListener("doLoadViewComplete",function(obj){
			instance.insertIntoView(obj)
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
		
		/*Le digo que cliente es el que sera editado*/
		this._person_component.setPersonID(id_nit);
				
		/*Y vuelvo a cargar la vista*/
		this._person_component.loadMainView();
		/**************************************/
   
		/************************************************/
		/*SETTEO LA VISTA PARA QUE CARGE DE PRIMERO LA VISTA DE DIRECCION*/
		this._person_component.selected(direccion.getTabID());
 
	},
	
	/*INSERTA LOS BOTONES DE CANCELAR Y SELECCIONAR EN UNA VISTA*/
	insertIntoView : function(obj){
		var instance=this;
		var data='<br><center><button type="button" class="greenButton" id="bt_pros_select">Finalizar</button>';
		data=data+'<button type="button" class="redButton" id="bt_pros_cancelar">Cancelar</button></center><br>';
		$("#main_module").append(data);
		
		$("#bt_pros_cancelar").click(function(){
			instance.fire("onEndUpdateData");
			instance._person_component.doClose();		
		});
		
		$("#bt_pros_select").click(function(){
			instance.fire("onEndUpdateData");
			instance._person_component.doClose();		
		});

	},
	
	validateForm: function(){
		var instance=this;
		$("#dt_fact_form").validate({
			rules: {
				vfac_direccion: {
					required: true
				},
				fact_v_id_documento: {
					required: true
				},
				fact_v_numero_documento: {
					required: true ,
					minlength: 7
				},
				fact_a_nombre_de: {
					required: true ,
					minlength: 7
				} 
			},
			messages : {
				vfac_direccion : {
					required: "Este campo es obligatorio"
				},
				fact_v_id_documento	 : {
					required: "Este campo es obligatorio"
				},
				fact_v_numero_documento	 : {
					required: "Este campo es obligatorio",
					minlength: "Debes de digitar un minimo de 7 caracteres"	
				},
				fact_a_nombre_de	 : {
					required: "Este campo es obligatorio",
					minlength: "Debes de digitar un minimo de 7 caracteres"	
				} 
				
			}
		
		});	
		$.validator.messages.required = "Campo obligatorio.";
	   
		
	},
	
	
});