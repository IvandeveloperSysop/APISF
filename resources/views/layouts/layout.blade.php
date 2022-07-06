
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Soccer FAN | admin</title>
    

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/dashboard/">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.1/css/all.css"; />

    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    
    <meta name="theme-color" content="#7952b3">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- Custom styles for this template -->
    <link href="{{asset('css/dashboard.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/navStyle.css')}}">
    <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}"/>
    <!-- Boxiocns CDN Link -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    {{-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> --}}
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
        #logutHamburg{
          display: none;
        }
      }

      @media (max-width: 769px) {
        #logutInput{
          display: none;
        }

        #logutHamburg{
          display: block;
        }
      }

    </style>
    @yield('css')

  </head>
  <body>
    
    {{-- <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
      <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">{{Session::get('promoName')}}</a>
      <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search">
      <ul class="navbar-nav px-3 d-flex">
        <li class="nav-item">
          {{ session()->get('nameAdmin') }} <span class="caret"></span>
        </li>

        <li class="nav-item" id="logutInput">
          <a class="nav-link" onclick="logoutMessage()">
            <i class="fas fa-door-open ml-3"></i>
            Cerrar sesion
          </a>
        </li>
        <form id="logout-form" action="{{ route('logoutAdmin') }}" method="POST" style="display: none;">
          @csrf
        </form>

      </ul>
    </header> --}}

    <div class="container-fluid">
      <div class="row">
        {{-- @include('layouts.sideBar') --}}
        @include('layouts.navbar')
        
        <section class="home-section">
          <div class="home-content">
            <i class='bx bx-menu'></i>
            {{-- <span class="text">Drop Down Sidebar</span> --}}
          </div>
          <div class="container-fluid">
            <div class="row justify-content-center">
              <div class="col-md-11">

                @yield('content')
    
    
                @yield('modals')
              </div>
            </div>

          </div>
          
        </section>
      </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" integrity="sha384-zNy6FEbO50N+Cg5wap8IKA4M/ZnLJgzc6w2NqACZaK0u0FXfOWRRJOnQtpZun8ha" crossorigin="anonymous"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.jsdelivr.net/npm/promise-polyfill"></script>
    <script src="{{asset('js/dashboard.js')}}"></script>
    <script>
      function logoutMessage(){
        Swal.fire({
          text: "Deseas cerrar sesión?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes'
        }).then((result) => {
          if (result.isConfirmed) {
            document.getElementById("logout-form").submit();
          }
        })
      }
    </script>
    <script>
      let arrow = document.querySelectorAll(".arrow");
      for (var i = 0; i < arrow.length; i++) {
          arrow[i].addEventListener("click", (e) => {
              let arrowParent = e.target.parentElement.parentElement; //selecting main parent of arrow
              arrowParent.classList.toggle("showMenu");
          });
      }
      let sidebar = document.querySelector(".sidebar");
      let sidebarBtn = document.querySelector(".bx-menu");
      console.log(sidebarBtn);
      sidebarBtn.addEventListener("click", () => {
          sidebar.classList.toggle("close");
      });

    </script>
    <script>

      function loading(){
        let timerInterval
        Swal.fire({
            title: 'Cargando..',
            didOpen: () => {
                Swal.showLoading()
            },
            willClose: () => {
                clearInterval(timerInterval)
            }
        }).then((result) => {
            /* Read more about handling dismissals below */
            if (result.dismiss === Swal.DismissReason.timer) {
                console.log('I was closed by the timer')
            }
        })
      }


      function alertSuccess(){
        Swal.close();
        
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Cambios guardados',
            showConfirmButton: false,
            timer: 1500
        });
      }
    </script>
    <script src="{{ asset('js/routeGlobal.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/filters.js') }}" ></script>
    @yield('js')
    
  </body>
</html>
