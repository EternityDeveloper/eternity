var ModuloPersonas = new Class({
	dialog_container : null,
	_dialog : null,
	_table_view_name:null,
	_module : [],
	_selectedTab : "2",
	_person_id : null,
	_title : null,
	initialize : function(title,dialog_container,page_loading){
		this.main_class="ModuloPersonas";
		this.dialog_container=dialog_container; 
		this._title=title;
	},
	loadMainView : function(obj){
		var instance=this; 
		this.post("./?mod_personas/delegate",{
				"main_view":'1' 
		},function(data){				 
			instance._dialog=instance.doDialog("doPersonModule",instance.dialog_container,data);  
			instance.addListener("onCloseWindow",function(){ 	
			}) ;				
			$("#main_module_title").html(instance._title);
			instance.fire("onloadView");
			
			$("#tabs_main").tabs(); 
			
			instance.__selectViewTab();
			
		});
	},
	doClose : function(){
		this.CloseDialog(this._dialog);
	},
	addModule : function(modulo){
		this._module.push(modulo);		 
	},
	
	getModule : function(modulo){
		for(var i=0;i<this._module.length;i++){
			//alert(this._module[i].main_class);
			if (this._module[i].main_class==modulo){
				return this._module[i];
				break;
			}
		} 
	},
	
	setPersonID : function(val){
		this._person_id=val;
		for(var i=0;i<this._module.length;i++){
			if (typeof this._module[i].setPersonID == 'function') {  
				this._module[i].setPersonID(val);
			}
		}
	},
	
	putInTheView : function(modulo){
		$("#ul_modulos").append('<li><a href="#'+modulo.getTabID()+'">'+modulo.getTitle()+'</a></li>');	 
		$("#tabs_main").append('<div id="'+modulo.getTabID()+'"></div>');
	},
	selected : function(id){
		this._selectedTab=id;
	},
	__selectViewTab : function(){ 
		var index = 0;
		var instance=this;
		$('#tabs_main ul li a').each(function(i) {
			if (("#"+instance._selectedTab)==$(this).attr("href")){
				index=i;
			}
		});
		$('#tabs_main').tabs("option",'active',index);
	},
	closeView : function(){
		//$("#"+this._dialog).dialog("destroy");
		//$("#"+this._dialog).remove();		 
		this.doClose();
	}
	
});