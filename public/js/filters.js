function enableCorte(){
    let promo_id = document.getElementById('selectPromoId').value;

    if( promo_id != 0){
        $.ajax({
            url: routeG + 'admin/getCortes/data',
            data: { 
                promo_id : promo_id,
                _token :$('meta[name="csrf-token"]').attr('content')
            },
            type : 'POST',
            dataType: 'json',
            success: function(resp) {
                if(resp.result == 'ok'){
                    $("#corteSelected").prop( "disabled", false );
                    document.getElementById('corteSelected').innerHTML = resp.cortes;
                }else{
                    document.getElementById('corteSelected').innerHTML = "<option selected>Favor de seleccionar una promoción valida</option>";
                    $("#corteSelected").prop( "disabled", true );
                }
            },
            error: function(err) {
                console.log(err);
                document.getElementById('corteSelected').innerHTML = "<option value='0' selected>Favor de seleccionar una promoción valida</option>";
                $("#corteSelected").prop( "disabled", true );
                // console.log(err);
            }
        });
        
    }else{
        document.getElementById('corteSelected').innerHTML = "<option value='0' selected>Favor de seleccionar una promoción valida</option>";
        $("#corteSelected").prop( "disabled", true );
    }

}

function filterPromotion(){

    let promo_id = document.getElementById('selectPromoId').value;
    let date_initial = document.getElementById('date_initial').value;
    let date_final = document.getElementById('date_final').value;

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
        url: routeG + 'admin/filters/promotion',
        data: { 
            promo_id,
            date_initial,
            date_final,
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        dataType: 'json',
        success: function(resp) {
            console.log(resp);
            if(resp.result == 'ok' || resp.result == 'non'){
                document.getElementById('bodyTable').innerHTML = resp.table;
                document.getElementById('paginationTable').innerHTML = "";
            }
        },
        error: function(err) {
            console.log(err);
        }
    });

}

function filterAwards(){

    let promo_id = document.getElementById('selectPromoId').value;
    // let date_initial = document.getElementById('date_initial').value;
    // let date_final = document.getElementById('date_final').value;

    $.ajax({
        url: routeG + 'admin/filters/awards',
        data: { 
            promo_id,
            // date_initial,
            // date_final,
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        dataType: 'json',
        success: function(resp) {
            console.log(resp);
            if(resp.result == 'ok' || resp.result == 'non'){
                document.getElementById('bodyTableAward').innerHTML = resp.table;
                document.getElementById('paginationTable').innerHTML = "";
            }
        },
        error: function(err) {
            console.log(err);
        }
    });

}

function filterPresentations(){
    let presentation_id = document.getElementById('selectPromoId').value;
    // let date_initial = document.getElementById('date_initial').value;
    // let date_final = document.getElementById('date_final').value;

    $.ajax({
        url: routeG + 'admin/filters/presentation',
        data: { 
            presentation_id,
            // date_initial,
            // date_final,
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        dataType: 'json',
        success: function(resp) {
            console.log(resp);
            if(resp.result == 'ok' || resp.result == 'non'){
                document.getElementById('bodyTablePresentation').innerHTML = resp.table;
                document.getElementById('paginationTable').innerHTML = "";
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function validatorsFilters(){
    nameUser = document.getElementById('nameFilter').value;
    email = document.getElementById('emailFilter').value;
    status = document.getElementById('statusFilter').value;

    $.ajax({
        url: routeG + 'admin/filters/validatorsUsers',
        data: { 
            nameUser,
            email,
            status,
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        dataType: 'json',
        success: function(resp) {
            console.log(resp);
            if(resp.result == 'ok' || resp.result == 'non'){
                document.getElementById('bodyTable').innerHTML = resp.table;
                document.getElementById('paginationTable').innerHTML = "";
            }
        },
        error: function(err) {
            console.log(err);
        }
    });

}
