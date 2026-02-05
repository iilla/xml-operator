<?php

/*
	include("../script/php/XMLoperator.php");
	$operador = new xml_operator("../include/contenidos3.xml",false,true);
	
	
	$data = $operador->getFile();
	echo $data->totalChilds(); 

	*/


	
	//var_dump($data->getNamespaces(true));
	//echo "<br />-----------------------------------------------------------<br />";
	//var_dump($data->getDocNamespaces(true));
	
	//$ns = $data->getNamespaces(true);
	/*
	foreach($data->children() as $child) {
		echo "HIJO: ".$child->getName()."<br />";
	}*/
?>
 
<?php
/*
$xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
 <people xmlns:p="http://example.org/ns">
	<p:bbb xmlns:r="thisisSPARTANS">content
		<p:hhz xmlns:z="thisisSPARTANS">RELOAD</p:hhz>
	</p:bbb>
	<ccc>
		<www>RELOAD</www>
	</ccc>		
	<aaa attr="atttrval">value
        <p:title>This is a test of namespaces and my patience</p:title>
		<p:title2>value</p:title2>
	</aaa>	
	
 </people>
XML;

$data = new SimpleXMLElement($xml);
$node = $data->aaa->children();

var_dump($node);
*/



$array = array(1,2,3);
unset($array[count($array)-1]);
$array[] = "2";
echo $array;

/*
foreach ($data->children() as $child) {
	$namespace = $child->getDocNamespaces(false);
	echo "&lt".$child->getName();
	foreach ($namespace as $k =>$v) {
		echo " xmlns:".$k."=".$v;
	}
	echo "&gt<br />";
}
*/

/*
$xml = <<<XML
<?xml version="1.0" standalone="yes"?>
<gente xmlns:p="sample" xmlns="default">
	<p:aaa>Hijo3</p:aaa>
</gente>
XML;

$sxe = new SimpleXMLElement($xml);
foreach ($sxe->children("p",true) as $child) { 
	$child->addChild("sample","content","default");
}
echo $sxe->asXML();

*/
/*
foreach ($sxe->children("p",true) as $child) {
	var_dump($child->__toString());
} */

//$sxe = $sxe->asXML();



/*
$operador = new xml_operator("../include/contenidos3.xml");
$std_data = new stdclass();
$std_data->node_name = "p:clave";
$std_data->nsprefix = "p";
$std_data->node_value = "contenidoa";
$operador->insert_node($std_data,null);

*/

/*
$string ="probando:prueba";
$string2 ="prueba";
$string = explode(":",$string2);
var_dump($string[0]);*/


/*
$operador = simplexml_load_file("../include/contenidos3.xml");
$operador->addChild("key");
$operador->key->addChild("OtherNode");
$operador->asXML("../include/contenidos3.xml");*/
/*
$aaa = "Hola:quetal";
$var = explode(":",$aaa);
if ($aaa )
*/

/*
//Tomamos los namespaces DECLARADOS en el nodo raíz. 
$allNamespaces = $sxe->nnn->ddd->aaa->bbb->getDocNamespaces(true);
var_dump($allNamespaces);
echo "<br /><br />";
//Tomamos los namespaces DECLARADOS en el nodo raíz y en el resto del documento
$allNamespaces = $sxe->getDocNamespaces(true);
var_dump($allNamespaces);
echo "<br /><br />";
//Tomamos los namespaces USADOS en el nodo escogido
$localNamespaces = $sxe->getNamespaces();
var_dump($localNamespaces);
echo "<br /><br />";
//Tomamos los namespaces USADOS en los nodos padre e hijos a partir del nodo actual
$localNamespaces = $sxe->nnn->ddd->aaa->getNamespaces(true);
var_dump($localNamespaces);
echo "<br /><br />";
*/
?>
<?
//$var = null;

?>
<!--
<script type="text/javascript">
	var v = '<?=$var?>';
	alert (typeof(v));
</script>
-->
























