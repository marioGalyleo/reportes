<html>
<head>
<!-- <link rel="stylesheet" type="text/css" href="/galyleo/views/styles/layout.css"/>  -->

<script type="text/javascript">
function loadXMLDoc($id_contenido, $id_pregunta){
	var xmlhttp;
	if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
  		xmlhttp=new XMLHttpRequest();
  	}
	else{// code for IE6, IE5
  		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  	}
	xmlhttp.onreadystatechange=function(){
  		if (xmlhttp.readyState==4 && xmlhttp.status==200){
    		//document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
    	}
  	}
	xmlhttp.open("POST","<?php print($_SERVER['PHP_SELF']);?>?rt=contenidos/asociar_ajax&id_contenido="+$id_contenido+"&id_pregunta="+$id_pregunta,true);
	xmlhttp.send();
}
</script>
</head>
<body>
<div id="myDiv"/>
<table>
<?php
$combo = 'name="contenido" onchange="loadXMLDoc(this.value, this.id)">';
$combo.='<option value="-1">Seleccione un Contenido</option>';
foreach ($contenidos as $contenido){
	$combo.='<option value="'.$contenido->id.'">'.$contenido->nombre.'</option>';
} 
$combo.='</select>';
?>
<?php 
//TODO: mucho trabajo se necesita en esta vista
foreach($preguntas_sin_asociar as $pregunta){
	echo '<tr>';
	echo '<td>'.$pregunta->categoria->nombre.'</td>';
	echo '<td>'.'<select id="'.$pregunta->id.'" '.$combo.'</td>';
	echo '</tr>';
}
?>
</table>
</body>
</html>