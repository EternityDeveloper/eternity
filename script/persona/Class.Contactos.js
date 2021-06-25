var Contactos = new Class({
	_dialog : null,
	_table_view_name:null,
	_title : "Contactos",
	_tab_id : "tab",
	_type_view : "create",
	_person_id : null,
	_objectList : null,
	main_component :null,
	initialize : function(main_component){
		this.main_class="Contactos";
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
				"view_contacto":this._type_view,
				"id":this._person_id
			},function(data){
		//		$('#'+instance.getTabID()).hideLoading();	
				$("#"+instance.getTabID()).html(data);
				 
				instance._objectList = new PersonContactos('personal_contact_'+instance.getRand(),instance._person_id,'contactos_field');
				instance._objectList.loadContact();
				
				/*CAPTURA DEL CLICK PARA GREGAR*/
				$("#add_contactos").click(function(){
					instance._objectList.addNewContact();
				});
				 
				
			},"text");	
	},
	getTitle : function(){
		return this._title;	
	},
	getTabID : function(){
		return this._tab_id+"-7";		
	},
	setPersonID : function(val){
		this._person_id=val;
	}
	
});