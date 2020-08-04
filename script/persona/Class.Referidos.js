var Referidos = new Class({
	_dialog : null,
	_table_view_name:null,
	_title : "Referidos",
	_tab_id : "tab",
	_type_view : "create",
	_person_id : null,
	_objectList : null,
	main_component :null,
	initialize : function(main_component){
		this.main_class="Referidos";
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
				"view_referidos":this._type_view,
				"id":this._person_id
			},function(data){
			//	$('#'+instance.getTabID()).hideLoading();	
				$("#"+instance.getTabID()).html(data);
				 
				instance._objectList = new PersonReferidos('personal_referidos_',instance._person_id,'referidos_fields');
				instance._objectList.loadRef();
				
				/*CAPTURA DEL CLICK PARA GREGAR NUEVA DIRECCION*/
				$("#add_referido").click(function(){
					instance._objectList.addNewRef();
				});
				 
				
			},"text");	
	},
	getTitle : function(){
		return this._title;	
	},
	getTabID : function(){
		return this._tab_id+"-8";		
	},
	setPersonID : function(val){
		this._person_id=val;
	}
	
});