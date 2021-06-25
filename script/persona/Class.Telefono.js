var Telefono = new Class({
	_dialog : null,
	_table_view_name:null,
	_title : "Telefono",
	_tab_id : "tab",
	_type_view : "create",
	_person_id : null,
	_phoneList : null,
	main_component :null,
	initialize : function(main_component){
		this.main_class="Telefono";
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
				"view_telefono":this._type_view,
				"id":this._person_id
			},function(data){
			//	$('#'+instance.getTabID()).hideLoading();	
				$("#"+instance.getTabID()).html(data);
				 
				instance._phoneList = new PersonPhone('personal_phone_',instance._person_id,'phoneFields');
				instance._phoneList.loadPhone();
				
				/*CAPTURA DEL CLICK PARA GREGAR NUEVA DIRECCION*/
				$("#addphone").click(function(){
					instance._phoneList.addNewPhone();
				});
				
				instance._phoneList.addListener("onCreatePhone",function(data){
					this.loadPhone(); 
				});
				
			},"text");	
	},
	getTitle : function(){
		return this._title;	
	},
	getTabID : function(){
		return this._tab_id+"-4";		
	},
	setPersonID : function(val){
		this._person_id=val;
	}
	
});