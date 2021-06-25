var personEmpresa = new Class({
	_dialog : null,
	_table_view_name:null,
	_title : "Corporativo",
	_tab_id : "tab",
	_type_view : "create",
	_person_id : null,
	_instanceObj : null,
	main_component :null,
	initialize : function(main_component){
		this.main_class="personEmpresa";
		this.main_component=main_component;
		var instance = this;
		this.main_component.addListener("onloadView",function(){
			instance.main_component.putInTheView(instance);	
			instance.loadView();
		});
		
	},
	loadView : function(){
 
 		var instance = this;
		//$('#'+this.getTabID()).showLoading({'addClass': 'loading-indicator-bars'});	
 
		this.post("./?mod_personas/delegate",{
				"view_empresa":this._type_view,
				"id":this._person_id
			},function(data){
			//	$('#'+instance.getTabID()).hideLoading();	
				$("#"+instance.getTabID()).html(data);
				 
				instance._instanceObj = new Empresa('empresaList','form_empresa',instance._person_id);
				instance._instanceObj.loadEmpresa();
				instance._instanceObj.addListener("onCreateEmpresa",function(dt){
				});
 				
			},"text");	
	},
	getTitle : function(){
		return this._title;	
	},
	getTabID : function(){
		return this._tab_id+"-3";		
	},
	setPersonID : function(val){
		this._person_id=val;
	}
	
});