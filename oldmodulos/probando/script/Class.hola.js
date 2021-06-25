// JavaScript Document
var Hola = new Class({ 
	dialog_container : null,
	tb_item_list : null, 
	initialize : function(dialog_container){
		this.main_class="Hola";
		this.dialog_container=dialog_container; 
		
	},
	loadview : function(){
		var instance=this;
		$("#button2").click(function(){ 
			instance.post(".?mod_probando/hola", { loadview:"1"  }, function(data){
				instance.createDialog(instance.dialog_container,"Cargue la vista",data,500);  
				instance.fire("modulo_cargado",data);
			}); 	
		});		
	},
	procesar : function(){
		var instance=this;
		$("#button").click(function(){ 
			instance.post("./?mod_probando/hola",
					{
					 paramt:"envio_dato",
					 nombre:$("#nombre1").val(),
					 nombre2:$("#nombre2").val()},			
					function(data){
				if (data.valid=="1"){
					alert(data.mesnaje);	
				}
			},"json"); 	
		});
	}
	
	
})