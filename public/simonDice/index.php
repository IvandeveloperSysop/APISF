<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Simon Dice</title>
  <link rel="stylesheet" href="css/style.css">

  <!--<meta name="viewport" content="width=device-width, initial-scale=.6, maximum-scale=.6, user-scalable=no" /> -->
  <script src="_site/js/jquery-2.0.3.min.js" type="text/javascript"></script>
  <script src="_site/js/jquery.cookie.js" type="text/javascript"></script>
  <script src="_site/phaser/phaser.2.7.7.min.js" type="text/javascript"></script>
  <style>
    body {
      position: absolute;
      width: 100%;
      height: 100%;
      background-color: #000;
      margin: 0;
    }
    #phaser-example {
      text-align: center;
    }
    canvas {
      display: inline !important;
    }

  </style>
</head>
<body>
	<h2 class="home-text">VIRTUALÍZATE con SIMON DICE</h2>
	<img class="home-img" src="assets/buttons/minigameLG.png" />
	<a class="home-btn" href="minigame.php?id=<?php echo $_GET['id'] ?>">EMPEZAR JUEGO</a>
</body>
</html>