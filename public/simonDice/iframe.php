<?php

$data_array =  array(
   "id_minigame" => $_GET['id'],
   "level_score" => $_GET['level'],
   "points" => $_GET['points'],
);
$url = "https://api.virtualizate.mx/public/api/miniGame/getpoints";
// $url = "http://hidratate.demo:8000/api/miniGame/getpoints";
print_r(json_encode($data_array));
$curl = curl_init();
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data_array));

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
   'Content-Type: application/json',
));

curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

$result = curl_exec($curl);
if(!$result){die("Connection error");}
curl_close($curl);
print_r($result);
