<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Simon Dice</title>

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

  <div id="phaser-example"></div>

  <script type="text/javascript">
	  
    var width = $(window).width();
    var vHeight = $(window).height();
    var mx = 1;
    var id = <?php echo $_GET['id'] ?>;

    if (width < 600) {
      mx = 2
    }

    var game = new Phaser.Game(600 / mx, 600 / mx, Phaser.CANVAS, 'phaser-example', { preload: preload, create: create, update: update, render: render });

    function preload() {
      if (mx == 1) {
        game.load.spritesheet('item', 'assets/buttons/minigameLG.png', 160 / mx, 160 / mx);
      } else {
        game.load.spritesheet('item', 'assets/buttons/minigameSM.png', 160 / mx, 160 / mx);
      }
    }

    var simon;
    var N = 1;
    var userCount = 0;
    var currentCount = 0;
    var sequenceCount = 16;
    var sequenceList = [];
    var simonSez = false;
    var timeCheck;
    var litSquare;
    var winner;
    var loser;
    var intro;
    var points;

function create() {

  simon = game.add.group();
  var item;

  for (var i = 0; i < 3; i++)
  {
    item = simon.create((50 + 168 * i) / mx, 150 / mx, 'item', i);
    // Enable input.
    item.inputEnabled = true;
    item.input.start(0, true);
    item.events.onInputDown.add(select);
    //item.events.onInputUp.add(release);
    item.events.onInputOut.add(moveOff);
    simon.getAt(i).alpha = 0;
  }

  for (var i = 0; i < 3; i++)
  {
    item = simon.create((50 + 168 * i) / mx, 318 / mx, 'item', i + 3);
    // Enable input.
    item.inputEnabled = true;
    item.input.start(0, true);
    item.events.onInputDown.add(select);
    //item.events.onInputUp.add(release);
    item.events.onInputOut.add(moveOff);
    simon.getAt(i + 3).alpha = 0;
  }

  introTween();
  setUp();
  setTimeout(function(){simonSequence(); intro = false;}, 6000);

}

function restart() {
  N = 1;
  userCount = 0;
  currentCount = 0;
  sequenceList = [];
  winner = false;
  loser = false;
  introTween();
  setUp();
  setTimeout(function(){simonSequence(); intro=false;}, 6000);
}

function introTween() {

  intro = true;
  for (var i = 0; i < 6; i++)
  {
    var flashing = game.add.tween(simon.getAt(i)).to( { alpha: 1 }, 500, Phaser.Easing.Linear.None, true, 0, 4, true);
    var final = game.add.tween(simon.getAt(i)).to( { alpha: .25 }, 500, Phaser.Easing.Linear.None, true);

    flashing.chain(final);
    flashing.start();
  }

}

  function update() {

    if (simonSez)
    {
      if (game.time.now - timeCheck >700-N*40)
      {
        simon.getAt(litSquare).alpha = .25;
        game.paused = true;

        setTimeout(function()
        {
          if ( currentCount< N)
          {
            game.paused = false;
            simonSequence();
          }
          else
          {
            simonSez = false;
            game.paused = false;
          }
        }, 400 - N * 20);
      }
    }
  }

  function playerSequence(selected) {

    correctSquare = sequenceList[userCount];
    userCount++;
    thisSquare = simon.getIndex(selected);

    if (thisSquare == correctSquare)
    {
      if (userCount == N)
      {
        if (N == sequenceCount)
        {
          winner = true;
          //setTimeout(function(){restart();}, 3000);
        }
        else
        {
          userCount = 0;
          currentCount = 0;
          N++;
          simonSez = true;
        }
      }
    }
    else
    {
      loser = true;
      //setTimeout(function(){restart();}, 3000);
    }

  }

  function simonSequence () {

    simonSez = true;
    litSquare = sequenceList[currentCount];
    simon.getAt(litSquare).alpha = 1;
    timeCheck = game.time.now;
    currentCount++;

  }

  function setUp() {

    for (var i = 0; i < sequenceCount; i++)
    {
      thisSquare = game.rnd.integerInRange(0,5);
      sequenceList.push(thisSquare);
    }

  }

  function select(item, pointer) {

    if (!simonSez && !intro && !loser && !winner)
    {
      if(item.alpha == .25) {
        item.alpha = 1;
        setTimeout(function(){
          item.alpha = .25;
          playerSequence(item);
        }, 250);
      }
    }

  }

  function release(item, pointer) {

    if (!simonSez && !intro && !loser && !winner)
    {
      setTimeout(function(){}, 30);
      if(item.alpha == 1) {
        item.alpha = .25;
        playerSequence(item);
      }
    }
  }

  function moveOff(item, pointer) {

    if (!simonSez && !intro && !loser && !winner)
    {
      item.alpha = .25;
    }
  }

  function render() {
    if (!intro)
    {
      if (simonSez)
      {
        game.debug.text('Simon Dice', 260 / mx, 96 / mx, 'rgb(255, 255, 255)');
      }
      else
      {
        game.debug.text('Tu turno', 260 / mx, 96 / mx, 'rgb(255,255, 255)');
      }
    }
    else
    {
      game.debug.text('Listos!', 260 / mx, 96 / mx, 'rgb(255, 255, 255)');
    }

    if (winner)
    {
      points = Math.floor(currentCount / 5);
    game.debug.text('Fin del juego', 260 /mx, 32 / mx, 'rgb(255, 255, 255)');
      //game.debug.text('', 260 / mx, 96 / mx, 'rgb(0,255,0)');
    // Simulate an HTTP redirect:
    game.stage.disableVisibilityChange = true;
    game.destroy();
    window.location.href =  "finish.php?id="+id+"&level="+currentCount+"&points="+points;
      //game.debug.text('Felicidades, ganaste ' + points + ' punto(s) extra', 130 / mx, 32 / mx, 'rgb(0,0,255)');
    }
    else if (loser)
    {
      points = Math.floor(currentCount / 5);
      game.debug.text('Fin del juego', 260 /mx, 32 / mx, 'rgb(255, 255, 255)');
      //game.debug.text('', 260, 96, 'rgb(0,255,0)');
      game.stage.disableVisibilityChange = true;
      game.destroy();
      window.location.href =  "finish.php?id="+id+"&level="+currentCount+"&points="+points;
      //game.debug.text('Felicidades, ganaste ' + points + ' punto(s) extra', 130 / mx, 32 / mx, 'rgb(0,0,255)');
    }
  }

  $(document).ready(function(){
  // window.focus();
  });

</script>


</body>
</html>