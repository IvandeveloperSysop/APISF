<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pruebas</title>
</head>
<body>
    <form action="{{route('createTicket')}}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="form-group">
          <label for="">Archivo</label>
        </div>

        <div class="form-group">
            <label for="">Amount</label>
            <input type="number" class="form-control" id="amoun" name="amoun" aria-describedby="hoola">
        </div>

        <div class="form-group">
            <label for="">Token</label>
            <input type="text" class="form-control" id="token" name="token" value="Fe32dolr4wCvZFSfM1vfrPoqb2UlS9" aria-describedby="hoola">
        </div>
        
        <div class="form-group">
            <label for="">extension</label>
            <input type="text" class="form-control" id="extension" name="extension" value="jpg" aria-describedby="hoola">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</body>
</html>