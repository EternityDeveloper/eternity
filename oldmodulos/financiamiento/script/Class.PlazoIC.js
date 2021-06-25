/*  Plazo interes y comision*/
var PlazoIC = new Class({
	dialog_container : null,  
	_rand : null,
	_polygon :null,
	initialize : function(dialog_container){
		this.main_class="DashBoard";
		this.dialog_container=dialog_container; 
	},	  
	 
	doInit : function( ){ 
		var instance=this; 
		 		 
		$("#crear_pic").click(function(){ 
			instance.doCreateView();
		}); 
		
		$(".list_plc").click(function(){
			instance.doEditView($(this).attr("id"));
		}); 
		 
	},  
	getToken : function(){
		return this._rand;
	}, 
	doCreateView : function(){
		var instance=this;
		instance.post("./?mod_financiamiento/listar&pl_inters_comision",{
				"add_PIC":'1' 
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"CREAR INTERES Y COMISION",data,500);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 0]);  
			
			$("#bt_pro_f_cancel").click(function(){
				instance.close(dialog); 
			});
			
			$("#bt_pro_f_save").click(function(){ 
 				
				var plc=$("#form_plazo_i_c").serializeArray(); 
				plc.push({"name":"savePLC","value":1}); 
				
				instance.post("./?mod_financiamiento/listar&pl_inters_comision",plc,function(data){ 
					alert(data.mensaje);
					if (data.valid){
						instance.close(dialog); 
						window.location.reload();
					} 
				},"json");
																 
			});	
  	 
		});	
	} , 
 
	doEditView : function(id){
		var instance=this;
		instance.post("./?mod_financiamiento/listar&pl_inters_comision",{
				"edit_PIC":'1',
				"id": id
		},function(data){  
			var dialog=instance.createDialog(instance.dialog_container,"CREAR INTERES Y COMISION",data,500);
			instance._dialog=dialog;
			var n = $('#'+dialog);
			n.dialog('option', 'position', [(document.scrollLeft/550), 0]);  
			
			$("#bt_pro_f_cancel").click(function(){
				instance.close(dialog); 
			});
			
			$("#bt_pro_f_save").click(function(){ 
 				
				var plc=$("#form_plazo_i_c").serializeArray(); 
				plc.push({"name":"editPLC","value":1}); 
				plc.push({"name":"id","value":id}); 
				
				instance.post("./?mod_financiamiento/listar&pl_inters_comision",plc,function(data){ 
					alert(data.mensaje);
					if (data.valid){
						instance.close(dialog); 
					//	window.location.reload();
					} 
				},"json");
																 
			});	
  	 
		});	
	}
	
	 
});