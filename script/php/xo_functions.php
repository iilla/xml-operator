<?php	
	
	function align_content($output) {
		$output = preg_replace('/\t{1,}/', ' ', $output);
		$output = preg_replace('/\n{1,}/', ' ', $output);
		$output = preg_replace('/\r{1,}/', ' ', $output);
		$output = preg_replace('/\s{1,}/', ' ', $output);
		return $output;
	}
	
	function getNodeNamespaces($node,$isrootnode = false) {
		if ($isrootnode) {
			$rootNamespaces = $node->getDocNamespaces();
			return $rootNamespaces;
		} else {
			return $nodeNamespaces;
		}
	}
	
	//Genera y devuelve una lista de nodos estructurada en funcion de las caracteristicas del nodo
	function doStructure($node,$brank,&$ID,&$nodeList,$father,$pos = 0,$nsPrefix=null) {
		$list_insertion['father'] = $father;
		$rootNode = ($father==null)?true:false;
		$list_insertion['ID'] = $ID;
		$list_insertion['brank'] = $brank;
		$list_insertion['key'] = $node->getName();
		$list_insertion['pos'] = $pos;
		$list_insertion['content'] = align_content(htmlspecialchars(trim($node->__toString())));
		$list_insertion['nsprefix'] = $nsPrefix;
		
		if (empty($nsPrefix)) {
			echo "<span id='nodelist_".$ID."' class='getdata brank_".$brank."'>&#60;".$node->getName();	
		} else {
			echo "<span id='nodelist_".$ID."' class='getdata brank_".$brank."'>&#60;".$nsPrefix.":".$node->getName();
		}

		$docNamespaces = $node->getDocNamespaces();
		if (!array_key_exists("",$docNamespaces)) {
			$docNamespaces[""] = null;
		}
		$nodeNamespaces = $node->getDocNamespaces(false,false);
		
		//Insertamos espacios de nombres declarados
		$i = 0;
		foreach ($nodeNamespaces as $nsKey => $nsVal) {
			if(!empty($nsKey)) {
				$list_insertion['namespace_key'][$i] = $nsKey; 
				$list_insertion['namespace_value'][$i] = $nsVal;
				echo " xmlns:".$nsKey."=&#34".$nsVal."&#34"; 
			} else if (empty($nsKey) && !empty($nsVal) && $nsVal!="http://ghostdefaultnamespace") {
				$list_insertion['namespace_key'][$i] = $nsKey; 
				$list_insertion['namespace_value'][$i] = $nsVal;
				echo " xmlns=&#34".$nsVal."&#34";
			} 
			$i++;
		}
		
		//Insertamos atributos
		$i = 0;
		foreach ($docNamespaces as $nsKey => $nsVal) {
			foreach ($node->attributes($nsKey,true) as $k=>$v) {
				$list_insertion['ind_attr'][$i] = $k;
				$list_insertion['attr_val'][$i] = $v->__toString();	
				$list_insertion['attr_prefix'][$i] = $nsKey;
				$i++;
				if(!empty($nsKey)) {
					echo " ".$nsKey.":".$k."=&#34;".$v."&#34;";
				} else {
					echo " ".$k."=&#34;".$v."&#34;";
				}
			}
		}
		
		echo "&#62;</span><br />";
		
		if ($list_insertion['content'] != "") {
			echo "<span class='nodelist_content brank_".$brank."'>Posee contenido</span><br />";
		}
		
		if(!$rootNode) {
			$list_insertion['route'] = array();
			$list_insertion['route'][] = $ID;
			$next = $father;
			for ($i=0;$i<$brank;$i++) {
				if($next!=null) $list_insertion['route'][] = $next['ID'];
				$next = $next['father'];
			}
		} else {
			$list_insertion['route'] = array($ID);
		}
		
		$ID++;
		foreach ($docNamespaces as $ns => $nsValue) {
			$pos = 0;
			foreach ($node->children($ns,true) as $son) {
				$newbrank = $brank+1;
				$nodeList[] = doStructure($son,$newbrank,$ID,$nodeList,$list_insertion,$pos,$ns);
				$pos++;
			}
		}
		
		if (empty($nsPrefix)) {
			echo "<span class='brank_".$brank."'>&#60/".$node->getName()."&#62</span><br />";
		} else {
			echo "<span class='brank_".$brank."'>&#60/".$nsPrefix.":".$node->getName()."&#62</span><br />";
		}		

		return $list_insertion;
	}
	
	function fill_node($father,$route,$operator) {
		//Si no existe namespace por defecto, incluímos uno vacío
		$docNamespaces = $father->getDocNamespaces(true);
		if (!array_key_exists("",$docNamespaces)) {
			$docNamespaces[""] = null;
		}
		
		if(empty($route)) {
			$index=0;
		} else {
			$index = count($route);
		}
						
		//Rellenar nodos con hijos con espacios de nombres				
		//foreach ($docNamespaces as $nsKey => $nsValue) {
			//foreach($father->children($nsKey,true) as $child) {
			foreach($father->children() as $child) {
				$std_data = new stdclass();
				$std_data->node_name = $child->getName();
				//$std_data->nsprefix = $nsKey;
				$std_data->node_value = $child->__toString();
				$std_data->iscdata = (preg_match("/[<>\"&']+/",$child->__toString())==1)?true:false; 

				//foreach($child->attributes($nsKey,true) as $key=>$value) {
				foreach($child->attributes() as $key=>$value) {
					$std_data->attr[] = $key;
					$std_data->attr_value[] = $value; 
					//$std_data->attr_prefix[] = $nsKey; 
				}
															
				$operator->insert_node($std_data,$route);
				
				//if($child->totalChilds()>0) {
				if($child->children()>0) {
					$new_route = $route;
					$new_route[$index]['tag'] = $child->getName();
					
					fill_node($child,$new_route,$operator);
				}
			//}
		}
	}
	
	function get_prefix($operating_string) {
		$operating_string = explode(":",$operating_string);
		if (count($operating_string)>1) {
			return $operating_string[0];
		} else {
			return false;
		}
	}
	
	function print_files ($act_dir) {
		if (is_dir($act_dir)) {
			if ($act_dir!="/" && !preg_match("/[a-z]\:\//",$act_dir)) {
				$parent_dir = explode("/",$act_dir);
				array_pop($parent_dir);array_pop($parent_dir);
				$parent_dir = implode("/",$parent_dir);
				$parent_dir .= "/";
			} else {
				$parent_dir = $act_dir;
			}
				
			if ($dh = opendir($act_dir)) {
				while ($file = readdir($dh)) {
					if (($file != ".") && ($file != "..")) {
						if (filetype($act_dir.$file) == "dir") {
							$directory_directories_content[] = "<a href='".$act_dir.$file."'>".$file."</a>";
						} else {
							$directory_files_content[] = $file;
						}
					}
				}
	
				asort($directory_directories_content);
				asort($directory_files_content);
	
				$directory_content = array_merge($directory_directories_content,$directory_files_content);
				//$json['result'] += "."."<br />";
				echo "."."<br />";
				if($parent_dir != $act_dir) {
					//$json['result'] += "<a href='".$parent_dir."'>..</a>";
					echo "<a href='".$parent_dir."'>..</a>";
				}
				foreach ($directory_content as $file) {
					//$json['result'] += $file."<br />";
					echo $file."<br />";
				}
				closedir($dh);
			}
		} else {
			//$json['result'] = "directorio incorrecto.";
			echo "directorio incorrecto.";
		}
	}	
?>

