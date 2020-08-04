var Metas = new Class({
	dialog_container : null,
	_isCharge : false,
	_rand : 0,
	initialize : function(dialog_container){
		this.main_class="Metas";
		this.dialog_container=dialog_container; 
		this._rand=this.getRand(); 
		
		this.addListener("onSelect",function(code){
			alert(code);
		});
	}, 
	listOfGerentes : function(table_name){
		var instance=this;
		this._table_view_name=table_name;
		
		
		createTable(this._table_view_name,{
			"bFilter": true,
			"bInfo": false,
			"bPaginate": true,
			  "oLanguage": {
					"sLengthMenu": "Mostrar _MENU_ registros por pagina",
					"sZeroRecords": "No se ha encontrado - lo siento",
					"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
					"sInfoEmpty": "Mostrando 0 to 0 of 0 registros",
					"sInfoFiltered": "(filtrado de _MAX_ total registros)",
					"sSearch":"Buscar"
				}
			});

		var tr=$('tbody tr', $('#'+this._table_view_name).parent());
 
		tr.css('cursor', 'pointer');
		tr.click(function(e){ 
			instance.openMetasView($(this).attr("id")); 
		}); 
		tr.hover(function(){  
			$(this).addClass('hover_tr');  
		},function(){ 
			$(this).removeClass('hover_tr'); 
		}); 
		
		
	},
	openMetasView : function(id){
		var instance=this;
		this.post("./?mod_estructurac/list_view2&edit_metas=1&id="+id,null,function(data){ 
			var dialog=instance.createDialog(instance.dialog_container,"Metas",data,800);
			
			$("#bt_m_cancel").click(function(){
				instance.close(dialog);	
			});
			
			$("#bt_m_save").click(function(){ 
				instance.post("./?mod_estructurac/list_view2&edit_metas=1&submit=1",
					$("#metas_form").serializeArray(),
					function(data){
						alert(data);
					instance.close(dialog);	
				});
			});
		
		
		})
	}

});