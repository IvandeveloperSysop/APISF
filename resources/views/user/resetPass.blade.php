
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/docs/4.0/assets/img/favicons/favicon.ico">

    <title>Signin Template for Bootstrap</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/sign-in/">

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- Custom styles for this template -->
    <link href="signin.css" rel="stylesheet">
  </head>

  <body class="text-center">
      <div class="row justify-content-md-center mt-5">
          <div class="col-md-5 mt-5">
              <form class="form-signin" id="formPass" action="{{route('change-Password')}}" method="POST">
                @csrf
                <div id="alerts">
                    
                </div>
                <input type="hidden" value="{{ $userToken }}" name="tokenUser" id="tokenUser">
                <h1 class="h3 mb-3 font-weight-normal">Cambiar contraseña</h1>
                <div class="my-3">
                    <label for="inputPassword" class="sr-only">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                </div>

                <div class="my-3">
                    <label for="inputPassword" class="sr-only">Confirmar contraseña</label>
                    <input type="password" id="confirmPassword" class="form-control" placeholder="Confirmar contraseña" required>
                </div>

                
            </form>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <button class="btn btn-lg btn-primary btn-block" onclick="validPass()">Guardar</button>
                </div>
            </div>
          </div>
      </div>

      <script>
          function validPass(){
              pass = document.getElementById('password').value;
              confirmPass =  document.getElementById('confirmPassword').value;

              if(pass === confirmPass){
                document.getElementById('formPass').submit();
                document.getElementById('password').classList.remove('is-invalid');
                document.getElementById('confirmPassword').classList.remove('is-invalid');
                document.getElementById('alerts').innerHTML = "";
                return;
              }else{
                document.getElementById('password').classList.add('is-invalid');
                document.getElementById('confirmPassword').classList.add('is-invalid');
                document.getElementById('alerts').innerHTML = "<div class='alert alert-danger' role='alert'> Las contraseñas no coinciden </div>"
                return;
              }
          }
      </script>
  </body>
</html>
