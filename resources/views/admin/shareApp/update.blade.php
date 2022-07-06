@extends('layouts.layout')
@section('css')
    <link href="{{asset('css/imagestyle.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/image-zoom.css')}}">
	{{-- <link rel="stylesheet" href="{{asset('css/style.css')}}"> --}}

@endsection
@section('content')
    <div class=" my-5">
        <input type="hidden" value="{{$appShare["id"]}}" id="ticketId">
        <div class="row">
            <div class=" d-flex mb-4">
                <div class="pt-1">
                    <a type="button" class="btn btn-outline-primary me-3 btn-sm" href="{{ url()->previous() }}">
                        <i class="fas fa-arrow-left me-1"></i>Regresar
                    </a>
                </div>
                <div>
                    <h3>Compartidos en redes sociales</h3>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <form>

                    <div class="row">
                        <div class="col-lg-5 col-md-12">
                            <div class="mb-3">
                              <label for="user" class="form-label">Usario</label>
                              <input type="text" class="form-control" id="user" name="user" value="{{$appShare["name"]}}" readonly="readonly">
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-12">
                            <div class="mb-3">
                              <label for="user" class="form-label">Url</label>
                              <input type="text" class="form-control" id="user" name="user" value="{{$appShare["url"]}}" readonly="readonly">
                            </div>
                        </div>
                        @if ($appShare["validate"] != 0)
                        <div class="mb-3">
                            <label for="user" class="form-label">Modificado por:</label>
                            <input type="text" class="form-control" id="date_ticket" name="date_ticket" value="{{$appShare["adminName"]}}" readonly="readonly">
                        </div>
                        @endif

                    </div>

                    <div class="row">

                        <div class="col-lg-5 col-md-12">
                            <div class="card  container_foto " data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12 ">
                                        <div class="ver_mas text-center">
                                            <span ><i class="fas fa-eye"></i></span>
                                        </div>
                                        <img src="{{$appShare["image"]}}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-7 col-md-12 mt-4">

                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3" id="estatusDiv">
                                        <label for="user" class="form-label me-4">Estatus:</label>
                                        @if ($appShare["statusId"] == 9)
                                            <button type="button" class=" btn btn-warning rounded-pill text-white text-center">En revisión</button>
                                        @elseif ($appShare["statusId"] == 8)
                                            <button type='button' class=' btn btn-success rounded-pill text-white text-center'>
                                                {{$appShare["status"]}}
                                            </button>
                                        @else 
                                            <button type='button' class=' btn btn-danger rounded-pill text-white text-center'>
                                                {{$appShare["status"]}}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-8 col-md-6">
                                    <div class="mb-3">
                                        <select class="form-select form-select-lg" aria-label=".form-select-sm example" id="selectOption">
                                            <option selected>Selecciona el estatus</option>
                                            @foreach ($status as $statu)
                                                @if ($statu->name == 'Revisando')
                                                    {{-- <option value="{{$statu->id}}" class="text-warning">En revision</option> --}}
                                                @elseif ($statu->name == 'Aprobado')
                                                    <option value="{{$statu->id}}" class="text-success">{{$statu->name}}</option>
                                                @elseif ($statu->name == 'Cancelado')
                                                    <option value="{{$statu->id}}" class="text-danger">{{$statu->name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <div id="validationServer03Feedback" class="invalid-feedback">
                                            Favor de seleccionar el estatus
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="exampleFormControlTextarea1" class="form-label">Comentario</label>
                                <textarea class="form-control" id="comment" rows="5" >{{$appShare["comment"]}}</textarea>
                                {{-- {{$appShare->comment}} --}}
                                <div id="validationServer03Feedback" class="invalid-feedback">
                                    Campo obligatorio para el estatus de cancelado
                                </div>
                            </div>

                        </div>

                    </div>
    
                    @if ($appShare["validate"] == 0)
                        <div class="text-end" id="buttonSubmit">
                            <button type="button" onclick="validOption()" class="btn btn-primary">Actualizar</button>
                        </div>
                    @else 
                        <div class="text-end">
                            <button type="button" class="btn btn-primary" disabled>Actualizar</button>
                        </div>
                    @endif
                </form>

                <div class="modal fade" id="staticBackdrop"  tabindex="-1"  >
                    <div class="modal-dialog" >
                        <div class="modal-content" style="width: 700px;">
                            <div class="modal-body">
                                <img id="imageZoom" src="{{$appShare["image"]}}">
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
	<script>

		var $ = jQuery.noConflict();

		$(document).ready(function(){

			// Image zoom plugin code
			var zoomImage = $('#imageZoom');
			var zoomImageExtra = $('#imageZoomExtra');
			// var zoomImagePlus = $('#imageZoomExtraPlus');
			var zoomImages = $('.zoom-images');

			zoomImage.imageZoom();
			// zoomImageExtra.imageZoom({zoom : 200});
			// zoomImagePlus.imageZoom({zoom : 300});

			zoomImages.each(function() {
				$(this).imageZoom();
			});

        });
        
        function validOption(){
            status = document.getElementById('selectOption').value;
            // numTicket = document.getElementById('numTicket').value;
            if(status === "Selecciona el estatus"){
                document.getElementById('selectOption').classList.add('is-invalid');
            }else{
                document.getElementById('selectOption').classList.remove('is-invalid');
                if(status == 6){
                    comment = document.getElementById('comment').value;
                    if(!comment){
                        document.getElementById('comment').classList.add('is-invalid');
                        return;
                    }
                }else{
                    comment = document.getElementById('comment').value;
                    if(!comment){
                        comment = 'nada';
                    }
                }
                document.getElementById('comment').classList.remove('is-invalid');
                Swal.fire({
                    title: '¿Quieres guardar los cambios?',
                    showDenyButton: true,
                    showCancelButton: false,
                    confirmButtonText: `Guardar`,
                    denyButtonText: `No guardar`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        ticketId = document.getElementById('ticketId').value;
                        $.ajax({
                            url: 'conf/'+ticketId+'/'+status+'/'+comment,
                            type : 'GET',
                            dataType: 'json',
                            success: function(respuesta) {
                                // console.log(respuesta);
                                if(respuesta.message == 'ok'){
                                    document.getElementById('estatusDiv').innerHTML = respuesta.result;
                                    document.getElementById('buttonSubmit').innerHTML = "<div class='text-end'> <button type='button' class='btn btn-primary' disabled>Submit</button> </div>";
                                    Swal.fire({
                                        position: 'top-end',
                                        icon: 'success',
                                        text: 'Información guardada',
                                        showConfirmButton: false,
                                        timer: 2100
                                    });
                                }else{
                                    console.log(respuesta.result);
                                    Swal.fire('error!', '', 'error');
                                }
                            },
                            error: function() {
                                console.log("No se ha podido obtener la información de los empleados");
                            }
                        });
                        
                    } 
                });
                
            }
        }

	</script>
@endsection
