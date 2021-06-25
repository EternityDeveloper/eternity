/* ACTAS DE COBRO*/
var cActas = new Class({
	dialog_container : null,  
	_rand : null,
	_polygon :null,
	initialize : function(dialog_container){
		this.main_class="cActas";
		this.dialog_container=dialog_container; 
	},	  

	doInitListadoActa : function( ){ 
		var instance=this;   
		$("#crear_acta").click(function(){
			instance.doViwCreateActa();
		});
		$(".listado_acta_css").click(function(){ 
			instance.doViewSeleccionaTipoActa($(this).attr("acta_id"));
		});		
		
		window['cerrar_acta']=function(id){
			instance.viewCloseActa(id);
		}	
		/**/
		window['imprimir_acta']=function(id){  
			window.open("?mod_cobros/delegate&cierre_acta_listado&download_ctr=1&inf="+id);	  
		};			 	
	},  
	doViewSeleccionaTipoActa : function(id){
		var instance=this; 
		instance.post("./?mod_cobros/delegate&cierre_acta&tipo_acta=1",{  
		},function(data){   
			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			})   
			$("#desistidos").click(function(){
				window.location.href="./?mod_cobros/delegate&acta&listar=1&tipo=D&id="+id;	
			}); 
			$("#anulados").click(function(){
				window.location.href="./?mod_cobros/delegate&acta&listar=1&tipo=A&id="+id;	
			}); 			
						
		});			
	},		
	viewCloseActa : function(id){
		var instance=this; 
		instance.post("./?mod_cobros/delegate&acta&doViewCloseActa=1",{ id:id},function(data){   
			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){});	
				 			
			$("#question_process").click(function(){
				if (!confirm("Esta seguro de que quiere cerrar el acta?")){
					return false;	
				}
				instance.post("./?mod_cobros/delegate&acta&procesar_cierre_acta",{ 
						"acta":id,
						"comentarios":$("#comentarios").val()
					},function(data){
						alert(data.mensaje)	
						if (data.valid){ 
							window.location.reload(); 
						} 
						
					},"json");		
			});
						
		});			
	},	
	doViewListadoActa : function(acta){
		var instance=this;
		$("#view_listado_acta").dataTable({
			"bFilter": true,
			"bInfo": false,
			"bPaginate": false,
			  "oLanguage": {
					"sLengthMenu": "Mostrar _MENU_ registros por pagina",
					"sZeroRecords": "No se ha encontrado - lo siento",
					"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
					"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
					"sInfoFiltered": "(filtrado de _MAX_ total registros)",
					"sSearch":"Buscar"
				} 	
			});	
		window['remover_item']=function(id){
			instance.viewRemoverItemFromActa(acta,id);
		}		
	},
	viewRemoverItemFromActa : function(acta,id){
		var instance=this; 
		instance.post("./?mod_cobros/delegate&acta&remover_from_acta=1",{ id:id},function(data){   
			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){});	
				 			
			$("#question_process").click(function(){
				if (!confirm("Esta seguro de que quiere remover este item de la lista?")){
					return false;	
				}
				instance.post("./?mod_cobros/delegate&acta&remover_del_acta",{
						"id":id,
						"acta":acta,
						"comentarios":$("#comentarios").val()
					},function(data){
						alert(data.mensaje)	
						if (data.valid){ 
							window.location.reload(); 
						} 
						
					},"json");		
			});
						
		});			
	},	
	doInitListdoContratos : function(tipo,id_acta){  
		var instance=this;   
		$(".cC_list").click(function(){    
			$("#crear_ac").hide();
			$(".cC_list:checked").each(function(index, element){   
				if ($(this).prop("checked")){
					$("#crear_ac").show();  
				}
			});			
			instance.doAddItemOnActa(id_acta,$(this).val(),$(this).prop("checked")==true?"add":"remove");  
		});			

		$("#listado_de_contratos").dataTable({
			"bFilter": true,
			"bInfo": false,
			"bPaginate": false,
			  "oLanguage": {
					"sLengthMenu": "Mostrar _MENU_ registros por pagina",
					"sZeroRecords": "No se ha encontrado - lo siento",
					"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
					"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
					"sInfoFiltered": "(filtrado de _MAX_ total registros)",
					"sSearch":"Buscar"
				},
				 "fnDrawCallback": function( oSettings ) {
						 
				}				
			});	
		
		$("#agregar_acta").click(function(){  
			if (!confirm("Desea agregar estos item al acta?")){
				return false;	
			} 
			instance.post("./?mod_cobros/delegate&acta&listar=1",{ 
					'tipo':tipo,
					'acta':id_acta,
					'addActa':1
			},function(data){   
				alert(data.mensaje)
				if (data.valid){
					window.location.reload();
				}
			},"json");	 
		});
		 
		//AGREGAR TODAS LA CUOTAS
	 	$("#select_all_ct").click(function(){
			if ($(this).prop("checked")){
 				$(".cC_list").each(function(index, element){  
					$(this).prop("disabled",false); 
					$(this).prop("checked", true); 
					$("#crear_ac").show();
				});	
			}else{
				$(".cC_list").each(function(index, element) {   
					$(this).prop("checked", false); 
				});		
			}
		}); 
		  
		$(".cierre_acta").find("img").click(function(event){
 			event.stopPropagation(); 
			instance.post("./?mod_cobros/delegate&cierre_acta&doViewActa",{ 
				'id_acta':$($(this).parent().parent()).attr('id') 
			},function(data){  
				instance.doDialog("myModal",instance.dialog_container,data);
			});
		});		
		 
		$("#procesar_acta").click(function(){ 
			instance.doCierreWizard($(this).val());
		}); 
	 	
	},
	/*AGREGAR UN ITEM A EL ACTA*/
	doAddItemOnActa : function(acta,items,accion){
		var instance=this; 
		instance.post("./?mod_cobros/delegate&acta",{ 
				'accion':accion,
				'acta':acta,
				'items':items,
				"putOnActaList":1
		},function(data){   
			if (!data.valid){
				alert(data.mensaje)
			}
		},"json");			
	},
	doViwCreateActa : function(){
		var instance=this; 
		instance.post("./?mod_cobros/delegate&acta&tipo_acta=1",{  
		},function(data){   
			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			})
			$("#p_fecha_desde").datepicker({
				changeMonth: true,
				changeYear: true,
				yearRange: '1900:2050',
				monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'], 
				monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'], 
				dateFormat: 'mm-yy',  
				dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'], 
				dayNamesMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'], 
				dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],  
			});
			 
			var periodo=null; 
			$("#p_fecha_desde").change(function(){
				periodo=$(this).val();  
			});
							
			$("#procesar_acta").click(function(){ 
				if (periodo==null){
					return false;
				}	
				instance.post("./?mod_cobros/delegate&cierre_acta&create_new_acta",{
						"periodo":periodo
					},function(data){
						alert(data.mensaje)	
						if (data.valid){ 
							window.location.reload();
							//window.location.href="./?mod_cobros/delegate&acta&listar=1";	
						} 
						
					},"json");		
			});
						
		});			
	},	
	doPutInActa : function(){
		var instance=this;
		instance.post("./?mod_cobros/delegate&cierre_acta_listado&createActa",
		{
			descripcion:$("#doc_descripcion").val(),
			"id_acta":acta,
			'contratos':contratos
		},function(data){
			alert(data.mensaje) 
		},"json");		
	},	
	doInitCierre : function( ){ 
		var instance=this;   
		var instance= this;
		
		$("#crear_acta").click(function(){
			var contratos=[];
			$(".cC_list").each(function(index, element){   
				if ($(this).prop("checked")){
					contratos.push($(this).val());
				}
			});	
			if (!confirm("Esta seguro de querer crear el acta?")){
				return false;	
			} 
			instance.post("./?mod_cobros/delegate&acta&listar=1",{ 
					'acta':contratos,
					'addActa':1
			},function(data){   
				alert(data.mensaje)
				if (data.valid){
					window.location.reload();
				}
			},"json");		
		});
		/*
		$(".cC_list").click(function(){
			if ($(this).prop("checked")){
				$("#crear_ac").show();
			}else{
				$("#crear_ac").hide();
			}
			if ($(".cC_list:checked").length==0){
				$("#crear_ac").hide();
				$("#select_all_ct").prop("checked",false); 
			}else{
				$("#crear_ac").show();
			}		
		});*/

		/*AGREGAR TODAS LA CUOTAS*/
		$("#select_all_ct").click(function(){
			if ($(this).prop("checked")){
 				$(".cC_list").each(function(index, element){  
					$(this).prop("disabled",false); 
					$(this).prop("checked", true); 
					$("#crear_ac").show();
				});	
			}else{
				var hide=false;
 				$(".cC_list").each(function(index, element) {  
					if (!$(this).prop("checked")){ 
						hide=true;
					}
					$(this).prop("checked", false); 
				});
				if (hide){
					$("#crear_ac").show();		
				}
			}
		}); 
	  
		$("#procesar_cierre_acta").click(function(){ 
			var contratos=[];
		//	$(".cC_list").each(function(index, element){  
				contratos.push($(this).val());
		//	}); 
			instance.doCierreWizard($(this).val(),contratos);
		});
		
		$(".commentary_add").click(function(){
			var ctt=($(this).parent().parent().attr("id"))
			instance.post("./?mod_cobros/delegate&cierre_acta&doViewComentary",{ 
				'id_acta':$($(this).parent().parent()).attr('id') 
			},function(data){  
				instance.doDialog("myModal",instance.dialog_container,data);
				 
				$("#save_comentary").click(function(){
					instance.post("./?mod_cobros/delegate&cierre_acta_listado&saveComentary",
					{
						descripcion:$("#doc_descripcion").val(),
						"contrato":ctt 
					},function(data){
						alert(data.mensaje) 
						window.location.reload();
					},"json");
				}); 
			});
		});
		 
	}, 
	doCierreWizard : function(acta,contratos){ 
 
		var instance=this; 
		instance.post("./?mod_cobros/delegate&cierre_acta_listado&wizard=1",{ 
			'id_acta':acta,
			'contratos':contratos
		},function(data){  
			$("#"+instance.dialog_container).html(data);
 			var wizard = $('#satellite-wizard').wizard({
				keyboard : false,
				contentHeight : 400,
				contentWidth : 700,
				backdrop: 'static',
				showCancel:true,
				buttons: {
					cancelText: "Cancelar",
					nextText: "Siguiente",
					backText: "Ir atras",
					submitText: "Procesar",
					submittingText: "Procesando..",
				}
			});  
			wizard.show();	
			var validate_upload=false;
			
			wizard.on("submit", function(wizard) { 
			
 				instance.post("./?mod_cobros/delegate&cierre_acta_listado&createActa",
				{
					descripcion:$("#doc_descripcion").val(),
					"id_acta":acta,
					'contratos':contratos
				},function(data){
					alert(data.mensaje) 
				},"json");
			 
				wizard.trigger("success");
				wizard.hideButtons();
				wizard._submitting = false;
				wizard.showSubmitCard("success");
				wizard.updateProgressBar(0);
				
				$("#close_w").click(function(){  
					wizard.cancelButton.click();
					window.location.reload();
				});
			 
			});
			window["validateUpload"]=function(){  
				var retValue = {}; 
				retValue.status = validate_upload; 
				if (!validate_upload){ alert('Debe de cargar el acta firmada para poder continuar!')}
				return retValue;	
			}
		  
			$("#descarga_acta").click(function(){  
 				window.open("?mod_cobros/delegate&cierre_acta_listado&download_ctr=1&inf="+$(this).val());	  
			});	
			
				var ul = $('#upload ul');
				
				$('#drop a').click(function(){
					$(this).parent().find('input').click();
				});
				
				$('#upload').fileupload({
					dropZone: $('#drop'), 
					add: function (e, data) {
					
						var tpl = $('<li class="working"><input type="text" value="0" data-width="48" data-height="48"'+
							' data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /><p></p><span></span></li>');
					
						tpl.find('p').text(data.files[0].name)
									 .append('<i>' + formatFileSize(data.files[0].size) + '</i>'); 
						data.context = tpl.appendTo(ul); 
						tpl.find('span').click(function(){ 
							if(tpl.hasClass('working')){
								jqXHR.abort();
							}
					
							tpl.fadeOut(function(){
								tpl.remove();
							});
					
						});
					
						var jqXHR = data.submit();
					}, 
					progress: function(e, data){
						var progress = parseInt(data.loaded / data.total * 100, 10);
						data.context.find('input').val(progress).change();			
						if(progress == 100){
							data.context.removeClass('working');
						}
					}, 
					fail:function(e, data){
							data.context.addClass('error');
						}, 
					done : function (e, data) { 
						validate_upload=true;
					}
				});
				 
				$(document).on('drop dragover', function (e) {
					e.preventDefault();
				});  
				
				function formatFileSize(bytes) {
					if (typeof bytes !== 'number') {
						return '';
					} 
					if (bytes >= 1000000000) {
						return (bytes / 1000000000).toFixed(2) + ' GB';
					} 
					if (bytes >= 1000000) {
						return (bytes / 1000000).toFixed(2) + ' MB';
					} 
					return (bytes / 1000).toFixed(2) + ' KB';
				}		 
			}
		); 	

	},
	doNextValidator: function(wizard){
		var instance=this;
		this.post("./?mod_cobros/delegate&cierre_acta_listado&validate_upload",{},function(data){
 			if (!data.valid){
				alert(data.mensaje)
			}else{
				wizard.nextButton.click()	
			}
		},"json"); 
	},
	doViewQuestion : function(){
		var instance=this; 
		instance.post("./?mod_cobros/delegate&acta&tipo_acta=1",{ 
				'contrato':this._contrato 
		},function(data){   
			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			})
			$("#p_fecha_desde").datepicker({
				changeMonth: true,
				changeYear: true,
				yearRange: '1900:2050',
				monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'], 
				monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'], 
				dateFormat: 'mm-yy',  
				dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'], 
				dayNamesMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'], 
				dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],  
			});
			
			var tipo_acta=null;
			var periodo=null;
			$("#tipo_acta").change(function(){
				tipo_acta=$(this).val(); 
			});	
			$("#p_fecha_desde").change(function(){
				periodo=$(this).val();  
			});
							
			$("#procesar_acta").click(function(){
				if (tipo_acta==null){
					return false;
				}
				if (periodo==null){
					return false;
				}	
				instance.post("./?mod_cobros/delegate&acta&savePeriodo",{
						"tipo":tipo_acta,
						"periodo":periodo
					},function(data){
						if (data.valid){
							window.location.href="./?mod_cobros/delegate&acta&listar=1";	
						}else{
							alert(data.mensaje)	
						}
						
					},"json");		
			});
						
		});			
	},
	
	doViewCierre: function(){
		var instance=this; 
		instance.post("./?mod_cobros/delegate&cierre_acta&tipo_acta_cierre=1",{ 
				'contrato':this._contrato 
		},function(data){   
			instance.doDialog("myModal",instance.dialog_container,data); 
			instance.addListener("onCloseWindow",function(){
				//alert('fsd');	
			});
			 
			var tipo_acta=null; 
			$("#tipo_acta").change(function(){
				tipo_acta=$(this).val(); 
			});	 
							
			$("#procesar_acta").click(function(){
				if (tipo_acta==null){
					return false;
				}
				window.location.href="./?mod_cobros/delegate&cierre_acta&tipo_acta="+tipo_acta;	
			});
						
		});			
	}
	
	
});