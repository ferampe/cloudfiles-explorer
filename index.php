<?
require 'vendor/autoload.php';
use OpenCloud\Rackspace;

// Instantiate a Rackspace client.
$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
    'username' => 'username', // Colocar el username de su cuenta rackspace
    'apiKey'   => 'apiKey'//Colocar el token que esta en el panel de Administracion de rackspace
));


$objectStoreService = $client->objectStoreService(null, 'DFW'); //Colocar la region
$container = $objectStoreService->getContainer('photos');//Colocar el container
$prefix = (isset($_GET['folder']) ? $_GET['folder'] : '');

$options = array(
    'prefix' => $prefix,
    'limit' => '100',
    'delimiter' => '/'
);

$objects = $container->objectList($options);

// Activar URL Temporales
$account = $objectStoreService->getAccount();
$account->setTempUrlSecret('asda65468784643');
$expirationTimeInSeconds = 1800; // Media hora
$httpMethodAllowed = 'GET';

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

		//Solo para impresion de nombres
		//Quita el ultimo slash
		$name = end(explode('/', preg_replace('/\/$/', "", $object->getName())));

		if($object->isDirectory())
		{
			echo '<tr><td><i class="glyphicon glyphicon-folder-close"></i> <a href="'.$_SERVER["PHP_SELF"].'?folder='.$object->getName().'">'.$name."</a></td></tr>";
		}else{
			$tempUrl = $object->getTemporaryUrl($expirationTimeInSeconds, $httpMethodAllowed);

			echo '<tr><td><a href="'.$tempUrl.'"><i class="glyphicon glyphicon-download"></i> '.$name.'</a></td></tr>';
		}


	}
	?>
</table>

</div>
</div>
</body>
</html>