@extends('layouts.layout')
@section('css')
    <link href="{{asset('css/imagestyle.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/image-zoom.css')}}">
    {{-- <link rel="stylesheet" href="{{ asset('css/cardAward.css') }}"> --}}
    <style>
        .tableDetails{
            max-height: 300px;
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

        .cardsDetails{
            max-height: 924px;
            min-height: 324px;
            overflow: auto;
        }

        .btnChangeStatus{
            width: 173px;
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

    <div class="accordion accordion-flush mt-5" id="accordionFlushExample">
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

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nameFilter" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Correo</label>
                                <input type="email" class="form-control" id="emailFilter" >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Estatus</label>
                                <select class="form-select" id="statusFilter" >
                                    @foreach ($status as $s)
                                        @php
                                            if($s->id == 44){
                                                $statusName = 'Activo';
                                            }else{
                                                $statusName = 'Inactivo';
                                            }
                                        @endphp
                                        <option value="{{ $s->id }}">{{ $statusName}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="text-end">
                            <a type="button" class="btn btn-primary" onclick="validatorsFilters()">Buscar</a>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row mb-5">
        <div class="col-md-12">
            <div class="card rounded cardsDetails" >
                <div class="card-header">
                    <div class="row justify-content-end">
                        <div class="col-md 3">
                            <h3>Usuarios</h3>
                        </div>

                        <div class="col-md-5 text-end buttonsTable">
                            <a type="button" class="btn text-secondary" data-bs-toggle="modal" data-bs-target="#addUser"><i class="fas fa-plus"></i></a>
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
                                            <th scope="col">Correo</th>
                                            <th scope="col">Estatus</th>
                                            <th scope="col">Detalles</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyTable">
                                        @foreach ($validators as $user)
                                        <tr>
                                            <th>{{ $user->name }}</th>
                                            <td>{{ $user->email }}</td>
                                            @if ($user->status_id == 44)
                                                <td class="text-success">Activo</td>
                                            @else
                                                <td class="text-danger">Inactivo</td>
                                            @endif
                                            <td>
                                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#viewDetailsValidator" onclick="getValidatorDetails({{ $user->id }})" ><i class="fas fa-eye me-2"></i>Ver detalles</button>
                                                @if ($user->status_id == 44)
                                                    
                                                    <button type='button' class='btn btn-danger btnChangeStatus' onclick='downAdminUser({{ $user->id }}, 45)' >
                                                        <i class="fas fa-user-slash"></i> Quitar acceso
                                                    </button>

                                                @else
                                                    
                                                    <button type='button' class='btn btn-success btnChangeStatus' onclick='downAdminUser({{ $user->id }}, 44)' >
                                                        <i class="fas fa-user-check me-2"></i> Dar acceso
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

            <div id="paginationTable" class="mt-3">
                {{ $validators->links('pagination::bootstrap-4') }}
            </div>
            
        </div>
    </div>

                
@endsection

@section('modals')

    {{-- Modal new warad --}}
    <div class="modal fade" id="addUser" tabindex="-1" aria-labelledby="exampleModalPromo" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalPromo">Agregar</h5>
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
                            <div class="col-md-10">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="addName" class="form-label">Nombre: </label>
                                            <input type="text" class="form-control" id="addName" name="addName" >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="addEmail" class="form-label">Correo: </label>
                                            <input type="email" class="form-control" id="addEmail" name="addEmail">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="addPassword" class="form-label">Contraseña: </label>
                                    <input type="password" class="form-control" id="addPassword" name="addPassword">
                                </div>
                                
                            </div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="postUser()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Modal Details Awrads -->
    <div class="modal fade" id="viewDetailsValidator" tabindex="-1" aria-labelledby="exampleModalPromo" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalPromo">Detalle del usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form id="formUpdateUser"  method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-md-10">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="updateName" class="form-label">Nombre: </label>
                                            <input type="text" class="form-control" id="updateName" name="updateName">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="updateEmail" class="form-label">Correo: </label>
                                            <input type="email" class="form-control" id="updateEmail" name="updateEmail">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="updtaePassword" class="form-label">Contraseña: </label>
                                    <input type="password" class="form-control" id="updatePassword" name="updtaePassword">
                                </div>
                                
                                <input type="hidden" class="form-control" id="userId" name="userId">
                            </div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="editValidator()">Actualizar</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        
 
   </script>

   <script src="{{ asset('js/filters.js') }}" type="text/javascript"></script>
   <script src="{{ asset('js/validatorUsers.js') }}" type="text/javascript"></script>
   

@endsection
