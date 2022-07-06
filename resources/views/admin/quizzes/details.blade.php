@extends('layouts.layout')

@section('css')
    <link href="{{asset('css/imagestyle.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/image-zoom.css')}}">
    <link rel="stylesheet" href="{{ asset('css/cardAward.css') }}">
    <style>
        .tableDetails{
            height: 300px;
        }
        .table-overflow{
            max-height: 632px;
            overflow: auto;
        }
    </style>

@endsection

@section('content')

    <div class=" d-flex my-4">
        <div class="">
            <a type="button" class="btn btn-outline-primary me-3 btn-sm" href="{{ route('adminQuizzes') }}">
                <i class="fas fa-arrow-left me-1"></i>Regresar
            </a>
        </div>
    </div>
    <h3 class="mt-5">Listado de preguntas</h3>
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
                            <a type="button" class="btn text-secondary" data-bs-toggle="modal" data-bs-target="#addQuestion" ><i class="fas fa-plus"></i></a>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <div class="row justify-content-center">

                        <div class="col-md-12 table-overflow">
                            <table class="table text-center">
                                <thead>
                                    <tr>
                                    <th scope="col">Pregunta</th>
                                    <th scope="col">Tipo de trivia</th>
                                    <th scope="col">Tipo de pregunta</th>
                                    <th scope="col">Respuestas</th>
                                    </tr>
                                </thead>

                                <tbody id="bodyTablePopUp">
                                    @foreach ($questions as $q)
                                        <tr>
                                            <td>{{ $q->title }}</td>
                                            <td>{{ $q->quiz->title }}</td>
                                            <td>{{ $q->type->name }}</td>
                                            <td>
                                                <div class="row justify-conten-center">
                                                    <div class="col-md-6">
                                                        @if ($q->type->id == 10)
                                                            <a type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#findAnswers" onclick="findAnswers({{ $q->id }})">
                                                                <i class="fas fa-eye me-2"></i>Ver respuestas
                                                            </a>
                                                        @else
                                                            <p class="text-success"> Pregunta con respuesta libre </p>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        <form id="deleteQuestionForm" action="{{route('deleteQuestion')}}" method="post" enctype="multipart/form-data">
                                                            @method('PUT')
                                                            @csrf
                                                            <input type="hidden" value="{{ $q->id }}" id="questionId_delete" name="questionId_delete">
                                                            <input type="hidden" value="{{ $q->quiz->id }}" id="quizId_delete" name="quizId_delete">
                                                            <a type="button" class="btn btn-danger" onclick="deleteQuestion()" >
                                                                <i class="fas fa-eye me-2"></i>Borrar pregunta
                                                            </a>
                                                        </form>
                                                    </div>
                                                </div>
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

    {{-- Modal new Question --}}
        <div class="modal fade" id="addQuestion" tabindex="-1" aria-labelledby="exampleModalPromo" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalPromo">Agregar pregunta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formAddQuestion" action="{{route('addQuestion')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-md-10">
                                    <div class="row justify-content-center">
                                        <div class="col-md-8">
                                            <div id="alertAddQuestion">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="user" class="form-label">Pregunta: </label>
                                        <input type="text" class="form-control" id="title_question" name="title_question" >
                                    </div>

                                    <div class="row justify-content-center">
                                        <div class="col-md-6 mb-3">
                                            <select class="form-select" id="type_id" name="type_id" aria-label="Default select example">
                                                @foreach ($types as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="addQuestion()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    {{-- End Modal new Question --}}


    {{-- Modal details Question --}}
        <div class="modal fade" id="findAnswers" tabindex="-1" aria-labelledby="exampleModalPromo" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalPromo">Modificar pregunta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formAddPopUp"  method="post" enctype="multipart/form-data">
                            <div class="row justify-content-center">
                                <div class="col-md-10">
                                    <div class="row justify-content-center">
                                        <div class="col-md-8">
                                            <div id="alertUpdateQuestion">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="user" class="form-label">Pregunta: </label>
                                        <input type="text" class="form-control" id="title_questionAnswer" name="title_questionAnswer" >
                                    </div>

                                    <input type="hidden" id="questionIdDetails">

                                    <div class="row justify-content-center">

                                        <div class="col-md-12">

                                            <div class="card rounded cardsDetails" >
                                                <div class="card-header">
                                                    <div class="row justify-content-end">
                                                        <div class="col-md 3">
                                                            <h3>Respuestas</h3>
                                                        </div>

                                                        <div class="col-md-5 text-end buttonsTable">
                                                            <a type="button" class="btn text-secondary" data-bs-toggle="modal" data-bs-target="#addAnswerModal" onclick="resetValidAnswer()"><i class="fas fa-plus"></i></a>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card-body">

                                                    <div class="row justify-content-center">

                                                        <div class="col-md-12">
                                                            <table class="table text-center">
                                                                <thead>
                                                                    <tr>
                                                                    {{-- <th scope="col">#</th> --}}
                                                                    <th scope="col">Respuesta</th>
                                                                    <th scope="col">Es una respuesta correcta</th>
                                                                    <th scope="col">Detalles</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody id="bodyTableAnswers">
                                                                </tbody>

                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="updateQuiz()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    {{-- End modal details Questions --}}

    {{-- Modal add Answer --}}
    <div class="modal fade" id="addAnswerModal" tabindex="-1" aria-labelledby="exampleModalPromo" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalPromo">Agregar respuesta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formAddAnswer"  method="post" enctype="multipart/form-data">
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <div class="row justify-content-center">
                                    <div class="col-md-8">
                                        <div id="alertAnswerAdd">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="user" class="form-label">Respuesta: </label>
                                    <input type="text" class="form-control" id="answer_add" name="answer_add" >
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="validAnswer" name="validAnswer" onclick="validAnswerF()">
                                    <label class="form-check-label" for="validAnswer">Respuesta correcta</label>
                                    <div id="validAnswerHelp" class="form-text">Al seleccionar esta casilla estas aceptando que esta opción es la respuesta correcta.</div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="showModalQDetails()">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="addAnswer()">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    {{-- End Modal add Answer --}}

    {{-- Modal update Answer --}}
    <div class="modal fade" id="updateAnswerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalPromo" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalPromo">Modificar respuesta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formUpdateAnswer"  method="post" enctype="multipart/form-data">
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <div class="row justify-content-center">
                                    <div class="col-md-8">
                                        <div id="alertAnswerUpdate">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="user" class="form-label">Respuesta: </label>
                                    <input type="text" class="form-control" id="answer_update" name="answer_update" >
                                </div>

                                <input type="hidden" id="answer_id_update">
                                <input type="hidden" id="question_id_update">

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="validAnswerUpdate" name="validAnswerUpdate" onclick="validAnswerF()">
                                    <label class="form-check-label" for="validAnswerUpdate">Respuesta correcta</label>
                                    <div id="validAnswerHelp" class="form-text">Al seleccionar esta casilla estas aceptando que esta opción es la respuesta correcta.</div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="showModalQDetails()">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="updateAnswer()">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    {{-- End Modal update Answer --}}

@endsection

@section('js')
    {{-- <script src="{{asset('js/image-zoom.js')}}" type="text/javascript"></script> --}}
    <script src="{{ asset('js/quizzes.js') }}" type="text/javascript"></script>


@endsection
