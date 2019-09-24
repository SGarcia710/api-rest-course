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

    //Ejemlo de GET con curl: curl "http://localhost:8000?resource_type=books&resource_id=1" | jq
  case 'POST':
    //file_get_contents()Lee un archivo por completo y devuelve su contenido
    $json = file_get_contents('php://input');//Tomamos la entrada "Cruda"
    //El json_decode() recibe como segundo parametro un true si quiero que sea un ARREGLo, de lo contrario, me devuelve un OBJETO
    $books[] = json_decode( $json, true );//Transformamos el Json recibido a un nuevo elemento del Array de libros

    echo array_keys( $books )[ count($books) - 1]; //Emitimos hacia la salida la ultima clave del arreglo de libros
    //echo json_encode($books);
    //Eejemplo de post con curl : curl -X "POST" http://localhost:8000/books -d '{"titulo": "Nuevo Libro", "id_autor": 1, "id_genero": 2}'
    break;
  case 'PUT':
    //Validamos que se envié un ID y que el recurso buscado exista 
    if ( !empty($resourceId) && array_key_exists( $resourceId, $books)){
      //Tomamos la entrada "Cruda"
      $json = file_get_contents('php://input');
      //Transformamos el Json recibido a un nuevo elemento Array
      $books[ $resourceId ] = json_decode( $json, true ); 
      // Retornamos la colección modificada y formato JSON
      echo json_encode( $books );

    }
    //Ejemplo de PUT curl -X "PUT" http://localhost:8000/books/2 -d '{"titulo": "Nuevo Libro 2", "id_autor": 1, "id_genero": 2}' | jq
    break;
  case 'DELETE':
    //Validamos que el recurso exista
    if ( !empty($resourceId) && array_key_exists( $resourceId, $books)){
      //Eliminamos el recurso
      unset( $books[ $resourceId ] );
    }
    // Retornamos la colección con los cambios y formato JSON
    echo json_encode( $books );
    //Ejemplo de DELETE curl -X "DELETE" http://localhost:8000/books/2 | jq
    break;
}

//Para levantar el servidor web uso php -S localhost:8000 archivoAUsar
