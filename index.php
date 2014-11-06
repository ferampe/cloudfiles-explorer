<?
require 'vendor/autoload.php';
use OpenCloud\Rackspace;

// Instantiate a Rackspace client.
$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
    'username' => 'username', // Colocar el username de su cuenta rackspace
    'apiKey'   => 'apiKey'//Colocar el token que esta en el panel de Administracion de rackspace
));


$objectStoreService = $client->objectStoreService(null, 'ORD'); //Colocar la region
$container = $objectStoreService->getContainer('Folders Todos');//Colocar el container
$prefix = (isset($_GET['folder']) ? $_GET['folder'] : '');

$options = array(
    'prefix' => $prefix,
    'limit' => '9000',
    'delimiter' => '/'
);

$objects = $container->objectList($options);
$container->enableCdn();

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Explorer Cloud Files RackSpace</title>
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css">
	
</head>
<body>

<div class="panel panel-default">
  <div class="panel-body">
   
   <ol class="breadcrumb">
   		<li><a href="<? echo $_SERVER["PHP_SELF"] ?>">root</a></li>
	   <? 
	   		$breadcrumb = explode('/', $prefix);

	   		$cant = count($breadcrumb);
	   		$cont = 1;
	   		$link = null;	
	   		foreach($breadcrumb as $b){
	   			$cont++;
	   			$link .= $b.'%2F';

	   			if($cant == $cont){
	   				echo '<li class="active">'.$b.'</li>';
	   			}else{
	   				echo '<li><a href="'.$_SERVER["PHP_SELF"].'?folder='.$link.'">'.$b.'</a></li>';
	   			}   			
	   		}
	   	?>
  

	</ol>


  
<table class="table table-hover">
	<tr><th>Files List</th></tr>
	<?php
	foreach ($objects as $object) {

		if(!preg_match('/\/$/', $object->getName()))
		{
			$name = end(explode('/', $object->getName()));

			if($object->getContentType() == "application/directory")
			{
				echo '<tr><td><i class="glyphicon glyphicon-folder-close"></i> <a href="'.$_SERVER["PHP_SELF"].'?folder='.$object->getName().'%2F">'.$name."</a></td></tr>";
			}else{
				echo '<tr><td><a href="'.$object->getPublicUrl().'"><i class="glyphicon glyphicon-download"></i> '.$name.'</a></td></tr>';
			}
		}
	}
	?>
</table>

</div>
</div>
</body>
</html>