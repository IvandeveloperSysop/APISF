function  getDetailsOrder(id){

    $.ajax({
        url: routeG+'admin/get/order/details/'+ id,
        data: { 
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'GET',
        dataType: 'json',
        success: function(resp) {
            // console.log(resp);
            if ( resp.result == 'ok' ) {

                document.getElementById('orderId').value = resp.order['orderId'];
                document.getElementById('updateAwardName').value = resp.order['productName'];
                document.getElementById('updateUserName').value = resp.order['userName'];
                document.getElementById('updateUserCellphone').value = resp.order['cellPhone'];
                document.getElementById('selectOrderStatus').innerHTML = resp.selectStatus;
                document.getElementById('updateAwardPrice').value = resp.order['price'];
                document.getElementById('imageAwardDetail').innerHTML = resp.image;
                // image

                document.getElementById('updateAwardAddress').value = resp.order['street'];
                document.getElementById('city').value = resp.order['city'];
                document.getElementById('estate').value = resp.order['state'];
                document.getElementById('country').value = resp.order['suburb'];
                document.getElementById('zip').value = resp.order['zip'];

                document.getElementById('country').value = "México";
                document.getElementById('zip').value = resp.order['cp']
                
            }
        },
        error: function(err) {
            console.log(err);
        }
    });

}

function editOrder(){
    orderId = document.getElementById('orderId').value;
    status_id = document.getElementById('statusSelectOrder').value;

    // return status_id;

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
                url: routeG+'admin/order/update',
                data: {
                    orderId,
                    status_id,
                    _token :$('meta[name="csrf-token"]').attr('content')
                },
                type : 'POST',
                dataType: 'json',
                success: function(resp) {
                    console.log(resp);
                    Swal.close();
                    if ( resp.result == 'ok' ) {
                        $('#viewDetailsAwards').modal({
                            keyboard: false
                        });
                        $('#viewDetailsAwards').modal('hide');
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'Orden actualizada',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        location.reload();
                    }
                },
                error: function(err) {
                    console.log(err);
                }
            });
            
        }
    });

}