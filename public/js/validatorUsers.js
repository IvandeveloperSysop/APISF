function postUser(){
    nameUser = document.getElementById('addName').value;
    email = document.getElementById('addEmail').value;
    password = document.getElementById('addPassword').value;

    if (nameUser && email && password) {
        
        Swal.fire({
            title: '¿Quieres guardar los cambios?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: `Guardar`,
            denyButtonText: `No guardar`,
        }).then((result) => {
            if (result.isConfirmed) {
                loading();
                
                $.ajax({
                    url: routeG+'admin/added/validator/user',
                    data: { 
                        nameUser,
                        email,
                        password,
                        _token :$('meta[name="csrf-token"]').attr('content')
                    },
                    type : 'POST',
                    dataType: 'json',
                    success: function(resp) {
                        console.log(resp);
                        if(resp.result == 'ok'){
                            alertSuccess();
                        }else{
                            console.log(resp);
                        }
                        Swal.close();
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
                
            }
        });

    } else {
        document.getElementById('alerts').innerHTML = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
        "Favor de llenar los campos obligatorios"+
        "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>"+
      "</div>" ;
    }
    

}


function getValidatorDetails(id){

    $.ajax({
        url: routeG+'admin/get/validatorDetails/'+id,
        type : 'GET',
        dataType: 'json',
        success: function(resp) {
            console.log(resp);
            if(resp.result == 'ok'){
                document.getElementById('updateName').value = resp.user['name'];
                document.getElementById('updateEmail').value = resp.user['email'];
                document.getElementById('userId').value = id;
            }else{
                console.log(resp);
            }
            Swal.close();
        },
        error: function(err) {
            console.log(err);
        }
    });

}

function editValidator(){
    
    nameUser = document.getElementById('updateName').value;
    email = document.getElementById('updateEmail').value;
    password = document.getElementById('updatePassword').value;
    userId = document.getElementById('userId').value;

    $.ajax({
        url: routeG+'admin/update/userValidator',
        data: {
            nameUser,
            email,
            password,
            userId,
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        dataType: 'json',
        success: function(resp) {
            console.log(resp);
            if (resp.result == 'ok') {
                alertSuccess();
                return;
            }else{}
        },
        error: function(err) {
            console.log(err);
        }
    });

}

function downAdminUser(id,status){

    Swal.fire({
        title: '¿Deseas eliminar el usuario?',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Guardar`,
        denyButtonText: `No guardar`,
    }).then((result) => {
        if (result.isConfirmed) {
            loading();
            
            $.ajax({
                url: routeG+'admin/down/validator/user',
                data: { 
                    id,
                    status,
                    _token :$('meta[name="csrf-token"]').attr('content')
                },
                type : 'POST',
                dataType: 'json',
                success: function(resp) {
                    console.log(resp);
                    if(resp.result == 'ok'){
                        alertSuccess();
                        return;
                    }else{
                        console.log(resp);
                    }
                    Swal.close();
                },
                error: function(err) {
                    console.log(err);
                }
            });
            
        }
    });

}

function alertSuccess(){
    Swal.fire({
        position: 'top-end',
        icon: 'success',
        text: 'Información guardada',
        showConfirmButton: false,
        timer: 2100
    });
    
    location.reload();
}

