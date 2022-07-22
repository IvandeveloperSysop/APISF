@extends('layouts.layout')
@section('css')
    <link href="{{asset('css/imagestyle.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/image-zoom.css')}}">
	{{-- <link rel="stylesheet" href="{{asset('css/style.css')}}"> --}}
    <style>
        .swal2-popup.swal2-modal.swal2-icon-error.swal2-show {
            height: 22vh;
            /* background-color: ; */
        }

        .table-height{
            display: block;
            height: 28.9vh;
            overflow: auto;
        }
    </style>
@endsection
@section('content')
    <div class=" my-5">
        <input type="hidden" value="{{$ticket["id"]}}" id="ticketId">
        <div class="row">
            <div class=" d-flex mb-4">
                <div class="">
                    <a type="button" class="btn btn-outline-primary me-3 btn-sm" href="{{ route($view) }}">
                        <i class="fas fa-arrow-left me-1"></i>Regresar
                    </a>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="mt-5">
                    <h1>Ticket  <small style="font-size: 1.6rem">{{$ticket["created_at"]}}</small></h1>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div id="alerts">
                        </div>
                    </div>
                </div>

                <form >

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user" class="form-label">Promoción</label>
                                <input type="text" class="form-control" id="namePromo" name="namePromo" value="{{$ticket["namePromo"]}}" readonly="readonly">
                            </div>
                        </div>

                        <div class="col-md-6">
                            @if ($ticket["validate"] != 0)
                                <div class="mb-3 ">
                                    <label for="user" class="form-label">Modificado por:</label>
                                    <input type="text" class="form-control" id="date_ticket" name="date_ticket" value="{{$ticket["adminName"]}}" readonly="readonly">
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user" class="form-label">Usario <b>(Id usuario: {{$ticket["userId"]}})</b></label>
                                <input type="text" class="form-control" id="user" name="user" value="{{$ticket["userName"]}}" readonly="readonly">
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="mb-3">
                                <label for="user" class="form-label">Fecha del ticket</label>
                                <input type="date" class="form-control" id="date_ticket" name="date_ticket" value="{{$ticket["date_ticket"]}}" readonly="readonly">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">

                        <div class="col-md-6">
                            <div class="card rounded cardsDetails" >
                                <div class="card-header">
                                    <div class="row justify-content-end">
                                        <div class="col-md 3">
                                            <h3>Productos ARCA</h3>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body p-0">

                                    <div class="row justify-content-center">

                                        <div class="col-md-12 ">
                                            <table class="table mb-0 table-height">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Presentación</th>
                                                        <th scope="col">Cantidad</th>
                                                        <th scope="col">Precio</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if ($ticket["statusId"] == 2)
                                                        @foreach ($presentations as $presentation)
                                                            <tr>
                                                                <th scope="row" style="width: 45%">{{ $presentation->name }}</th>
                                                                <td><input type="number" class="form-control presentation" id="presentation{{$presentation->id}}"  name="presentation{{$presentation->id}}" value="{{$presentation->quantity}}" onchange="productAll({{$presentation->id}})"></td>
                                                                <td>
                                                                    <div class="input-group mb-3">
                                                                        <span class="input-group-text">$</span>
                                                                        <input type="number" class="form-control" id="presentationPrice{{$presentation->id}}"  name="presentationPrice{{$presentation->id}}"  onchange="productAll({{$presentation->id}})" value="{{$presentation->price}}">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        @foreach ($presentations as $presentation)
                                                            <tr>
                                                                <th scope="row" style="width: 45%">{{ $presentation->name }}</th>
                                                                <td>
                                                                    <input type="number" class="form-control presentation" id="presentation{{$presentation->id}}"  name="presentation{{$presentation->id}}" value="{{$presentation->quantity}}" readonly="readonly">
                                                                </td>
                                                                <td>
                                                                    <div class="input-group mb-3">
                                                                        <span class="input-group-text">$</span>
                                                                        <input type="number" class="form-control" id="presentationPrice{{$presentation->id}}"  name="presentationPrice{{$presentation->id}}" value="{{$presentation->price}}" readonly="readonly">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row justify-content-end mt-4">
                                <div class="col-md-4 text-end">
                                    Total
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" value="{{$totalPresentations->total}}" id="totalPriceProducts" readonly="readonly">
                                    </div>
                                </div>
                            </div>

                        </div>

                        @php
                            $showNonPresentation = true
                        @endphp

                        @if ($showNonPresentation)

                            <div class="col-md-6">
                                <div class="card rounded cardsDetails" >
                                    <div class="card-header">
                                        <div class="row justify-content-end">
                                            <div class="col-md 3">
                                                <h3>Otros productos</h3>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body p-0">

                                        <div class="row justify-content-center">

                                            <div class="col-md-12 ">

                                                <table class="table mb-0 table-height">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Presentación</th>
                                                            <th scope="col">Cantidad</th>
                                                            <th scope="col">Precio</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody >
                                                        @foreach ($presentationsNotArca as $presentation)
                                                            <tr>
                                                                <th scope="row" style="width: 45%">{{ $presentation->name }}</th>
                                                                @if ($ticket["statusId"] == 2)

                                                                    <td>
                                                                        <input type="number" class="form-control" id="presentationOther{{$presentation->id}}"  name="presentationOther{{$presentation->id}}" onchange="productOtherAll({{$presentation->id}})"  value="{{$presentation->quantity}}">
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group mb-3">
                                                                            <span class="input-group-text">$</span>
                                                                            <input type="number" class="form-control" id="presentationOtherPrice{{$presentation->id}}"  name="presentationOtherPrice{{$presentation->id}}" onchange="productOtherAll({{$presentation->id}})" value="{{$presentation->price}}">
                                                                        </div>
                                                                    </td>

                                                                @else

                                                                    <td>
                                                                        <input type="number" class="form-control" id="presentationOther{{$presentation->id}}"  name="presentationOther{{$presentation->id}}" value="{{$presentation->quantity}}" readonly="readonly">
                                                                    </td>
                                                                    <td>
                                                                        <div class="input-group mb-3">
                                                                            <span class="input-group-text">$</span>
                                                                            <input type="number" class="form-control" id="presentationOtherPrice{{$presentation->id}}"  name="presentationOtherPrice{{$presentation->id}}" value="{{$presentation->price}}" readonly="readonly">
                                                                        </div>
                                                                    </td>

                                                                @endif
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="row justify-content-end mt-4">
                                    <div class="col-md-4 text-end">
                                        Total
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" value="{{$totalPresentationsNonArca->total}}" id="totalPriceProductsNotArca" readonly="readonly">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endif




                    </div>

                    <div class="row">
                        <div class="col-lg-5 col-md-12">
                            <div class="card  container_foto " >
                                {{-- data-bs-toggle="modal" data-bs-target="#staticBackdrop" --}}
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12 ">
                                        {{-- <div class="ver_mas text-center">
                                            <span ><i class="fas fa-eye"></i></span>
                                        </div> --}}
                                        <a onclick="windoNewOpen('{{$ticket['image']}}')">
                                            <img src="{{$ticket["image"]}}">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($ticket["validate"] != 0 && $ticket['statusId'] == 1)
                            <div class="row justify-content-end">
                                <div class="col-md-4 mt-4">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="mb-3 ">
                                                <label for="user" class="form-label">Puntos obtenidos:</label>
                                                <input type="text" class="form-control" id="date_ticket" name="date_ticket" value="{{$ticket["score"]}}" readonly="readonly">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-lg-7 col-md-12 mt-4">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="user" class="form-label"># Ticket</label>
                                        <input type="text" class="form-control" id="numTicket" name="numTicket" value="{{$ticket["numTicket"]}}" disabled>
                                        <div id="validNumTicket" class="invalid-feedback">
                                            CampoObligatorio
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">

                                    <div class="mb-3">
                                        <label for="user" class="form-label">Hora del ticket</label>
                                        <div class="input-group date" id="timepicker" data-target-input="nearest">
                                            <input type="time" class="form-control datetimepicker-input" id="hour_ticket" value="{{ $ticket['hour_ticket'] }}" data-target="#timepicker" disabled>
                                            <div id="validNumHour" class="invalid-feedback">
                                                Campo obligatorio
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="mb-3">
                                <label for="user" class="form-label">Comentarios</label>
                                @if ($ticket["validate"] == 0)
                                    <select class="form-select" id="comment" aria-label="Default select example" disabled>
                                        <option selected>Comentario</option>
                                        <option>Fecha de ticket no corresponde al periodo.</option>
                                        <option>Ticket de compra es de una Ciudad que no entra en la promoción.</option>
                                        <option>El ticket de compra es de un establecimiento que no participa en la promoción.</option>
                                        <option>Los productos que están en el ticket no participan en esta promoción.</option>
                                        <option>Foto de ticket borrosa o incompleta. (Favor de subirla de nuevo)</option>
                                        <option>No se ve el numero de ticket. (Favor de subirla de nuevo)</option>
                                        <option>Ticket duplicado.</option>
                                        <option>Ticket con leyenda “Duplicado Cliente”.</option>
                                        <option>Participante bloqueado por violación a Bases, Términos y Condiciones del concurso.</option>
                                        <option>Favor de enviar correo a promociones@desafiosoccerfans.com o llamar al teléfono (81) 4444 1019.</option>
                                        <option>Pruebas sysop.</option>
                                    </select>

                                    <select class="form-select" id="commentInvalid" aria-label="Default select example" style="display: none;">
                                        <option selected>Comentario</option>
                                        <option>Ticket invalidado 1.</option>
                                        <option>Ticket invalidado 2.</option>
                                    </select>
                                @else
                                    <select class="form-select" id="comment" aria-label="Default select example" disabled>
                                        @if ($ticket["comment"])
                                            <option selected>{{ $ticket["comment"] }}</option>
                                        @else
                                            <option selected>Comentario</option>
                                        @endif
                                    </select>
                                @endif
                                <div id="validationServer03Feedback" class="invalid-feedback">
                                    Campo obligatorio para el estatus de cancelado
                                </div>
                            </div>

                            {{-- Monto total y cantidad de productos --}}
                                <label for="store" class="form-label me-4">Tienda:</label>
                                <div class="mb-3">
                                    @if ($ticket["validate"] == 0)

                                        <select class="form-select" id="store" aria-label="Default select example" disabled>
                                            <option value="7-Eleven"  {{ $ticket['store'] == '7-Eleven' ? 'selected' : '' }} >7-Eleven</option>
                                            {{-- <option value="Del Río"  {{ $ticket['store'] == 'Del Río' ? 'selected' : '' }} >Del Río</option>
                                            <option value="Extra"  {{ $ticket['store'] == 'Extra' ? 'selected' : '' }} >Extra</option>
                                            <option value="Farmacias Guadalajara"  {{ $ticket['store'] == 'Farmacias Guadalajara' ? 'selected' : '' }} >Farmacias Guadalajara</option>
                                            <option value="Kiosko"  {{ $ticket['store'] == 'Kiosko' ? 'selected' : '' }} >Kiosko</option>--}}
                                            {{-- <option value="Go-Mart"  {{ $ticket['store'] == 'Go-Mart' ? 'selected' : ''; }} selected>Go-Mart</option> --}}
                                            {{-- <option value="Súper City"  {{ $ticket['store'] == 'Super City' ? 'selected' : ''; }} selected>Super City</option> --}}
                                        </select>
                                    @else
                                        <select class="form-select" id="store" aria-label="Default select example" disabled>
                                            <option selected>7-Eleven</option>
                                        </select>
                                    @endif
                                    <div id="validationServer03Feedback" class="invalid-feedback">
                                        Campo obligatorio para el estatus de cancelado
                                    </div>
                                </div>
                                <input type="hidden" class="form-control" id="state" value="{{ $ticket["nameSate"] }}" disabled>


                            <div class="row">

                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3" id="estatusDiv">
                                        <label for="user" class="form-label me-4">Estatus:</label>
                                        @if ($ticket["status"] == 'Revisando')
                                            <button type="button" class=" btn btn-warning rounded-pill text-white text-center">En revisión</button>
                                        @elseif ($ticket["status"] == 'Aprobado')
                                            <button type='button' class=' btn btn-success rounded-pill text-white text-center'>
                                                {{$ticket["status"]}}
                                            </button>
                                        @elseif ($ticket["status"] == 'invalid')
                                            <button type='button' class=' btn btn-info rounded-pill text-white text-center'>
                                                Ticket Invalidado
                                            </button>
                                        @else
                                            <button type='button' class=' btn btn-danger rounded-pill text-white text-center'>
                                                {{$ticket["status"]}}
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                @if ($ticket["status"] != 'invalid')
                                    <div class="col-lg-7 col-md-7">
                                        <div class="mb-3 pt-3">
                                            <select class="form-select form-select-lg" aria-label=".form-select-sm example" id="selectOption" onchange="validStatus()">
                                                <option selected>Selecciona el estatus</option>
                                                @foreach ($status as $statu)
                                                    @if ($statu->name == 'Revisando')
                                                        {{-- <option value="{{$statu->id}}" class="text-warning">En revision</option> --}}
                                                    @elseif ($statu->name == 'Aprobado')
                                                        <option value="{{$statu->id}}" class="text-success">{{$statu->name}}</option>
                                                    @elseif ($statu->name == 'Cancelado')
                                                        <option value="{{$statu->id}}" class="text-danger">{{$statu->name}}</option>
                                                    @elseif ($statu->name == 'invalid')
                                                        <option value="{{$statu->id}}" class="text-danger">Ticket invalido</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <div id="validationServer03Feedback" class="invalid-feedback">
                                                Favor de seleccionar el estatus
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>
                        @if ($ticket["validate"] == 0)
                            <div class="text-end" id="button-submitSave">
                                <button type="button" onclick="validOption()" class="btn btn-primary">Guardar</button>
                            </div>
                        @else
                            <div class="text-end">
                                <button type="button" class="btn btn-primary" disabled>Guardar</button>
                            </div>
                        @endif
                    </div>

                    <input type="hidden" class="form-control" id="products" name="user" disabled>

                </form>

                <div class="modal fade" id="staticBackdrop"  tabindex="-1"  >
                    <div class="modal-dialog" >
                        <div class="modal-content" style="width: 700px;">
                            <div class="modal-body">
                                <img id="imageZoom" src="{{$ticket["image"]}}">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="{{asset('js/image-zoom.js')}}" type="text/javascript"></script>
    <script src="{{ asset('js/updateTicket.js') }}" type="text/javascript"></script>
@endsection
