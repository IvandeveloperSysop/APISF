@extends('layouts.layout')
@section('css')
    <link href="{{asset('css/imagestyle.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/image-zoom.css')}}">
    {{-- <link rel="stylesheet" href="{{ asset('css/cardAward.css') }}"> --}}
    <style>
        .tableDetails{
            height: 366px;
        }
        td, th{
            vertical-align: middle;
        }

        @media (min-width: 576px){}
            .modal-dialog {
                max-width: 962px;
            }
        }
    </style>
   
@endsection
@section('content')
    <div class=" d-flex my-4">
        <div class="">
            <a type="button" class="btn btn-outline-primary me-3 btn-sm" href="{{route('adminPromociones')}}">
                <i class="fas fa-arrow-left me-1"></i>Regresar
            </a>
        </div>
    </div>
    
    <h1 class="">Promoción</h1>
    
    <form class="mb-5">
        <div class="row justify-content-center">

            <div class="col-md-5">

                <div class="card  container_foto " data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12 ">
                            <div class="ver_mas text-center">
                                <span ><i class="fas fa-eye"></i></span>
                            </div>
                            <img src="{{ $routeGlobal }}/{{$promo->image}}">
                        </div>
                    </div>
                </div>

                @if ($promo->promo_id != 1)
                    <div class="mb-3">
                        <label for="user" class="form-label">Imagen logo: </label>
                        <div class="input-group mb-3">
                            <input type="file" class="form-control" id="addImagePromo" name="addImagePromo" accept="image/*" data-type='image' onchange="changeImgToB64Promo('addImagePromo')">
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-5">

                <div class="card  container_foto " data-bs-toggle="modal" data-bs-target="#staticBackdropBanner">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12 ">
                            <div class="ver_mas text-center">
                                <span ><i class="fas fa-eye"></i></span>
                            </div>
                            <img src="{{ $routeGlobal }}{{$promo->imageBanner}}">
                        </div>
                    </div>
                </div>


                @if ($promo->promo_id != 1)
                    <div class="mb-3">
                        <label for="user" class="form-label">Imagen banner: </label>
                        <div class="input-group mb-3">
                            <input type="file" class="form-control" id="addImageBanner" name="addImageBanner" accept="image/*" data-type='image' onchange="changeImgToB64Banner('addImageBanner')">
                        </div>
                    </div>
                @endif

            </div>


            <div class="col-md-5 mb-2 mt-4">

                <div class="mb-3">
                    <label for="promoTitle" class="form-label">Nombre de la promoción: </label>
                    <input type="text" class="form-control" id="promoTitle" name="promoTitle" value="{{$promo->title}}" >
                </div>

                
                <div class="mb-3">
                    <label for="begin_date" class="form-label">Fecha d einicio de la promoción: </label>
                    <input type="date" data-date-format="dd/mm/yyyy" class="form-control" id="begin_date" name="begin_date" value="{{ \Carbon\Carbon::parse($promo->begin_date)->format('Y-m-d') }}" >
                </div>

                <div class="mb-3">
                    <label for="end_date" class="form-label">Fecha de fiinalización de la promoción: </label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ \Carbon\Carbon::parse($promo->end_date)->format('Y-m-d') }}" >
                </div>

                @if ($promo->promo_id != 1)
                    <div class="mb-3">
                        <label for="awards_qty" class="form-label">Nº de ganadores de la promoción: </label>
                        <input type="number" class="form-control" id="awards_qty" name="awards_qty" value="{{$promo->awards_qty}}" >
                    </div>
                @endif    

                <div class="mb-3">
                    <label for="user" class="form-label">Estatus: </label> <br>
                    @if ($promo->statusId == 24)
                        <button type="button" class="btn btn-success">{{$promo->statusName}}</button>
                    @elseif($promo->statusId == 25)
                        <button type="button" class="btn btn-danger">{{$promo->statusName}}</button>
                    @elseif($promo->statusId == 38)
                        <button type="button" class="btn btn-warning">Promoción terminada</button>
                    @endif
                </div>

            </div>
                

            @if ($promo->promo_id != 1)
                
                <div class="row justify-content-center">

                    <div class="col-md-6">
                        <div class="text-center">
                            @if ($promo->statusId != 24)
                                <button type="button" disabled class="btn btn-success">Actualizar información</button>
                                <button type="button" disabled class="btn btn-info text-light">Terminar promoción</button>
                                
                            @else
                                <button type="button" onclick="updatePromo({{$promo->promo_id}})" class="btn btn-success">Actualizar información</button>
                                <button type="button" onclick="endPromotion({{$promo->promo_id}})" class="btn btn-info text-light">Terminar promoción</button>
                            @endif
                        </div>
                    </div>

                </div>

                <div class="col-md-5 my-3">
                    <div class="text-center">
                        @if ($promo->statusId == 25)
                            <button type="button" disabled class="btn btn-danger">Dar de baja la promoción</button>
                        @else
                            <button type="button" onclick="downPromo({{$promo->promo_id}})" class="btn btn-danger">Dar de baja la promoción</button>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </form>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div id="alerts">
            </div>
        </div>
    </div>


    {{-- Table Bonus --}}
        <div class="row mb-5">

            {{-- Table cortes --}}
                {{-- <div class="col-md-12 mb-5">

                    <div class="card rounded cardsDetails tableDetails" >

                        <div class="card-header">
                            <div class="row justify-content-end">
                                <div class="col">
                                    <h3>Cortes</h3>
                                </div>

                                <div class="col-md-5 text-end buttonsTable">
                                    @if ($promo->statusId != 24)
                                        <a type="button" class="btn text-secondary"  disabled><i class="fas fa-plus"></i></a>
                                    @else
                                        <a type="button" class="btn text-secondary" data-bs-toggle="modal" data-bs-target="#addPeriod" ><i class="fas fa-plus"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card-body table-responsive">
                            <table class="table text-center text-center">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Corte</th>
                                        <th scope="col">Fecha de inicio</th>
                                        <th scope="col">Fecha de finalización</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyTableBody">
                                    @foreach ($periods as $period)
                                        
                                        <tr>
                                            <td scope='row'>Corte {{ $period->order }}</td>
                                            <td>{{ \Carbon\Carbon::parse( $period->inicial_date)->format('d-m-Y h:i:s') }}</td>
                                            <td>{{ \Carbon\Carbon::parse( $period->final_date)->format('d-m-Y h:i:s') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div> --}}
            {{-- End table Cortes --}}


            {{-- Table bonus --}}
                <div class="col-md-12 ">
                    <div class="card rounded cardsDetails tableDetails" >
                        <div class="card-header">
                            <div class="row justify-content-end">
                                <div class="col">
                                    <h3>Bonus</h3>
                                </div>

                                <div class="col-md-5 text-end buttonsTable">
                                    @if ($promo->statusId != 24)
                                        <a type="button" class="btn text-secondary" disabled ><i class="fas fa-plus"></i></a>
                                    @else
                                        <a type="button" class="btn text-secondary" data-bs-toggle="modal" data-bs-target="#addBonus" ><i class="fas fa-plus"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>


                        <div class="card-body table-responsive">
            
                            <table class="table text-center">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Multiplicador</th>
                                        <th scope="col">Fecha de inicio</th>
                                        <th scope="col">Fecha de finalización</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyTableBonus">
                                    @foreach ($bonus as $bono)
                                        
                                        <tr>
                                            <td>x{{ $bono->multiplier }}</td>
                                            <td>{{ \Carbon\Carbon::parse( $bono->begins_at)->format('d-m-Y h:i:s') }}</td>
                                            <td>{{ \Carbon\Carbon::parse( $bono->ends_at)->format('d-m-Y h:i:s') }}</td>
                                            <td>
                                                <button type='button' class='btn btn-success' data-bs-toggle='modal' data-bs-target='#viewDetailsBonus' onclick='getBonus({{ $bono->id }})' >
                                                    <i class='fas fa-eye me-2'></i>Ver detalles
                                                </button>
                                                
                                                <button type='button' class='btn btn-danger' onclick='deleteBonus({{ $bono->id }},"details")' >
                                                    <i class='fas fa-trash me-2'></i>Dar de baja
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            {{-- End table bonus --}}

        </div>
    {{-- End table Bonus --}}
    
    @if ($promo->promo_id != 1)
        
        {{-- Table winners --}}
            <div class="row">
                <div class="col-md-12">

                    <div class="card rounded cardsDetails tableDetails" >

                        <div class="card-header">
                            <div class="row justify-content-end">
                                <div class="col-md 3">
                                    <h3>Ganadores</h3>
                                </div>

                                <div class="col-md-5 text-end buttonsTable">
                                    @if ($promo->statusId != 24)
                                        <a type="button" class="btn text-secondary"  disabled><i class="fas fa-plus"></i></a>
                                    @else
                                        <a type="button" class="btn text-secondary" data-bs-toggle="modal" data-bs-target="#addWinner" onclick="searchUsers()"><i class="fas fa-plus"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card-body table-responsive pb-0">
                            <table class="table text-center mb-0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Posición</th>
                                        <th scope="col">Usuario</th>
                                        <th scope="col">Premio</th>
                                        <th scope="col">Imagen</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyTableWinners">
                                    @foreach ($winners as $winner)
                                        
                                        <tr>
                                            <td scope='row'> {{ $winner->position }}</td>
                                            <td scope='row'> {{ $winner->name }}</td>
                                            <td scope='row'> {{ $winner->award }}</td>
                                            <td scope="row" style="width: 20%;">
                                                <img src="{{ $routeGlobal }}{{ $winner->image }}" style="width: 100px;" alt="{{ $winner->award }}" />
                                            </th>
                                            <td>
                                                <button type='button' class='btn btn-danger' onclick='deleteWinner({{ $winner->id }})' >
                                                    <i class="fas fa-trash me-2"></i>Borrar ganador
                                                </button>
                                            </td>
                                        </tr>

                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        {{-- End table winners --}}


        {{-- Table awards --}}
            <div class="row my-5">
                <div class="col-md-12">
                    <div class="card rounded cardsDetails" >
                        <div class="card-header">
                            <div class="row justify-content-end">
                                <div class="col">
                                    <h3>Premios</h3>
                                </div>

                                <div class="col-md-5 text-end buttonsTable">
                                    @if ($promo->statusId != 24)
                                        <a type="button" class="btn text-secondary" disabled><i class="fas fa-plus"></i></a>
                                    @else
                                        <a type="button" class="btn text-secondary" data-bs-toggle="modal" data-bs-target="#addAward" onclick="searchPositionAwards()"><i class="fas fa-plus"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card-body">

                                <div class="row justify-content-center">
        
                                    <div class="col-md-12">
                                        <table class="table text-center">
                                            <thead>
                                                <tr>
                                                <th scope="col">Imagen</th>
                                                <th scope="col">Nombre</th>
                                                <th scope="col">Posición</th>
                                                {{-- <th scope="col">Redimidos</th> --}}
                                                <th scope="col">Estatus</th>
                                                <th scope="col">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="bodyTableAward">
                                                @foreach ($awards as $award)
        
                                                    @php
                                                        $textClass = 'text-danger';
                                                        if ( $award->statusId == 26 ){
                                                            $textClass = 'text-success';
                                                        }
                                                    @endphp
        
                                                <tr>
                                                    <td scope="row" style="width: 20%;">
                                                        <img src="{{ $routeGlobal }}{{ $award->image }}" style="width: 100px;" alt="{{ $award->awardName }}" />
                                                    </th>
                                                    <th>{{ $award->awardName }}</td>
                                                    <td>{{ $award->position }}</td>
                                                    {{-- <td>{{ $award->redeem }}</td> --}}
                                                    <td class="{{ $textClass }}">
                                                        {{ $award->statusName }}
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#viewDetailsAwards" onclick="getDetailsAwards({{ $award->awardId }})" >
                                                            <i class="fas fa-eye me-2"></i>Ver detalles
                                                        </button>

                                                        <button type='button' class='btn btn-danger' onclick='deleteAward({{ $award->awardId }},"details")' >
                                                            <i class="fas fa-trash me-2"></i>Borrar premio
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
        
                                </div>
                        </div>
                    </div>

                </div>
            </div>
        {{-- End table Awards --}}
        
    @endif


    {{-- Promo imagen Futura --}}
    {{-- <div class="modal fade" id="staticBackdrop"  tabindex="-1"  >
        <div class="modal-dialog" >
            <div class="modal-content" style="width: 700px;">
                <div class="modal-body">
                    <img id="imageZoom" src="{{$promo["image"]}}">
                </div>
            </div>
        </div>
    </div> --}}
                
@endsection

@section('modals')

    
    {{-- Bonnus Modals --}}
        <!-- Modal add bonus -->
        <div class="modal fade" id="addBonus" tabindex="-1" aria-labelledby="exampleModalPromo" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalPromo">Agregar bonus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="addBonusForm" >
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-md-10">


                                    {{-- <input type="hidden" name="promoId" value="{{ $promo->promo_id }}"> --}}

                                    <div class="mb-3">
                                        <label for="user" class="form-label">Nombre: </label>
                                        <input type="text" class="form-control" id="bonusName" >
                                    </div>

                                    <div class="form-floating">
                                        <textarea class="form-control" id="bonusDescriptión" name="bonusDescriptión" placeholder="Leave a comment here" style="height: 100px"></textarea>
                                        <label for="bonusDescriptión">Descripcion del bonus</label>
                                    </div>

                                    <div class="mb-3">
                                        <label for="user" class="form-label">Multiplicador: </label>
                                        <input type="number" class="form-control" id="multiplier" >
                                    </div>

                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="exampleInputEmail1" class="form-label">Fecha incio</label>
                                                <input type="datetime-local" id="bonusInitial_date" class="form-control" name="trip-start"  >
                                            </div>
                                        </div>
            
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="exampleInputEmail1" class="form-label">Fecha final</label>
                                                <input type="datetime-local" id="bonusFinal_date" class="form-control" name="trip-start">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"  id="bonusValid3months">
                                        <label class="form-check-label" for="flexCheckDefault">
                                            Crear bonus x3 meses
                                        </label>
                                      </div>

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="addBonus()" >Agregar periodo</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- View detals --}}
        <div class="modal fade" id="viewDetailsBonus" tabindex="-1" aria-labelledby="detailsBonusModal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailsBonusModal">Editar bonificación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" >
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-md-10">


                                    <input type="hidden" name="bonusId" id="bonusId" >

                                    <div class="mb-3">
                                        <label for="user" class="form-label">Nombre: </label>
                                        <input type="text" class="form-control" id="bonusNameUpdate" >
                                    </div>
                                    
                                    <div class="mb-3">
                                        {{-- <textarea class="form-control" id="bonusDescriptiónUpdate" name="bonusDescriptiónUpdate" placeholder="Leave a comment here" style="height: 100px"></textarea> --}}
                                        <label for="bonusDescriptiónUpdate" class="form-label">Descripcion del bonus</label>
                                        <input type="text" class="form-control" id="bonusDescriptiónUpdate" name="bonusDescriptiónUpdate" >
                                    </div>

                                    <div class="mb-3">
                                        <label for="user" class="form-label">Multiplicador: </label>
                                        <input type="number" class="form-control" id="multiplierUpdate" >
                                    </div>

                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="exampleInputEmail1" class="form-label">Fecha incio</label>
                                                <input type="datetime-local" id="bonusInitial_dateUpdate" class="form-control" name="trip-start"  >
                                            </div>
                                        </div>
            
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="exampleInputEmail1" class="form-label">Fecha final</label>
                                                <input type="datetime-local" id="bonusFinal_dateUpdate" class="form-control" name="trip-start">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="updateBonus()" >Actualizar</button>
                    </div>
                </div>
            </div>
        </div>

    {{-- End Bonnus modals --}}


    <!-- Modal add period -->
    <div class="modal fade" id="addPeriod" tabindex="-1" aria-labelledby="exampleModalPromo" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalPromo">Agregar periodo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" >
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-md-10">


                                {{-- <input type="hidden" name="promoId" value="{{ $promo->promo_id }}"> --}}

                                <div class="mb-3">
                                    <label for="user" class="form-label">Corte: </label>
                                    <input type="text" class="form-control" aria-label="readonly input example" readonly value="Corte {{ $lastPeriod['order'] }}" >
                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="exampleInputEmail1" class="form-label">Fecha incio</label>
                                            <input type="date" min="{{$lastPeriod['final_date']}}" id="initial_date" class="form-control" name="trip-start"  >
                                        </div>
                                    </div>
        
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="exampleInputEmail1" class="form-label">Fecha final</label>
                                            <input type="date" min="{{$lastPeriod['final_date']}}" id="final_date" class="form-control" name="trip-start">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="addPeriod()" >Agregar periodo</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Awards modals --}}

        {{-- Modal new warad --}}
        <div class="modal fade" id="addAward" tabindex="-1" aria-labelledby="exampleModalPromo" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalPromo">Agregar premio</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formAddAward" action="{{ route('addAward')  }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-md-10">

                                    <div id="alertModalAddAward"></div>

                                    <div class="row justify-content-center">
                                        <div class="col-md-7">
                                            <div class="mb-3">
                                                <label for="user" class="form-label">Imagen: </label>
                                                <div class="input-group mb-3">
                                                    <input type="file" class="form-control" id="addImageAward" name="addImageAward" accept="image/*" data-type='image' onchange="changeImgToB64('addImageAward')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="promoId" id="promoId" value="{{ $promo->promo_id }}">

                                    <div class="mb-3">
                                        <label for="user" class="form-label">Nombre del premio: </label>
                                        <input type="text" class="form-control" id="addAwardName" name="addAwardName" >
                                    </div>

                                    <div class="mb-3">
                                        {{-- <label for="user" class="form-label">Descripcion: </label> --}}
                                        <div class="form-floating">
                                            <textarea class="form-control" id="addAwardDescriptión" name="addAwardDescriptión" placeholder="Leave a comment here" style="height: 100px"></textarea>
                                            <label for="floatingTextarea2">Descripcion del producto</label>
                                        </div>
                                    </div>

                                    <div class="mb-3" id="positionsChecks">
                                        
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="postAward('details')">Cargar premio</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Details Awrads -->
        <div class="modal fade" id="viewDetailsAwards" tabindex="-1" aria-labelledby="exampleModalPromo" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalPromo">Información del premio</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formAddAward" action="{{ route('addAward')  }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-md-10">

                                    <div class="row justify-content-center">
                                        <div class="col-md-7">
                                            <div class="mb-3">
                                                <label for="user" class="form-label">Imagen: </label>
                                                <div class="input-group mb-3">
                                                    <input type="file" class="form-control" id="updateImageAward" name="updateImageAward" accept="image/*" data-type='image' onchange="changeImgToB64('updateImageAward')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                <div class='form-floating'>
                                        <select class='form-select' id='promoIdUpdate' aria-label='Floating label select example'>
                                            
                                        </select>
                                        <label for='promoIdUpdate'>Promociones:</label>
                                    </div>



                                    <div class="mb-3">
                                        <label for="user" class="form-label">Nombre del premio: </label>
                                        <input type="text" class="form-control" id="updateAwardName" name="updateAwardName" >
                                    </div>

                                    <input type="hidden" id="awardId">

                                    <div class="mb-3">
                                        {{-- <label for="user" class="form-label">Descripcion: </label> --}}
                                        <div class="form-floating">
                                            <textarea class="form-control" id="updateAwardDescriptión" name="updateAwardDescriptión" placeholder="Leave a comment here" style="height: 100px"></textarea>
                                            <label for="floatingTextarea2">Descripcion del producto</label>
                                        </div>
                                    </div>

                                    <div class="mb-3" id="positionsChecksUpdates">
                                        
                                    </div>

                                    <input type="hidden" id="positionUpdate">

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="editAward()">Cargar premio</button>
                    </div>
                </div>
            </div>
        </div>

    {{-- End Awards Modals --}}

    {{-- Modal winner --}}
        <div class="modal fade" id="addWinner" tabindex="-1" aria-labelledby="exampleModalPromo" data-bs-backdrop="static" data-bs-keyboard="false"  aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalPromo">Agregar ganador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formAddAward" action="{{ route('addAward')  }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-md-10">


                                    <input type="hidden" name="promoId" id="promoId" value="{{ $promo->promo_id }}">

                                    {{-- Campo select: 
                                    premio,user,posicion --}}

                                    {{-- <div class="form-floating">
                                        <select class="form-select" id="selectCorte" aria-label="Floating label select example" onchange="searchUsers()">
                                            <option  value="0" selected>Selecciona el corte</option>
                                            @foreach ($periods as $period)
                                                <option value="{{ $period->id }}">Corte {{ $period->order }}</option>
                                            @endforeach
                                        </select>
                                        <label for="selectCorte">Corte</label>
                                    </div> --}}


                                    
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-md-11">
                                    <h3 class="my-4">Usuarios</h3>

                                    <div class="row justify-content-center">
                                        <div class="col-md-8">
                                            <div  id="alertSelectWinners">
            
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table text-center">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th scope="col">Nombre</th>
                                                <th scope="col">Posición</th>
                                                <th scope="col">premio</th>
                                                <th scope="col">seleccionar</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyTableWinnersSelect">
                                            <tr class="">
                                                <th scope="row" colspan="5">Favor de seleccionar un un corte para poder seleccionar a los ganadores</th>
                                            </tr>
                                            {{-- --}}
                                        </tbody>
                                    </table>

                                </div>
                            </div>


                                
                        </form>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="postWinners()">Guardar ganador(es)</button>
                    </div>
                </div>
            </div>
        </div>
    {{-- End modal winner --}}

    {{-- Modal Images --}}
        <div class="modal fade" id="staticBackdrop"  tabindex="-1"  >
            <div class="modal-dialog" >
                <div class="modal-content" style="width: 1000px;">
                    <div class="modal-body">
                        <img id="imageZoom" src="{{ $routeGlobal }}{{ $promo->image }}" width="100%">
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="staticBackdropBanner"  tabindex="-1"  >
            <div class="modal-dialog" >
                <div class="modal-content" style="width: 1000px;">
                    <div class="modal-body">
                        <img id="imageZoom" src="{{ $routeGlobal }}{{$promo->imageBanner}}" width="100%">
                    </div>
                </div>
            </div>
        </div>
    {{-- End Images Modal --}}

@endsection

@section('js')
    {{-- <script src="{{asset('js/image-zoom.js')}}" type="text/javascript"></script> --}}
    <script src="{{ asset('js/promo.js') }}" type="text/javascript"></script>
   <script src="{{ asset('js/awards.js') }}" type="text/javascript"></script>
   <script src="{{ asset('js/periods.js') }}" type="text/javascript"></script>
   <script src="{{ asset('js/routeGlobal.js') }}" type="text/javascript"></script>
   <script src="{{ asset('js/bonus.js') }}" type="text/javascript"></script>
   <script src="{{ asset('js/winners.js') }}" type="text/javascript"></script>
@endsection
