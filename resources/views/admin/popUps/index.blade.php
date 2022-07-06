@extends('layouts.layout')

@section('css')
    <link href="{{asset('css/imagestyle.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/image-zoom.css')}}">
    <link rel="stylesheet" href="{{ asset('css/cardAward.css') }}">
    <style>
        .tableDetails{
            height: 300px;
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
    <h3 class="mt-5">Mensajes de la pantalla de inicio</h3>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div id="alerts">
            </div>
        </div>
    </div>


    <div class="row my-5">
        <div class="col-md-12">
            <div class="card rounded cardsDetails" >
                <div class="card-header">
                    <div class="row justify-content-end">
                        <div class="col-md 3">
                            <h3>Mensajes</h3>
                        </div>

                        <div class="col-md-5 text-end buttonsTable">
                            <a type="button" class="btn text-secondary" data-bs-toggle="modal" data-bs-target="#addPopUp"  ><i class="fas fa-plus"></i></a>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                        <div class="row justify-content-center">

                            <div class="col-md-11">
                                <table class="table text-center">
                                    <thead>
                                        <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Titulo</th>
                                        <th scope="col">Estatus</th>
                                        <th scope="col">Fecha</th>
                                        <th scope="col">Detalles</th>
                                        </tr>
                                    </thead>

                                    <tbody id="bodyTablePopUp">
                                        @foreach ($popUps as $popUp)

                                            @php
                                                
                                                if( $popUp->statusId == 29 ){
                                                    $textStatus = 'text-danger';
                                                }else{
                                                    $textStatus = 'text-success';
                                                }


                                            @endphp

                                            <tr>
                                                <td>{{ $popUp->popId }}</td>
                                                <td>{{ $popUp->title }}</td>
                                                <td class="{{ $textStatus }}">{{ $popUp->statusName }}</td>
                                                <td>{{ $popUp->created_at }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#viewDetailsPopUp" onclick="getDetailsPopUp({{ $popUp->popId }})" >
                                                        <i class="fas fa-eye me-2"></i>Ver detalles
                                                    </button>

                                                    @if ( $popUp->statusId == 29 )
                                                        <button type='button' class='btn btn-success' onclick='updateStatusPopUp( {{$popUp->popId}}, 28)' >
                                                            <i class='far fa-check-circle me-2'></i>Activar mensaje
                                                        </button>
                                                    @else
                                                        <button type='button' class='btn btn-danger' onclick='updateStatusPopUp({{ $popUp->popId }}, 29)' >
                                                            <i class='fas fa-trash me-2'></i>Dar de baja
                                                        </button>
                                                    @endif
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

@endsection

@section('modals')

    {{-- Modal new Pop up --}}
        <div class="modal fade" id="addPopUp" tabindex="-1" aria-labelledby="exampleModalPromo" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalPromo">Agregar mensaje</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formAddPopUp" action="{{ route('addAward')  }}"  method="post" enctype="multipart/form-data">
                            <div class="row justify-content-center">
                                <div class="col-md-10">
                                    <div class="row justify-content-center">
                                        <div class="col-md-8">
                                            <div id="alertAddPopUp">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row justify-content-center">
                                        <div class="col-md-7">
                                            <div class="mb-3">
                                                <label for="user" class="form-label">Imagen: </label>
                                                <div class="input-group mb-3">
                                                    <input type="file" class="form-control" id="addImagePopUp" name="addImagePopUp" accept="image/*" data-type='image' onchange="changeImgToB64('addImagePopUp')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="user" class="form-label">Titulo del mensaje: </label>
                                        <input type="text" class="form-control" id="addTitlePopUp" name="addTitlePopUp" >
                                    </div>

                                    <div class="mb-3">
                                        {{-- <label for="user" class="form-label">Descripcion: </label> --}}
                                        <div class="form-floating">
                                            <textarea class="form-control" id="descriptionPopUp" name="descriptionPopUp" placeholder="Leave a comment here" style="height: 100px"></textarea>
                                            <label for="floatingTextarea2">Contenido del mensaje</label>
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
                    <button type="button" class="btn btn-primary" onclick="addPopUp()">Cargar premio</button>
                    </div>
                </div>
            </div>
        </div>
    {{-- End Modal new Pop Up --}}

    <!-- Modal Details Pop up -->
        <div class="modal fade" id="viewDetailsPopUp" tabindex="-1" aria-labelledby="exampleModalDetailsPopUp" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalDetailsPopUp">Información del mensaje</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formAddAward" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-md-10">

                                    <div class="row justify-content-center">
                                        <div class="col-md-7">
                                            <div class="mb-3">
                                                <label for="user" class="form-label">Imagen: </label>
                                                <div class="input-group mb-3">
                                                    <input type="file" class="form-control" id="updateImagePop" name="updateImagePop" accept="image/*" data-type='image' onchange="changeImgToB64('updateImagePop')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <input type="hidden" class="form-control" id="popId" name="popId" >

                                    <div class="mb-3">
                                        <label for="user" class="form-label">Titulo del mensaje: </label>
                                        <input type="text" class="form-control" id="updateTitlePopUp" name="updateTitlePopUp" >
                                    </div>

                                    <div class="mb-3">
                                        {{-- <label for="user" class="form-label">Descripcion: </label> --}}
                                        <div class="form-floating">
                                            <textarea class="form-control" id="updateDescriptionPopUp" name="updateDescriptionPopUp" placeholder="Leave a comment here" style="height: 100px"></textarea>
                                            <label for="floatingTextarea2">Contenido del mensaje</label>
                                        </div>
                                    </div>

                                    <input type="hidden" id="positionUpdate">

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="editPopUp()">Cargar premio</button>
                    </div>
                </div>
            </div>
        </div>
    {{-- End Modal Details Pop up --}}

@endsection

@section('js')
    {{-- <script src="{{asset('js/image-zoom.js')}}" type="text/javascript"></script> --}}
    <script src="{{ asset('js/popUp.js') }}" type="text/javascript"></script>
   

@endsection
