let img;
let extension;
const plus = document.querySelector(".plus"),
minus = document.querySelector(".minus"),
num = document.querySelector(".num");
let a = 1;


// add award BD Window = La pantalla donde se mostrara, esto sirve para mostrar todos los premios o solo mostrar los de la promoción actual
function postStore(){

 
    promoId = document.getElementById('promoId').value;
    nameAward = document.getElementById('addAwardName').value;
    descriptionAward = document.getElementById('addAwardDescriptión').value;
    stockAward = document.getElementById('addAwardStock').value;
    addAwardPrice = document.getElementById('addAwardPrice').value;
    maxPerUser = document.getElementById('maxPerUser').value;

    if(img && extension){
        image = img._value;
    }else{
        image = "";
    }

    if( promoId && nameAward && descriptionAward && stockAward && addAwardPrice && img && maxPerUser){

        Swal.fire({
            title: '¿Deseas agregar el producto a la tienda?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: `Guardar`,
            denyButtonText: `No guardar`,
        }).then((result) => {
            if (result.isConfirmed) {
                loading();
                $.ajax({
                    url: routeG+'admin/add/product/store',
                    data: { 
                        promoId,
                        nameAward,
                        descriptionAward,
                        image,
                        extension,
                        stockAward,
                        addAwardPrice,
                        maxPerUser,
                        _token :$('meta[name="csrf-token"]').attr('content')
                    },
                    type : 'POST',
                    dataType: 'json',
                    success: function(resp) {
                        console.log(resp);
                        document.getElementById('bodyTableAward').innerHTML = resp.table;
                        $('#addAward').modal({
                            keyboard: false
                        });
                        $('#addAward').modal('hide');
                        document.getElementById("formAddAward").reset();

                        alertSuccess();
                        
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
                
            }
        });
    }else{
        document.getElementById('alertAddAward').innerHTML = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
            "Favor de llenar todos los campos obligatorios"+
            "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>"+
        "</div>";
    }

}

function  getDetailsAwards(id){

    $.ajax({
        url: routeG+'admin/get/store/details/'+ id,
        data: { 
            _token :$('meta[name="csrf-token"]').attr('content')
        },
        type : 'GET',
        dataType: 'json',
        success: function(resp) {
            console.log(resp);
            if ( resp.result == 'ok' ) {

                document.getElementById('updateAwardName').value = resp.award['awardName'];
                document.getElementById('updateAwardDescriptión').value = resp.award['description'];
                document.getElementById('updateAwardStock').value = resp.award['stock'];
                document.getElementById('updateAwardPrice').value = resp.award['price'];
                document.getElementById('UpdateAwardId').value = resp.award['awardId'];
                document.getElementById('updatePromoId').value = resp.award['promoId'];
                document.getElementById('maxPerUserUpdate').value = resp.award['max_per_user'];
                
            }
        },
        error: function(err) {
            console.log(err);
        }
    });

}

// Update 
function editAward(){

    promoId = document.getElementById('updatePromoId').value;
    awardName = document.getElementById('updateAwardName').value;
    description = document.getElementById('updateAwardDescriptión').value;
    stock = document.getElementById('updateAwardStock').value;
    price = document.getElementById('updateAwardPrice').value;
    awardId = document.getElementById('UpdateAwardId').value;
    maxPerUser = document.getElementById('maxPerUserUpdate').value;


    if(img && extension){
        image = img._value;
    }else{
        image = "";
    }

    if( awardName && description && maxPerUser ){

        Swal.fire({
            title: '¿Estas seguro de modificar la información?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: `Guardar`,
            denyButtonText: `No guardar`,
        }).then((result) => {
            if (result.isConfirmed) {
    
               loading();
                $.ajax({
                    url: routeG+'admin/update/store/product',
                    data: { 
                        promoId,
                        awardName,
                        description,
                        stock,
                        image,
                        price,
                        awardId,
                        extension,
                        maxPerUser,
                        _token :$('meta[name="csrf-token"]').attr('content')
                    },
                    type : 'POST',
                    dataType: 'json',
                    success: function(resp) {
                        console.log(resp);
                        alertSuccess();
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

        
    }else{
        document.getElementById('alertUpdateAward').innerHTML = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>"+
            "Favor de llenar todos los campos obligatorios"+
            "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>"+
        "</div>";
    }




}

async function changeImgToB64Product(inputName){
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

function alertSuccess(){
    Swal.close();
    img = null;
    document.getElementById('addImageAward').value = '';

    Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: 'Cambios guardados',
        showConfirmButton: false,
        timer: 1500
    });
}

function updatePlus(){
    stockNum = parseInt(document.getElementById('updateAwardStock').value);
    if(isNaN(stockNum)){
        stockNum = 0;
    }
    document.getElementById('updateAwardStock').value = stockNum + 1;
}

//  Delete
function deleteAward(awardId){

    // promoId = document.getElementById('promoId').value;

    Swal.fire({
        title: '¿Estas seguro de borrar este premio?',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `Guardar`,
        denyButtonText: `No guardar`,
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: routeG+'admin/delete/store/product',
                data: {
                    awardId,
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

function updateMin(){
    stockNum = parseInt(document.getElementById('updateAwardStock').value);
    if(stockNum > 1){
        document.getElementById('updateAwardStock').value = stockNum - 1;
    }
}

plus.addEventListener("click", ()=>{
    a++;
    a = (a < 10) ? a : a;
    num.value = a;
});

minus.addEventListener("click", ()=>{
    if(a > 1){
        a--;
        a = (a < 10) ? a : a;
        num.value = a;
    }
});


