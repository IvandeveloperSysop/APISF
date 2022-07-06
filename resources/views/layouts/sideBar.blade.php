
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <br>
            <div class="nav-tickets">

                <li class="nav-item">
                    <a class="nav-link" >
                        <h5>
                            Tickets
                        </h5>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin') ? 'active' : '' }} " aria-current="page" href="{{route('admin')}}" style="margin-left: 15px">
                        <i class="fas fa-list-ul ml-3" style="font-size: 1.1rem"></i>
                        TICKETS PENDIENTE DE VALIDAR
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/ticket/aprobados') ? 'active' : '' }}" href="{{route('aprobados')}}" style="margin-left: 15px">
                        <i class="fas fa-tasks ml-3" style="font-size: 1.1rem"></i>
                        TICKETS APROBADOS
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/ticket/cancelados') ? 'active' : '' }}" href="{{route('cancelados')}}" style="margin-left: 15px">
                        <i class="fas fa-ban ml-3" style="font-size: 1.1rem"></i>
                        TICKETS CANCELADOS
                    </a>
                </li>
            </div>
            <br>
            <li class="nav-item">
                <a class="nav-link" >
                    <h5>
                        Compartidos en redes
                    </h5>
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link {{ Request::is('admin/createAppShare') ? 'active' : '' }}" href="{{route('createApp')}}" style="margin-left: 15px">
                    <i class="fas fa-mobile ml-3" style="font-size: 1.1rem"></i>
                    Compartidos en Redes (Pendientes)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/createAppShare/Aprobe') ? 'active' : '' }}" href="{{route('shareAprobe')}}" style="margin-left: 15px">
                    <i class="fas fa-mobile ml-3" style="font-size: 1.1rem"></i>
                    Compartidos en Redes (Aprobados)
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link {{ Request::is('admin/createAppShare/Cancel') ? 'active' : '' }}" href="{{route('shareCancel')}}" style="margin-left: 15px">
                    <i class="fas fa-mobile ml-3" style="font-size: 1.1rem"></i>
                    Compartidos en Redes (Cancelados)
                </a>
            </li>
            <br>
            <div class="nav-tickets">

                <li class="nav-item">
                    <a class="nav-link" >
                        <h5>
                            Promociones
                        </h5>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/promociones') ? 'active' : '' }} " aria-current="page" href="{{route('adminPromociones')}}" style="margin-left: 15px">
                        <i class="fas fa-list-ul ml-3" style="font-size: 1.1rem"></i>
                        Promociones
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/awards') ? 'active' : '' }} " aria-current="page" href="{{route('awardsAdmin')}}" style="margin-left: 15px">
                        <i class="fas fa-list-ul ml-3" style="font-size: 1.1rem"></i>
                        Premios
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/store') ? 'active' : '' }} " aria-current="page" href="{{route('storeAdmin')}}" style="margin-left: 15px">
                        <i class="fas fa-list-ul ml-3" style="font-size: 1.1rem"></i>
                        Productos en tienda
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/awards') ? 'active' : '' }} " aria-current="page" href="{{route('awardsAdmin')}}" style="margin-left: 15px">
                        <i class="fas fa-list-ul ml-3" style="font-size: 1.1rem"></i>
                        Premios redimidos
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/get/popUps') ? 'active' : '' }} " aria-current="page" href="{{route('popUpAdmin')}}" style="margin-left: 15px">
                        <i class="fas fa-list-ul ml-3" style="font-size: 1.1rem"></i>
                        Pop up
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/wallets') ? 'active' : '' }} " aria-current="page" href="{{route('walletsAdmin')}}" style="margin-left: 15px">
                        <i class="fas fa-list-ul ml-3" style="font-size: 1.1rem"></i>
                        Monederos de puntos
                    </a>
                </li>
            </div>
            <br>
            <li class="nav-item" id="logutHamburg">
                <a class="nav-link" onclick="logoutMessage()" style="font-size: 1.5rem">
                    <i class="fas fa-door-open ml-3" style="font-size: 1.5rem"></i>
                    Cerrar sesion
                </a>
            </li>

        </ul>
    </div>
</nav>
