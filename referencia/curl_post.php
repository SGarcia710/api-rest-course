<?php

$data = [
  'username' => 'tecadmin',
  'password' => '012345678'
];//se crea un arreglo con la información de login 

$payload = json_encode($data);//La información se codifica en json para poder enviarla a través del post

$ch = curl_init('https://api.example.com/api/1.0/user/login');//se inicia una conexión hacia el servidor de la URL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//el resultado de lo que de el servidor NO sea retornado
curl_setopt($ch, CURLINFO_HEADER_OUT, true);//No nos interesa ver los encabezados
curl_setopt($ch, CURLOPT_POST, true);//acá se le dice que la petición no será hecha por get sino por POST
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);//Dentro de ese post, tendrá que viajar la información que encodeamos previamente
curl_setopt($ch, CURLOPT_HTTPHEADER,
  [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($payload)
  ]
);//Configuramos algunos encabezados que son de interes para que el servidor sepa que se le está enviando

$result = curl_exce($ch); //Hacemos la ejecución de la petición, enviando la información al servidor y se guarda el resultado
curl_close($ch); //cerramos la conexión.