<?php

	$data_array =  array(
	   "id_minigame" => $_GET['id'],
	   "level_score" => $_GET['level'],
	   "points" => $_GET['points'],
	);
	$url = "https://api.virtualizate.mx/public/api/miniGame/getpoints";
	// $url = "http://api.guiasde.com/hidratate/public/api/miniGame/getpoints";
	// $url = "http://hidratate.demo:8000/api/miniGame/getpoints";

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
	print_r($result);
	if(!$result){
		
		die("Connection error");
	}
	curl_close($curl);
	

?><!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Simon Dice</title>
  <link rel="stylesheet" href="css/style.css">

  <!--<meta name="viewport" content="width=device-width, initial-scale=.6, maximum-scale=.6, user-scalable=no" /> -->
  <script src="_site/js/jquery-2.0.3.min.js" type="text/javascript"></script>
  <script src="_site/js/jquery.cookie.js" type="text/javascript"></script>
  
  <style>
    body {
      position: absolute;
      width: 100%;
      height: 100%;
      background-color: #000;
      margin: 0;
    }
  </style>
</head>
<body>
	<?php if($_GET['points'] > 0) {?>
	<h2 class="home-text">¡FELICIDADES! GANASTE <br> <?php echo $_GET['points'] ?> punto(s)</h2>
	<?php } else {?>
	<h2 class="home-text">¡UPS! NO GANASTE PUNTOS. SIGUE PARTICIPANDO</h2>
	<?php }?>
</body>
</html>