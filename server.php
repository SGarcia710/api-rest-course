<?php
//Autenticación por pedido http ejemplo: curl http://sebastian:1234@localhost:8000/books | jq
//Si efectivamente hay un usuario/contraseña registrado en el $_SERVER, vamos a obtener su valor
// $user = array_key_exists( 'PHP_AUTH_USER', $_SERVER ) ? $_SERVER['PHP_AUTH_USER'] : '';
// $pwd = array_key_exists( 'PHP_AUTH_PW', $_SERVER ) ? $_SERVER['PHP_AUTH_PW'] : '';

// if ( $user !== 'sebastian' || $pwd !== '1234'){
//   die;
// }

//HAMC ejemplo: curl http://localhost:8000/books -H 'X-HASH: 0d1d84bb0563611c010e3ce7f0597b07673f6afc' -H 'X-UID: 1' -H 'X-TIMESTAMP: 1569306255' | jq
// Esta forma de autentificación utiliza los hash generados por generate_hash.php
// if (
//   !array_key_exists('HTTP_X_HASH', $_SERVER) ||
//   !array_key_exists('HTTP_X_TIMESTAMP', $_SERVER) ||
//   !array_key_exists('HTTP_X_UID', $_SERVER)
// ){
//   die;
// }

// list( $hash, $uid, $timestamp ) = [
//   $_SERVER['HTTP_X_HASH'],
//   $_SERVER['HTTP_X_UID'],
//   $_SERVER['HTTP_X_TIMESTAMP'],
// ];

// $secret = 'Sh!! No se lo cuentes a nadie!';

// $newHash = sha1($uid.$timestamp.$secret);

// if ( $newHash !== $hash ){
//   die;
// }

//Acces Tokens: ejemplo: curl http://localhost:8000/books -H 'X-Token: c4b02a1525349e7888d4140dcd524aff2d6296dd' | jq
// if ( !array_key_exists('HTTP_X_TOKEN', $_SERVER) ) {
//   die; //El cliente no está autenticado porque no tiene token
// };

// $url = 'http://localhost:8001'; //Aquí es donde estará escuchando el servidor de autentificación (auth_server.php)

// $ch = curl_init( $url );
// curl_setopt(//Le configuro esta opción, para que obtenga el token que se ha enviado mediante el pedido
//   $ch,
//   CURLOPT_HTTPHEADER,
//   [
//     "X-Token: {$_SERVER['HTTP_X_TOKEN']}",
//   ]
//   );
// curl_setopt(//Le configuro esta otra opción, que nos permitirá obtener el resultado de lo que el servidor nos está devolviendo
//   $ch,
//   CURLOPT_RETURNTRANSFER,
//   true
// );

// $ret = curl_exec( $ch ); //Realizamos la llamada del Curl.

// if( $ret !== 'true' ) {
//   die;
// }
//Aquí terminan las autentificaciones.

// Definimos los recursos disponibles
$allowedResourceTypes = [
  'books',
  'authors',
  'genres'
];

// Validamos que el recurso esté disponible
$resourceType = $_GET['resource_type'];

if ( !in_array( $resourceType, $allowedResourceTypes) ){
  http_response_code(400);
  die;
} 

// Definimos los recursos
$books = [
  1 => [
    'title' => 'Lo que el viento se llevó',
    'id_author' => 2,
    'id_genre' => 2,
  ],
  2 => [
    'title' => 'La Iliada',
    'id_author' => 1,
    'id_genre' => 1,
  ],
  3 => [
    'title' => 'Ciudades de Papel',
    'id_author' => 3,
    'id_genre' => 3,
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
      echo json_encode( $books );// Devolvemos la colección completa
    } else { // Si el usuario si envió el Id
      if ( array_key_exists( $resourceId, $books ) ) { // Si este Id existe en la colección 
        echo json_encode( $books[ $resourceId ] );// Devolvemos el caso en particular
      } else { // Si este ID no existe
        http_response_code(404);
      }
    }
    break;
  case 'POST':
    // Tomamos la entrada "Cruda"
    $json = file_get_contents('php://input');
    // Transformamos el Json recibido a un nuevo elemento del Array de libros
    $books[] = json_decode( $json, true );
    //Emitimos hacia la salida la ultima clave del arreglo de libros
    echo array_keys( $books )[ count($books) - 1]; 
    //echo json_encode($books);
    break;
  case 'PUT':
    // Validamos que se envié un ID y que el recurso buscado exista 
    if ( !empty($resourceId) && array_key_exists( $resourceId, $books)){
      // Tomamos la entrada "Cruda"
      $json = file_get_contents('php://input');
      // Transformamos el Json recibido a un nuevo elemento Array
      $books[ $resourceId ] = json_decode( $json, true ); 
      // Retornamos la colección modificada y formato JSON
      echo json_encode( $books );
    }
    break;
  case 'DELETE':
    // Validamos que el recurso exista
    if ( !empty($resourceId) && array_key_exists( $resourceId, $books)){
      // Eliminamos el recurso
      unset( $books[ $resourceId ] );
    }
    // Retornamos la colección con los cambios y formato JSON
    echo json_encode( $books );
    break;
}

//Para levantar el servidor web uso php -S localhost:8000 archivoAUsar
//Para ver los headers con curl: curl -v http://localhost:8000/books/9
//file_get_contents() Lee un archivo por completo y devuelve su contenido
//json_decode() recibe como segundo parametro un true si quiero que sea un ARRAY, de lo contrario, me devuelve un OBJETO

//Ejemplo de GET: curl "http://localhost:8000?resource_type=books&resource_id=1" | jq
//Ejemplo de POST: curl -X "POST" http://localhost:8000/books -d '{"titulo": "Nuevo Libro", "id_autor": 1, "id_genero": 2}'
//Ejemplo de PUT: curl -X "PUT" http://localhost:8000/books/2 -d '{"titulo": "Nuevo Libro 2", "id_autor": 1, "id_genero": 2}' | jq
//Ejemplo de DELETE: curl -X "DELETE" http://localhost:8000/books/2 | jq