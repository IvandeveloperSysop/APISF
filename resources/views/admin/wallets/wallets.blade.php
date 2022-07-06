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
        <h3 class="my-3">Monederos</h3>

        <table class="table table-striped">
            <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Usuario</th>
                  <th scope="col">Email </th>
                  <th scope="col">Saldo</th>
                  <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="bodyTable">
                @foreach ($wallets as $wallet)
                <tr>
                    <th scope="row">{{ $wallet->userId }}</th>
                    <td>{{ $wallet->name }}</td>
                    <td>{{ $wallet->email }}</td>
                    <td>{{ $wallet->balance }}</td>
                    <td style="width: 15vw;" class="text-center">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#viewDetailsWallet" onclick="searchInfoWallet({{$wallet->userId}})" class="btn btn-success mb-2">Editar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $wallets->links('pagination::bootstrap-4') }}
@endsection

@section('modals')
    
    <div class="modal fade" id="viewDetailsWallet" tabindex="-1" aria-labelledby="exampleModalPromo" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalPromo">Información del las transacciónes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formAddAward" action="{{ route('addAward')  }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-md-10">

                                <table class="table table-striped text-center">
                                    <thead>
                                        <tr>
                                          <th scope="col">Mensage</th>
                                          <th scope="col">Monto </th>
                                          <th scope="col">Fecha</th>
                                          <th scope="col">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyTableTransaction">
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary"> Modificar saldo</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="{{ asset('js/filtersShare.js') }}" ></script>
    <script src="{{ asset('js/wallets.js') }}" ></script>

@endsection