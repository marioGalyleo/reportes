<html>
<head>
<title>Listado de contenidos</title>
</head>
<body>
<table border="1">
<tr>
<th>Nombre</th>
<th>Link Repaso</th>
<th>Frase No Logrado</th>
<th>Frase Logrado</th>
<th>opciones</th>
</tr>
<?php 
foreach ($contenidos as $contenido){
	echo '<tr>';
	echo '<td>'.$contenido->nombre.'</td>';
	echo '<td>'.$contenido->linkRepaso.'</td>';
	echo '<td>'.$contenido->fraseNoLogrado.'</td>';
	echo '<td>'.$contenido->fraseLogrado.'</td>';
?>
	<td>
	<a href="<?php print($_SERVER['PHP_SELF']);?>?rt=contenidos/eliminar&id=<?php echo $contenido->id;?>">eliminar</a>
	<a href="<?php print($_SERVER['PHP_SELF']);?>?rt=contenidos/editar&id=<?php echo $contenido->id;?>">modificar</a>	
	</td>
<?php
	echo '</tr>'; 
}
?>

</table>
<br/>
<a href="<?php echo str_replace("index.php","",$_SERVER['PHP_SELF']);?>contenidos/agregar">Nuevo Contenido</a><br/>
<a href="<?php echo str_replace("index.php","",$_SERVER['PHP_SELF']);?>contenidos/asociar">Asociar Contenido a Preguntas</a>
</body>
</html>
