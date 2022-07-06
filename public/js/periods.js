function addPeriod(){

    initial_date = document.getElementById('initial_date').value;
    final_date = document.getElementById('final_date').value;
    promoId = document.getElementById('promoId').value;
    // var myModal = new bootstrap.Modal(document.getElementById('addPeriod'), {
    //     keyboard: false
    // });

    if( initial_date && final_date ){
        Swal.fire({
            title: '¿Estas seguro de crear el nuevo periodo?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: `Guardar`,
            denyButtonText: `No guardar`,
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: routeG+'admin/new/period',
                    data: { 
                        initial_date,
                        final_date,
                        promoId,
                        _token :$('meta[name="csrf-token"]').attr('content')
                    },
                    type : 'POST',
                    dataType: 'json',
                    success: function(resp) {
                        console.log(resp);
                        if(resp.result == 'ok'){
                            document.getElementById('bodyTableBody').innerHTML = resp.table;
                            
                            $('#addPeriod').modal({
                                keyboard: false
                            });
                            $('#addPeriod').modal('hide');
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

}