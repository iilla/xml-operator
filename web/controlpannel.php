<html>
	<head>
		<script type="text/javascript" src="<?="../script/js/jquery-1.9.1.min.js"?>"></script>
		<script type="text/javascript">
			function myFunction() {
				/*
				var b = "hello";
				
				var cdata_regular_expression = /^(<!\[CDATA\[)(.)+(\]\]>)$/;
				var sample = false;
				var string ="<![CDATA[Hola que tal]]>";
				var string2 ="Holaquetal";

				if (string.match(cdata_regular_expression)) {
					$("#checkbox")[0].checked = true;
				}
				
				string = "h&amp;&lt;hhh";
				$("#content2").val(decodeHTMLEntities(string));
			}	
			
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
			}*/
		</script>
	</head>
	<body>
		<script type="text/javascript">
			$(document).ready(function() {
				var dir_path_array = new Array();
				var actual_path_index = -1;
				displayRoute(0);

				$("#show_route").click(function() {
					displayRoute(0);
				});
			
				$("#next").click(function() {
					if (actual_path_index < dir_path_array.length-1) {actual_path_index++;}
					$("#route_path").val(dir_path_array[actual_path_index]);
					displayRoute("navigation");							
				});

				$("#prev").click(function(ev) {
					ev.preventDefault();
					if(actual_path_index>0) {actual_path_index--;}
					$("#route_path").val(dir_path_array[actual_path_index]);
					displayRoute("navigation");
				});		
				
				function displayRoute(specifications) {
					var directory = $("#route_path").val();
					$.ajax({
						type: 'POST',				
						url: "../script/php/xo_controlpannel_json.php",
						data: {
							directory:directory,
							specs:specifications
						},
						dataType: 'json',						// Tipo de parametros que devuelve el php
						//contentType: 'application/json',		// Tipo de parametros que le enviamos al php. Si añadimos esta linea en el paso de parametros por json, entonces no funciona !!!				
						beforeSend: function(x) {
							if (x && x.overrideMimeType) x.overrideMimeType("application/j-son;charset=UTF-8");
						},
						error: function(XMLHttpRequest, errorText, errorThrown) {
							//alert("Ha ocurrido un error: "+errorText+" "+errorThrown);
						},		
						success: function (result) {
							if (!result.errors) {
								if (!result.fromNavigation) {
									var actualPath = $("#route_path").val();
									var new_arrayPath = new Array();
									for (var i=0;i<=actual_path_index;i++) {
										new_arrayPath[i] = dir_path_array[i];
									}
									dir_path_array = new_arrayPath;
									dir_path_array.push(actualPath);
									actual_path_index = (dir_path_array.length-1);
								} else {
									var actualPath = dir_path_array[actual_path_index];
								}
							}
							
							$("#dirContent").html(result.filelist);
							//console.log(dir_path_array+" INDICE ACTUAL:"+actual_path_index);

							$(".reflink").click(function(ev) {
								ev.preventDefault();
								var route = this.id;
								$("#route_path").val(route);
								displayRoute(0);
							});		
							
							$(".xmlfile").click(function(ev) {
								ev.preventDefault();
								var route = $("#route_path").val();
								var route = route+this.innerHTML;
								//var spanElement = this;
								//spanElement.style("color","blue");
								//alert(this);
								$("#filePath").val(route);
							});	
							
 							$(".parentLink").click(function(ev) {
								ev.preventDefault();
								$("#prev").click();
							});								
						},	
					});		
				}	
			});
		</script>
		
		<div id="dirList">
			<div id="route">
				<input id="route_path" name="route_path" type="text" value="/" />
				<input id="show_route" type="button" value="cargarRuta" />
				<input id="next" type="button" value="next" />
				<input id="prev" type="button" value="prev" />				
			</div>
			<div id="dirContent">

			</div>
			<form name="makeFile" action="./index.php" method="POST">
				<input id="filePath" name="filePath" type="hidden" value="" />
				<input id="openFile" name="openFile" type="submit" value="Open" />
			</form>
		</div>
	</body>
	
</html>
