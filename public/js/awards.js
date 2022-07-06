img = '';
extension = '';
positionsArr = [];


function getPromos(){

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
  

async function changeImgToB64(inputName){
    let file = document.getElementById(inputName).files[0];
    extension = document.getElementById(inputName).files[0]['name'].split('.').pop()
    const  fileType = file['type'];
    const validImageTypes = ['image/gif', 'image/jpeg', 'image/png'];
    if (!validImageTypes.includes(fileType)) {
        console.log('nonImage');
        return;
    }else{
        img = toBase64(file);
    }
}

toBase64 = file => new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result);
    reader.onerror = error => reject(error);
});


function  getDetailsAwards(id){

    $.ajax({
        url: routeG+'admin/get/award/details/'+ id,
        data: { 
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'GET',
        dataType: 'json',
        success: function(resp) {
            // console.log(resp);
            if ( resp.result == 'ok' ) {

                // document.getElementById('updateImageAward').value = resp.award['image'];
                document.getElementById('updateAwardName').value = resp.award['awardName'];
                document.getElementById('updateAwardDescriptión').value = resp.award['description'];
                document.getElementById('imageAward').innerHTML = resp.imgAward;
                // document.getElementById('promoIdUpdate').value = resp.award['title'];
                document.getElementById('promoIdUpdate').innerHTML = resp.selectedPromotions['selected'];
                document.getElementById('awardId').value = resp.award['awardId'];

                searchPositionAwardsUpdates( resp.award['awardId'], resp.award['promoAward'] );
                
            }
        },
        error: function(err) {
            console.log(err);
        }
    });

}

// add award BD Window = La pantalla donde se mostrara, esto sirve para mostrar todos los premios o solo mostrar los de la promoción actual
function postAward(window = 'index'){

 
    promoId = document.getElementById('promoId').value;
    nameAward = document.getElementById('addAwardName').value;
    awardDescription = document.getElementById('addAwardDescriptión').value;

    if(img && extension){
        image = img._value;
    }else{
        image = "";
    }

    var isEmpty  = isObjectEmpty(positionsArr);

    if( !isEmpty ){

        if( promoId && addAwardName && addAwardDescriptión){

            Swal.fire({
                title: '¿Deseas agregar el premio?',
                showDenyButton: true,
                showCancelButton: false,
                confirmButtonText: `Guardar`,
                denyButtonText: `No guardar`,
            }).then((result) => {
                if (result.isConfirmed) {
                    loading();
                    $.ajax({
                        url: routeG+'admin/add/award',
                        data: { 
                            promoId,
                            nameAward,
                            awardDescription,
                            image,
                            extension,
                            positionsArr,
                            window,
                            _token :$('meta[name="csrf-token"]').attr('content')
                        },
                        type : 'POST',
                        dataType: 'json',
                        success: function(resp) {
                            Swal.close();
                            console.log(resp);
                            document.getElementById('bodyTableAward').innerHTML = resp.table;
                            $('#addAward').modal({
                                keyboard: false
                            });
                            $('#addAward').modal('hide');
                            positionsArr = [];

                            document.getElementById('positionsChecks').innerHTML = '';
                            // document.getElementById('addAwardName').value = '';
                            document.getElementById("formAddAward").reset();
                        },
                        error: function(err) {
                            console.log(err);
                        }
                    });
                    
                }
            });
        }else{
            document.getElementById('alertModalAddAward').innerHTML = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
            "Es necesario escoger las posiciones para la entrega del premio"+
            "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>"+
        "</div>";
        }
    }else{
        console.log('Array vacio');
        document.getElementById('alertModalAddAward').innerHTML = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
            "Es necesario escoger las posiciones para la entrega del premio"+
            "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>"+
        "</div>";
    }




}

// Update 
function editAward(){

    promoId = document.getElementById('promoIdUpdate').value;
    awardName = document.getElementById('updateAwardName').value;
    description = document.getElementById('updateAwardDescriptión').value;
    // stock = document.getElementById('updateAwardStock').value;
    position = document.getElementById('positionUpdate').value;
    awardId = document.getElementById('awardId').value;

    if(img && extension){
        image = img._value;
    }else{
        image = "";
    }

    if( addAwardName && addAwardDescriptión ){

        Swal.fire({
            title: '¿Estas seguro de modificar la información?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: `Guardar`,
            denyButtonText: `No guardar`,
        }).then((result) => {
            if (result.isConfirmed) {
    
               
                $.ajax({
                    url: routeG+'admin/update/award',
                    data: { 
                        promoId,
                        awardName,
                        description,
                        position,
                        image,
                        awardId,
                        extension,
                        _token :$('meta[name="csrf-token"]').attr('content')
                    },
                    type : 'POST',
                    dataType: 'json',
                    success: function(resp) {
                        console.log(resp);
                        document.getElementById('bodyTableAward').innerHTML = resp.table;
                        $('#viewDetailsAwards').modal({
                            keyboard: false
                        });
                        $('#viewDetailsAwards').modal('hide');
        
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
                
            }
        });

        
    }




}

//  Delete
function deleteAward(awardId, window = 'index'){

    promoId = document.getElementById('promoId').value;

    Swal.fire({
        title: '¿Estas seguro de borrar este premio?',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Borrar`,
        denyButtonText: `Cancelar`,
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: routeG+'admin/delete/award',
                data: {
                    awardId,
                    window,
                    promoId,
                    _token :$('meta[name="csrf-token"]').attr('content')
                },
                type : 'POST',
                dataType: 'json',
                success: function(resp) {
                    console.log(resp);
                    if( resp.result == 'ok' ){
                        document.getElementById('bodyTableAward').innerHTML = resp.table;
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

function searchPositionAwards(){

    promoId = document.getElementById('promoId').value;

    if(promoId != 0){
        $.ajax({
            url: routeG+'admin/get/positions/awards',
            data: {
                promoId,
                _token :$('meta[name="csrf-token"]').attr('content')
            },
            type : 'POST',
            dataType: 'json',
            success: function(resp) {
                console.log(resp);
                document.getElementById('positionsChecks').innerHTML = resp.result;
    
            },
            error: function(err) {
                console.log(err);
            }
        });
    }else{
        document.getElementById('positionsChecks').innerHTML = "";
    }

}

function searchPositionAwardsUpdates(awardId, promoId = null){

    if(!promoId){   
        promoId = document.getElementById('promoId').value;
    }

    $.ajax({
        url: routeG+'admin/get/positions/awards/updates',
        data: {
            promoId,
            awardId,
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'POST',
        dataType: 'json',
        success: function(resp) {
            console.log(resp);
            document.getElementById('positionsChecksUpdates').innerHTML = resp.result;
            document.getElementById('positionUpdate').value = resp.positionSelected;

        },
        error: function(err) {
            console.log(err);
        }
    });

}


function checkedPosition(check, position){

    positionsArr = {
        ...positionsArr,
        [position]:{
            'check': check,
        }
    };

    console.log(positionsArr);

}

function changePosition(position){

    document.getElementById('positionUpdate').value = position;

}


function isObjectEmpty(object) {
    var isEmpty = true;
    for (keys in object) {
        isEmpty = false;
        break; // exiting since we found that the object is not empty
    }
    return isEmpty;
}