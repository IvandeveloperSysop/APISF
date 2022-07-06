
<form id="logout-form" action="{{ route('logoutAdmin') }}" method="POST" style="display: none;">
    @csrf
</form>

<div class="sidebar ">
    <div class="logo-details">
        {{-- <i class='bx bxl-c-plus-plus'></i> --}}
        <i class='bx bx-store-alt'></i>
        <span class="logo_name">SOCCER <br> FAN</span>

    </div>
    <ul class="nav-links">
        <li>
            {{-- <a href="#">
                <i class='bx bx-grid-alt'></i>
                <span class="link_name">Dashboard</span>
            </a> --}}
            <ul class="sub-menu blank">
                <li><a class="link_name" href="#">Tickets</a></li>
            </ul>
        </li>

        {{-- Tickets --}}
        <li class="showMenu">
            <div class="iocn-link">
                <a href="#">
                    <i class='bx bx-collection'></i>
                    <span class="link_name">Tickets</span>
                </a>
                <i class='bx bxs-chevron-down arrow'></i>
            </div>
            <ul class="sub-menu">
                <li><a class="link_name" href="#">Tickets</a></li>
                <li><a href="{{route('admin')}}" >TICKETS PENDIENTE DE VALIDAR</a></li>
                <li><a href="{{route('aprobados')}}" >TICKETS APROBADOS</a></li>
                <li><a href="{{route('cancelados')}}" >TICKETS CANCELADOS</a></li>
            </ul>
        </li>

        {{-- Compartidos en Redes  --}}
        <li class="showMenu">
            <div class="iocn-link">
                <a href="#">
                    {{-- <i class='bx bx-book-alt'></i> --}}
                    <i class='bx bx-share'></i>
                    <span class="link_name">Compartidos</span>
                </a>
                <i class='bx bxs-chevron-down arrow'></i>
            </div>
            <ul class="sub-menu">
                <li><a class="link_name" href="#">Compartidos en redes</a></li>
                <li><a href="{{route('createApp')}}">Pendientes</a></li>
                <li><a href="{{route('shareAprobe')}}">Aprobados</a></li>
                <li><a href="{{route('shareCancel')}}">Cancelados</a></li>
            </ul>
        </li>

        @if ( Session::get('type_id') == 1 )
            
            {{-- Promociones --}}
            <li>
                <a href="{{route('adminPromociones')}}">
                    {{-- <i class='bx bx-pie-chart-alt-2'></i> --}}
                    <i class='bx bx-purchase-tag-alt'></i>
                    <span class="link_name">Promociones</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="{{route('adminPromociones')}}">Promociones</a></li>
                </ul>
            </li>

            {{-- Premios --}}
            <li>
                <a href="{{route('awardsAdmin')}}" >
                    {{-- <i class='bx bx-line-chart'></i> --}}
                    <i class='bx bx-gift' ></i>
                    <span class="link_name">Premios</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="{{route('awardsAdmin')}}">Premios</a></li>
                </ul>
            </li>

            {{-- Presentaciones --}}
            <li class="showMenu">
                <div class="iocn-link">
                    <a href="#">
                        <i class='bx bxs-coffee'></i>
                    <span class="link_name">Presentaciones</span>
                    </a>
                    <i class='bx bxs-chevron-down arrow'></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" >Presentaciones</a></li>
                    <li><a href="{{route('adminPresentations')}}" >Grupo ARCA</a></li>
                    @if ( Session::get('type_id') == 999 )
                        <li><a href="{{route('adminPresentationsNonArca')}}" >Fuera del grupo ARCA</a></li>
                    @endif
                </ul>
            </li>

            {{-- Tienda --}}
            <li class="showMenu">
                <div class="iocn-link">
                    <a href="#">
                        <i class='bx bx-store' ></i>
                        <span class="link_name">Tienda</span>
                    </a>
                    <i class='bx bxs-chevron-down arrow'></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" >Tienda</a></li>
                    <li><a href="{{route('storeAdmin')}}" >Productos</a></li>
                    {{-- <li><a href="{{route('awardsAdmin')}}" >Productos redimidos</a></li> --}}
                    <li><a href="{{route('ordersAdmin')}}">Ordenes</a></li>
                </ul>
            </li>

            {{-- Pop up --}}
            <li>
                <a href="{{route('popUpAdmin')}}">
                    <i class='bx bx-message-alt-error' ></i>
                    <span class="link_name">Pop up</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="{{route('popUpAdmin')}}">Pop up</a></li>
                </ul>
            </li>

            {{-- Monedero de puntos --}}
            <li>
                <a href="{{route('walletsAdmin')}}" >
                    <i class='bx bx-id-card'></i>
                    <span class="link_name">Monederos</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="{{route('walletsAdmin')}}" >Monederos de puntos</a></li>
                </ul>
            </li>

            {{-- Preguntas --}}
            <li>
                <a href="{{route('adminQuizzes')}}" >
                    {{-- <i class='bx bxs-group'></i> --}}
                    <i class='bx bx-question-mark'></i>
                    <span class="link_name">Preguntas</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="{{route('adminQuizzes')}}">Preguntas</a></li>
                </ul>
            </li>

            {{-- Validadores --}}
            <li>
                <a href="{{route('adminValidators')}}" >
                    <i class='bx bxs-group'></i>
                    <span class="link_name">Usuarios validadores</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="{{route('adminValidators')}}">Usuarios</a></li>
                </ul>
            </li>
        @endif

        <li>
            <a onclick="logoutMessage()">
                <i class='bx bx-log-out'></i>
                <span class="link_name">Cerrar sesión</span>
            </a>
            <ul class="sub-menu blank">
                <li><a class="link_name" onclick="logoutMessage()">Cerrar sesión</a></li>
            </ul>
        </li>

        <li>
            <div class="profile-details">
                <div class="profile-content">
                    <!--<img src="image/profile.jpg" alt="profileImg">-->
                </div>
                <div class="name-job">
                    <div class="profile_name">{{ session()->get('nameAdmin') }}</div>
                    <div class="job">Admin</div>
                </div>
                <i class='bx bx-log-out' onclick="logoutMessage()"></i>
            </div>
        </li>

    </ul>
</div>