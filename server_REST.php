<?php
// Definimos los recursos disponibles
$allowedResourceTypes = [
  'books',
  'authors',
  'genres'
];


// Validamos que el recurso esté disponible
$resourceType = $_GET['resource_type'];

if (  !in_array( $resourceType, $allowedResourceTypes) ){
  die;
} 

// Defino los recursos

$books = [
  1 => [
    'title' => 'Lo que el viento se llevó',
    'id_author' => 2,
    'id_genero' => 2,
  ],
  2 => [
    'title' => 'La Iliada',
    'id_author' => 1,
    'id_genero' => 1,
  ],
  3 => [
    'title' => 'Ciudades de Papel',
    'id_author' => 3,
    'id_genero' => 3,
  ],
];

// Le avisamos al usuario que lo que recibe es un json (cortesias)
header('Content-Type: application/json');

// Levantamos el id del recurso buscado
$resourceId = array_key_exists( 'resource_id', $_GET ) ? $_GET['resource_id'] : ''; // If ternario que pregunta si en el GET se envió por parametro el resource_id

// Generamos la respuesta asumiendo que el pedido es correcto
switch ( strtoupper($_SERVER['REQUEST_METHOD'])  ) {
  case 'GET':
  if ( empty( $resourceId ) ) {// Si el resourceId está vacio
    echo json_encode( $books );// devolvemos la colección completa
  } else { // Si el usuario si envió el Id
    if ( array_key_exists( $resourceId, $books ) ) { // Si este Id existe en la colección 
      echo json_encode( $books[ $resourceId ] );// Devolvemos el caso en particular
    } else {
      echo 'Bad key';
    }
  }
    break;
  case 'POST':
    break;
  case 'PUT':
    break;
  case 'DELETE':
    break;
}

//Para levantar el servidor web uso php -S localhost:8000 archivoAUsar
