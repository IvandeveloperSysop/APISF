

function filterShare(status = 1){
    let promo_id = document.getElementById('selectPromoId').value;
    let user_name = document.getElementById('user_name').value;
    let date_initial = document.getElementById('date_initial').value;
    let date_final = document.getElementById('date_final').value;
    let corteSelected = document.getElementById('corteSelected').value;

    document.getElementById('date_initial').classList.remove('is-invalid');
    document.getElementById('date_final').classList.remove('is-invalid');
    if(date_initial && date_final){
        if(date_final < date_initial){
            document.getElementById('alertFilter').innerHTML = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
                "La fecha final no debe ser menor a la fecha inicial"+
                "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>"+
            "</div>";
            document.getElementById('date_initial').classList.add('is-invalid');
            document.getElementById('date_final').classList.add('is-invalid');
            return;
        }
    }

    $.ajax({
        url: routeG + 'admin/filterShare/search',
        data: { 
            promo_id,
            user_name,
            date_initial,
            date_final,
            corteSelected,
            status,
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        dataType: 'json',
        success: function(resp) {
            console.log(resp);
            if(resp.result == 'ok' || resp.result == 'non'){
                document.getElementById('bodyTable').innerHTML = resp.table;
            }
        },
        error: function(err) {
            console.log(err);
        }
    });

}