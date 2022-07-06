@extends('layouts.layout')

@section('content')
    <div class=" mt-4">
        <h3 class="my-3">Tickets pendientes de validar</h3>

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

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="user_name" placeholder="Nombre del usuario">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Promoción</label>
                                    <select class="form-select" id="selectPromoId" onchange="enableCorte()">
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

                            <div class="col-md-6">

                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Corte</label>
                                    <select class="form-select" id="corteSelected" aria-label="Disabled select example" disabled>
                                        <option value='0' selected>Favor de seleccionar una promoción valida</option>
                                    </select>
                                </div>
                            </div>

                            <div class="text-end">
                                <a type="button" class="btn btn-primary" onclick="filterTickets(2)">Buscar</a>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Usuario</th>
                  <th scope="col">Promo</th>
                  <th scope="col">Fecha ticket</th>
                  <th scope="col">Corte</th>
                  <th scope="col">Estatus</th>
                  <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody id="bodyTable">
                @foreach ($tickets as $ticket)
                <tr>
                    <th scope="row">{{ $ticket->id }}</th>
                    <td>{{ $ticket->name }}</td>
                    <td>{{ $ticket->promo_name }}</td>
                    <td>{{ $ticket->date_ticket }}</td>
                    <td>Corte {{ $ticket->orderNum }}</td>
                    <td class="text-warning">{{ $ticket->status }}</td>
                    <td style="width: 15vw;" >
                        <a type="button" href="{{route('ticketUpdate',['id'=> $ticket->id, 'view' => 'admin'])}}" class="btn btn-success mb-2">Editar</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="paginationTable">
        {{ $tickets->links('pagination::bootstrap-4') }}
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="{{ asset('js/filtersTickets.js') }}"></script>

@endsection