@extends('layouts.layout')
@section('css')
    <style>
        td, th{
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    <div class=" mt-4">
        <h3 class="my-3">Promociones</h3>

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

                            <div class="col-md-6">
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
                            </div>


                            <div class="text-end">
                                <a type="button" class="btn btn-primary" onclick="filterPromotion()">Buscar</a>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row my-5">
            <div class="col-md-12">
                <div class="card rounded cardsDetails" >
                    <div class="card-header">
                        <div class="row justify-content-end">
                            <div class="col-md 3">
                                <h3>Tabla total de Promociones</h3>
                            </div>

                            <div class="col-md-5 text-end buttonsTable">
                                <button type="button" class="btn text-secondary"  data-bs-toggle="modal" data-bs-target="#addPromo"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">

                            <div class="row justify-content-center m-0">
    
                                <div class="col-md-12 p-0">
                                    <table class="table table-striped text-center mb-0">
                                        <thead>
                                            <tr>
                                              {{-- <th scope="col">#</th> --}}
                                              <th scope="col">Promo</th>
                                              <th scope="col">Estatus</th>
                                              <th scope="col">Nº de ganadores por promoción</th>
                                              <th scope="col">Fecha incial de la promoción</th>
                                              <th scope="col">Fecha de finalización de la promoción</th>
                                              <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyTable">
                                            @foreach ($promos as $promo)
                                            <tr>
                                                {{-- <th scope="row">{{ $promo->id }}</th> --}}
                                                <td>{{ $promo->title }}</td>
                                                @php
                                                    if( $promo->statusId == 24){
                                                        $classStatus = "text-success";
                                                    }elseif($promo->statusId == 25){
                                                        $classStatus = "text-danger";
                                                    }else{
                                                        $classStatus = "text-warning";
                                                    }
                                                    @endphp
                                                <td class="{{$classStatus}}">{{ $promo->name }}</td>
                                                <td>{{ $promo->awards_qty }}</td>
                                                <td>{{ $promo->begin_date }}</td>
                                                <td>{{ $promo->end_date }}</td>
                                                <td style="width: 16vw;" >
                                                    <a type="button" href="{{route('promoDetails',['id'=> $promo->id])}}" class="btn btn-success ">Editar</a>
                                                    <a type="button" onclick="downPromo({{$promo->id}})" class="btn btn-danger">Dar de baja</a>
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
                    {{ $promos->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
        
    </div>

@endsection

@section('modals')

    <div class="modal fade" id="addPromo" tabindex="-1" aria-labelledby="exampleModalPromo" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalPromo">Agregar promocion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formAddPromo" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-md-10">

                                <div id="alertModalAddPromo"></div>

                                <div class="row justify-content-center">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="user" class="form-label">Imagen logo: </label>
                                            <div class="input-group mb-3">
                                                <input type="file" class="form-control" id="addImagePromo" name="addImagePromo" accept="image/*" data-type='image' onchange="changeImgToB64Promo('addImagePromo')">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="user" class="form-label">Imagen banner: </label>
                                            <div class="input-group mb-3">
                                                <input type="file" class="form-control" id="addImageBanner" name="addImageBanner" accept="image/*" data-type='image' onchange="changeImgToB64Banner('addImageBanner')">
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <div class="mb-3">
                                    <label for="user" class="form-label">Titulo de la promoción: </label>
                                    <input type="text" class="form-control" id="promoTitle" name="promoTitle" >
                                </div>

                                <div class="mb-3">
                                    <label for="user" class="form-label">Número de ganadores: </label>
                                    <input type="number" class="form-control" id="awards_qty" name="awards_qty" >
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="begin_date" class="form-label">Fecha de inicio de la promoción: </label>
                                            <input type="date" data-date-format="dd/mm/yyyy" class="form-control" id="begin_date" name="begin_date" >
                                        </div>
                                    </div>
        
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="end_date" class="form-label">Fecha de fiinalización de la promoción: </label>
                                            <input type="date" class="form-control" id="end_date" name="end_date" >
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="insertPromo()">Agregar promoción</button>
                </div>
            </div>
        </div>
    </div>

    
@endsection


@section('js')
    <script src="{{ asset('js/filters.js') }}"></script>
    <script src="{{ asset('js/promo.js') }}" type="text/javascript"></script>
@endsection