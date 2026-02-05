<?php
	session_start();
	require_once "./XMLoperator.php";
	require_once "../../config/xo_settings.php";
	require_once "../../script/php/xo_functions.php";
	
	//Generamos una ruta
	$array_tags = (!empty($_POST['route_tag']))?$_POST['route_tag']:null;
	$array_pos = (!empty($_POST['route_pos']))?$_POST['route_pos']:null;
	$array_prefix = (!empty($_POST['route_prefix']))?$_POST['route_prefix']:null;
	
	if ($array_tags!=null) {
		$j = (count($array_tags)-1);
		for ($i=0;$i<count($array_tags);$i++) {
			$array_route[$i]['tag'] = $array_tags[$j];
			$array_route[$i]['pos'] = $array_pos[$j];
			$array_route[$i]['prefix'] = $array_prefix[$j];
			$j--;
		}
	} else {
		$array_route = null;
	}
	
	$xml_operator = new xml_operator($_SESSION['specialFilePath'],true,true);

	switch ($_POST['action']) {
		case "delete": {
			try {
				$xml_operator->delete_node($array_route);
				$jsonresult['back'] = "&Eacute;xito al eliminar el nodo.";	
			} catch (Exception $e) {
				$jsonresult['back'] = "Error al realizar la operaci&oacute;n. <br />".$e->getMessage();
			}
			break;
		}
		case "edit": {
			try {
				$new_key = $_POST['node_key'];
				$new_content = $_POST['node_content'];
				$iscdata = ($_POST['iscdata']=="true")?true:false;
				$array_attr_ind = (!empty($_POST['node_attr_index']))?$_POST['node_attr_index']:null;
				$array_attr_val = (!empty($_POST['node_attr_val']))?$_POST['node_attr_val']:null;
				
				//Extraemos el nodo y generamos una estructura de datos compatible
				$original_node = $xml_operator->extract_node($array_route);
				$std_data = new stdclass();
				$std_data->node_name = $new_key;
				$std_data->nsprefix = get_prefix($new_key);
				$std_data->node_value = $new_content;
				$std_data->iscdata = $iscdata;
				if(!empty($array_attr_ind)) {
					for ($i=0;$i<count($array_attr_ind);$i++) {
						$std_data->attr[$i] = $array_attr_ind[$i];
						$std_data->attr_value[$i] = $array_attr_val[$i];
						$std_data->attr_prefix[$i] = get_prefix($array_attr_ind[$i]);
					}
				}
				
				//Situamos el nuevo nodo en el lugar del primero
				$xml_operator->delete_node($array_route);
				$new_route = $array_route;
				unset($new_route[count($new_route)-1]);
				$xml_operator->insert_node($std_data,$new_route);
							
				//Insertamos todos sus hijos, a partir del nodo nuevo
				$new_route[count($new_route)]['tag'] = $std_data->node_name;
				//$new_route[count($new_route)]['prefix'] = $std_data->nsprefix;

				fill_node($original_node,$new_route,$xml_operator);
				$jsonresult['back'] = "&Eacute;xito al editar.";
								
			} catch (Exception $e) {
				//$jsonresult['back'] = "Error al realizar la operaci&oacute;n. <br /><br />".$e->getMessage();
				$jsonresult['back'] = var_dump($new_route)." <br />".$e->getMessage();
			}
			break;
		}
		case "insert": {
			try {
				//Recogemos la estructura de datos
				$new_key = $_POST['node_key'];
				$new_content = $_POST['node_content'];
				$iscdata = ($_POST['iscdata']=="true")?true:false;
				$array_attr_ind = (!empty($_POST['node_attr_index']))?$_POST['node_attr_index']:null;
				$array_attr_val = (!empty($_POST['node_attr_val']))?$_POST['node_attr_val']:null;
			
				//Creamos una estructura std para insertar
				$std_data = new stdclass();
				$std_data->node_name = $new_key;
				$std_data->nsprefix = get_prefix($new_key);
				$std_data->node_value = $new_content;
				$std_data->iscdata = $iscdata;
				if(!empty($array_attr_ind)) {
					for ($i=0;$i<count($array_attr_ind);$i++) {
						$std_data->attr[$i] = $array_attr_ind[$i];
						$std_data->attr_value[$i] = $array_attr_val[$i];
						$std_data->attr_prefix[$i] = get_prefix($array_attr_ind[$i]);
					}
				}
				$xml_operator->insert_node($std_data,$array_route);
				$jsonresult['back'] = "&Eacute;xito al insertar.";	
			} catch (Exception $e) {
				$jsonresult['back'] = "Error al realizar la operaci&oacute;n. <br />".$e->getMessage();
			}
			break;
		}
	}
	
	echo json_encode($jsonresult);
	
?>