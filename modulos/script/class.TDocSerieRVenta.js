var TDocSerieRVenta = new Class({
	dialog_container : null,
	config: null,
	avico_detalle: null,
	_token: null,
	initialize : function(dialog_container){
		this.main_class="TDocSerieRVenta";
		this.dialog_container=dialog_container;  
	},	
	getData : function(){
		return this.config;
	},
	setToken : function(token){
		this._token=token;
	},
	loadView : function(view,id){
		var instance=this;  
		this.post("./?mod_caja/delegate&operacion",{"tipoDocSerieRventaView":1,"type":'form','id':id,'token':this._token},function(data){ 
			$("#"+view).html(data); 
			
			$("#tipo_documento").change(function(){ 
				if (typeof($("#tipo_documento option:selected").attr('serialize'))=='undefined'){
					instance.fire("onChangeTDocumento",null);
					return ;	
				}
				$("#crt_reporte_venta").hide();	
				$("#no_documento").val('');
				$("#serie_documento").val('');	
				
				var dat=$.parseJSON($.base64.decode($("#tipo_documento option:selected").attr('serialize')));
				
				instance.fire("onChangeTDocumento",dat);
				
				instance.post("./?mod_caja/delegate&operacion",{"getSerieDoc":1,"doc":$(this).val()},function(jobj){ 
					instance.config=jobj;
					
					if (jobj.document.REP_VENTA=="S"){
						$("#crt_reporte_venta").show();	
					}  
					//alert($.base64.encode(JSON.stringify(data.correlativo)));
					if (jobj.correlativo.length>1){
						instance.post("./?mod_caja/delegate&operacion",{"tipoDocSerieRventaView":1,"type":'serie_doc_list',"doc":jobj.document.TIPO_DOC},function(data){ 
							$("#serie_doc").html(data);
							$("#serie_documento").change(function(){
								var obj=$.parseJSON($.base64.decode($("#serie_documento option:selected").attr('serialize')));
								//alert(parseInt(obj.CORRELATIVO)+1);
								$("#no_documento").val(parseInt(obj.CORRELATIVO)+1);
							});
						});
					}else{
						instance.post("./?mod_caja/delegate&operacion",{"tipoDocSerieRventaView":1,"type":'serie_doc' },function(data){ 
							$("#serie_doc").html(data);
							if (typeof(jobj.correlativo[0].SERIE) != "undefined"){
								$("#serie_documento").val(jobj.correlativo[0].SERIE);	
								$("#no_documento").val(parseInt(jobj.correlativo[0].CORRELATIVO)+1);
							} 
						});
					}
					
				},"json"); 
			});
		});
	},
	/*
	* !-_-!
	* 
	*/
	loadViewCustom : function(view,id){
		var instance=this;  
		this.post("./?mod_caja/delegate&operacion",{"tipoDocSerieRventaView":1,"type":'form','id':id,'token':this._token},function(data){ 
			$("#"+view).html(data.html);  
			/*
				Envia los datos de un aviso de cobro
			*/
			instance.fire("AvisoCobroMontos",data.info)
			instance.avico_detalle=data.info;
			
			$("#tipo_documento").change(function(){ 
				if (typeof($("#tipo_documento option:selected").attr('serialize'))=='undefined'){
					instance.fire("onChangeTDocumento",null);
					return ;	
				}
				$("#crt_reporte_venta").hide();	
				$("#no_documento").val('');
				$("#serie_documento").val('');	
				
				var dat=$.parseJSON($.base64.decode($("#tipo_documento option:selected").attr('serialize')));
				
				instance.fire("onChangeTDocumento",dat);
				
				instance.post("./?mod_caja/delegate&operacion",{"getSerieDoc":1,"doc":$(this).val()},
					function(jobj){ 
					instance.config=jobj;
					
					if (jobj.document.REP_VENTA=="S"){
						$("#crt_reporte_venta").show();	
					}  
					//alert($.base64.encode(JSON.stringify(data.correlativo)));
					if (jobj.correlativo.length>1){
						instance.post("./?mod_caja/delegate&operacion",{"tipoDocSerieRventaView":1,"type":'serie_doc_list',"doc":jobj.document.TIPO_DOC},function(data){ 
							$("#serie_doc").html(data);
							$("#serie_documento").change(function(){
								var obj=$.parseJSON($.base64.decode($("#serie_documento option:selected").attr('serialize')));
								//alert(parseInt(obj.CORRELATIVO)+1);
								$("#no_documento").val(parseInt(obj.CORRELATIVO)+1);
							});
						});
					}else{
						instance.post("./?mod_caja/delegate&operacion",{"tipoDocSerieRventaView":1,"type":'serie_doc' },function(data){ 
							$("#serie_doc").html(data);
							if (typeof(jobj.correlativo[0].SERIE) != "undefined"){
								$("#serie_documento").val(jobj.correlativo[0].SERIE);	
								$("#no_documento").val(parseInt(jobj.correlativo[0].CORRELATIVO)+1);
							} 
						});
					}
					
				},"json"); 
			});
		},"json");
	}
	
});