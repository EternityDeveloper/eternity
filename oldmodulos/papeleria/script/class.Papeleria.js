var Papeleria = new Class({
	dialog_container : null, 
	_view : null,
	_minlote:1,
	_maxlote:1,
	initialize : function(dialog_container){
		this.main_class="Papeleria";
		this.dialog_container=dialog_container;   
	},	
	doViewRecibo : function(pending_cierre,fecha){
		var instance=this;
		$("#crear_lote").click(function(){
 			instance.doViewQuestion(pending_cierre,fecha); 
		}); 
		$(".lote_asing").click(function(){
 			instance.doAsignarLoteToDistribuidor($(this).val()); 
		}); 
		$(".lote_view").click(function(){
 			instance.doViewDetalleLote($(this).val()); 
		}); 
		$(".lote_reasing").click(function(){
 			instance.doReAsignarLoteToDistribuidor($(this).val()); 
		});   
	},
	doReAsignarLoteToDistribuidor : function(lote){
		var instance=this; 
		instance.post("./?mod_papeleria/delegate&viewAsignarAsesor",{"lote":lote},function(data){ 
 			instance.doDialog("view_modal_lote",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			});
 			var asesor=null;
  			$('#txt_oficial').select2({
			  multiple: false,
			  minimumInputLength: 4,
			  query: function (query){ 
				  $.post("./?mod_cobros/delegate&zonas",{"motorizados":'1',"sSearch":query.term},function(data){ 
					 query.callback(data);
				   },"json");   
			  }
			});		 
			$("#txt_oficial").on("change", 
				function(e) { 
					asesor=e.val; 
			});
			
			$("#cantidad").change(function(){
				var lt=$(this).val() ;
				if (lt<=5){
					instance.post("./?mod_papeleria/delegate&doCalcularAsignLote",{
						"lote":lote,
						"cantidad":$(this).val()
					},function(data){ 	
						if (data.valid){
							$("#pap_desde").val(data.desde);
							$("#pap_hasta").val(data.hasta);				 
						}else{
							$("#cantidad").val('');
							alert(data.mensaje)	
						}
					},"json");
				}else{
					$(this).val('');
					alert('No puede asignar mas de 5!');	
				}
			});
							
			$("#procesar_asg_dist").click(function(){ 
				if (asesor==null){
					alert('Debe de seleccionar un Asesor!');
					return false;	
				}
				var hasta=$("#pap_hasta").val();
				var desde =$("#pap_desde").val();
 				if (instance._maxlote>hasta){
					alert('Debe de ingresar un numero mayor!');
					return false;
				}   
				instance.post("./?mod_papeleria/delegate&doAsignarToAsesor",{
						"lote":lote,
						"cantidad":$("#cantidad").val() ,
						"asesor":asesor
					},function(data){
						alert(data.mensaje)	
						if (data.valid){
							//window.open("./?mod_papeleria/delegate&print_oferta&id="+data.recibos);
							instance.doPrintLote(data.recibos);
							instance.CloseDialog('view_modal_lote');
						//	window.location.reload()
						} 						
					},"json");		
			}); 			
		});			
	},
	doViewDetalleLote : function(lote){
		var instance=this; 
		instance.post("./?mod_papeleria/delegate&viewDetalleLote",{"lote":lote},function(data){ 
 			instance.doDialog("view_modal_lote_detalle",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			}); 
		});			
	},
	doPrintLote : function(id){
		var instance=this;  
		instance.post("./?mod_papeleria/delegate&print_dialog",{id:id},function(data){ 
 			instance.doDialog("DetalleImprimir",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//window.location.reload();
			});
			
			$('#detalle_imprimir').load(function(){
				$('#detalle_imprimir')[0].contentWindow.print();
				setTimeout(function(){
					instance.CloseDialog("DetalleImprimir");
				},5000);
			});	
			
		});
	},	
	doViewQuestion : function(){
		var instance=this; 
		instance.post("./?mod_papeleria/delegate&viewLoteAdd",{},function(data){ 
 			instance.doDialog("view_modal_lote",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			});
			
		 	var pap_documento=null;
			$("#pap_documento").change(function(){ 
				pap_documento=$(this).val() ;
			}); 		
					
		 	var tipo_serv_prod=null;
			$("#tipo_serv_prod").change(function(){
				tipo_serv_prod=$(this).val();  
				instance.post("./?mod_papeleria/delegate&doGetLote",{
					"tipo_lote":tipo_serv_prod,
					"documento":pap_documento
				},function(data){ 
					instance._minlote=data.desde;
					instance._maxlote=data.hasta;	
					$("#pap_desde").val(data.desde);
					$("#pap_hasta").val(data.hasta);				 
				},"json");				
			});			
							
			$("#procesar_acta").click(function(){ 
				var hasta=$("#pap_hasta").val();
				var desde =$("#pap_desde").val();
				if (hasta<instance._maxlote){
					alert('Debe de ingresar un numero mayor!');
					return false;
				}
				 	  
				instance.post("./?mod_papeleria/delegate&createLote",{
						"documento":pap_documento,
						"tipo_serv_prod":tipo_serv_prod,
						"pap_desde":desde, 
						"pap_hasta":hasta 
					},function(data){
						alert(data.mensaje)	
						if (data.valid){
							window.location.reload()
						} 						
					},"json");		
			});
			
						
		});			
	},
	doAsignarLoteToDistribuidor : function(lote){
		var instance=this;  
		instance.post("./?mod_papeleria/delegate&viewAsignarLote",{"lote":lote},function(data){ 
 			instance.doDialog("view_modal_lote",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			});
 			var oficial=null;
  			$('#txt_oficial').select2({
			  multiple: false,
			  minimumInputLength: 4,
			  query: function (query){ 
				  $.post("./?mod_cobros/delegate&zonas",{"motorizados":'1',"sSearch":query.term},function(data){ 
					 query.callback(data);
				   },"json");   
			  }
			});		 
			$("#txt_oficial").on("change", 
				function(e) { 
					oficial=e.val; 
			});
			
			$("#cantidad").change(function(){
				var lt=$(this).val() ;
				instance.post("./?mod_papeleria/delegate&doCalcularAsignLote",{
					"lote":lote,
					"cantidad":$(this).val()
				},function(data){ 	
					if (data.valid){
						$("#pap_desde").val(data.desde);
						$("#pap_hasta").val(data.hasta);				 
					}else{
						$("#cantidad").val('');
						alert(data.mensaje)	
					}				 
				},"json");
			});
				
			$("#procesar_asg_dist").click(function(){ 
				if (oficial==null){
					alert('Debe de seleccionar un Oficial!');
					return false;	
				}
				var hasta=$("#pap_hasta").val();
				var desde =$("#pap_desde").val();
				if (instance._maxlote>hasta){
					alert('Debe de ingresar un numero mayor!');
					return false;
				}   
				instance.post("./?mod_papeleria/delegate&doAsignLote",{
						"lote":lote,
						"cantidad":$("#cantidad").val() ,
						"oficial":oficial
					},function(data){
						alert(data.mensaje)	
						if (data.valid){
							window.location.reload()
						} 						
					},"json");		
			}); 			
		});			
	},	
	doListadoDocumento : function(pending_cierre,fecha){
		var instance=this;
		$("#crear_").click(function(){
 			instance.doDocumentCreate(); 
		});  
		$(".doct_view").click(function(){
 			instance.doViewDocumentEdit($(this).val()); 
		});    
	},
	doDocumentCreate : function(){
		var instance=this; 
		instance.post("./?mod_papeleria/delegate&viewAddDocument",{},function(data){ 
 			instance.doDialog("view_modal_lote",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			});
		 	var tipo_moneda=null;
			$("#tipo_moneda").change(function(){
				var lt=$(this).val() ;
				tipo_moneda=lt;
			});
			var aplica_para=null; 
			$("#aplica_para").change(function(){
				var lt=$(this).val() ;
				aplica_para=lt;
			}); 					
							
			$("#procesar_acta").click(function(){  
				var nombre=$("#pap_nombre").val();
				if (tipo_moneda==null){
					alert('Debe de seleccionar el tipo de moneda');
					return false;
				}
				if (aplica_para==null){
					alert('Debe de seleccionar para que tipo de producto/servicio aplica');
					return false;
				}		
				if (nombre==""){
					alert('Debe de ingresar un nombre para el documento');
					return false;
				}				
				 	  
				instance.post("./?mod_papeleria/delegate&createDocument",{
						"aplica_para":aplica_para,
						"tipo_moneda":tipo_moneda, 
						"nombre":nombre 
					},function(data){
						alert(data.mensaje)	
						if (data.valid){
							window.location.reload()
						} 						
					},"json");		
			});
			
						
		});			
	}, 
	doViewDocumentEdit : function(id){
		var instance=this; 
		instance.post("./?mod_papeleria/delegate&viewEditDocument",{"id":id},function(data){ 
 			instance.doDialog("view_modal_edit_document",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			});
  
 			var tiny=new TINY.editor.edit('editor',{
									id:'pap_input',
									width:900,
									height:300,
									cssclass:'te',
									controlclass:'tecontrol',
									rowclass:'teheader',
									dividerclass:'tedivider',
									controls:['bold','italic','underline','strikethrough','|','subscript','superscript','|',
											  'orderedlist','unorderedlist','|','outdent','indent','|','leftalign',
											  'centeralign','rightalign','blockjustify','|','unformat','|','undo','redo','n',
											  'font','size','style','|','image','hr','link','unlink','|','cut','copy','paste','print'],
									footer:false,
									fonts:['Verdana','Arial','Georgia','Trebuchet MS'],
									xhtml:false,
									cssfile:'style.css',
									bodyid:'editor',
									footerclass:'tefooter',
									toggle:{text:'source',activetext:'wysiwyg',cssclass:'toggle'},
									resize:{cssclass:'resize'}
								});	
		  
			$("#pap_edit_document").click(function(){ 
				editor.post();
				var input=editor.t.value; 
				if (input==""){
					alert('Debe de ingresar algun texto en la descripcion!')	
				}
				var tipo_moneda=$("#tipo_moneda").val();
				if (tipo_moneda==null){
					alert('Debe de seleccionar el tipo de moneda');
					return false;
				}
				var aplica_para=$("#aplica_para").val();
				if (aplica_para==null){
					alert('Debe de seleccionar para que tipo de producto/servicio aplica');
					return false;
				}					
				   
				$.post("./?mod_papeleria/delegate&doEditDocument",{
						"id":id,
						"aplica_para":aplica_para,
						"tipo_moneda":tipo_moneda, 						
						"text":input 
					},function(data){
						alert(data.mensaje)	
						if (data.valid){
							window.location.reload()
						} 						
					},"json");		
			});
			
			$("#pap_print_doct").click(function(){
				window.open("?mod_papeleria/delegate&doViewPrintDocto");
			});
			
						
		});			
	}
	
	
});