<?php

$matches = [];

//preg_match en este caso verifica si la REQUEST_URI(La url que enviamos) recibida, coincide con el patrón del primer parametro 
//Patrón: un string que empieza con una barra, tiene cualquier cosa que no sea una barra, después una barra y después cualquier cosa que no sea una barra /asdasd/asdasd 

if ( preg_match( '/\/([^\/]+)\/([^\/]+)/', $_SERVER["REQUEST_URI"], $matches ) ) {
  //En caso de que haya coincidencia, lo que hago es generar las variables del $_GET
  $_GET['resource_type'] = $matches[1];
  $_GET['resource_id'] = $matches[2];
  //Por ultimo se delega el control al server.php para que continue como si la llamada se hubiese hecho pasandole los parametros de la URL como siempre
  error_log(print_r($matches, 1));
  require 'server.php';
} elseif ( preg_match( '/\/([^\/]+)\/?/', $_SERVER["REQUEST_URI"], $matches ) ) {
  //Este hace lo mismo, toma el patrón /asdasd, de manera que se refiere solo a una colección, y no a un recurso en particular  
  $_GET['resource_type'] = $matches[1];
  error_log( print_r($matches, 1) );

  require 'server.php';
} else {
  //Si el patrón enviado no coincide con ninguna de las 2, el servidor responderá este error (404: No conozco lo que estás pidiendo)
  error_log('No matches');
  http_response_code( 404 );
}

// Este será el servidor inicial, ejecutará el server.php en casos coincidentes.