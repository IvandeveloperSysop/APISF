function postPresentatión(window = 'index'){

 
    typeId = document.getElementById('tipoId').value;
    presentationName = document.getElementById('addPresentationName').value;


    if( presentationName  && tipoId){

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
                    url: routeG+'admin/add/presentationNonArca',
                    data: { 
                        typeId,
                        presentationName,
                        _token :$('meta[name="csrf-token"]').attr('content')
                    },
                    type : 'POST',
                    dataType: 'json',
                    success: function(resp) {
                        Swal.close();
                        console.log(resp);
                        // document.getElementById('bodyTablePresentation').innerHTML = resp.table;
                        $('#addPresentation').modal({
                            keyboard: false
                        });
                        $('#addPresentation').modal('hide');
                        document.getElementById("formAddPresentation").reset();
                        messageSave();
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

function getPresentationDetails($id){
    $.ajax({
        url: routeG+'admin/get/presentationNonArca/'+$id,
        type : 'GET',
        dataType: 'json',
        success: function(resp) {
            console.log(resp);

            document.getElementById('addPresentationName').value = resp.presentation['presentationName'];
            document.getElementById('tipoId').innerHTML = resp.selected;
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function getSelectedTypes(){
    $.ajax({
        url: routeG+'admin/get/types/presentationNonArca',
        data: {
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        dataType: 'json',
        success: function(resp) {
            console.log(resp);
            console.log('hola')

            document.getElementById('tipoId').innerHTML = resp.selected;
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function getPresentation($id){
    
    $.ajax({
        url: routeG+'admin/get/presentationNonArca/'+$id,
        type : 'GET',
        dataType: 'json',
        success: function(resp) {
            // console.log(resp.presentation['presentationName']);

            document.getElementById('updatePresentationName').value = resp.presentation['presentationName'];
            document.getElementById('presentationIdUpdate').value = resp.presentation['presentationId'];
            document.getElementById('typeIdUpdate').innerHTML = resp.selected;

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
                url: routeG+'admin/down/presentationNonArca',
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
                        messageSave();
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

function messageSave(){
    Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: 'Cambios guardados',
        showConfirmButton: false,
        timer: 1500
    });

    window.location.reload();
}

function editPresentation(){

    presentationId = document.getElementById('presentationIdUpdate').value;
    typeId = document.getElementById('typeIdUpdate').value;
    presentationName = document.getElementById('updatePresentationName').value
    $.ajax({
        url: routeG+'admin/update/presentationNonArca',
        data: {
            presentationId,
            typeId,
            presentationName,
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        dataType: 'json',
        success: function(resp) {
            // console.log(resp);

            if (resp.result == 'ok') {
                
                // document.getElementById('bodyTablePresentation').innerHTML = resp.table;
    
                $('#viewDetailsPresentation').modal({
                    keyboard: false
                });
                $('#viewDetailsPresentation').modal('hide');

                messageSave();
                
            } else {
                console.log(resp);
            }

        },
        error: function(err) {
            console.log(err);
        }
    });

}