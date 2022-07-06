function postPresentatión(window = 'index'){

 
    promoId = document.getElementById('promoId').value;
    presentationName = document.getElementById('addPresentationName').value;
    presentationPoints = document.getElementById('addPresentationPoints').value;


    if( presentationName && presentationPoints && promoId){

        Swal.fire({
            title: '¿Deseas agregar la presentación?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: `Guardar`,
            denyButtonText: `No guardar`,
        }).then((result) => {
            if (result.isConfirmed) {
                
                loading();
                $.ajax({
                    url: routeG+'admin/add/presentation',
                    data: { 
                        promoId,
                        presentationName,
                        presentationPoints,
                        _token :$('meta[name="csrf-token"]').attr('content')
                    },
                    type : 'POST',
                    dataType: 'json',
                    success: function(resp) {
                        Swal.close();
                        console.log(resp);
                        document.getElementById('bodyTablePresentation').innerHTML = resp.table;
                        $('#addPresentation').modal({
                            keyboard: false
                        });
                        $('#addPresentation').modal('hide');
                        document.getElementById("formAddPresentation").reset();
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
                
            }
        });
    }else{
        document.getElementById('alertModalAddPosition').innerHTML = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
            "Favor de llenar todos los campos"+
            "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>"+
        "</div>";
    }

}

function getPromoSelect(){

    $.ajax({
        url: routeG+'admin/get/promos/selected/awards',
        data: { 
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        dataType: 'json',
        success: function(resp) {
            // console.log(resp);
            document.getElementById('promoId').innerHTML = resp.selected;
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function getPresentation($id){
    $.ajax({
        url: routeG+'admin/get/presentation/'+$id,
        type : 'GET',
        dataType: 'json',
        success: function(resp) {
            console.log(resp);

            document.getElementById('updatePresentationName').value = resp.presentation['presentationName'];
            document.getElementById('promoIdUpdate').innerHTML = resp.selected;
            document.getElementById('updatePresentationPoints').value = resp.presentation['points'];
            document.getElementById('presentationIdUpdate').value = resp.presentation['presentationId'];
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function editPresentation(){

    presentationId = document.getElementById('presentationIdUpdate').value;
    presentationName = document.getElementById('updatePresentationName').value
    promoId = document.getElementById('promoIdUpdate').value
    points = document.getElementById('updatePresentationPoints').value
    $.ajax({
        url: routeG+'admin/update/presentation',
        data: {
            presentationId,
            presentationName,
            promoId,
            points,
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        dataType: 'json',
        success: function(resp) {
            // console.log(resp);

            document.getElementById('bodyTablePresentation').innerHTML = resp.table;

            $('#viewDetailsPresentation').modal({
                keyboard: false
            });
            $('#viewDetailsPresentation').modal('hide');

                        
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: 'Cambios guardados',
                showConfirmButton: false,
                timer: 1500
            });

        },
        error: function(err) {
            console.log(err);
        }
    });

}


function downPresentation(presentation_id){

    Swal.fire({
        title: '¿Eliminar presentación?',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Aceptar`,
        denyButtonText: `Cacelar`,
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: routeG+'admin/down/presentation',
                data: {
                    presentation_id,
                    _token :$('meta[name="csrf-token"]').attr('content')
                },
                type : 'POST',
                dataType: 'json',
                success: function(resp) {
                    console.log(resp);
                    if(resp.result == 'ok'){
                        // document.getElementById('bodyTableBody').innerHTML = resp.table;
                        
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'La presentación fue dada de baja',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        location.reload();
                    }else{
                        console.log(resp)
                    }
                },
                error: function(err) {
                    console.log(err);
                }
            });
            
        }
    });

}