// JavaScript Document
var Empresa = new Class({
	divItemName	:  null, //Div contenedor
	formName 	: null, //Nombre del formulario contenedor
	client_id 	: null, //ID del cliente
	initialize : function(divItemName,formName,client_id){
		this.divItemName=divItemName;
		this.formName=formName;
		this.client_id=client_id;
	},
	loadEmpresa : function(){
//		alert(this.divItemName);
		//$('#'+this.divItemName).showLoading({'addClass': 'loading-indicator-bars'});	
		var divItemName=this.divItemName;
		var formName=this.formName;
		var client_id=this.client_id;
		
		var instance=this;
		
		this.post("./?mod_component/comp_empresa",{id:this.client_id,form:this.formName},function(data){
 
			//$('#'+divItemName).hideLoading();
			$('#'+divItemName).html(data);
			
			//$("#"+formName).validate();
			
			$("#nitempresa").change(function(e){
				$('#'+divItemName).showLoading({'addClass': 'loading-indicator-bars'});	
				 
				$.post("./?mod_personas/delegate",{
						"validate_empresa":1,
						"nit":$("#nitempresa").val()
					},function(data){
						$('#'+divItemName).hideLoading();	
						 if (data.valid){
						//	 alert(data.data.nombre_empresa);
							 $("#empresalabora").val(data.data.nombre_empresa);
						 }else{
							alert("RNC Invalido!");	 
						 }
						
					},"json");
			});
			$("#update_empresa").click(function(e){
				
				if ($("#"+formName).valid()){
					$('#'+divItemName).showLoading({'addClass': 'loading-indicator-bars'});	
					$.post("./?mod_client/client_edit",$("#"+formName).serializeArray(),function(data){
						$('#'+divItemName).hideLoading();
				 
							if (data.typeError=="104"){
								alert(data.mensaje);
								instance.fire("onCreateEmpresa",data);
								//window.location.reload();
							}else{
								alert(data.mensaje + " error "+data.typeError);
							} 
				
					},"json");
				}
			});
			
		
		});
		
	}
	
});