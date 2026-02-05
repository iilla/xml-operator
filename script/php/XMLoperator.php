<?php

class SimpleXMLExtended extends SimpleXMLElement {
	public function addCData($nodevalue) {
		//$nodevalue = htmlentities($nodevalue);
		$node = dom_import_simplexml($this);
		$fileData = $node->ownerDocument;
		$node->appendChild($fileData->createCDATASection($nodevalue));
	}
	
	public function totalChilds() {
		$docNamespaces = $this->getDocNamespaces(true);
		if (!array_key_exists("",$docNamespaces)) $docNamespaces[""] = null;
		
		$totalChilds = 0;
		foreach ($docNamespaces as $nsKey => $nsValue) {
			foreach ($this->children($nsKey,true) as $child) {
				$totalChilds++;
			}
		}
		return $totalChilds;
	}
}

class xml_operator {
	private $xml_file;
	private $xml_path;
	private $xml_array_structure;
	private $xml_node_list;
	private $error_log;
	private $error_management;
	private $default_namespace;
	
	public function __construct($_xml_path,$_manage_errors = false,$_extended_XMLElement = false) {
		$this->error_log = "Errores encontrados: <br />";
		$this->error_management = $_manage_errors;
		
		if(file_exists($_xml_path)) {
			libxml_use_internal_errors(true);
			$this->xml_path = $_xml_path;
			if ($_extended_XMLElement) {
				$this->xml_file = new SimpleXMLExtended($_xml_path,0,true);
			} else {
				$this->xml_file = simplexml_load_file($_xml_path);
			}
			//$this->xml_array_structure = new simpleXMLIterator($_xml_path,null,true);
			//$this->xml_array_structure = $this->create_array_structure($this->xml_array_structure);
			if($this->xml_file === false) {
				$this->crash();
			} else {
				//Establecemos un espacio de nombres por defecto, para que en las futuras inserciones los nodos hijos no hereden los prefijos de los padres
				$docNamespaces = $this->xml_file->getDocNamespaces();
				if(!array_key_exists("",$docNamespaces)) {
					$this->xml_file->addAttribute("xmlns:xmlns","http://ghostdefaultnamespace");
					$this->default_namespace = "http://ghostdefaultnamespace";
				} else {
					$this->default_namespace = $docNamespaces[""];
				}
				$this->refresh_data();
			}
		} else {
			$this->error_log .= "- La ruta del archivo no es correcta. <br />";
			$this->crash();
		}
	}
	
	public function insert_nodelist($_nodelist) {
		$this->xml_node_list = $_nodelist; 
	}
	
	public function extract_nodelist() {
		return $this->xml_node_list;
	}
	
	private function refresh_data() {
		$proceed = true;
		if (!($this->xml_file->asXML($this->xml_path))) {
			$this->crash();
			$proceed = false;
		}
		//if($proceed) $this->xml_array_structure = $this->create_array_structure($this->xml_array_structure);
	}
	
	private function create_array_structure($sxi_file) {
		$a = array();
		for( $sxi_file->rewind(); $sxi_file->valid(); $sxi_file->next() ) {
		
			if(!array_key_exists($sxi_file->key(), $a)){
			  $a[$sxi_file->key()] = array();
			}

			if($sxi_file->hasChildren()){
			  $a[$sxi_file->key()][] = $this->create_array_structure($sxi_file->current());
			} else {
			  $a[$sxi_file->key()][] = strval($sxi_file->current());
			}
		  }
		  return $a;
	}

	public function getFile() {
		return $this->xml_file;
	}
	
	public function getStructure() {
		return $this->xml_array_structure;
	}
	
	public function crash() {
		foreach(libxml_get_errors() as $error) $this->error_log .= "- XML: ".$error->message."<br />";	
		if ($this->error_management) {
			throw new Exception ($this->error_log);
		} else {
			exit($this->error_log);
		}
	}

	//Recorremos los nodos según la ruta, esto situa el puntero de
	//la última variable extraída de modo que se relaciona con el puntero
	//del fichero xml que hemos abierto
	private function node_indexer($father,$tag_data,$firstIteration = true) {
		if ($firstIteration) $father = $this->xml_file;
		$next_node = null;
		$found = false;
		$position = 0;
		//$analizingChilds = (empty($tag_data['prefix']))?$father->children():$father->children($tag_data['prefix'],true);
		if (empty($tag_data['prefix'])) {
			$analizingChilds =  $father->children();
		} else {
			$analizingChilds = $father->children($tag_data['prefix'],true);
		}
		
		foreach ($analizingChilds as $new_node) {
			if (!$found) {
				//echo "Buscamos ".$tag_data['tag']." - ".$new_node->getName()."<br />";
				if ($new_node->getName() == $tag_data['tag'])  {
					//Si el tag existe y coincide con el nombre de tag de la ruta, lo cogemos inicialmente
					$next_node = $new_node;
					//Buscamos el tag por posición si existe
					if(isset($tag_data['pos'])) {
						//Lo iniciamos en false, por si hemos especificado una posición por encima de las existentes
						$next_node = null;
						if ($tag_data['pos']==$position) {
							$next_node = $new_node;		
							$found = true;
						}
					} else {
						//Si no existe la búsqueda por posición, probamos con la búsqueda por atributo y valor
						if (isset($tag_data['ind_attr'])) {
							if($tag_data['attr_val'] == $new_node[$tag_data['ind_attr']]) {
								//Si existe atributo para el tag, y este coincide con el de la ruta lo seleccionamos
								$next_node = $new_node;
								$found = true;
							} else {
								//Si existe atributo para el tag pero este no coincide, lo desechamos
								$next_node = null;
							}
						} else {
							$next_node = null;
						}
					}
				}
			}
			$position++;
		}
		/*echo "<br />-------DEFINITIVO ------- <br />";
		echo $next_node->getName();
		var_dump($next_node);
		echo "<br />------------------------- <br />";*/
		return $next_node;
	}
	
	//Fabrica una ruta a través de etiquetas específicas del XML
	private function trace_route ($array_route) {
		$last_node = null;
		for($i=0;$i<count($array_route);$i++) {
			if ($i==0) $last_node = $this->node_indexer($last_node,$array_route[$i]);
			else $last_node = $this->node_indexer($last_node,$array_route[$i],false);
		}
		return $last_node;
	}
	
	//Inserta contenido mediante stdclass en una etiqueta específica 
	//por $arrayRoute. Si arrayRoute es false, insertará el nodo en 
	//la raíz del archivo
	public function insert_node($std_data,$array_route) {
		if (!empty($array_route)) {
			$last_node = $this->trace_route($array_route);
		} else {
			$last_node = $this->xml_file;
		}
		
		if ($last_node!=null) {	
			$docNamespaces = $this->xml_file->getDocNamespaces(true,true);
			//Insertamos los datos encapsulados en una clase std en el nodo especificado
			if (!empty($std_data->iscdata)) {
				if (empty($std_data->nsprefix)) {
					$inserted_node = $last_node->addChild($std_data->node_name,"",$this->default_namespace);
				} else {
					if (array_key_exists($std_data->nsprefix,$docNamespaces)) {
						$inserted_node = $last_node->addChild($std_data->node_name,"",$docNamespaces[$std_data->nsprefix]);
					} else {
						$inserted_node = $last_node->addChild($std_data->nsprefix.":".$std_data->node_name,"",$this->default_namespace);
					}
				}
				$inserted_node->addCDATA($std_data->node_value);
			} else {
				if (empty($std_data->nsprefix)) {
					$inserted_node = $last_node->addChild($std_data->node_name,$std_data->node_value,$this->default_namespace);
				} else {
					if (array_key_exists($std_data->nsprefix,$docNamespaces)) {
						$inserted_node = $last_node->addChild($std_data->node_name,$std_data->node_value,$docNamespaces[$std_data->nsprefix]);
					} else {
						$inserted_node = $last_node->addChild($std_data->nsprefix.":".$std_data->node_name,$std_data->node_value,$this->default_namespace);
					}
				}
			}

			// - !!! - Atributos con prefijos
			if (!empty($std_data->attr)) {
				for($i=0;$i<count($std_data->attr);$i++) {
					if(!empty($std_data->attr_prefix[$i])) {
						$inserted_node->addAttribute($std_data->attr_prefix[$i].":".$std_data->attr[$i],$std_data->attr_value[$i]);
					} else {
						$inserted_node->addAttribute($std_data->attr[$i],$std_data->attr_value[$i]);
					}
				}
			}
			if (!empty($std_data->data)) {
				foreach ($std_data->data as $data_tag) {
					$inserted_child = $inserted_node->addChild($data_tag['tag'],$data_tag['value']);
					if(!empty($data_tag['attr_ind'])) {
						for($i=0;$i<count($data_tag['attr_ind']);$i++) {
							$inserted_child->addAttribute($data_tag['attr_ind'][$i],$data_tag['attr_val'][$i]); 
						}
					}
				}
			}
			$this->refresh_data();
			
		} else {
			$this->error_log .= "- La ruta entre nodos no es correcta. <br />";
			$this->crash();
		}
	}
	
	public function delete_node($array_route) {
		$delete_file = false;
		if (!empty($array_route)) {
			$last_node = $this->trace_route($array_route);
		} else {
			$last_node = $this->xml_file;
			$delete_file = true;
		}
		
		if ($last_node!=null) {
			$xml_dom = dom_import_simplexml($last_node); 
			if (!$delete_file) {
				$xml_dom->parentNode->removeChild($xml_dom);
				$this->refresh_data();
			} else {
				// - !!! - VERSION: ELIMINAR DOCUMENTO - !!! -
				$this->error_log .= "- No se puede manipular el nodo ra&iacute;z desde esta aplicaci&oacute;n. <br />";
				$this->crash();
			}
		} else {
			$this->error_log .= "- La ruta entre nodos no es correcta. <br />";
			$this->crash();
		}
	}
	
	//extrae el contenido de un nodo y sus hijos
	public function extract_node($array_route) {
		if (!empty($array_route)) {
			$last_node = $this->trace_route($array_route);
		} else {
			$last_node = $this->xml_file;
		}
		
		if ($last_node!=null) {
			return $last_node;
		} else {
			$this->error_log .= "- La ruta entre nodos no es correcta. <br />";
			$this->crash();
		}
	}
}


// ESTRUCTURAS DE DATOS PARA INTERACTUAR CON XML OPERATOR
	/*
	 	ARRAY QUE CONFORMA LA RUTA ENTRE NODOS 
		$array_route[]['tag'] = "clave";
		$array_route[]['prefix'] = "prefijo"*;
		$array_route[]['ind_attr'] = "atributo"*;
		$array_route[]['attr_val'] = "valor"*;
		$array_route[]['attr_prefix'] = "prefijo"*;
		$array_route[]['pos'] = "posicion entre hijos"*;
		------------
		Nota: La ruta aumenta a medida que nos alejamos del 
		nodo raíz, podiendo en las primeras posiciones a los 
		nodos padres.
		------------
		CLASE STD PARA INTRODUCIR UN NODO
		$std_data = new stdclass();
		$std_data->node_name = "clave";
		$std_data->node_value = "contenido";
		$std_data->iscdata = "comprueba si el contenido requiere estar anidado en etiquetas CDATA";
		$std_data->attr[] = "atributo";
		$std_data->attr_value[] = "valor del atributo";
		$std_data->data[]['tag'] = "nodo hijo";
		$std_data->data[]['value'] = "contenido nodo hijo";	
		$std_data->data[]['attr_ind'][] = "atributo nodo hijo";	
		$std_data->data[]['attr_val'][] = "valor de atributo nodo hijo";	
		
		
		*
	*/
?>