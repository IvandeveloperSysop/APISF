function addBonus(){
    bonusName = document.getElementById('bonusName').value;
    bonusDescriptión = document.getElementById('bonusDescriptión').value;
    multiplier = document.getElementById('multiplier').value;
    begins_at = document.getElementById('bonusInitial_date').value;
    ends_at = document.getElementById('bonusFinal_date').value;
    promoId = document.getElementById('promoId').value;
    bono3months = document.getElementById('bonusValid3months').checked;

    if( multiplier && begins_at && ends_at){

        Swal.fire({
            title: '¿Estas seguro de crear el bonus?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: `Guardar`,
            denyButtonText: `No guardar`,
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: routeG+'admin/new/bonus',
                    data: { 
                        bonusName,
                        bonusDescriptión,
                        multiplier,
                        begins_at,
                        ends_at,
                        promoId,
                        bono3months,
                        _token :$('meta[name="csrf-token"]').attr('content')
                    },
                    type : 'POST',
                    dataType: 'json',
                    success: function(resp) {
                        console.log(resp);
                        if(resp.result == 'ok'){
                            document.getElementById('bodyTableBonus').innerHTML = resp.table;
                            
                            $('#addBonus').modal({
                                keyboard: false
                            });
                            $('#addBonus').modal('hide');
                            document.getElementById("addBonusForm").reset();
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

function getBonus(bonus_id){

    $.ajax({
        url: routeG+'admin/get/bonus/'+bonus_id,
        data: { 
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        dataType: 'json',
        success: function(resp) {
            console.log(resp);
            document.getElementById('bonusNameUpdate').value = resp.bonus['title'];
            document.getElementById('bonusDescriptiónUpdate').value = resp.bonus['description'];
            document.getElementById('multiplierUpdate').value = resp.bonus['multiplier'];
            document.getElementById('bonusInitial_dateUpdate').value = resp.bonus['begins_at'].replace(' ', 'T');
            document.getElementById('bonusFinal_dateUpdate').value = resp.bonus['ends_at'].replace(' ', 'T');
            document.getElementById('bonusId').value = bonus_id;
            // document.getElementById('').innerHTML = resp.selected;
        },
        error: function(err) {
            console.log(err);
        }
    });

}

function updateBonus(){

    title = document.getElementById('bonusNameUpdate').value;
    description = document.getElementById('bonusDescriptiónUpdate').value;
    multiplier = document.getElementById('multiplierUpdate').value;
    begins_at = document.getElementById('bonusInitial_dateUpdate').value;
    ends_at = document.getElementById('bonusFinal_dateUpdate').value;
    bonus_id = document.getElementById('bonusId').value;

    if(title && description && multiplier && begins_at && ends_at){

        $.ajax({
            url: routeG+'admin/update/bonus',
            data: {
                title,
                description,
                multiplier,
                begins_at,
                ends_at,
                bonus_id,
                _token :$('meta[name="csrf-token"]').attr('content')
            },
            type : 'POST',
            dataType: 'json',
            success: function(resp) {
                console.log(resp);
                if(resp.result == 'ok'){
                    document.getElementById('bodyTableBonus').innerHTML = resp.table;
                    $('#viewDetailsBonus').modal({
                        keyboard: false
                    });
                    $('#viewDetailsBonus').modal('hide');
                }

                // document.getElementById('').innerHTML = resp.selected;
            },
            error: function(err) {
                console.log(err);
            }
        });

    }
}

function deleteBonus(bonus_id, typeView){

    Swal.fire({
        title: '¿Deseas dar de baja el bonus?',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Aceptar`,
        denyButtonText: `Cancelar`,
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: routeG+'admin/down/bonus/'+bonus_id,
                data: { 
                    typeView,
                    _token :$('meta[name="csrf-token"]').attr('content')
                },
                type : 'POST',
                dataType: 'json',
                success: function(resp) {
                    console.log(resp);
                    if(resp.result == 'ok'){
                        document.getElementById('bodyTableBonus').innerHTML = resp.table;
                    }
                },
                error: function(err) {
                    console.log(err);
                }
            });
            
        }
    });

}
