/*MODULO PARA ADICIONAR COMPONENTES*/
var AdicionarComponente = new Class({
	_id:null,
	_window: null,
	initialize : function(id){
		this._id=id;	
	},
	create : function(){
 		
		var instance=this;
		
		this.post("./?mod_servicios/component/servicios_component",{"id":this._id},function(data){
  			 /*CREO LA VENTANA ADICIONAR COMPONENTE*/
			 var dialog=createNewDialog("Adicionar componente",data,600);
			 instance._window=dialog;
			 
				/*CAPTURO EL EVENTO ON CLICK DEL BOTON PARA REALIZAR LA BUSQUEDA*/
				$("#button").click(function () {
				   $("#tree_estruct").jstree("search", $("#search").val());
				});
				
				$("#c_cancel_button").click(function () {
					$("#"+dialog).dialog("destroy");
					$("#"+dialog).remove();
				});
				
				$("#search").keypress(function (e) {
				   if (e.keyCode==13){
					$("#tree_estruct").jstree("search", $("#search").val());
				   }
				 
				});
				
				/*Este evento si dispara y serializa el formulario para ser enviado*/
				$("#c_add_button").click(function () {
				   instance.fire("SubmitComponenteAdicional",$("#form_component").serializeArray());
				});
			
			
				$("#tree_estruct").jstree({
					//"plugins" : ["themes","html_data","dnd","ui","hotkeys","search"],
					"plugins" : ["themes","html_data","dnd","ui","types","search"],
					"core" : { "initially_open" : [ "top_main","gerentes_divicion" ]},
					"types" : {
						"valid_children" : [ "default" ],
						"types" : {
							"root" : {
								"icon" : { 
									"image" : "./images/blockdevice.png" 
								},
								"valid_children" : [ "default" ],
								"max_depth" : 2,
								"hover_node" : true,
								"select_node" : true
							},					
							"subcomponent" : {
								"icon" : { 
									"image" : "./images/component.png" 
								},
								"valid_children" : [ "default" ],
								"max_depth" : 2,
								"hover_node" : true,
								"select_node" : true
							} 
						}				
					}
				});
	
		});			
	},
	closeView : function(){
		$("#"+this._window).dialog("destroy");
		$("#"+this._window).remove();	
	}
	
});