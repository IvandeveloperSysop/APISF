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
                            <h3>Productos en tienda</h3>
                        </div>

                        <div class="col-md-5 text-end buttonsTable">
                            <a type="button" class="btn text-secondary" data-bs-toggle="modal" data-bs-target="#addAward" ><i class="fas fa-plus"></i></a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">

                        <div class="row justify-content-center">

                            <div class="col-md-12 ">
                                <table class="table text-center mb-0">
                                    <thead>
                                        <tr>
                                        <th scope="col">Imagen</th>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Stock</th>
                                        <th scope="col">Precio</th>
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
                                            <th scope="row" style="width: 20%;">
                                                <img src="{{ $routeGlobal }}{{ $award->image }}" style="width: 40%;" alt="{{ $award->awardName }}" />
                                            </th>
                                            <th>{{ $award->awardName }}</th>
                                            <td>{{ $award->stock }}</td>
                                            <td>{{ $award->price }}</td>
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
                    <form id="formAddAward" action="{{ route('addAward') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-md-10">

                                <div id="alertAddAward"></div>

                                <div class="row justify-content-center">
                                    <div class="col-md-7">
                                        <div class="mb-3">
                                            <label for="user" class="form-label">Imagen: </label>
                                            <div class="input-group mb-3">
                                                <input type="file" class="form-control" id="addImageAward" name="addImageAward" accept="image/*" data-type='image' onchange="changeImgToB64Product('addImageAward')">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <input type="hidden" id="promoId" value="1">
                                
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
                                <div class="row justify-content-center">
                                    <div class="col-md-4">
    
                                        <div class="mb-3">
                                            <label for="user" class="form-label">Stock: </label>
                                            {{-- <input type="number" class="form-control" id="addAwardStock" name="addAwardStock" > --}}
                                            <div class="wrapper">
                                                <span class="minus">-</span>
                                                <input class="num" type="number" class="form-control" id="addAwardStock" name="addAwardStock" value="1" >
                                                <span class="plus">+</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-7">
                                        <label for="addAwardPrice" class="form-label">Precio en puntos: </label>
                                        <input type="number" class="form-control" id="addAwardPrice" name="addAwardPrice" >
                                        <div id="emailHelp" class="form-text">los puntos no pueden llevar decimales.</div>
                                    </div>
                                </div>

                                <div class="row justify-content-center mt-4">
                                    <div class="col-md-7">
                                        <div class="mb-3">
                                            <label for="maxPerUser" class="form-label">Maximo de veces redimidos por usuario: </label>
                                            <input type="number" class="form-control" id="maxPerUser" name="maxPerUser" aria-describedby="maxHelp" >
                                            <div id="maxHelp" class="form-text">Campo para limitar cuantas veces un usuario puede comprar este producto.</div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="postStore()">Cargar premio</button>
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

                                <div id="alertUpdateAward"></div>

                                <div class="row justify-content-center">
                                    <div class="col-md-7">
                                        <div class="mb-3">
                                            <label for="user" class="form-label">Imagen: </label>
                                            <div class="input-group mb-3">
                                                <input type="file" class="form-control" id="updateImageAward" name="updateImageAward" accept="image/*" data-type='image' onchange="changeImgToB64Product('updateImageAward')">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <input type="hidden" id="updatePromoId" >


                                <input type="hidden" id="UpdateAwardId">

                                <div class="mb-3">
                                    <label for="user" class="form-label">Nombre del premio: </label>
                                    <input type="text" class="form-control" id="updateAwardName" name="updateAwardName" >
                                </div>


                                <div class="mb-3">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="updateAwardDescriptión" name="updateAwardDescriptión" placeholder="Leave a comment here" style="height: 100px"></textarea>
                                        <label for="floatingTextarea2">Descripcion del producto</label>
                                    </div>
                                </div>

                                <div class="row justify-content-center">
                                    <div class="col-md-4">
    
                                        <div class="mb-3">
                                            <label for="updateAwardStock" class="form-label">Stock: </label>
                                            <div class="wrapper">
                                                <span class="minus" onclick="updateMin()">-</span>
                                                <input class="num" type="number" class="form-control" id="updateAwardStock" name="updateAwardStock" >
                                                <span class="plus" onclick="updatePlus()">+</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-7">
                                        <label for="updateAwardPrice" class="form-label">Precio en puntos: </label>
                                        <input type="number" class="form-control" id="updateAwardPrice" name="updateAwardPrice" >
                                        <div id="priceHelp" class="form-text">los puntos no pueden llevar decimales.</div>
                                    </div>
                                </div>


                                <div class="row justify-content-center mt-4">
                                    <div class="col-md-7">
                                        <div class="mb-3">
                                            <label for="maxPerUserUpdate" class="form-label">Maximo de veces redimidos por usuario: </label>
                                            <input type="number" class="form-control" id="maxPerUserUpdate" name="maxPerUserUpdate" aria-describedby="maxHelp" >
                                            <div id="maxHelp" class="form-text">Campo para limitar cuantas veces un usuario puede comprar este producto.</div>
                                        </div>
                                    </div>
                                </div>


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
    <script>
        
 
   </script>

    {{-- <script src="{{asset('js/image-zoom.js')}}" type="text/javascript"></script> --}}
   <script src="{{ asset('js/store.js') }}" type="text/javascript"></script>
   

@endsection
