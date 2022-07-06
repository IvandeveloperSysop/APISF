var $ = jQuery.noConflict();
list = document.getElementsByClassName('presentation');
// arrPresentation = { };
var valueToPush = new Array();
totalProducts = 0;
arrPresentation = [];
arrPresentation[0];
totalPriceProducts = 0;

totalOtherProducts = 0;
arrOtherPresentation = [];
arrOtherPresentation[0];
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

function validStatus(){
    status = document.getElementById('selectOption').value;
    document.getElementById('selectOption').classList.remove('is-invalid');
    document.getElementById('numTicket').classList.remove('is-invalid');
    document.getElementById('comment').classList.remove('is-invalid');

    if(status == 0){
        $('#numTicket').attr('disabled', 'disabled');
        $('#hour_ticket').attr('disabled', 'disabled');
        $('#state').attr('disabled', 'disabled');
        $('#store').attr('disabled', 'disabled');
        $('#products').attr('disabled', 'disabled');
        $('#comment').removeAttr('disabled');
        document.getElementById('commentInvalid').style.display = "none";
        document.getElementById('comment').style.display = "block";
    }else{
        $('#comment').attr('disabled', 'disabled');
        $('#numTicket').removeAttr('disabled');
        $('#hour_ticket').removeAttr('disabled');
        $('#store').removeAttr('disabled');
        $('#state').removeAttr('disabled');
        $('#products').removeAttr('disabled');

        if(status == 46){
            document.getElementById('commentInvalid').style.display = "block";
            document.getElementById('comment').style.display = "none";
            $('#commentInvalid').removeAttr('disabled');
        }else{
            $('#commentInvalid').attr('disabled', 'disabled');
            document.getElementById('commentInvalid').value = 'Comentario';
        }
    }
}

function validOption(){
    status = document.getElementById('selectOption').value;
    numTicket = document.getElementById('numTicket').value;
    comment = document.getElementById('comment').value;
    hour_ticket = document.getElementById('hour_ticket').value;
    commentTInvalid = document.getElementById('commentInvalid').value;
    store = document.getElementById('store').value;
    // store = 'Guadalajara';
    // state = document.getElementById('state').value;
    state = 1;
    totalProducts = document.getElementById('products').value;

    if(status === "Selecciona el estatus"){
        document.getElementById('selectOption').classList.add('is-invalid');
        return
    }else{
        document.getElementById('selectOption').classList.remove('is-invalid');
        document.getElementById('numTicket').classList.remove('is-invalid');
        document.getElementById('store').classList.remove('is-invalid');
        document.getElementById('products').classList.remove('is-invalid');
        document.getElementById('comment').classList.remove('is-invalid');
        if(status == 0){
            if(comment === "Comentario"){
                document.getElementById('comment').classList.add('is-invalid');
                return;
            }
            numTicket = 'nada';
        }else{
            if(status == 46 && commentTInvalid == "Comentario"){
                document.getElementById('commentInvalid').classList.add('is-invalid');
                return;
            }

            // document.getElementById('commentInvalid')
            if(!numTicket){
                document.getElementById('numTicket').classList.add('is-invalid');
                return;
            }

            if(!hour_ticket){
                document.getElementById('hour_ticket').classList.add('is-invalid');
                return;
            }

            if((!state) ){
                document.getElementById('alerts').innerHTML = "<div class='alert alert-danger' role='alert'>"+
                "Favor de llenar los campos obligatorios"+
                "</div>";
                document.getElementById('state').classList.add('is-invalid');
                // document.getElementById('products').classList.add('is-invalid');
                return;
            }else{
                document.getElementById('alerts').innerHTML = "";
            }

            if((!store) ){
                document.getElementById('alerts').innerHTML = "<div class='alert alert-danger' role='alert'>"+
                "Favor de llenar los campos obligatorios"+
                "</div>";
                document.getElementById('store').classList.add('is-invalid');
                // document.getElementById('products').classList.add('is-invalid');
                return;
            }else{
                document.getElementById('alerts').innerHTML = "";
            }

            // return;
            comment = 'nada';
        }

        document.getElementById('comment').classList.remove('is-invalid');

        validP = validProducts();

        if(validP != 'ok'){
            // console.log(validP)
            document.getElementById('alerts').innerHTML = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
                "Favor de poner un valor en alguna de las presentaciones"+
                "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>"+
            "</div>";

            errorConfig();

        }else{
            Swal.fire({
                title: '¿Quieres guardar los cambios?',
                showDenyButton: true,
                showCancelButton: false,
                confirmButtonText: `Guardar`,
                denyButtonText: `No guardar`,
            }).then((result) => {
                if (result.isConfirmed) {
                    loading();
                    ticketId = document.getElementById('ticketId').value;
                    comment = comment.replace(/\s/g, '_'); //HimynameisFlavio

                    $.ajax({
                        url: routeG+'admin/ticket/update/conf/'+ticketId+'/'+status+'/'+comment+'/'+numTicket,
                        data: {
                            store : store,
                            arrPresentation : arrPresentation,
                            arrOtherPresentation,
                            commentTInvalid,
                            hour_ticket,
                            _token :$('meta[name="csrf-token"]').attr('content')
                        },
                        type : 'POST',
                        dataType: 'json',
                        success: function(respuesta) {
                            console.log(respuesta);
                            if(respuesta.message == 'ok'){

                                document.getElementById('estatusDiv').innerHTML = respuesta.result;
                                document.getElementById('numTicket').classList.remove('is-invalid');
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    text: 'Información guardada',
                                    showConfirmButton: false,
                                    timer: 2100
                                });
                                document.getElementById('button-submitSave').innerHTML = respuesta.buttonSave;
                                alertSuccess();
                                // location.reload();
                                return;
                            }else if(respuesta.message == 'numTicketInvalid'){

                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'error',
                                    color: '#F8D7DA',
                                    text: 'El numero de ticket ya se encuentra en el sistema',
                                    showConfirmButton: false,
                                    timer: 2500
                                });
                                document.getElementById('numTicket').classList.add('is-invalid');

                            } else{
                                console.log(respuesta.result);
                            }
                            Swal.close();
                        },
                        error: function(err) {
                            console.log(err.responseJSON.errors);
                        }
                    });

                }
            });
        }

    }
}

function errorConfig(){
    Swal.fire({
        position: 'top-end',
        icon: 'error',
        text: 'Favor de poner un valor en alguna de las presentaciones',
        showConfirmButton: false,
        timer: 1500
    });
}

function productAll(idPresentation){

    sumtotalProducts = 0;
    arrPresentationProduct = new Array();
    valor = document.getElementById('presentation'+idPresentation).value;
    price = document.getElementById('presentationPrice'+idPresentation).value;
    for (let item of list) {
        if(item.value){
            sumtotalProducts = sumtotalProducts + parseFloat(item.value);
        }
    }
    arrPresentation[idPresentation] = {idPresentation : idPresentation, price: price ,value : valor};
    totalProducts = sumtotalProducts;
    // console.log(arrPresentation);
    document.getElementById('products').value = sumtotalProducts;
    totalProductsP = 0;
    arrPresentation.map(function(x) {
        totalProductsP = Number(totalProductsP) + Number(x.price);
     });

    document.getElementById('totalPriceProducts').value = totalProductsP;

}

function productOtherAll(idPresentation){
    sumtotalProducts = 0;
    // arrPresentationProduct = new Array();
    valor = document.getElementById('presentationOther'+idPresentation).value;
    price = document.getElementById('presentationOtherPrice'+idPresentation).value;
    for (let item of list) {
        if(item.value){
            sumtotalProducts = sumtotalProducts + parseFloat(item.value);
        }
    }
    arrOtherPresentation[idPresentation] = {idPresentation : idPresentation, price: price ,value : valor};
    totalOtherProducts = sumtotalProducts;
    // console.log(arrOtherPresentation);
    document.getElementById('products').value = sumtotalProducts;

    totalProductsP = 0;
    arrOtherPresentation.map(function(x) {
        totalProductsP = Number(totalProductsP) + Number(x.price);
     });
    document.getElementById('totalPriceProductsNotArca').value = totalProductsP;

}

function windoNewOpen(url){
    window.open(url,'_blank','location=0,menubar=0,toolbar=0,personalbar=0,status=0,scrollbars=0,width=650,height=860')
}

function validProducts(){

    if(document.getElementById('selectOption').value != 0){

        validP = 'non';
        $(".presentation").map(function() {
            if(this.value){
                validP = 'ok';
            }
        });
    }else{
        validP = 'ok';
    }

    return validP;

}
