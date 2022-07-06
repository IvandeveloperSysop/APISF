@extends('layouts.layout')
@section('css')
    <link href="{{asset('css/imagestyle.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/image-zoom.css')}}">
    <link rel="stylesheet" href="{{ asset('css/cardAward.css') }}">
    <style>
        .tableDetails{
            height: 300px;
        }

        td, th{
            vertical-align: middle;
        }
    </style>
   
@endsection
@section('content')
    <div class=" d-flex my-4">
        <div class="">
            <a type="button" class="btn btn-outline-primary me-3 btn-sm" href="{{ url()->previous() }}">
                <i class="fas fa-arrow-left me-1"></i>Regresar
            </a>
        </div>
    </div>


    <h1 class="mt-5">Premios</h1>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div id="alerts">
            </div>
        </div>
    </div>

    <div class="accordion accordion-flush" id="accordionFlushExample">
        <div class="accordion-item border border-secondary">
            <h2 class="accordion-header" id="flush-headingOne">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionFilters" aria-expanded="false" aria-controls="accordionFilters">
                    Filtros
                </button>
            </h2>
            <div id="accordionFilters" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">

                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div id="alertFilter">
    
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-center">

                        {{-- <div class="col-md-6">
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="user_name" placeholder="Nombre del usuario">
                            </div>
                        </div> --}}

                        <div class="col-md-7">
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Promoción</label>
                                <select class="form-select" id="selectPromoId" >
                                    <option value="0" selected>Selecciona una promoción valida</option>
                                    @foreach ($promos as $promo)
                                        <option value="{{ $promo->id }}">{{ $promo->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- <div class="col-md-6">
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Fecha incio</label>
                                <input type="date" id="date_initial" class="form-control" name="trip-start">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Fecha final</label>
                                <input type="date" id="date_final" class="form-control" name="trip-start">
                            </div>
                        </div> --}}


                        <div class="text-end">
                            <a type="button" class="btn btn-primary" onclick="filterAwards()">Buscar</a>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5 mt-2">
        <div class="col-md-12">
            <div class="card rounded cardsDetails" >
                <div class="card-header">
                    <div class="row justify-content-end">
                        <div class="col-md 3">
                            <h3>Premios</h3>
                        </div>

                        <div class="col-md-5 text-end buttonsTable">
                            <a type="button" class="btn text-secondary" data-bs-toggle="modal" data-bs-target="#addAward" ><i class="fas fa-plus"></i></a>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                        <div class="row justify-content-center">

                            <div class="col-md-11">
                                <table class="table text-center">
                                    <thead>
                                        <tr>
                                        <th scope="col">Imagen</th>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Promoción</th>
                                        <th scope="col">Posición</th>
                                        {{-- <th scope="col">Redimidos</th> --}}
                                        <th scope="col">Estatus</th>
                                        <th scope="col">Detalles</th>
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
                                            <th>{{ $award->promoTitle }}</td>
                                            <td>{{ $award->position }}</td>
                                            {{-- <td>{{ $award->redeem }}</td> --}}
                                            <td class="{{ $textClass }}">
                                                {{ $award->statusName }}
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#viewDetailsAwards" onclick="getDetailsAwards({{ $award->awardId }}, {{ $award->promoId }})" ><i class="fas fa-eye me-2"></i>Ver detalles</button>
                                                <button type='button' class='btn btn-danger' onclick='deleteAward({{ $award->awardId }})' >
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

            <div id="paginationTable">
                {{ $awards->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

@endsection

@section('modals')

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

                                <div class='form-floating'>
                                    <select class='form-select' id='promoId' aria-label='Floating label select example' onchange="searchPositionAwards()">
                                        <option value="0" selected>Seleccionar promoción</option>
                                        @foreach ($promos as $promo)
                                        <option value='{{$promo->id}}' >{{$promo->title}}</option>
                                        @endforeach
                                    </select>
                                    <label for='promoId'>Promociones:</label>
                                </div>



                                <div class="mb-3">
                                    <label for="user" class="form-label">Nombre del premio: </label>
                                    <input type="text" class="form-control" id="addAwardName" name="addAwardName" >
                                </div>

                                <div class="mb-3">
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
                <button type="button" class="btn btn-primary" onclick="postAward()">Cargar premio</button>
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
                                            <div id="imageAward">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                {{-- <div class="mb-3">
                                    <label for="user" class="form-label">Promociones: </label>
                                    <input type="text" class="form-control" id="promoIdUpdate" name="promoIdUpdate" disabled>
                                </div> --}}

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
                                    <div class="form-floating">
                                        <textarea class="form-control" id="updateAwardDescriptión" name="updateAwardDescriptión" placeholder="Leave a comment here" style="height: 100px" ></textarea>
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



@endsection

@section('js')
    {{-- <script src="{{asset('js/image-zoom.js')}}" type="text/javascript"></script> --}}
   <script src="{{ asset('js/awards.js') }}" type="text/javascript"></script>
   <script src="{{ asset('js/filters.js') }}" type="text/javascript"></script>
   

@endsection
