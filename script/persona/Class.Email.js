var Email = new Class({
	_dialog : null,
	_table_view_name:null,
	_title : "Email",
	_tab_id : "tab",
	_type_view : "create",
	_person_id : null,
	_objectList : null,
	main_component :null,
	initialize : function(main_component){
		this.main_class="Email";
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
		//$('#'+this.getTabID()).showLoading({'addClass': 'loading-indicator-bars'});	
 
		this.post("./?mod_personas/delegate",{
				"view_email":this._type_view,
				"id":this._person_id
			},function(data){
				//$('#'+instance.getTabID()).hideLoading();	
				$("#"+instance.getTabID()).html(data);
				 
				instance._objectList = new PersonEmail('personal_email_',instance._person_id,'email_content');
				instance._objectList.loadEMail();
				
				/*CAPTURA DEL CLICK PARA GREGAR NUEVA DIRECCION*/
				$("#add_email").click(function(){
					instance._objectList.addNewEmail();
				});
				
				instance._objectList.addListener("onCreateEmail",function(data){
					this.loadEMail(); 
				});
				
			},"text");	
	},
	getTitle : function(){
		return this._title;	
	},
	getTabID : function(){
		return this._tab_id+"-5";		
	},
	setPersonID : function(val){
		this._person_id=val;
	}
	
});