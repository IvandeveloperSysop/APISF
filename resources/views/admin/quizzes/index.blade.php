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
    <h3 class="mt-5">Listado de trivias</h3>
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

                        <div class="col-md-5 text-end buttonsTable">
                            <a type="button" class="btn text-secondary" data-bs-toggle="modal" data-bs-target="#addQuizModal"  ><i class="fas fa-plus"></i></a>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <div class="row justify-content-center">

                        <div class="col-md-11">
                            <table class="table text-center">
                                <thead>
                                    <tr>
                                    <th scope="col">Titulo</th>
                                    <th scope="col">Promoción</th>
                                    <th scope="col">Detalles</th>
                                    </tr>
                                </thead>

                                <tbody id="bodyTablePopUp">
                                    @foreach ($quizzes as $quiz)
                                        <tr>
                                            <td>{{ $quiz->title }}</td>
                                            <td>{{ $quiz->promo->title }}</td>
                                            <td>
                                                <a type="button" class="btn btn-success" href="{{route('quizDetails',['id'=> $quiz->id])}}">
                                                    <i class="fas fa-eye me-2"></i>Ver detalles
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                    {{ $quizzes->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection

@section('modals')

    {{-- Modal new Pop up --}}
        <div class="modal fade" id="addQuizModal" tabindex="-1" aria-labelledby="exampleModalPromo" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalPromo">Agregar trivia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formAddPopUp"  method="post" enctype="multipart/form-data">
                            <div class="row justify-content-center">
                                <div class="col-md-10">
                                    <div class="row justify-content-center">
                                        <div class="col-md-8">
                                            <div id="alertAddPopUp">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="user" class="form-label">Titulo del la trivia: </label>
                                        <input type="text" class="form-control" id="title_quiz" name="title_quiz" >
                                    </div>

                                    <div class="mb-3">
                                        <label for="promo" class="form-label">Promoción: </label>
                                        <select class="form-select" id="promo_id" aria-label="Default select example">
                                            @foreach ($promos as $promo)
                                                <option value="{{$promo->id}}">{{$promo->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="addQuiz()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    {{-- End Modal new Pop Up --}}

@endsection

@section('js')
    {{-- <script src="{{asset('js/image-zoom.js')}}" type="text/javascript"></script> --}}
    <script src="{{ asset('js/quizzes.js') }}" type="text/javascript"></script>


@endsection
