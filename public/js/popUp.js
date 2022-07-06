let addImage;
let extension;
let updateImage;

async function changeImgToB64(inputName){
    let file = document.getElementById(inputName).files[0];
    extension = document.getElementById(inputName).files[0]['name'].split('.').pop()
    const  fileType = file['type'];
    const validImageTypes = ['image/gif', 'image/jpeg', 'image/png'];
    if (!validImageTypes.includes(fileType)) {
        console.log('nonImage');
    }else{
        if(inputName == "updateImagePop"){
            updateImage = toBase64(file);
        }else{
            addImage = toBase64(file);
        }
    }
    return;
}

toBase64 = file => new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result);
    reader.onerror = error => reject(error);
});


function addPopUp(){


    title = document.getElementById('addTitlePopUp').value;
    content = document.getElementById('descriptionPopUp').value;

    if( title && content && addImage ){

        if(addImage && extension){
            image = addImage._value;
        }else{
            image = "";
        }
        
        Swal.fire({
                title: '¿Desea agregar el nevo mensaje de inicio?',
                text: 'Se dara de baja los otros avisos para poder mostrar este.',
                showDenyButton: true,
                showCancelButton: false,
                confirmButtonText: `Guardar`,
                denyButtonText: `No guardar`,
            }).then((result) => {
            if (result.isConfirmed) {
                
                $.ajax({
                    url: routeG+'admin/add/popUp',
                    data: { 
                        image,
                        extension,
                        title,
                        content,
                        _token :$('meta[name="csrf-token"]').attr('content')
                    },
                    type : 'POST',
                    dataType: 'json',
                    success: function(resp) {
                        console.log(resp);
                        document.getElementById('bodyTablePopUp').innerHTML = resp.table;
                        $('#addPopUp').modal({
                            keyboard: false
                        });
                        $('#addPopUp').modal('hide');
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            }
        });

    }else{
        if(!title){
            document.getElementById('addTitlePopUp').classList.add('is-invalid');
            message = "favor completar los cmapos obligatorios";
        }
        
        if(!description){
            document.getElementById('descriptionPopUp').classList.add('is-invalid');
            message = "favor completar los cmapos obligatorios";
        }

        if(!imageNew){
            message = "favor completar los cmapos obligatorios, la imagen tambien es obligatoria para estos mensajes";
        }

        document.getElementById('alertAddPopUp').innerHTML = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
            message +
            "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>"+
        "</div>";
        
    }




}

function getDetailsPopUp(popId){

    $.ajax({
        url: routeG+'admin/get/information/popUp',
        data: {
            popId,
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        dataType: 'json',
        success: function(resp) {
            console.log(resp);
            
            document.getElementById('updateTitlePopUp').value = resp.popUp['title'];
            document.getElementById('updateDescriptionPopUp').value = resp.popUp['content'];
            document.getElementById('popId').value = resp.popUp['popId'];
            
            // document.getElementById('bodyTablePopUp').innerHTML = resp.table;
            // $('#viewDetailsAwards').modal({
            //     keyboard: false
            // });
            // $('#viewDetailsAwards').modal('hide');

        },
        error: function(err) {
            console.log(err);
        }
    });
    

}

function editPopUp(){

    title = document.getElementById('updateTitlePopUp').value;
    content = document.getElementById('updateDescriptionPopUp').value;
    popId = document.getElementById('popId').value;
    if(updateImage && extension){
        image = updateImage._value;
    }else{
        image = "";
    }

    Swal.fire({
        title: '¿Estas seguro de modificar la información?',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Guardar`,
        denyButtonText: `No guardar`,
    }).then((result) => {
        if (result.isConfirmed) {

            
            $.ajax({
                url: routeG+'admin/update/popUp',
                data: { 
                    image,
                    extension,
                    title,
                    popId,
                    content,
                    _token :$('meta[name="csrf-token"]').attr('content')
                },
                type : 'POST',
                dataType: 'json',
                success: function(resp) {
                    console.log(resp);
                    document.getElementById('bodyTablePopUp').innerHTML = resp.table;
                    $('#viewDetailsPopUp').modal({
                        keyboard: false
                    });
                    $('#viewDetailsPopUp').modal('hide');
    
                },
                error: function(err) {
                    console.log(err);
                }
            });
            
        }
    });

}

function updateStatusPopUp(popId, status_id ){

    textMessage = "";
    if( status_id == 29 ){
        titleMessage = "¿Estas seguro de desactivar este mensaje?";
    }else{
        titleMessage = "¿Estas seguro de activar este mensaje?";
        textMessage = "Se daran de baja los otros avisos para poder mostrar este.";
    }

    Swal.fire({
        title: titleMessage,
        text: textMessage,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Guardar`,
        denyButtonText: `No guardar`,
    }).then((result) => {
    if (result.isConfirmed) {
        
        $.ajax({
            url: routeG+'admin/update/status/popUp',
            data: { 
                popId,
                status_id,
                _token :$('meta[name="csrf-token"]').attr('content')
            },
            type : 'POST',
            dataType: 'json',
            success: function(resp) {
                console.log(resp);
                document.getElementById('bodyTablePopUp').innerHTML = resp.table;
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
});

}

