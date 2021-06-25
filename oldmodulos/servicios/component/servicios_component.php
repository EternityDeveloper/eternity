<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
 
if (!isset($_REQUEST['id'])){
	echo "Debe seleccionar un componente!";
	exit;
}

$id_serv=System::getInstance()->Request("id");
 
?> 
<h2>Listado de componentes</h2>
<form action="" method="post"  name="form_component"  id="form_component" onSubmit="return false;">

<div class="fsPage">
    <input type="text" name="search" id="search" size="50" />
    <input type="button" name="button" id="button" value="Buscar" />
    <input name="list_component_submit" type="hidden" id="list_component_submit" value="1">
    <input name="serv_codigo" type="hidden" id="serv_codigo" value="<?php echo $_REQUEST['id']; ?>">
</div>
<div id="tree_estruct" class="fsPage" style="clear:both;width:100%">

<ul>
<?php 

$SQL="SELECT *,
(SELECT COUNT(srv.id_componente) AS total FROM `componentes_servicio`  AS srv 
WHERE srv.serv_codigo='".$id_serv."' AND srv.id_componente=componentes.id_componente AND srv.sub_subcomponente='0')  AS total
FROM `componentes`    ";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
//	print_r($row);
	$id=System::getInstance()->getEncrypt()->encrypt($row['id_componente'],$protect->getSessionID());
?> 
         <li id="component" ids="<?php echo $id?>" rel="root">
         
             <input name="id_component[]" type="checkbox" id="id_component[]"  value="<?php echo $id?>" <?php if ($row['total']>0){?>disabled checked<?php }?>>
        
             <a href="#"><?php echo $row['descripcion_comp'];?></a>    
             
 
         <ul>
        <?php 
        
        $SQL="SELECT *,
(SELECT COUNT(srv.sub_subcomponente) AS total  FROM `componentes_servicio` AS srv  WHERE srv.serv_codigo='".$id_serv."' 
AND srv.id_componente=subcomponentes.id_componente AND 
srv.`sub_subcomponente`=`subcomponentes`.`sub_subcomponente`) AS total
 FROM `subcomponentes`
 WHERE
  subcomponentes.`id_componente`='".$row['id_componente']."' ";
  
        $rsx=mysql_query($SQL);
        while($rowx=mysql_fetch_assoc($rsx)){
        //	print_r($row);
            $id=System::getInstance()->getEncrypt()->encrypt(json_encode($rowx),$protect->getSessionID());
        ?> 
                 <li id="subcomponent" ids="<?php echo $id?>" rel="subcomponent">
                    <input name="id_subcomponent[]" type="checkbox" id="id_subcomponent[]" value="<?php echo $id?>"  <?php if ($rowx['total']>0){?>disabled checked<?php }?>> <a href="#"><?php echo $rowx['sub_descripcion'];?></a>    
                </li>
        <?php } ?>
        </ul>
 
             
              	
 		</li>
<?php } ?>
</ul>
</div>
<div style="display:block;clear:both">
  <center> 
  <input type="button" name="c_add_button" id="c_add_button" value="Adicionar componentes" />&nbsp;
  <input type="button" name="c_cancel_button" id="c_cancel_button" value="Cancelar" /></center>
</div>
</form>   
<div id="content_dialog"  ></div>
 
