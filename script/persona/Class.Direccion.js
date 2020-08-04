var Direccion = new Class({
	_dialog : null,
	_table_view_name:null,
	_title : "Direccion",
	_tab_id : "tab",
	_type_view : "create",
	_person_id : null,
	_addressList : null,
	main_component :null,
	initialize : function(main_component){
		this.main_class="Persona";
		this.main_component=main_component;
		var instance = this;
		this.main_component.addListener("onloadView",function(){
			instance.main_component.putInTheView(instance);	
			instance.loadView();
			
		});
		
	},
	loadView : function(){
		//$("#"+instance.getTabID()).html('fdafdad');
		var instance = this;
	//	$('#'+this.getTabID()).showLoading({'addClass': 'loading-indicator-bars'});	
		 
		this.post("./?mod_personas/delegate",{
				"view_address":this._type_view,
				"id":this._person_id
			},function(data){
			//	$('#'+instance.getTabID()).hideLoading();	
				$("#"+instance.getTabID()).html(data);
				 
				instance._addressList = new PersonAddress('personal_address_',instance._person_id,'list_direcciones');
				instance._addressList.loadAddress();
				
				/*CAPTURA DEL CLICK PARA GREGAR NUEVA DIRECCION*/
				$("#address_add").click(function(){
					instance._addressList.addNewAddress();
				});

				var back_form_name=instance._addressList.formName;
				/* CAPTURA EL BOTON DE GUARDAR DE UNA DIRECCION */
				instance._addressList.addListener("AddressSave",function(form_data){
					instance.post("./?mod_client/client_edit",form_data,function(data){
						if (data.typeError=="100"){
							instance._addressList.formName=back_form_name;
							instance._addressList.loadAddress();
							alert(data.mensaje); 
						}else{
							alert(data.mensaje + " error "+data.typeError);
						} 
					},"json");
				});
				
				instance._addressList.addListener("AddressEditSave",function(form_data){
					instance.post("./?mod_client/client_edit",form_data,function(data){
						if (data.typeError=="100"){
							instance._addressList.formName=back_form_name;
							instance._addressList.loadAddress();
							alert('Registro actualizado!'); 
						}else{
							alert(data.mensaje + " error "+data.typeError);
						} 
					},"json");
				});
				
				/*EVENTO CUANDO SE EDITA UNA PERSONA*/
				instance._addressList.addListener("doEditAddress",function(info){
					instance._addressList.viewAddress(instance.main_component.dialog_container,info.contact_id,info.index);
				});
				/*CAPTURO EL EVENTO DE CAMBIO DE ESTATUS*/
				instance._addressList.addListener("AddressChangeEstatus",function(info){
					instance._addressList.loadAddress();
				});
				 
				instance.fire("doLoadViewComplete",instance);
				 
			},"text");	
	},
	getTitle : function(){
		return this._title;	
	},
	getTabID : function(){
		return this._tab_id+"-2";		
	},
	setPersonID : function(val){
		this._person_id=val;
	}
	
});