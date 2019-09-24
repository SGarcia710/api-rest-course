<?php

// echo file_get_contents('https://xkcd.com/info.0.json').PHP_EOL;
//Imprimo el contenido sacado de la URL y con PHP_EOL hago un salto de linea.
$json = file_get_contents('https://xkcd.com/info.0.json');//String llamado json

$data = json_decode( $json, true ); //Esto decodifica lo que le entra como un objeto
//Se le pasa true para que lo decofique como un arreglo

echo $data['img'].PHP_EOL; //Muestro la key img y hago un salto de linea 