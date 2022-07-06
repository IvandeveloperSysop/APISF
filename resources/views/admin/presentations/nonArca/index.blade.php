@extends('layouts.layout')

@section('css')
    <link href="{{asset('css/imagestyle.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/image-zoom.css')}}">
    {{-- <link rel="stylesheet" href="{{ asset('css/cardAward.css') }}"> --}}
    <style>
        .tableDetails{
            height: 300px;
        }

        .wrapper{
            height: 40px;
            width: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #FFF;
            border-radius: 12px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.2);
        }
        .wrapper span{
            width: 100%;
            text-align: center;
            font-size: 25px;
            font-weight: 600;
            cursor: pointer;
            user-select: none;
        }
        .wrapper .num{
            width: 100%;
            text-align: center;
            border: 0;
            font-size: 25px;
            /* border-right: 2px solid rgba(0,0,0,0.2);
            border-left: 2px solid rgba(0,0,0,0.2); */
            pointer-events: none;
        }

        td, th{
            vertical-align: middle;
        }
    </style>
   
@endsection

@section('content')

    {{-- <h1 class="mt-5">Premios</h1> --}}
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
                            <h3>Presentaciones fuera del grupo ARCA</h3>
                        </div>

                        <div class="col-md-5 text-end buttonsTable">
                            <a type="button" class="btn text-secondary" data-bs-toggle="modal" data-bs-target="#addPresentation" onclick="getSelectedTypes()"><i class="fas fa-plus"></i></a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">

                    <div class="row justify-content-center">

                        <div class="col-md-12 ">
                            <table class="table text-center mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Tipo</th>
                                        <th scope="col">Detalles</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyTablePresentation">
                                    @foreach ($presentations as $presentation)
                                    <tr>
                                        <th>{{ $presentation->presentationName }}</th>
                                        <th>{{ $presentation->typePresentation }}</th>
                                        <td>
                                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#viewDetailsPresentation" onclick="getPresentation({{ $presentation->presentationId }})" ><i class="fas fa-eye me-2"></i>Ver detalles</button>
                                            <button type='button' class='btn btn-danger' onclick='downPresentation({{ $presentation->presentationId }})' >
                                                <i class="fas fa-trash me-2"></i>Borrar presentación
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

            <div id="paginationTable" class="mt-3">
                {{ $presentations->links('pagination::bootstrap-4') }}
            </div>
            
        </div>
    </div>

                
@endsection

@section('modals')

    {{-- Modal new warad --}}
    <div class="modal fade" id="addPresentation" tabindex="-1" aria-labelledby="exampleModalPromo" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalPromo">Agregar presentación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div id="alertModalAddPosition">
                            </div>
                        </div>
                    </div>
                
                    <form id="formAddPresentation"  method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-md-12">

                                
                                
                                <div class="row justify-content-center">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="addPresentationName" class="form-label">Nombre de la presentación: </label>
                                            <input type="text" class="form-control" id="addPresentationName" name="addPresentationName" >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
    
                                        <div class="mb-3">
                                            <label for="user" class="form-label">Tipo: </label>
                                            <select class="form-select" aria-label="Default select example" id="tipoId">
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="postPresentatión()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Details Awrads -->
    <div class="modal fade" id="viewDetailsPresentation" tabindex="-1" aria-labelledby="exampleModalPromo" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalPromo">Detalles de la presentación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formAddPresentation"  method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-md-12">

                                
                                
                                <div class="row justify-content-center">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="updatePresentationName" class="form-label">Nombre de la presentación: </label>
                                            <input type="text" class="form-control" id="updatePresentationName" name="updatePresentationName" >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
    
                                        <div class="mb-3">
                                            <label for="user" class="form-label">Tipo: </label>
                                            <select class="form-select" aria-label="Default select example" id="typeIdUpdate">
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <input type="hidden" id="presentationIdUpdate">
                                
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="editPresentation()">Actualizar</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
   <script src="{{ asset('js/presentationsNonArca.js') }}" type="text/javascript"></script>
@endsection
