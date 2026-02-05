<?php
	header('Content-Type: text/html; charset=utf-8');
	header("Expires: Fri, 14 Mar 1980 20:53:00 GMT"); //la pagina expira en fecha pasada
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT"); //ultima actualizacion ahora cuando la cargamos
	header("Cache-Control: no-cache, must-revalidate"); //No guardar en cache
	header('Expires: 0');
	header("Pragma: no-cache"); //No guardar en cache
	
	session_start();
	require_once "../config/xo_ubicador.php";
	require_once CONF."xo_settings.php";
	require_once PHP."XMLoperator.php";
	require_once PHP."xo_functions.php";
?>
<!DOCTYPE html> 
<html>
	<head> 
		<title><?=$APP_NAME?></title>
		<meta charset="utf-8" />
		<meta name="title" content="<?=$APP_NAME?>" />
		<meta property="og:title" content="<?=$APP_NAME?>" />
		
		<link rel="stylesheet" href="<?=CSS."xo_basestyle.css"?>" type="text/css" media="screen, projection, print" />	
		<script type="text/javascript" src="<?=JS."jquery-1.9.1.min.js"?>"></script>
	
		<style type="text/css">
			<?php
			for ($i=0;$i<10;$i++) {
				echo ".brank_".$i." {";
				echo " margin-left:".($i*40)."px;";
				echo "}";
			}
			?>
			.getdata {cursor:pointer;}
			.getdata:hover {color:blue;}
			.asterisk_marker {display:none;color:red}
		</style>	
	</head>
	<body>
		<div id="main_container" style="background-color: grey; width: 1200px; box-shadow: 0px 5px 5px 2px #777; border-radius: 15px; margin: 30px auto;">
			<div id="header">
				<div style="border-bottom: 1px solid red; position: relative; bottom: -33px;width:95%;margin:0 auto;"></div>
				<div style="font-size: 30px; text-align: center; background-color: grey; width: 230px; position: relative; margin: 0px auto;">XML Operator</div>
			</div>
			
			<div id="content" style="padding-bottom:20px;">
				<div id="section_titles" style="text-align:center;margin-bottom:20px;">
					<div style="width:50%;float:right;text-align:center;">Operaciones</div>
					<div style="width:50%;text-align:center;">Estructura del archivo XML</div>
				</div>
				
				<div id="sections">
					<div id="operations" style="width:50%;margin-right:25px;float:right;">
						<div id="main_operation_box" style="background-color:#D8D8D8;margin-left:25px;border-radius:3px;">
							<div class="node_data" style="padding:20px;">
								<div class="operation_title" style="margin:10px auto;text-align:center;">Información</div>
								
								<input id="nodedata_ID" value="null" type="hidden" />
								Nodo:
								<div id="nodedata_info" style="background-color:white;margin-bottom:10px;"><i>Selecciona un nodo</i></div>
								Contenido:
								<div id="nodedata_content" style="background-color:white;margin-bottom:10px;"><i>Selecciona un nodo</i></div>
								Características:
								<div id="nodedata_other" style="background-color:white;"><i>Selecciona un nodo</i></div>
							</div>

							<script type="text/javascript">
				
									function displayOperation() {
										switch($("#operation_selector").val()) {
											case "none": {
												$("#operation_edit").css("display","none");
												$("#operation_insert").css("display","none");
												$("#message_box").css("display","none");
												break;
											}
											case "insert": {
												$("#operation_edit").css("display","none");
												$("#operation_insert").css("display","block");									
												break;
											}
											case "edit": {
												$("#operation_edit").css("display","block");
												$("#operation_insert").css("display","none");										
												break;
											}				
										}
									}

									function newInsertionButton(operation_type) {
										var actual_value = $("#"+operation_type+"node_attr_num").val();
										actual_value++;
										var attributes_html = "<div id='"+operation_type+"node_attr_container_"+actual_value+"'>"+
										"<span id='"+operation_type+"node_index_tag_"+actual_value+"' >"+actual_value+".</span>"+
										"<input id='"+operation_type+"node_attrind_"+actual_value+"' style='background-color:white;margin-bottom:5px;' value='' type='text'/>"+
										"<input id='"+operation_type+"node_attrval_"+actual_value+"' style='background-color:white;margin-bottom:10px;' value='' type='text'/>"+
										"<input id=\""+operation_type+"node_insertion_btn_"+actual_value+"\" onClick=\"newInsertionButton('"+operation_type+"')\" type=\"button\" value=\"+\" />"+
										"<input id=\""+operation_type+"node_remove_btn_"+actual_value+"\" onClick=\"deleteInsertionButton('"+operation_type+"')\" type=\"button\" value=\" - \" />"+
										"<span id=\""+operation_type+"node_attr_error_"+actual_value+"\" class=\"asterisk_marker\">*</span>"
										"</div>";
										
										$("#"+operation_type+"node_attr_num").val(actual_value);
										$("#"+operation_type+"node_insertion_btn_"+(actual_value-1)).css("display","none");
										$("#"+operation_type+"node_remove_btn_"+(actual_value-1)).css("display","none");
										$("#"+operation_type+"node_insertion_container").append(attributes_html);
									}		

									function deleteInsertionButton(operation_type) {
										var actual_value = $("#"+operation_type+"node_attr_num").val();
										$("#"+operation_type+"node_attr_container_"+actual_value).remove();
										actual_value--;
										$("#"+operation_type+"node_attr_num").val(actual_value);
										$("#"+operation_type+"node_insertion_btn_"+(actual_value)).css("display","inline");
										$("#"+operation_type+"node_remove_btn_"+(actual_value)).css("display","inline");										
									}						
							</script>

							
							<div id="operation_menu" style="padding:10px 20px;display:none;" >
								<hr />
								<input id="deletenode_submit"  type="button" value="Eliminar nodo" style="float:right;"/>
								
								<select id="operation_selector" name="operation_selector" onchange="displayOperation()">
									<option value="none"><i>Operación</i></option>
									<option value="edit">Editar</option>
									<option value="insert">Insertar</option>
								</select> 
							</div>
						</div>

						<div id="operation_edit" style="background-color:#D8D8D8;margin-left:25px;border-radius:3px;margin-top:20px;display:none;">
							<div class="node_data" style="padding:20px;">
								<div class="operation_title" style="margin:10px auto;text-align:center;">Editar</div>
								
								Clave:<br />
								<input id="editnode_key" style="background-color:white;margin-bottom:10px;" value="" type="text"/><span id="editnode_key_error" class="asterisk_marker">*</span><br />
								Contenido:<br />
								<textarea id="editnode_content" rows="5" style="background-color:white;margin-bottom:10px;width:96%;vertical-align:top;" value="" type="text"/></textarea><span id="editnode_content_error" class="asterisk_marker">*</span><br />
								Atributos:<br />
								<div id="editnode_attributes">
									<input id="editnode_attr_num" value="0" type="hidden" />
									<div id="editnode_insertion_container">
										<!--
										- DEFAULT CODE THAT SHOULD GENERATE HERE VIA JAVASCRIPT -
										<span id="editnode_index_tag" >1.</span>
										<input id="editnode_attrind_1" style="background-color:white;margin-bottom:5px;" value="" type="text"/>
										<input id="editnode_attrval_1" style="background-color:white;margin-bottom:10px;" value="" type="text"/>
										<input id="editnode_insertion_btn_1" onClick="newInsertionButton('edit')" type="button" value="+" />
										-->
									</div>
								</div>
								<hr />
								<input id="editnode_submit"  type="button" value="Editar Nodo" />
								<div style="float:right;">Habilitar contenido con CDATA<input id="editnode_check_cdata" type="checkbox" /></div>
							</div>
						</div>

						<div id="operation_insert" style="background-color:#D8D8D8;margin-left:25px;border-radius:3px;margin-top:20px;display:none;">
							<div class="node_data" style="padding:20px;">
								<div class="operation_title" style="margin:10px auto;text-align:center;">Insertar</div>
								Clave:<br />
								<input id="insertnode_key" style="background-color:white;margin-bottom:10px;" value="" type="text"/><span id="insertnode_key_error" class="asterisk_marker">*</span><br />
								Contenido:<br />
								<textarea id="insertnode_content" rows="5" style="background-color:white;margin-bottom:10px;width:96%" value="" type="text"></textarea><span id="insertnode_content_error" class="asterisk_marker">*</span><br />
								Atributos:<br />
								<div id="insertnode_attributes">
									<input id="insertnode_attr_num" value="0" type="hidden" />
									<div id="insertnode_insertion_container">
										<input id="insertnode_insertion_btn_0" onClick="newInsertionButton('insert')" type="button" value="+" />
									</div>
								</div>
								<hr />
								<input id="insertnode_submit"  type="button" value="Insertar Nodo" />
								<div style="float:right;">Habilitar contenido con CDATA<input id="insertnode_check_cdata" type="checkbox" /></div>
							</div>							
						</div>
						
						<div id="message_box" style="background-color:#D8D8D8;margin-left:25px;border-radius:3px;margin-top:20px;display:none;padding:20px;">
							
						</div>
				</div>				
					
					<div id="xml_structure" style="position:relative;
					background-color:white;
					border:1px solid black;
					width:500px;
					min-height:500px;
					margin-left:25px;border-radius:3px;font-family:'Lucida Console';font-size:14px;/*overflow:auto;*/">
						<?php
							$operador = new xml_operator($_SESSION['filePath']);
							$data = $operador->getFile();
							$ID = 0;
							$nodeList = array();
							$father = null;
							$route = array();
							$nodeList[] = doStructure($data,0,$ID,$nodeList,$father);
						?>
					</div>
			</div>

				<script type="text/javascript">
					$(window).load(function() {
						Object.size = function(obj) {
							var size = 0, key;
							for (key in obj) {
								if (obj.hasOwnProperty(key)) size++;
							}
							return size;
						};
						
						function decodeHTMLEntities(text) {
						    var entities = [
						        ['apos', '\''],
						        ['amp', '&'],
						        ['lt', '<'],
						        ['gt', '>']
						    ];

						    for (var i = 0, max = entities.length; i < max; ++i) 
						        text = text.replace(new RegExp('&'+entities[i][0]+';', 'g'), entities[i][1]);
						    return text;
						}
						
						//Almacenamos en un Objeto todo el contenido
						var nodeList = new Object();
						<? for($i=0;$i<count($nodeList);$i++) { ?>
							nodeList[<?=$i?>] = new Object();
							nodeList[<?=$i?>]['ID'] = "<?=$nodeList[$i]['ID'];?>"; 
							nodeList[<?=$i?>]['key'] = "<?=$nodeList[$i]['key'];?>"; 
							nodeList[<?=$i?>]['content'] = "<?=$nodeList[$i]['content']?>";
							nodeList[<?=$i?>]['pos'] = "<?=$nodeList[$i]['pos'];?>";
							nodeList[<?=$i?>]['nsprefix'] = "<?=(isset($nodeList[$i]['nsprefix']))?$nodeList[$i]['nsprefix']:null;?>";
							<? if (isset($nodeList[$i]['namespace_key']) && !empty($nodeList[$i]['namespace_value'])) { ?>
								nodeList[<?=$i?>]['namespace_key'] = new Array ();
								nodeList[<?=$i?>]['namespace_key'] = [<? for($j=0;$j<count($nodeList[$i]['namespace_key']);$j++) {?>'<?=$nodeList[$i]['namespace_key'][$j];?>'<?if ($j!=(count($nodeList[$i]['namespace_key'])-1)) echo ",";?><? } ?>];
								nodeList[<?=$i?>]['namespace_value'] = new Array ();
								nodeList[<?=$i?>]['namespace_value'] = [<? for($j=0;$j<count($nodeList[$i]['namespace_value']);$j++) {?>'<?=$nodeList[$i]['namespace_value'][$j];?>'<?if ($j!=(count($nodeList[$i]['namespace_value'])-1)) echo ",";?><? } ?>];
							<? } ?>
							
							<? if (isset($nodeList[$i]['ind_attr'])) { ?>
								nodeList[<?=$i?>]['ind_attr'] = new Array ();
								nodeList[<?=$i?>]['ind_attr'] = [<? for($j=0;$j<count($nodeList[$i]['ind_attr']);$j++) {?>'<?=$nodeList[$i]['ind_attr'][$j];?>'<?if ($j!=(count($nodeList[$i]['ind_attr'])-1)) echo ",";?><? } ?>];
								nodeList[<?=$i?>]['attr_val'] = new Array ();
								nodeList[<?=$i?>]['attr_val'] = [<? for($j=0;$j<count($nodeList[$i]['attr_val']);$j++) { ?>'<?=$nodeList[$i]['attr_val'][$j];?>'<? if ($j!=(count($nodeList[$i]['attr_val'])-1)) echo ","; ?><? } ?>];		
								nodeList[<?=$i?>]['attr_prefix'] = new Array ();
								nodeList[<?=$i?>]['attr_prefix'] = [<? for($j=0;$j<count($nodeList[$i]['attr_prefix']);$j++) { ?>'<?=$nodeList[$i]['attr_prefix'][$j];?>'<? if ($j!=(count($nodeList[$i]['attr_prefix'])-1)) echo ","; ?><? } ?>];		
								//console.log("Para el nodo con ID: "+nodeList[<?=$i?>]['ID']+" tenemos "+nodeList[<?=$i?>]['ind_attr']);	
							<? } ?>
							nodeList[<?=$i?>]['route'] = new Array();
							nodeList[<?=$i?>]['route'] = [<? for($j=0;$j<count($nodeList[$i]['route']);$j++) { ?>parseInt(<?=$nodeList[$i]['route'][$j];?>)<? if ($j!=(count($nodeList[$i]['route']) -1)) echo ",";} ?>	];
						<? } ?>

						<? for ($i=0;$i<count($nodeList);$i++) { ?>
							$("#nodelist_"+<?=$nodeList[$i]['ID']?>).click(function() {
								operatingNode = search_node(<?=$nodeList[$i]['ID']?>,nodeList);
								display_data(operatingNode);
							});
						<? } ?>
						
						function search_node(nodeID,nodeList) {
							var found = false;
							var nodeFound = new Array();
							
							for (var i=0;(i<Object.size(nodeList) || !found);i++) {
								if (nodeID == nodeList[i]['ID']) {
									nodeFound = nodeList[i];
									found = true;
								}
							}
							if (found) return nodeFound;
							else return 0;
						}

						function display_data(operatingNode) {
							$("#operation_selector").val("none");
							displayOperation();
							
							//Preparamos contenidos
							var prefixedElements = false;
							var declaredNamespaces = false;
							var xmlInfo = "&lt;";
							if (operatingNode['nsprefix'] == "") {
								xmlInfo += operatingNode['key'];
							} else {
								prefixedElements = true;
								xmlInfo += operatingNode['nsprefix']+":"+operatingNode['key'];
							}
							
							for (var i=0;i<(Object.size(operatingNode['namespace_key']));i++) {
								if (operatingNode['namespace_key'][i]=="") {
									xmlInfo += " xmlns=\""+operatingNode['namespace_value'][i]+"\"";
								} else {
									xmlInfo += " xmlns:"+operatingNode['namespace_key'][i]+"=\""+operatingNode['namespace_value'][i]+"\"";
								}
								declaredNamespaces = true;
							}
							
							//console.log(Object.size(operatingNode['route']));
							for (var i=0;i<(Object.size(operatingNode['ind_attr']));i++) {
								if (operatingNode['attr_prefix'][i] == "") {
									xmlInfo += " "+operatingNode['ind_attr'][i]+"=\""+operatingNode['attr_val'][i]+"\"";
								} else {
									prefixedElements = true;
									xmlInfo += " "+operatingNode['attr_prefix'][i]+":"+operatingNode['ind_attr'][i]+"=\""+operatingNode['attr_val'][i]+"\"";
								}
								//console.log(operatingNode['ind_attr']+"//"+operatingNode['attr_val']+"//"+operatingNode['attr_prefix']);
							}
							xmlInfo += "&gt;";

							//Comprobamos si el contenido del nodo tiene datos 
							//que requieren delimitación con cdata
							var specialChars = false;
							if (operatingNode['content'] == "") {
								var content_msg = "Nodo sin contenido";
							} else {
								//Detección de cdata
								specialChar_regular_expression = /[<>"&']+/;    
								if (operatingNode['content'].match(specialChar_regular_expression)) {
									specialChars = true;
								}
								var content_msg = operatingNode['content'];
							}
							
							//Eliminamos los asteriscos que señalan los errores
							$(".asterisk_marker").fadeOut(0);
							
							//Mostramos información básica
							$("#nodedata_ID").val(operatingNode['ID']);
							$("#nodedata_info").html(xmlInfo);
							$("#nodedata_content").html(content_msg);
							if (!specialChars && !declaredNamespaces && !prefixedElements) { 
								$("#nodedata_other").html("No tiene caracter&iacute;sticas especiales.");
							} else {
								var characteristics = "Tiene las siguientes caracter&iacute;sticas:<br />";
								if (specialChars) characteristics += "- Contiene car&aacute;cteres especiales, se recomienda anidar contenido en CDATA.<br />";
								if (declaredNamespaces) characteristics += "- Tiene declarado uno o más espacios de nombres.<br />";
								if (prefixedElements) characteristics += "- El nombre de la etiqueta o uno de sus atributos usan un espacio de nombres.<br />";
								$("#nodedata_other").html(characteristics);
							}
							
							$("#operation_menu").fadeIn(200);

							//Mostramos información de Edición
							if (operatingNode['nsprefix'] == "") {
								$("#editnode_key").val(operatingNode['key']);
							} else {
								$("#editnode_key").val(operatingNode['nsprefix']+":"+operatingNode['key']);
							}
							$("#editnode_content").val(decodeHTMLEntities(operatingNode['content']));
							$("#editnode_attr_num").val(0);
							$("#editnode_insertion_container").html("");
							if (specialChars) {
								$("#editnode_check_cdata")[0].checked = true; 
							} else {
								$("#editnode_check_cdata")[0].checked = false;
							}

							//Reseteamos el formulario de Inserción
							$("#insertnode_attr_num").val("0");
							$("#insertnode_key").val("");
							$("#insertnode_content").val("");
							$("#insertnode_insertion_container").html("<input id=\"insertnode_insertion_btn_0\" onClick=\"newInsertionButton('insert')\" type=\"button\" value=\"+\" />");
							$("#insertnode_check_cdata")[0].checked = false;
							
							var attributes_html = "<input id='editnode_insertion_btn_0' onClick='newInsertionButton(\"edit\")' type='button' value='+' />";
							$("#editnode_insertion_container").html(attributes_html);
							
							//Insertamos campos para atributos 
							var attr_num = Object.size(operatingNode['ind_attr']);
							if (attr_num>=1) {
								for (var i=1;i<=attr_num;i++) {
									if (operatingNode['attr_prefix'][i-1].length == 0) {
										var complete_attr_name = operatingNode['ind_attr'][i-1];
									} else {
										var complete_attr_name = operatingNode['attr_prefix'][i-1]+":"+operatingNode['ind_attr'][i-1];
									}
								
									var attributes_html = "<div id='editnode_attr_container_"+i+"'>"+
										"<span id='editnode_index_tag_"+i+"'>"+i+".</span>"+
										"<input id='editnode_attrind_"+i+"' style='background-color:white;margin-bottom:5px;' value='"+complete_attr_name+"' type='text'/>"+
										"<input id='editnode_attrval_"+i+"' style='background-color:white;margin-bottom:10px;' value='"+operatingNode['attr_val'][i-1]+"' type='text'/>"+
										"<input id='editnode_insertion_btn_"+i+"' onClick='newInsertionButton(\"edit\")' type='button' value=' + ' />"+
										"<input id='editnode_remove_btn_"+i+"' onClick='deleteInsertionButton(\"edit\")' type='button' value=' - ' />"+
										"<span id='editnode_attr_error_"+i+"' class='asterisk_marker'>*</span><br />";					
									"</div>";
									$("#editnode_attr_num").val(i);
									for(var j=(i-1);j>=0;j--) {
										$("#editnode_remove_btn_"+j).css("display","none");
										$("#editnode_insertion_btn_"+j).css("display","none");
									}
						
									$("#editnode_insertion_container").append(attributes_html);
								}								
							} 
						}

						$("#editnode_submit").click(function() {operationControl("edit")});
						$("#insertnode_submit").click(function() {operationControl("insert")});
						$("#deletenode_submit").click(function() {operationControl("delete")});
						
						function operationControl(operation) {
							if (formControl(operation)) {
								//console.log("Realiza "+operation);					
								var searchID = $("#nodedata_ID").val();
								if (searchID!="null") {
									operating_node = search_node(searchID,nodeList);
									operate(operating_node,operation);
								} else {
									print_message("No se ha seleccionado nodo alguno");
								}
							}
						}
						
						function formControl(operation) {
							$(".asterisk_marker").fadeOut(0);
							var error_clear = true;
							var error_log = "Existen los siguientes errores: <br />";
							
							function makeControl (type,field) {
								var evaluate_content = $(field).val();
								var error_found = false;
								// -!!!- Cambiar expresión regular para evitar dobles ":"
								var key_regular_expression = /^(?!((x|X)(m|M)(l|L)))^[a-zA-Z_][\.:\-_a-zA-Z0-9]*([\.\-_a-zA-Z0-9])$/;
								var cdata_regular_expression = /^(<!\[CDATA\[)(.)+(\]\]>)$/;
								var xml_regular_expression = /^([^<>&"']*)$/;
								
								switch(type) {
									case "key": {
										if (!evaluate_content.match(key_regular_expression) || !evaluate_content.match(xml_regular_expression)) {
											error_clear = false;
											error_found = true;
											error_log += "- La clave o el nombre de atributo no tiene un patr&oacute;n de car&aacute;cteres v&aacute;lido para xml. <br />";											
										}
										break;
									} 
									case "attr_value": {
										if (!evaluate_content.match(xml_regular_expression)) {
											error_clear = false;
											error_found = true;
											error_log += "- El contenido de un nodo o el valor de un atributo contiene car&aacute;cteres no v&aacute;lidos para xml. <br />";
										}
										if (evaluate_content.length == 0) {
												error_clear = false;
												error_found = true;
												error_log += "- El valor de un atributo no puede estar en blanco. <br />";
										}
										break;
									} 
									case "value": {
											if (!evaluate_content.match(xml_regular_expression)) {
												error_clear = false;
												error_found = true;
												error_log += "- El contenido del nodo contiene car&aacute;cteres no v&aacute;lidos para xml. <br />";
											}
										break;
									}
								}
								return error_found;
							}
							
							if (operation!="delete") {
								//Comprobamos la clave 
								if(makeControl("key","#"+operation+"node_key")) {
									 $("#"+operation+"node_key_error").fadeIn(0);
								}

								//Comprobamos el valor
								var enableCDATA = $("#"+operation+"node_check_cdata")[0].checked;
								if(!enableCDATA) {
									if(makeControl("value","#"+operation+"node_content")) {
										 $("#"+operation+"node_content_error").fadeIn(0);
									}
								}
																
								//Comprobamos los atributos
								var attr_num = $("#"+operation+"node_attr_num").val();
								if (attr_num > 0) {
									for (var i=1;i<=attr_num;i++) {
										
										//Comprobamos que no haya atributos iguales
										var actual_field = $("#"+operation+"node_attrind_"+i).val();
										for (var j=i+1;j<=attr_num;j++) {
											var compare_field = $("#"+operation+"node_attrind_"+j).val();
											if(actual_field == compare_field) {
												error_clear = false;
												error_log += "- Los nombres de los atributos deben ser distintos. <br />";	
												$("#"+operation+"node_attr_error_"+i).fadeIn(0);				
											}
										}
										
										//Comprobacion de contenido
										if (makeControl("key","#"+operation+"node_attrind_"+i)) {
											$("#"+operation+"node_attr_error_"+i).fadeIn(0);
										}
										if (makeControl("attr_value","#"+operation+"node_attrval_"+i)) {
											$("#"+operation+"node_attr_error_"+i).fadeIn(0);
										}
									}
									
								}	
								if (!error_clear) { print_message(error_log); }
							}

							return error_clear;
						}

						function print_message(msg) {
							$("#message_box").fadeIn(100);
							$("#message_box").html(msg);
						}
						
						function operate(nodeData,action) {
							//Fabricamos una ruta
							var route_tag = new Array();
							var route_pos = new Array();
							var route_prefix = new Array();
							var route_length = Object.size(nodeData['route']);
							for (var i=0;i<route_length;i++) {
								var taken_node = search_node(nodeData['route'][i],nodeList);
								console.log(taken_node['key']+" "+taken_node['pos']+" "+taken_node['nsprefix']);
								route_tag.push(taken_node['key']);
								route_pos.push(taken_node['pos']);
								route_prefix.push(taken_node['nsprefix']);
							}
							
							//Eliminamos el último elemento de la ruta: se trata del nodo raíz.
							route_tag.splice((route_length-1),1);
							route_pos.splice((route_length-1),1);
							route_prefix.splice((route_length-1),1);
							
							var data_structure = new Object();
							switch(action) {
								case "delete": {
									data_structure = {
										route_tag:route_tag,
										route_pos:route_pos,
										route_prefix:route_prefix,
										action:action
									};
									break;
								}
								case "edit": {
									//Fabricamos el contenido
									node_key = $("#editnode_key").val();
									if ($("#editnode_check_cdata")[0].checked) {
										var iscdata = true;
									} else {
										var iscdata = false;
									}
									
									var node_content = $("#editnode_content").val();
									var attr_num = $("#editnode_attr_num").val();
									var node_attr_index = new Array();
									var node_attr_val = new Array();	
									for	(var i=1;i<=attr_num;i++) {
										node_attr_index.push($("#editnode_attrind_"+i).val());
										node_attr_val.push($("#editnode_attrval_"+i).val());
									}
									
									data_structure = {
										route_tag:route_tag,
										route_pos:route_pos,
										route_prefix:route_prefix,
										node_key:node_key,
										iscdata:iscdata,
										node_content:node_content,
										node_attr_index:node_attr_index,
										node_attr_val:node_attr_val,
										action:action
									};
									break;
								}
								case "insert": {
									//Fabricamos el contenido
									node_key = $("#insertnode_key").val();
									if ($("#insertnode_check_cdata")[0].checked) {
										var iscdata = true;
									} else {
										var iscdata = false;
									}
									
									node_content = $("#insertnode_content").val();
									var attr_num = $("#insertnode_attr_num").val();
									var node_attr_index = new Array();
									var node_attr_val = new Array();
									for	(var i=1;i<=attr_num;i++) {
										node_attr_index.push($("#insertnode_attrind_"+i).val());
										node_attr_val.push($("#insertnode_attrval_"+i).val());
									}
									data_structure = {
										route_tag:route_tag,
										route_pos:route_pos,
										route_prefix:route_prefix,										
										node_key:node_key,
										iscdata:iscdata,
										node_content:node_content,
										node_attr_index:node_attr_index,
										node_attr_val:node_attr_val,
										action:action
									};									
									break;
								}
							}
								
								$.ajax({
									type: 'POST',				
									url: '<?=PHP."xo_nodeOperations_json.php"?>',
									data: data_structure,
									dataType: 'json',						// Tipo de parametros que devuelve el php
									//contentType: 'application/json',		// Tipo de parametros que le enviamos al php. Si añadimos esta linea en el paso de parametros por json, entonces no funciona !!!				
									beforeSend: function(x) {
										if (x && x.overrideMimeType) x.overrideMimeType("application/j-son;charset=UTF-8");
										//$("#preloader").fadeIn(200);
									},
									error: function(XMLHttpRequest, errorText, errorThrown) {
										alert("XMLHttpRequest="+XMLHttpRequest.responseText+"\nError="+errorText+"\nerrorThrown="+errorThrown);
										//show_error("Se ha producido un error: \n"+errorText);
									},		
									success: function (result) {
										$("#operation_selector").val("none");
										displayOperation();
										$("#main_operation_box").fadeOut(0);
										$("#xml_structure").html("<div style=\"margin:45% auto;text-align:center;\">RECARGANDO CONTENIDO...</div>");
										print_message(result.back);
										setTimeout(function() {
											//location.reload();
										},2000);
									},	
									// COMPLETE  
									complete: function(objeto, exito) {
										//$("#preloader").fadeOut(10);
										if (exito=="success"){}
									}
								});
						}
					});
				</script>
				
			</div>
		</div>
	</body>
</html>


